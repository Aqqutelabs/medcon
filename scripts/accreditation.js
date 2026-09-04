(function(){
  const dialog=document.querySelector('[data-certificate-dialog]');if(!dialog)return;
  document.querySelectorAll('.verification-stage').forEach(function(stage,index){stage.id='stage-'+String(index+1).padStart(2,'0');});
  const frame=dialog.querySelector('[data-certificate-frame]'),title=dialog.querySelector('[data-certificate-title]'),fallback=dialog.querySelector('[data-certificate-fallback]');let trigger=null;
  document.addEventListener('click',function(event){const button=event.target.closest('[data-certificate-open]');if(!button)return;trigger=button;title.textContent=button.dataset.documentTitle;frame.src=button.dataset.documentUrl+'#view=FitH';fallback.href=button.dataset.documentUrl;dialog.showModal();});
  function close(){dialog.close();frame.removeAttribute('src');if(trigger)trigger.focus();}
  dialog.querySelector('[data-certificate-close]').addEventListener('click',close);
  dialog.addEventListener('click',function(event){if(event.target===dialog)close();});
  dialog.addEventListener('cancel',function(event){event.preventDefault();close();});
})();
