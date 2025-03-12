<!-- JAVASCRIPT -->
<script src="{{ asset('back') }}/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

{{-- Sweet Alert 2 --}}
<script src="{{ asset('front/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>

<script src="{{ asset('front/libs/flatpickr/flatpickr.min.js') }}"></script>

{{-- Jquery --}}
<script src="{{ asset('front/libs/jquery/jquery-3.6.4.min.js') }}"></script>

<script src="{{ asset('front/libs/intl-tel-input/js/intlTelInput.min.js') }}"></script>

@yield('additional-js-libs')

<!-- App js -->
<script src="{{ asset('front') }}/js/main.js" type="module"></script>

@yield('custom-js')