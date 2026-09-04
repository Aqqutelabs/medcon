(function(){
const script=document.currentScript;
const selects=Array.from(document.querySelectorAll('[data-state-select]'));if(!selects.length)return;
const endpoint=script&&script.dataset.locationsUrl?script.dataset.locationsUrl:new URL('../../location-options.php',script.src).href;
fetch(endpoint,{headers:{Accept:'application/json'}}).then(function(response){if(!response.ok)throw new Error('Locations unavailable');return response.json();}).then(function(locations){
selects.forEach(function(state){const lga=state.closest('form').querySelector('[data-lga-select]');if(!lga)return;
function update(){const current=lga.dataset.current||lga.value;const items=locations[state.value]||[];const prompt=state.value?'Select LGA':'Select state first';lga.innerHTML='<option value="">'+prompt+'</option>'+items.map(function(item){const safe=item.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/"/g,'&quot;');return '<option value="'+safe+'">'+safe+'</option>';}).join('');lga.disabled=!state.value;if(current&&items.includes(current))lga.value=current;lga.dataset.current='';}
state.addEventListener('change',update);update();});
}).catch(function(){selects.forEach(function(state){const lga=state.closest('form').querySelector('[data-lga-select]');if(lga)lga.innerHTML='<option value="">Unable to load LGAs</option>';});});
})();
