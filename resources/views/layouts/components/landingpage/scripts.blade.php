<div class="scrollToTop">
    <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
</div>
<div id="responsive-overlay"></div>

<!-- Popper JS -->
<script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>

<!-- Bootstrap JS -->
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Color Picker JS -->
<script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>

<!-- Choices JS -->
<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

<!-- Swiper JS -->
<script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

@yield('scripts')

<!-- Defaultmenu JS -->
<script type="module" src="{{ asset('assets/js/defaultmenu.js') }}"></script>

<!-- Internal Landing JS -->
<script src="{{ asset('assets/js/landing.js') }}"></script>

<!-- Node Waves JS-->
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

<!-- Landing Sticky JS -->
<script src="{{ asset('assets/js/landing-sticky.js') }}"></script>

<!-- Toastify JS -->
<script src="{{asset('assets/libs/toastify-js/src/toastify.js')}}"></script>

<!-- show toast -->
<script>
    // Check for session flash messages and display them
    @if (session('successMessage'))
        showToast('{{ session('successMessage') }}', 'success', '');
    @endif

    @if (session('errorMessage'))
        showToast('{{ session('errorMessage') }}', 'error', '');
    @endif

    @if (session('warningMessage'))
        showToast('{{ session('warningMessage') }}', 'warning', '');
    @endif

    @if (session('infoMessage'))
        showToast('{{ session('infoMessage') }}', 'info', '');
    @endif
    function showToast(message, type = 'success', position = 'top') {
        backgroundColor = 'var(--primary-color)';
        if (type === 'error') {
            backgroundColor = '#e74c3c';
        } else if (type === 'warning') {
            backgroundColor = '#f1c40f';
        } else if (type === 'info') {
            backgroundColor = '#3498db';
        } else if (type === 'success') {
            backgroundColor = '#2ecc71';
        }
        Toastify({
            text: message,
            duration: 3000,
            newWindow: true,
            close: true,
            gravity: position,
            backgroundColor: backgroundColor,
        }).showToast();
    }
</script>
