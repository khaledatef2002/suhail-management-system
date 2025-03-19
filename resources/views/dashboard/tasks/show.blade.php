@extends('dashboard.layouts.app')

@section('title', __('dashboard.task.edit'))

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between g-2">
            <div>
                <input type="radio" name="status" value="{{ App\Enum\TaskStatus::NEW->value }}" class="btn-check status_filter" id="new" {{ !auth()->user()->hasRole('manager') && auth()->user()->id != $task->creator->id ? 'disabled' : '' }} {{ $task->status == App\Enum\TaskStatus::NEW->value ? 'checked' : '' }}>
                <label class="btn btn-outline-dark" for="new">@lang('dashboard.new') <i class="ri-check-line"></i></label>
                
                <input type="radio" name="status" value="{{ App\Enum\TaskStatus::WORKING->value }}" class="btn-check status_filter" id="working" {{ $task->status == App\Enum\TaskStatus::WORKING->value ? 'checked' : '' }}>
                <label class="btn btn-outline-primary" for="working">@lang('dashboard.working') <i class="ri-check-line"></i></label>
                
                <input type="radio" name="status" value="{{ App\Enum\TaskStatus::REVIEW->value }}" class="btn-check status_filter" id="review" {{ $task->status == App\Enum\TaskStatus::REVIEW->value ? 'checked' : '' }}>
                <label class="btn btn-outline-warning" for="review">@lang('dashboard.review') <i class="ri-check-line"></i></label>

                <input type="radio" name="status" value="{{ App\Enum\TaskStatus::FEEDBACK->value }}" {{ !auth()->user()->hasRole('manager') && auth()->user()->id != $task->creator->id ? 'disabled' : '' }} class="btn-check status_filter" id="feedback" {{ $task->status == App\Enum\TaskStatus::FEEDBACK->value ? 'checked' : '' }}>
                <label class="btn btn-outline-warning" for="feedback">@lang('dashboard.feedback') <i class="ri-check-line"></i></label>

                <input type="radio" name="status" value="{{ App\Enum\TaskStatus::DONE->value }}" class="btn-check status_filter" id="done" {{ !auth()->user()->hasRole('manager') && auth()->user()->id != $task->creator->id ? 'disabled' : '' }} {{ $task->status == App\Enum\TaskStatus::DONE->value ? 'checked' : '' }}>
                <label class="btn btn-outline-success" for="done">@lang('dashboard.done') <i class="ri-check-line"></i></label>
            </div>
            <div class="d-flex gap-3">
                @if (auth()->user()->id == $task->creator->id)
                    <a href="{{ route('dashboard.tasks.edit', $task) }}" class="btn btn-primary d-flex align-items-center"><i class="ri-pencil-line me-1 align-bottom"></i> @lang('dashboard.edit')</a>
                @endif
                <a href="{{ route('dashboard.tasks.index') }}" class="btn btn-light d-flex align-items-center"><i class="ri-arrow-go-forward-fill me-1 align-bottom"></i> @lang('dashboard.return')</a>
            </div>
        </div>
        <!--end row-->
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label d-block" for="title">@lang('dashboard.title')</label>
                    <span>{{ $task->title }}</span>
                </div>
                <div class="d-flex gap-3 flex-wrap mb-3">
                    <div class="flex-fill">
                        <label class="form-label d-block" for="due_date">@lang('dashboard.due_date')</label>
                        <span>{{ $task->due_date }}</span>
                    </div>
                    <div class="flex-fill">
                        <label class="form-label d-block" for="created_at">@lang('dashboard.created_at')</label>
                        <span>{{ $task->created_at->format('Y-m-d H:i:s a') }}</span>
                    </div>
                </div>
                <div class="d-flex gap-3 flex-wrap mb-3">
                    <div class="flex-fill">
                        <label class="form-label d-block" for="assignee_id">@lang('dashboard.creator')</label>
                        <div class="d-flex align-items-center">
                            <img src="{{ $task->creator->display_image }}" class="rounded-circle" style="width: 30px; height: 30px; margin-right: 10px;">
                            <span>{{ $task->creator->full_name }}</span>
                        </div>
                    </div>
                    <div class="flex-fill">
                        <label class="form-label d-block" for="assignee_id">@lang('dashboard.assignee')</label>
                        <div class="d-flex align-items-center">
                            <img src="{{ $task->assignee->display_image }}" class="rounded-circle" style="width: 30px; height: 30px; margin-right: 10px;">
                            <span>{{ $task->assignee->full_name }}</span>
                        </div>
                    </div>
                </div>
                @if ($task->description)
                    <div class="mb-3">
                        <label class="form-label" for="description">@lang('dashboard.description')</label>
                        <span>{{ $task->description }}</span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- end card -->
        <div class="card">
            <div class="card-body">
                <form action="#" class="form-steps" autocomplete="off">
                    <div class="step-arrow-nav mb-4">

                        <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="messages_tab_nav" data-bs-toggle="pill" data-bs-target="#messages_tab" type="button" role="tab" aria-controls="steparrow-gen-info" aria-selected="true" data-position="0">@lang('dashboard.messages')</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="attachment_tab_nav" data-bs-toggle="pill" data-bs-target="#attachments_tab" type="button" role="tab" aria-controls="steparrow-description-info" aria-selected="false" data-position="1" tabindex="-1">@lang('dashboard.attachments')</button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="messages_tab" role="tabpanel" aria-labelledby="steparrow-gen-info-tab">
                            <div class="d-flex justify-content-end mb-2">
                                <button id="add_message_button" type="button" class="btn btn-success ms-auto btn-sm"><i class="ri-add-line"></i> @lang('dashboard.add-message')</button>
                            </div>
                            <div id="add_message_container">
                                
                            </div>
                            <div id="messages" class="d-flex flex-column">
                                @php($index = 0)
                                @foreach ($task->messages->sortByDesc('created_at') as $message)
                                    @if ($index++ > 0)
                                        <hr>
                                    @endif
                                    <div class="d-flex gap-2 align-items-center">
                                        <img src="{{ $message->user->display_image }}" class="rounded-circle" style="width: 65px; height: 65px;">
                                        <div class="d-flex justify-content-between w-100">
                                            <div class="d-flex flex-column">
                                                <div class="d-flex gap-2">
                                                    <span class="fw-bold">{{ $message->user->full_name }}</span> -
                                                    <span>{{ $message->user->created_at->format('Y-m-d H:i:s a') }}</span>
                                                </div>
                                                <span>{{ $message->message }}</span>
                                                @if ($message->attachments()->count() > 0)
                                                    <div class="message_attachments mt-2 d-flex gap-5 flex-wrap">
                                                        @foreach ($message->attachments as $attachment)
                                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank">
                                                                <div class="d-flex align-items-center">
                                                                    <img src="{{ $attachment->display_image }}" class="rounded-circle" style="width: 30px; height: 30px;">
                                                                    <div class="d-flex flex-column">
                                                                        <span>{{ $attachment->file_name }}</span>
                                                                        <span>{{ $attachment->formatted_size }}</span>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            @if (auth()->user()->hasRole('manager') || auth()->user()->id == $task->creator_id)
                                                <div>
                                                    <i class="ri-delete-bin-line text-danger fs-3 delete_message" data-id="{{ $message->id }}" role="button"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- end tab pane -->

                        <div class="tab-pane fade" id="attachments_tab" role="tabpanel" aria-labelledby="steparrow-description-info-tab">
                            <div class="d-flex gap-5 flex-wrap">
                                @foreach ($task->attachments as $attachment)
                                <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $attachment->display_image }}" class="rounded-circle" style="width: 30px; height: 30px;">
                                        <div class="d-flex flex-column">
                                            <span>{{ $attachment->file_name }}</span>
                                            <span>{{ $attachment->formatted_size }}</span>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        <!-- end tab pane -->
                    </div>
                    <!-- end tab content -->
                </form>
            </div>
        </div>
    </div>
    <!-- end col -->
</div>
@endsection

@section('custom-js')
    <script src="{{ asset('back/js/tasks.js') }}" type="module"></script>
    <script>
        const task_id = {{ $task->id }}
        let existingFiles = @json($task->attachments);

        let dropzonePreviewNode = document.querySelector("#dropzone-preview-list");
        dropzonePreviewNode.id = "";
        let previewTemplate = dropzonePreviewNode.parentNode.innerHTML;
        dropzonePreviewNode.parentNode.removeChild(dropzonePreviewNode);
        let dropzone = new Dropzone(".dropzone", {
            url: "#",
            // autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 5,
            maxFilesize: 1024 * 2,
            acceptedFiles: ".jpg,.jpeg,.png,.gif,.zip,.rar,.pdf,.doc,.docx,.xls,.xlsx",
            previewTemplate: previewTemplate,
            previewsContainer: "#dropzone-preview",
            init: function() {
                let dropzoneInstance = this;
                let noAttachmentsMessage = document.getElementById("noAttachments");

                // Show "No Attachments" when Dropzone is empty
                function updateAttachmentMessage() {
                    noAttachmentsMessage.style.display = dropzoneInstance.files.length + existingFiles.length === 0 ? "block" : "none";
                }

                // Update message when a file is added
                this.on("addedfile", function() {
                    updateAttachmentMessage();
                });

                // Update message when a file is removed
                this.on("removedfile", function(file) {
                    if(file.id)
                    {
                        existingFiles = existingFiles.filter(existingFile => existingFile.id != file.id)
                    }
                    updateAttachmentMessage();
                });


                existingFiles.forEach(image => {
                    let mockFile = { name: image.file_name, size: image.file_size, dataURL: image.file_path, id: image.id };

                    this.emit("addedfile", mockFile);     // Add file to Dropzone
                    this.emit("thumbnail", mockFile, "{{ asset('storage') }}/" + image.file_path); // Show thumbnail
                    this.emit("complete", mockFile);      // Mark as complete
                    mockFile.previewElement.classList.add('dz-complete');

                    mockFile.existing = true;
                });

                // Initial state
                updateAttachmentMessage();
            }
        });
    </script>
@endsection