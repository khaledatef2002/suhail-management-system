@extends('dashboard.layouts.app')

@section('title', __('dashboard.tasks'))

@section('content')

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between g-2">
            <div>
                <input type="checkbox" class="btn-check status_filter" id="new_status" checked="">
                <label class="btn btn-outline-dark" for="new_status">@lang('dashboard.new') <i class="ri-check-line"></i></label>
                
                <input type="checkbox" class="btn-check status_filter" id="review_status" checked="">
                <label class="btn btn-outline-warning" for="review_status">@lang('dashboard.review') <i class="ri-check-line"></i></label>

                <input type="checkbox" class="btn-check status_filter" id="working_status" checked="">
                <label class="btn btn-outline-primary" for="working_status">@lang('dashboard.working') <i class="ri-check-line"></i></label>

                <input type="checkbox" class="btn-check status_filter" id="feedback_status" checked="">
                <label class="btn btn-outline-warning" for="feedback_status">@lang('dashboard.feedback') <i class="ri-check-line"></i></label>

                <input type="checkbox" class="btn-check status_filter" id="done_status" checked="">
                <label class="btn btn-outline-success" for="done_status">@lang('dashboard.done') <i class="ri-check-line"></i></label>
            </div>
            @if (Auth::user()->hasAnyRole(['manager', 'admin']))
                <a href="{{ route('dashboard.tasks.create') }}"><button class="btn btn-success"><i class="ri-add-fill me-1 align-bottom"></i> @lang('dashboard.add')</button></a>
            @endif
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
                    <th>@lang('dashboard.task')</th>
                    <th>@lang('dashboard.assignee')</th>
                    <th>@lang('dashboard.creator')</th>
                    <th>@lang('dashboard.due_date')</th>
                    <th>@lang('dashboard.status')</th>
                    <th>@lang('dashboard.action')</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('custom-js')
    <script src="{{ asset('back/js/tasks.js') }}" type="module"></script>
    <script>
        let table
        $(document).ready( function () {
            table = $('#dataTables').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    ur: "{{ route('dashboard.tasks.index') }}",
                    data: function(d) {
                        d.new_status = document.querySelector("#new_status").checked
                        d.review_status = document.querySelector("#review_status").checked
                        d.working_status = document.querySelector("#working_status").checked
                        d.feedback_status = document.querySelector("#feedback_status").checked
                        d.done_status = document.querySelector("#done_status").checked
                    }
                },
                columns: [
                            { data: 'id', name: 'id' },
                            { data: 'task', name: 'task' },
                            { data: 'assignee', name: 'assignee' },
                            { data: 'creator', name: 'creator' },
                            { data: 'due_date', name: 'due_date' },
                            { data: 'status', name: 'status' },
                            { data: 'action', name: 'action'}
                        ],
                language: __table_lang
            });
        });
    </script>
@endsection