// Minimal client-side validation
document.addEventListener('DOMContentLoaded', function(){
    var sf = document.getElementById('signup-form');
    if (sf){
        sf.addEventListener('submit', function(e){
            var p = sf.querySelector('input[name="password"]').value || '';
            var c = sf.querySelector('input[name="confirm_password"]').value || '';
            if (p.length < 8){
                alert('Password must be at least 8 characters');
                e.preventDefault();
                return;
            }
            if (p !== c){
                alert('Passwords do not match');
                e.preventDefault();
            }
        });
    }
});
