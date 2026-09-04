<?php
require_once __DIR__ . '/../includes/student.php';
require_once __DIR__ . '/../includes/document-storage.php';
require_once dirname(__DIR__,2) . '/scripts/mail/NotificationService.php';
require_once dirname(__DIR__,2) . '/scripts/mail/config.php';

$student=require_student($pdo); $errors=[];
$applicationStmt=$pdo->prepare("SELECT id,status FROM applications WHERE student_id=? AND status<>'draft' ORDER BY created_at DESC LIMIT 1");
$applicationStmt->execute([$student['id']]); $application=$applicationStmt->fetch()?:null;
$documentTypes=document_type_options(); $requiredTypes=required_document_types();

$loadDocuments=function() use($pdo,$student,$application){
    if(!$application) return [];
    $stmt=$pdo->prepare('SELECT id,document_type,file_name,file_type,file_size,status,admin_note,uploaded_at,updated_at,file_path FROM documents WHERE student_id=? AND application_id=? ORDER BY created_at');
    $stmt->execute([$student['id'],$application['id']]); return $stmt->fetchAll();
};
$documents=$loadDocuments(); $documentsByType=[];
foreach($documents as $document) $documentsByType[$document['document_type']][]=$document;

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!verify_csrf($_POST['_csrf']??'')) $errors[]='Your session expired. Refresh the page and try again.';
    if(!$application) $errors[]='Submit your application before adding documents.';
    $batch=[]; $validatedBatch=[];
    foreach($documentTypes as $type=>$label){
        $set=$_FILES['documents']['name'][$type]??[];
        if(!is_array($set)) $set=[$set];
        foreach($set as $index=>$name){
            if($name==='') continue;
            $batch[$type][]=[
                'name'=>$name,
                'type'=>$_FILES['documents']['type'][$type][$index]??'',
                'tmp_name'=>$_FILES['documents']['tmp_name'][$type][$index]??'',
                'error'=>$_FILES['documents']['error'][$type][$index]??UPLOAD_ERR_NO_FILE,
                'size'=>$_FILES['documents']['size'][$type][$index]??0,
            ];
        }
    }
    foreach($batch as $type=>$uploads){
        if($type!=='wassce_result'&&count($uploads)>1) $errors[]=$documentTypes[$type].' accepts one file only.';
        foreach($uploads as $index=>$upload){
            try{$validatedBatch[$type][$index]=validate_application_document_upload($upload);}
            catch(RuntimeException $exception){$errors[]=$documentTypes[$type].': '.$exception->getMessage();}
        }
    }
    $completeTypes=[];
    foreach($documentsByType as $type=>$items) foreach($items as $item) if(in_array($item['status'],['uploaded','approved'],true)){$completeTypes[]=$type;break;}
    $completeTypes=array_unique(array_merge($completeTypes,array_keys($batch)));
    if(array_diff($requiredTypes,$completeTypes)) $errors[]='Add all five required document categories before clicking Finish.';

    if(!$errors){
        $savedPaths=[]; $oldPaths=[]; $pdo->beginTransaction();
        try{
            foreach($batch as $type=>$uploads){
                $replacementQueue=[];
                foreach($documentsByType[$type]??[] as $item) if(in_array($item['status'],['rejected','resubmission_required'],true)) $replacementQueue[]=$item;
                $hasCurrent=false; foreach($documentsByType[$type]??[] as $item) if(in_array($item['status'],['uploaded','approved'],true)){$hasCurrent=true;break;}
                if($type!=='wassce_result'&&$hasCurrent) throw new RuntimeException($documentTypes[$type].' has already been submitted.');
                foreach($uploads as $index=>$upload){
                    $stored=store_application_document($upload,(int)$student['id'],(int)$application['id'],$validatedBatch[$type][$index]);
                    $savedPaths[]=document_storage_path($stored['storage_key']); $now=date('Y-m-d H:i:s'); $replace=array_shift($replacementQueue);
                    if($replace){
                        $pdo->prepare("UPDATE documents SET file_name=?,file_path=?,file_type=?,file_size=?,status='uploaded',admin_note=NULL,uploaded_at=?,reviewed_at=NULL,reviewed_by=NULL,updated_at=? WHERE id=? AND student_id=?")->execute([$stored['original_name'],$stored['storage_key'],$stored['mime_type'],$stored['file_size'],$now,$now,$replace['id'],$student['id']]);
                        if($replace['file_path']) $oldPaths[]=document_storage_path($replace['file_path']);
                    }else{
                        $pdo->prepare("INSERT INTO documents (student_id,application_id,document_type,file_name,file_path,file_type,file_size,status,uploaded_at,created_at,updated_at) VALUES (?,?,?,?,?,?,?,'uploaded',?,?,?)")->execute([$student['id'],$application['id'],$type,$stored['original_name'],$stored['storage_key'],$stored['mime_type'],$stored['file_size'],$now,$now,$now]);
                    }
                }
            }
            $now=date('Y-m-d H:i:s');
            $pdo->prepare("UPDATE applications SET status='received',updated_at=? WHERE id=? AND student_id=? AND status IN ('submitted','documents_pending','received')")->execute([$now,$application['id'],$student['id']]);
            $pdo->prepare('INSERT INTO messages (recipient_user_id,application_id,subject,body) VALUES (?,?,?,?)')->execute([$student['user_id'],$application['id'],'Application and documents received','Your application and required supporting documents are complete and have been sent to the admissions team for review.']);
            $pdo->commit(); foreach($oldPaths as $oldPath) if(is_file($oldPath)) @unlink($oldPath);
            $mailConfig=medcon_mail_config();medcon_notify($pdo,'application.completed',(int)$student['user_id'],'application.completed:application:'.$application['id'],['action_url'=>$mailConfig['base_url'].'/app/student/application-summary.php'],'application',(int)$application['id']);
            flash_set('success','Success! Your application and documents have been submitted for review.');
            header('Location: '.app_url('student/dashboard.php')); exit;
        }catch(Throwable $exception){
            if($pdo->inTransaction()) $pdo->rollBack(); foreach($savedPaths as $savedPath) if(is_file($savedPath)) @unlink($savedPath);
            $errors[]=$exception instanceof RuntimeException?$exception->getMessage():'Your documents could not be submitted. Please try again.';
        }
    }
}

$completedTypes=[]; foreach($documentsByType as $type=>$items) foreach($items as $item) if(in_array($item['status'],['uploaded','approved'],true)){$completedTypes[]=$type;break;}
$requiredComplete=!array_diff($requiredTypes,$completedTypes);
student_portal_start('Documents','documents',$student);
?>
<section class="mc-portal-heading"><div><span class="mc-kicker">Application documents</span><h1>Your document checklist</h1><p>Select PDF, JPG, or PNG files up to 5MB. Files remain here and are uploaded only when you click Finish.</p></div></section>
<?php if($errors): ?><div class="mc-errors" role="alert"><?php foreach($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach; ?></div><?php endif; ?>
<?php if(!$application): ?><section class="mc-panel mc-empty-state"><h2>Submit your application first</h2><p>Documents become available after your application enquiry is submitted.</p><a class="mc-arrow-link" href="<?= esc(app_url('student/application.php')) ?>">Open application →</a></section><?php else: ?>
<form method="post" enctype="multipart/form-data" class="mc-document-stage" data-document-stage><?= csrf_field() ?>
<section class="mc-panel"><div class="mc-section-head"><div><h2>Documents</h2><p>Transcript and other supporting documents are optional. WASSCE allows multiple files.</p></div><strong data-required-progress><?= count(array_intersect($requiredTypes,$completedTypes)) ?>/<?= count($requiredTypes) ?> required</strong></div><div class="mc-document-list">
<?php foreach($documentTypes as $key=>$label): $items=$documentsByType[$key]??[]; $isRequired=in_array($key,$requiredTypes,true); $hasCurrent=in_array($key,$completedTypes,true); $canSelect=$key==='wassce_result'||!$hasCurrent; ?>
<article class="mc-document-row mc-student-document-row" data-document-row data-required="<?= $isRequired?'true':'false' ?>" data-complete="<?= $hasCurrent?'true':'false' ?>"><div><span class="mc-document-label"><strong><?= esc($label) ?></strong><em><?= $isRequired?'Required':'Optional' ?></em></span><?php if($items): ?><div class="mc-uploaded-files"><?php foreach($items as $document): ?><span><a href="<?= esc(app_url('document-download.php?id='.$document['id'])) ?>" target="_blank" rel="noopener"><?= esc($document['file_name']) ?></a><small><?= esc(number_format(((int)$document['file_size'])/1048576,2)) ?>MB</small><b class="mc-status-pill"><?= esc(application_status_label($document['status'])) ?></b><?php if($document['admin_note']): ?><small><?= esc($document['admin_note']) ?></small><?php endif; ?></span><?php endforeach; ?></div><?php else: ?><small data-empty-label>No file selected</small><?php endif; ?><div class="mc-staged-files" data-staged-files aria-live="polite"></div></div>
<?php if($canSelect): ?><label class="mc-upload-control" for="document-<?= esc($key) ?>"><span><?= $key==='wassce_result'&&$items?'Add result':'Select file' ?></span><input id="document-<?= esc($key) ?>" type="file" name="documents[<?= esc($key) ?>][]" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"<?= $key==='wassce_result'?' multiple':'' ?> data-document-input></label><?php endif; ?></article>
<?php endforeach; ?></div></section>
<section class="mc-finish-panel<?= $requiredComplete?' is-ready':'' ?>" data-finish-panel><div><span class="mc-kicker" data-finish-kicker><?= $requiredComplete?'Required documents complete':'Complete the required documents' ?></span><h2>Finish your submission</h2><p>Nothing is uploaded until you click Finish. You can remove a selected file using ×.</p></div><?php if($application['status']==='received'): ?><a class="mc-btn-primary mc-button-link" href="<?= esc(app_url('student/application-summary.php')) ?>">View summary →</a><?php else: ?><button class="mc-btn-primary" type="submit" data-finish-button<?= $requiredComplete?'':' disabled' ?>>Finish</button><?php endif; ?></section>
</form>
<?php endif; ?>
<script>
(function(){
  var form=document.querySelector('[data-document-stage]'); if(!form)return;
  var requiredTotal=<?= count($requiredTypes) ?>,button=form.querySelector('[data-finish-button]'),panel=form.querySelector('[data-finish-panel]'),progress=form.querySelector('[data-required-progress]'),kicker=form.querySelector('[data-finish-kicker]');
  function refresh(){var complete=0;form.querySelectorAll('[data-document-row][data-required="true"]').forEach(function(row){var input=row.querySelector('[data-document-input]');if(row.dataset.complete==='true'||(input&&input.files.length))complete++;});progress.textContent=complete+'/'+requiredTotal+' required';if(button)button.disabled=complete!==requiredTotal;panel.classList.toggle('is-ready',complete===requiredTotal);kicker.textContent=complete===requiredTotal?'Required documents complete':'Complete the required documents';}
  function render(input){var row=input.closest('[data-document-row]'),list=row.querySelector('[data-staged-files]'),empty=row.querySelector('[data-empty-label]');list.innerHTML='';if(empty)empty.hidden=input.files.length>0;Array.from(input.files).forEach(function(file,index){var chip=document.createElement('span');chip.className='mc-staged-file';var name=document.createElement('span');name.textContent=file.name;var remove=document.createElement('button');remove.type='button';remove.setAttribute('aria-label','Remove '+file.name);remove.textContent='×';remove.addEventListener('click',function(){var transfer=new DataTransfer();Array.from(input.files).forEach(function(candidate,i){if(i!==index)transfer.items.add(candidate);});input.files=transfer.files;render(input);refresh();});chip.append(name,remove);list.appendChild(chip);});}
  form.querySelectorAll('[data-document-input]').forEach(function(input){input.addEventListener('change',function(){render(input);refresh();});});refresh();
})();
</script>
<?php student_portal_end(); ?>
