@extends('dashboard.layouts.app')

@section('title', __('dashboard.users'))

@section('content')

<div class="card">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-sm-auto ms-auto">
                <a href="{{ route('front.internship-apply') }}" target="_blank"><button class="btn btn-primary"><i class="ri-eye-fill me-1 align-bottom"></i> @lang('dashboard.view-apply-form')</button></a>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
</div>
<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped" id="dataTables">
            <thead>
                <tr class="table-dark">
                    <th>@lang('dashboard.id')</th>
                    <th>@lang('dashboard.full_name')</th>
                    <th>@lang('dashboard.email')</th>
                    <th>@lang('dashboard.date-of-birth')</th>
                    <th>@lang('dashboard.phone-number')</th>
                    <th>@lang('dashboard.cv')</th>
                    <th>@lang('dashboard.status')</th>
                    <th>@lang('dashboard.action')</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('custom-js')
    <script src="{{ asset('back/js/internship-requests.js') }}" type="module"></script>
    <script>
        var table
        $(document).ready( function () {
            table = $('#dataTables').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.internship-requests.index') }}",
                columns: [
                            { data: 'id', name: 'id' },
                            { data: 'full_name', name: 'full_name' },
                            { data: 'email', name: 'email' },
                            { data: 'date_of_birth', name: 'date_of_birth' },
                            { data: 'phone_number', name: 'phone_number' },
                            { data: 'cv', name: 'cv' },
                            { data: 'status', name: 'status' },
                            { data: 'action', name: 'action'}
                        ],
                language: __table_lang
            });
        });
    </script>
@endsection