<?php
declare(strict_types=1);

final class MedconTemplateRenderer
{
    public function render(string $event, array $data): array
    {
        $name = htmlspecialchars((string) ($data['name'] ?? 'there'), ENT_QUOTES, 'UTF-8');
        $link = htmlspecialchars((string) ($data['action_url'] ?? ''), ENT_QUOTES, 'UTF-8');
        $status = htmlspecialchars((string) ($data['status_label'] ?? ''), ENT_QUOTES, 'UTF-8');
        $note = nl2br(htmlspecialchars((string) ($data['note'] ?? ''), ENT_QUOTES, 'UTF-8'));
        $templates = [
            'account.created' => ['Verify your Medcon email', "Welcome, {$name}. Your Medcon account is ready. Verify your email to secure your profile.", 'Verify email'],
            'account.verified' => ['Your Medcon email is verified', "Hello {$name}, your email has been verified successfully.", 'Open your account'],
            'password.reset_requested' => ['Reset your Medcon password', "Hello {$name}, we received a request to reset your password. This link expires in one hour. If this was not you, you can ignore this email.", 'Reset password'],
            'password.reset_completed' => ['Your Medcon password was changed', "Hello {$name}, your password was changed successfully. If this was not you, contact Medcon support immediately.", 'Sign in'],
            'application.completed' => ['Your Medcon application is complete', "Hello {$name}, we have received your application and required documents. Our admissions team will review them and share the next step.", 'View application'],
            'application.status_changed' => ['Your application status changed', "Hello {$name}, your application is now <strong>{$status}</strong>." . ($note ? "<br><br>{$note}" : ''), 'View application'],
            'application.admission_update' => ['Admission update', "Hello {$name}, there is a new admission update on your application." . ($note ? "<br><br>{$note}" : ''), 'View admission update'],
            'application.visa_update' => ['Visa processing update', "Hello {$name}, your application has moved to visa processing." . ($note ? "<br><br>{$note}" : ''), 'View update'],
            'application.document_action_required' => ['Action required on a document', "Hello {$name}, a document needs your attention: <strong>" . htmlspecialchars((string) ($data['document_label'] ?? 'Uploaded document'), ENT_QUOTES, 'UTF-8') . "</strong>." . ($note ? "<br><br>{$note}" : ''), 'Review documents'],
            'affiliate.new_student' => ['A new student used your referral', "Hello {$name}, " . htmlspecialchars((string) ($data['student_name'] ?? 'a new student'), ENT_QUOTES, 'UTF-8') . " has been linked to your agent account.", 'View your students'],
            'inquiry.received' => ['Thank you for your Medcon enquiry', "Hello {$name}, thank you for contacting Medcon. We have received your information and a member of our admissions team will contact you soon.<br><br>While you wait, you can create your student account, check your initial eligibility, or estimate the cost of your preferred medical programme.", 'Create your account'],
            'eligibility.result' => ['Your Medcon eligibility result', "Hello {$name}, your initial eligibility assessment is ready.<br><br><strong>".htmlspecialchars((string)($data['result_label']??'Assessment complete'),ENT_QUOTES,'UTF-8')."</strong><br>".htmlspecialchars((string)($data['result_summary']??''),ENT_QUOTES,'UTF-8')."<br><br><strong>Recommended next step:</strong><br>".htmlspecialchars((string)($data['result_next']??''),ENT_QUOTES,'UTF-8'), 'Set password and open dashboard'],
        ];
        if (!isset($templates[$event])) throw new InvalidArgumentException('Unknown notification event.');
        [$subject, $message, $button] = $templates[$event];
        if($event==='eligibility.result'&&!empty($data['action_label']))$button=(string)$data['action_label'];
        $cta = $link !== '' ? '<p style="margin:28px 0"><a href="' . $link . '" style="background:#285bf5;color:#fff;text-decoration:none;padding:13px 20px;border-radius:8px;display:inline-block;font-weight:700">' . htmlspecialchars($button, ENT_QUOTES, 'UTF-8') . '</a></p>' : '';
        if($event==='inquiry.received'){$eligibility=htmlspecialchars((string)($data['eligibility_url']??''),ENT_QUOTES,'UTF-8');$fees=htmlspecialchars((string)($data['fees_url']??''),ENT_QUOTES,'UTF-8');$cta='<div style="margin:28px 0"><a href="'.$link.'" style="background:#285bf5;color:#fff;text-decoration:none;padding:13px 18px;border-radius:8px;display:inline-block;font-weight:700;margin:0 8px 10px 0">Create an account</a><a href="'.$eligibility.'" style="border:1px solid #285bf5;color:#285bf5;text-decoration:none;padding:12px 18px;border-radius:8px;display:inline-block;font-weight:700;margin:0 8px 10px 0">Check eligibility</a><a href="'.$fees.'" style="border:1px solid #285bf5;color:#285bf5;text-decoration:none;padding:12px 18px;border-radius:8px;display:inline-block;font-weight:700;margin:0 0 10px">Estimate programme cost</a></div>';}
        if($event==='eligibility.result'){$signIn=htmlspecialchars((string)($data['sign_in_url']??''),ENT_QUOTES,'UTF-8');$cta='<div style="margin:28px 0"><a href="'.$link.'" style="background:#285bf5;color:#fff;text-decoration:none;padding:13px 18px;border-radius:8px;display:inline-block;font-weight:700;margin:0 8px 10px 0">'.htmlspecialchars($button,ENT_QUOTES,'UTF-8').'</a><a href="'.$signIn.'" style="border:1px solid #285bf5;color:#285bf5;text-decoration:none;padding:12px 18px;border-radius:8px;display:inline-block;font-weight:700;margin-bottom:10px">Already registered? Sign in</a></div><p style="font-size:13px;color:#667085">The account link verifies this email address and expires in 24 hours. Your assessment remains an initial screening and is not a guarantee of admission or visa approval.</p>';}
        $html = '<!doctype html><html><body style="margin:0;background:#f4f7fb;font-family:Arial,sans-serif;color:#10182b"><div style="max-width:620px;margin:0 auto;padding:32px 16px"><div style="background:#fff;border:1px solid #e3e8f0;border-radius:12px;padding:32px"><img src="' . htmlspecialchars((string) ($data['logo_url'] ?? ''), ENT_QUOTES, 'UTF-8') . '" alt="Medcon" width="48" style="display:block;margin-bottom:24px"><h1 style="font-size:24px;margin:0 0 16px">' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '</h1><p style="font-size:16px;line-height:1.65;margin:0">' . $message . '</p>' . $cta . '<p style="font-size:13px;color:#667085;margin-top:28px">Medcon Educational Services and Consultancy Limited</p></div></div></body></html>';
        return ['subject' => $subject, 'html' => $html];
    }
}
