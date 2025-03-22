<head>
    <meta charset="utf-8" />
    <title>{{ $settings->name }} - @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ $settings->description }}" name="description" />
    <meta content="Khaled Atef" name="author" />
    <!-- App favicon -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('storage/'. $settings->logo) }}">
    
    {{-- RTL FILES --}}
    @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <!-- Bootstrap Css -->
        <link href="{{ asset('front') }}/libs/bootstrap/css/bootstrap.rtl.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    @else
        <!-- Bootstrap Css -->
        <link href="{{ asset('front') }}/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    @endif
    
    {{-- Sweet Alert2 --}}
    <link href="{{ asset('front/libs/sweetalert2/sweetalert2.all.min.js') }}" rel="stylesheet" type="text-/css" />

    <link rel="stylesheet" href="{{ asset('front/libs/intl-tel-input/css/intlTelInput.css') }}">
    <!-- custom Css-->
    <link href="{{ asset('front') }}/css/custom.css" rel="stylesheet" type="text/css" />
</head>