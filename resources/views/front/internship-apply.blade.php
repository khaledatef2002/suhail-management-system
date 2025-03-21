@extends('front.layouts.main')

@section('title', __('front.apply-internship-form'))

@section('content')
   
    <div class="content card border-0 rounded-3 col-lg-6 m-auto">
        <div class="card-body shadow-sm">
            <div class="image-container mx-auto d-flex justify-content-center align-items-center overflow-hidden" style="width:100px;height:100px;border-radius:50%;">
                <img src="{{ asset('storage/'. $settings->logo) }}">
            </div>
            <h1 class="text-center mb-5">@lang('front.internship-apply')</h1>

            @if ($settings->is_internship_form_avilable)
                <form id="apply-internship">
                    @csrf
                    <div class="d-flex gap-2 mb-3">
                        <div class="flex-fill">
                            <label for="first_name" class="form-label mb-0">@lang('front.first_name')<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="@lang('front.first_name')">
                        </div>
                        <div class="flex-fill">
                            <label for="last_name" class="form-label mb-0">@lang('front.last_name')<strong class="text-danger">*</strong></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="@lang('front.last_name')">
                        </div>
                    </div>
                    <div class="flex-fill mb-3">
                        <label for="email" class="form-label mb-0">@lang('front.email')<strong class="text-danger">*</strong></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="@lang('front.email')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="date_of_birth">@lang('dashboard.date_of_birth')<strong class="text-danger">*</strong></label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" placeholder="@lang('dashboard.date_of_birth')">
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="mb-0">@lang('front.phone_number')<strong class="text-danger">*</strong></label>
                        <input class="form-control country-selector" type="tel" name="phone_number" placeholder="@lang('dashboard.phone_number')">
                    </div>
                    <div class="mb-3">
                        <label for="cv" class="form-label mb-0">@lang('front.cv')<strong class="text-danger">*</strong> <span class="text-muted">(@lang('front.only') .pdf, .doc, .docx)</span></span></label>
                        <input class="form-control" type="file" id="cv" name="cv" accept=".pdf,.doc,.docx">
                    </div>

                    <button type="submit" class="btn btn-success w-100">@lang('front.apply')</button>

                    <div class="dropdown topbar-head-dropdown header-item text-center">
                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle border-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img id="header-lang-img" src="{{ asset('back') }}/images/flags/{{ LaravelLocalization::getCurrentLocale() }}.svg" alt="Header Language" height="20" class="rounded">
                        </button>
                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                            @foreach (LaravelLocalization::getSupportedLocales() as $key => $lang)
                                @if ($key != LaravelLocalization::getCurrentLocale())
                                    <a href="{{ LaravelLocalization::getLocalizedURL($key) }}" class="dropdown-item notify-item language py-2" data-lang="{{ $key }}" title="{{ $lang['name'] }}">
                                        <img src="{{ asset('back') }}/images/flags/{{ $key }}.svg" alt="user-image" class="me-2 rounded" height="18">
                                        <span class="align-middle">{{ $lang['name'] }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </form>
            @else
                <p class="text-danger fw-bold text-center">@lang('front.internship-applying-is-unavilable')</p>
            @endif
        </div>
    </div>

@endsection
@section('additional-js-libs')

<script>
    const __ = {
        ok: '@lang("front.ok")'
    }
</script>

@endsection