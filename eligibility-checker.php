<?php
$eligibilitySessionPath = __DIR__ . '/data/sessions';
if (is_dir($eligibilitySessionPath) && is_writable($eligibilitySessionPath)) {
    session_save_path($eligibilitySessionPath);
}
session_start();
require __DIR__ . '/includes/site.php';
require_once __DIR__ . '/app/includes/db.php';
require_once __DIR__ . '/app/includes/functions.php';
require_once __DIR__ . '/app/includes/cloudflare.php';
require_once __DIR__ . '/scripts/mail/NotificationService.php';
require_once __DIR__ . '/scripts/mail/config.php';

if (empty($_SESSION['eligibility_csrf'])) {
    $_SESSION['eligibility_csrf'] = bin2hex(random_bytes(24));
}

$fieldNames = [
    'current_country', 'current_country_other', 'nationality', 'nationality_other', 'age_range',
    'education_level', 'secondary_credits', 'mathematics', 'english_language', 'biology',
    'chemistry', 'physics', 'secondary_completion_year', 'university_status', 'degree_program',
    'degree_program_other', 'english_instruction', 'legal_case', 'medically_fit', 'passport_status',
    'financial_sponsor', 'cost_considered', 'full_name', 'phone', 'email', 'contact_method',
    'consultant_request'
];

$requiredFields = [
    'current_country', 'nationality', 'age_range', 'education_level', 'secondary_credits',
    'mathematics', 'english_language', 'biology', 'chemistry', 'physics', 'secondary_completion_year',
    'university_status', 'english_instruction', 'legal_case', 'medically_fit', 'passport_status',
    'financial_sponsor', 'cost_considered', 'full_name', 'phone', 'email', 'contact_method',
    'consultant_request'
];

$answers = array_fill_keys($fieldNames, '');
$errors = [];
$result = null;

function eligibility_csv_value($value) {
    $value = trim((string) $value);
    if ($value !== '' && preg_match('/^[=+\-@]/', $value)) {
        return "'" . $value;
    }
    return $value;
}

function selected_answer($answers, $field, $value) {
    return ($answers[$field] ?? '') === $value ? ' selected' : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fieldNames as $field) {
        $answers[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    if (!hash_equals($_SESSION['eligibility_csrf'], (string) ($_POST['csrf_token'] ?? ''))) {
        $errors[] = 'Your session expired. Please refresh the page and try again.';
    }

    if (!empty($_POST['website'])) {
        $errors[] = 'The submission could not be accepted.';
    }

    foreach ($requiredFields as $field) {
        if ($answers[$field] === '') {
            $errors[] = 'Please answer every required question before checking eligibility.';
            break;
        }
    }

    if ($answers['current_country'] === 'Other' && $answers['current_country_other'] === '') {
        $errors[] = 'Please enter your current country.';
    }
    if ($answers['nationality'] === 'Other' && $answers['nationality_other'] === '') {
        $errors[] = 'Please enter your nationality.';
    }
    if ($answers['university_status'] !== 'No' && $answers['degree_program'] === '') {
        $errors[] = 'Please select your university degree or programme.';
    }
    if ($answers['degree_program'] === 'Other' && $answers['degree_program_other'] === '') {
        $errors[] = 'Please enter your degree or programme.';
    }
    if (!filter_var($answers['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (!preg_match('/^[0-9+()\s\-]{7,24}$/', $answers['phone'])) {
        $errors[] = 'Please enter a valid phone or WhatsApp number.';
    }
    if (empty($_POST['consent'])) {
        $errors[] = 'Please confirm that Medcon may use your answers for this assessment and follow-up.';
    }

    if (!$errors) {
        $subjectAnswers = [
            $answers['secondary_credits'], $answers['mathematics'], $answers['english_language'],
            $answers['biology'], $answers['chemistry'], $answers['physics']
        ];
        $hasAcademicDeficiency = in_array('No', $subjectAnswers, true);
        $hasPendingAcademicAnswer = in_array('Awaiting', $subjectAnswers, true) || in_array('Awaiting results', $subjectAnswers, true);
        $completedDegree = $answers['university_status'] === 'Yes, completed';
        $readinessConcern = $answers['english_instruction'] === 'No'
            || $answers['legal_case'] !== 'No'
            || $answers['medically_fit'] !== 'Yes';

        if ($hasAcademicDeficiency || $answers['medically_fit'] === 'No') {
            $result = [
                'status' => 'not-eligible',
                'label' => 'Currently not eligible for direct Medicine admission',
                'summary' => 'Based on your answers, you do not currently appear to meet the minimum requirements for direct entry into a Doctor of Medicine programme.',
                'next' => 'You may need to obtain additional required subjects or qualifications, complete a bachelor’s or pre-medical pathway, or receive an individual academic assessment.',
            ];
        } elseif ($completedDegree && !$hasPendingAcademicAnswer && !$readinessConcern) {
            $result = [
                'status' => 'eligible',
                'label' => 'Likely eligible',
                'summary' => 'Based on the information you provided, you appear to meet the initial academic requirements to apply for Medicine in the Philippines.',
                'next' => 'Your next step is to complete document verification and receive a university-specific assessment. Admission remains subject to the selected university’s requirements.',
            ];
        } else {
            $result = [
                'status' => 'potential',
                'label' => 'Potentially eligible — further assessment required',
                'summary' => 'You may qualify for Medicine in the Philippines, but additional information or document review is required before your pathway can be confirmed.',
                'next' => 'A Medcon education consultant should review your academic records and advise whether you should apply directly or follow a preparatory or pre-medical pathway.',
            ];
        }

        $csvPath = getenv('MEDCON_ELIGIBILITY_CSV') ?: __DIR__ . '/data/eligibility-submissions.csv';
        $csvHandle = @fopen($csvPath, 'ab');
        if ($csvHandle && flock($csvHandle, LOCK_EX)) {
            $row = [date('c')];
            foreach ($fieldNames as $field) {
                $row[] = eligibility_csv_value($answers[$field]);
            }
            $row[] = $result['status'];
            $row[] = 'Yes';
            fputcsv($csvHandle, $row);
            fflush($csvHandle);
            flock($csvHandle, LOCK_UN);
            fclose($csvHandle);
        } else {
            if (is_resource($csvHandle)) {
                fclose($csvHandle);
            }
            $errors[] = 'Your assessment was completed, but the submission could not be saved. Please contact Medcon directly.';
        }

        if (!$errors) {
            try {
                $email=strtolower($answers['email']);$existing=$pdo->prepare('SELECT u.id,s.id student_id FROM users u LEFT JOIN students s ON s.user_id=u.id WHERE u.email=? LIMIT 1');$existing->execute([$email]);$account=$existing->fetch()?:null;
                $insert=$pdo->prepare('INSERT INTO eligibility_assessments(user_id,student_id,full_name,email,phone,result_status,result_label,result_summary,result_next_step,answers_json) VALUES(?,?,?,?,?,?,?,?,?,?)');
                $insert->execute([$account['id']??null,$account['student_id']??null,$answers['full_name'],$email,$answers['phone'],$result['status'],$result['label'],$result['summary'],$result['next'],json_encode($answers,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)]);$assessmentId=(int)$pdo->lastInsertId();$mailConfig=medcon_mail_config();$signInUrl=$mailConfig['base_url'].'/app/login.php';$actionUrl=$signInUrl;$actionLabel='Sign in to view your result';
                if(!$account){$rawToken=rtrim(strtr(base64_encode(random_bytes(32)),'+/','-_'),'=');$pdo->prepare('INSERT INTO eligibility_account_tokens(assessment_id,token_hash,expires_at) VALUES(?,?,DATE_ADD(NOW(),INTERVAL 24 HOUR))')->execute([$assessmentId,hash('sha256',$rawToken)]);$actionUrl=$mailConfig['base_url'].'/app/complete-eligibility-account.php?token='.rawurlencode($rawToken);$actionLabel='Set password and open dashboard';}
                medcon_notify_email($pdo,'eligibility.result',$email,'eligibility.result:assessment:'.$assessmentId,['name'=>$answers['full_name'],'result_label'=>$result['label'],'result_summary'=>$result['summary'],'result_next'=>$result['next'],'action_url'=>$actionUrl,'action_label'=>$actionLabel,'sign_in_url'=>$signInUrl],'eligibility_assessment',$assessmentId);
            } catch(Throwable $exception) { error_log('Eligibility assessment database persistence failed.');$errors[]='Your result was calculated, but your account record could not be prepared. Please contact Medcon.'; }
        }
    }
}

render_header(
    'Student Eligibility Checker | Medcon',
    'Complete the Medcon initial eligibility checker for medical education in the Philippines.',
    'eligibility-checker.php'
);
render_breadcrumb('Eligibility Checker');
?>

<section class="eligibility-intro">
    <div class="container eligibility-intro-grid">
        <div>
            <span class="eyebrow">Initial admissions screening</span>
            <h1>Check your eligibility to study Medicine in the Philippines</h1>
        </div>
        <p>Answer the questions below to receive an initial pathway assessment. This checker does not guarantee admission, a visa, professional recognition, or licensing.</p>
    </div>
</section>

<section class="section eligibility-section">
    <div class="container eligibility-layout">
        <form class="form-card eligibility-form" method="post" action="#eligibility-result">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['eligibility_csrf']) ?>">
            <label class="eligibility-honeypot" aria-hidden="true">Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
            <div class="eligibility-form-heading">
                <span class="eyebrow">Student eligibility checker</span>
                <h2>Tell us about your academic background</h2>
                <p class="form-note"><span class="required-marker" aria-hidden="true">*</span> indicates a required field. Complete all sections for the most useful assessment.</p>
            </div>

            <?php if ($errors): ?>
                <div class="form-errors" role="alert">
                    <strong>Please review your submission:</strong>
                    <ul><?php foreach (array_unique($errors) as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <fieldset>
                <legend><span>01</span> About you</legend>
                <div class="form-grid">
                    <label><?php form_label('Country where you currently live', true); ?><select required name="current_country"><option value="">Select country</option><?php foreach (['Nigeria','Ghana','Cameroon','South Africa','Other'] as $option): ?><option<?= selected_answer($answers, 'current_country', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <label><?php form_label('If other, enter your country'); ?><input type="text" name="current_country_other" value="<?= e($answers['current_country_other']) ?>" placeholder="e.g. Kenya"></label>
                    <label><?php form_label('Nationality', true); ?><select required name="nationality"><option value="">Select nationality</option><?php foreach (['Nigerian','Other'] as $option): ?><option<?= selected_answer($answers, 'nationality', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <label><?php form_label('If other, enter your nationality'); ?><input type="text" name="nationality_other" value="<?= e($answers['nationality_other']) ?>" placeholder="e.g. Ghanaian"></label>
                    <label><?php form_label('Age range', true); ?><select required name="age_range"><option value="">Select age range</option><?php foreach (['Under 18','18–20','21–24','25–29','30+'] as $option): ?><option<?= selected_answer($answers, 'age_range', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <label><?php form_label('Highest education completed', true); ?><select required name="education_level"><option value="">Select education level</option><?php foreach (['WAEC/NECO/High School','Diploma','Bachelor’s Degree','Master’s Degree','Other'] as $option): ?><option<?= selected_answer($answers, 'education_level', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                </div>
            </fieldset>

            <fieldset>
                <legend><span>02</span> Secondary school qualifications</legend>
                <div class="form-grid">
                    <?php
                    $secondaryQuestions = [
                        'secondary_credits' => ['At least 5 recognised secondary-school credits', ['Yes','No','Awaiting results']],
                        'mathematics' => ['Credit or pass in Mathematics', ['Yes','No','Awaiting']],
                        'english_language' => ['Credit or pass in English Language', ['Yes','No','Awaiting']],
                        'biology' => ['Credit or pass in Biology', ['Yes','No','Awaiting']],
                        'chemistry' => ['Credit or pass in Chemistry', ['Yes','No','Awaiting']],
                        'physics' => ['Credit or pass in Physics', ['Yes','No','Awaiting']],
                    ];
                    foreach ($secondaryQuestions as $field => [$label, $options]):
                    ?>
                        <label><?php form_label($label, true); ?><select required name="<?= e($field) ?>"><option value="">Select answer</option><?php foreach ($options as $option): ?><option<?= selected_answer($answers, $field, $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <?php endforeach; ?>
                    <label><?php form_label('Year completed WAEC/NECO or equivalent', true); ?><input required type="number" name="secondary_completion_year" min="1970" max="<?= date('Y') + 1 ?>" value="<?= e($answers['secondary_completion_year']) ?>" placeholder="e.g. 2025"></label>
                </div>
            </fieldset>

            <fieldset>
                <legend><span>03</span> University education</legend>
                <div class="form-grid">
                    <label><?php form_label('University degree status', true); ?><select required name="university_status" data-degree-status><option value="">Select answer</option><?php foreach (['Yes, completed','Yes, currently studying','No'] as $option): ?><option<?= selected_answer($answers, 'university_status', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <label data-degree-field><?php form_label('Degree or programme'); ?><select name="degree_program"><option value="">Select programme</option><?php foreach (['Biological Sciences','Biochemistry','Microbiology','Chemistry','Pharmacy','Nursing','Medical Laboratory Science','Anatomy/Physiology','Other science/health-related degree','Other'] as $option): ?><option<?= selected_answer($answers, 'degree_program', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <label data-degree-field><?php form_label('If other, enter your programme'); ?><input type="text" name="degree_program_other" value="<?= e($answers['degree_program_other']) ?>" placeholder="e.g. Public Health"></label>
                </div>
            </fieldset>

            <fieldset>
                <legend><span>04</span> Readiness, health and character</legend>
                <div class="form-grid">
                    <label><?php form_label('Previous education primarily conducted in English', true); ?><select required name="english_instruction"><option value="">Select answer</option><?php foreach (['Yes','No'] as $option): ?><option<?= selected_answer($answers, 'english_instruction', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <label><?php form_label('Criminal conviction or ongoing legal case', true); ?><select required name="legal_case"><option value="">Select answer</option><?php foreach (['No','Yes','Prefer to discuss privately'] as $option): ?><option<?= selected_answer($answers, 'legal_case', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <label><?php form_label('Medically fit for demanding study and clinical training', true); ?><select required name="medically_fit"><option value="">Select answer</option><?php foreach (['Yes','No','Unsure / require assessment'] as $option): ?><option<?= selected_answer($answers, 'medically_fit', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <label><?php form_label('International passport status', true); ?><select required name="passport_status"><option value="">Select answer</option><?php foreach (['Yes','No','Application in progress'] as $option): ?><option<?= selected_answer($answers, 'passport_status', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                </div>
            </fieldset>

            <fieldset>
                <legend><span>05</span> Financial readiness</legend>
                <div class="form-grid">
                    <label><?php form_label('Financial sponsor', true); ?><select required name="financial_sponsor"><option value="">Select sponsor</option><?php foreach (['Parents','Family member','Self-funded','Scholarship','Other'] as $option): ?><option<?= selected_answer($answers, 'financial_sponsor', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <label><?php form_label('Considered tuition and living costs', true); ?><select required name="cost_considered"><option value="">Select answer</option><?php foreach (['Yes','No','Need a cost estimate'] as $option): ?><option<?= selected_answer($answers, 'cost_considered', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                </div>
            </fieldset>

            <fieldset>
                <legend><span>06</span> Contact and consultation</legend>
                <div class="form-grid">
                    <label><?php form_label('Full name', true); ?><input required type="text" name="full_name" autocomplete="name" value="<?= e($answers['full_name']) ?>" placeholder="e.g. Adaeze Okafor"></label>
                    <label><?php form_label('WhatsApp or phone number', true); ?><input required type="tel" name="phone" autocomplete="tel" value="<?= e($answers['phone']) ?>" placeholder="e.g. +234 703 696 1056"></label>
                    <label><?php form_label('Email address', true); ?><input required type="email" name="email" autocomplete="email" value="<?= e($answers['email']) ?>" placeholder="e.g. yourname@email.com"></label>
                    <label><?php form_label('Preferred contact method', true); ?><select required name="contact_method"><option value="">Select method</option><?php foreach (['WhatsApp','Phone call','Email'] as $option): ?><option<?= selected_answer($answers, 'contact_method', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <label><?php form_label('Speak with an education consultant', true); ?><select required name="consultant_request"><option value="">Select answer</option><?php foreach (['Yes','No'] as $option): ?><option<?= selected_answer($answers, 'consultant_request', $option) ?>><?= e($option) ?></option><?php endforeach; ?></select></label>
                </div>
                <label class="checkbox-line"><input required type="checkbox" name="consent" value="1"<?= !empty($_POST['consent']) ? ' checked' : '' ?>><span>I agree that Medcon may use my answers to provide this initial assessment and contact me about admissions support. <span class="required-marker" aria-hidden="true">*</span><span class="sr-only"> (required)</span></span></label>
            </fieldset>

            <button class="btn btn-primary eligibility-submit" type="submit">Check My Eligibility</button>
        </form>

        <aside class="eligibility-aside">
            <span class="eyebrow">Before you begin</span>
            <h2>Have these details ready</h2>
            <ul class="check-list">
                <li>Your WAEC, NECO, or equivalent subjects</li>
                <li>Your highest completed qualification</li>
                <li>Your degree programme, where applicable</li>
                <li>Your passport and financial-readiness status</li>
                <li>Your preferred contact details</li>
            </ul>
            <p>This initial result helps Medcon understand the most suitable next step. Final decisions remain with the selected institution and relevant authorities.</p>
        </aside>
    </div>
</section>

<?php if ($result): ?>
<section class="eligibility-result-section" id="eligibility-result" tabindex="-1">
    <div class="container">
        <article class="eligibility-result eligibility-result-<?= e($result['status']) ?>" role="status" aria-live="polite">
            <span class="result-kicker">Your initial assessment</span>
            <h2><?= e($result['label']) ?></h2>
            <p><?= e($result['summary']) ?></p>
            <div class="result-next-step"><strong>Recommended next step</strong><p><?= e($result['next']) ?></p></div>
            <p class="result-disclaimer">This result is an initial screening only. It is not an offer of admission or a guarantee of visa approval, professional licensing, or recognition.</p>
            <div class="hero-actions">
                <?php if ($result['status'] === 'eligible'): ?>
                    <a class="btn btn-primary" href="<?= e(site_url('apply.php')) ?>">Apply Now</a>
                <?php else: ?>
                    <a class="btn btn-secondary" href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener">Talk to a Counsellor</a>
                <?php endif; ?>
            </div>
        </article>
    </div>
</section>
<?php endif; ?>

<?php render_footer(); ?>
