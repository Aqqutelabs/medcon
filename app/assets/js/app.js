// Minimal client-side validation
document.addEventListener('DOMContentLoaded', function(){
    var slider = document.querySelector('[data-auth-slider]');
    var progress = document.querySelector('[data-auth-progress]');
    if (slider) {
        var slides = Array.prototype.slice.call(slider.querySelectorAll('.mc-auth-slide'));
        var active = 0;
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (progress && !reduceMotion) progress.classList.add('is-running');
        if (slides.length > 1 && !reduceMotion) {
            window.setInterval(function () {
                slides[active].classList.remove('is-active');
                active = (active + 1) % slides.length;
                slides[active].classList.add('is-active');
                if (progress) {
                    progress.classList.remove('is-running');
                    void progress.offsetWidth;
                    progress.classList.add('is-running');
                }
            }, 20000);
        }
    }

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

    // Password visibility toggle
    var passwordToggles = document.querySelectorAll('.mc-password-toggle');
    passwordToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            var wrapper = this.closest('.mc-password-wrapper');
            var input = wrapper.querySelector('input');
            var isVisible = input.type === 'text';
            input.type = isVisible ? 'password' : 'text';
            this.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
            this.setAttribute('data-show', !isVisible);
        });
    });
});
