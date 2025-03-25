@extends('dashboard.layouts.guest')

@section('title', __('custom.login'))

@section('content')

<div class="auth-container d-flex align-items-center justify-content-center">
    <div class="auth-one-bg-position auth-one-bg">
        <div class="bg-overlay"></div>

        <div class="shape">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
            </svg>
        </div>
    </div>
    <div class="container form-container mt-5">
        <div class="row mt-5">
            <div class="col-md-8 col-lg-6 col-xl-5 mx-auto mt-5">
                <div class="card w-100 mt-5">
                    <div class="card-body py-4">
                        <div class="text-center mt-2">
                            <h5 class="text-primary">@lang('dashboard.welcome') !</h5>
                            <p class="text-muted">@lang('dashboard.sign-in-to-continue', ['at' => 'test'])</p>
                        </div>
                        <form id="login-form" action="{{ route('login') }}" method="post" style="position: relative;z-index: 1;">
                            @csrf
                            <div class="input-group d-flex flex-column mb-3">
                                <label for="email" class="fw-bold">@lang('dashboard.email')</label>
                                <input id="email" name="email" value="{{ old('email') }}" class="form-control w-100" type="email" placeholder="@lang('dashboard.email')">
                            </div>
                            <div class="input-group d-flex flex-column mb-2">
                                <label for="password" class="fw-bold">@lang('dashboard.password')</label>
                                <div class="input-holder password-toggler position-relative d-flex justify-content-end">
                                    <i class="ri-eye-line fs-4 me-2" role="button"></i>
                                    <input id="password" name="password" class="form-control" type="password" placeholder="@lang('dashboard.password')">
                                </div>
                                <a href="{{ route('password.request') }}" class="d-inline-block mt-2">@lang('dashboard.forget-password')?</a>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" value="1" id="auth-remember-check" role="button">
                                <label class="form-check-label" for="auth-remember-check" role="button">@lang('dashboard.login.keep')</label>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-primary w-100" type="submit">@lang('dashboard.login')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center gap-2 mt-1">
            @if (LaravelLocalization::getCurrentLocale() == 'ar')
                <a href="{{ LaravelLocalization::getLocalizedURL('en') }}"><img src="{{ asset('back') }}/images/flags/en.svg" height="25" class="rounded-2" role="button"></a>
            @else
                <a href="{{ LaravelLocalization::getLocalizedURL('ar') }}"><img src="{{ asset('back') }}/images/flags/ar.svg" height="25" class="rounded-2" role="button"></a>
            @endif
        </div>
    </div>
</div>

@endsection