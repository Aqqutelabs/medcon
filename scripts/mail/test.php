<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require_once __DIR__.'/config.php';require_once __DIR__.'/TemplateRenderer.php';require_once __DIR__.'/MailTransport.php';
$failures=[];$assert=function(bool $ok,string $label)use(&$failures){echo($ok?'PASS ':'FAIL ').$label.PHP_EOL;if(!$ok)$failures[]=$label;};
$events=['account.created','account.verified','password.reset_requested','password.reset_completed','application.completed','application.status_changed','application.document_action_required','application.admission_update','application.visa_update','affiliate.new_student','inquiry.received','eligibility.result'];
$renderer=new MedconTemplateRenderer();foreach($events as $event){$mail=$renderer->render($event,['name'=>'Ada <script>','action_url'=>'https://example.test/action','sign_in_url'=>'https://example.test/login','eligibility_url'=>'https://example.test/eligibility','fees_url'=>'https://example.test/fees','result_label'=>'Likely eligible','result_summary'=>'Initial result','result_next'=>'Review documents','status_label'=>'Under review','document_label'=>'Passport','student_name'=>'Eunice Student']);$assert($mail['subject']!==''&&str_contains($mail['html'],'&lt;script&gt;')&&!str_contains($mail['html'],'Ada <script>'),'template '.$event.' renders and escapes');}
$config=medcon_mail_config();$assert(in_array($config['mode'],['test','live','unconfigured'],true),'configuration mode is recognized');$assert($config['mode']!=='unconfigured','SendByte credentials are configured');if($config['mode']==='live')echo "INFO configured key is live; workflow events will deliver real email.\n";
exit($failures?1:0);
