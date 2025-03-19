@extends('dashboard.layouts.app')

@section('title', __('dashboard.task.edit'))

@section('content')

<form id="edit-task-form" data-id="{{ $task->id }}">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between g-2">
                <div>
                    <input type="radio" name="status" value="{{ App\Enum\TaskStatus::NEW->value }}" class="btn-check status_filter" id="new" {{ $task->status == App\Enum\TaskStatus::NEW->value ? 'checked' : '' }}>
                    <label class="btn btn-outline-dark" for="new">@lang('dashboard.new') <i class="ri-check-line"></i></label>
                    
                    <input type="radio" name="status" value="{{ App\Enum\TaskStatus::REVIEW->value }}" class="btn-check status_filter" id="review" {{ $task->status == App\Enum\TaskStatus::REVIEW->value ? 'checked' : '' }}>
                    <label class="btn btn-outline-warning" for="review">@lang('dashboard.review') <i class="ri-check-line"></i></label>
    
                    <input type="radio" name="status" value="{{ App\Enum\TaskStatus::WORKING->value }}" class="btn-check status_filter" id="working" {{ $task->status == App\Enum\TaskStatus::WORKING->value ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="working">@lang('dashboard.working') <i class="ri-check-line"></i></label>
    
                    <input type="radio" name="status" value="{{ App\Enum\TaskStatus::FEEDBACK->value }}" class="btn-check status_filter" id="feedback" {{ $task->status == App\Enum\TaskStatus::FEEDBACK->value ? 'checked' : '' }}>
                    <label class="btn btn-outline-warning" for="feedback">@lang('dashboard.feedback') <i class="ri-check-line"></i></label>
    
                    <input type="radio" name="status" value="{{ App\Enum\TaskStatus::DONE->value }}" class="btn-check status_filter" id="done" {{ $task->status == App\Enum\TaskStatus::DONE->value ? 'checked' : '' }}>
                    <label class="btn btn-outline-success" for="done">@lang('dashboard.done') <i class="ri-check-line"></i></label>
                </div>
                <a href="{{ route('dashboard.tasks.index') }}" class="btn btn-light d-flex align-items-center"><i class="ri-arrow-go-forward-fill me-1 align-bottom"></i> @lang('dashboard.return')</a>
            </div>
            <!--end row-->
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="title">@lang('dashboard.title')</label>
                        <input value="{{ $task->title }}" type="text" class="form-control" id="title" name="title" placeholder="@lang('dashboard.title')">
                    </div>
                    <div class="d-flex gap-3 flex-wrap mb-3">
                        <div class="flex-fill">
                            <label class="form-label" for="due_date">@lang('dashboard.due_date')</label>
                            <input value="{{ $task->due_date }}" type="date" data-provider="flatpickr" data-date-format="Y-m-d" data-deafult-date="{{ $task->due_date }}" class="form-control" id="due_date" name="due_date" placeholder="@lang('dashboard.date_of_birth')">
                        </div>
                        <div class="flex-fill">
                            <label class="form-label" for="assignee_id">@lang('dashboard.assignee')</label>
                            <select class="form-control" id="assignee_id" name="assignee_id">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">@lang('dashboard.description')</label>
                        <textarea class="form-control" id="description" name="description" placeholder="@lang('dashboard.description')">{{ $task->description }}</textarea>
                    </div>
                </div>
            </div>
            <!-- end card -->
            <div class="card">
                <div class="dropzone border-0">
                    <div class="card-header d-flex justify-content-between align-items-center p-0">
                        <h3 class="mb-0">@lang('dashboard.attachments')</h3>
                        <div class="dz-message dz-clickable m-0" style="width:fit-content;height:fit-content;">
                            <p class="btn btn-success mb-0 py-1 px-3 mb-1">Upload <i class="fs-4 ri-upload-cloud-2-fill"></i></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="fallback">
                            <input name="file" type="file" multiple="multiple">
                        </div>
                        <p id="noAttachments" class="fs-4 mb-0 text-center" style="color: gray;">No Attachments</p>
                    </div>

                    <ul class="list-unstyled mb-0" id="dropzone-preview">
                        <li class="mt-2" id="dropzone-preview-list">
                            <!-- This is used as the file preview template -->
                            <div class="border rounded">
                                <div class="d-flex p-2">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm bg-light rounded">
                                            <img data-dz-thumbnail class="img-fluid rounded d-block" src="{{ asset('back/images/document.png') }}" alt="Dropzone-Image" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="pt-1">
                                            <h5 class="fs-14 mb-1" data-dz-name>&nbsp;</h5>
                                            <p class="fs-13 text-muted mb-0" data-dz-size></p>
                                            <strong class="error text-danger" data-dz-errormessage></strong>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ms-3">
                                        <button data-dz-remove class="btn btn-sm btn-danger">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>                                      
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <div class="row">
        <div class="text-end mb-3">
            <button type="submit" class="btn btn-success w-sm">@lang('dashboard.save')</button>
        </div>
    </div>
</form>

@endsection

@section('custom-js')
    <script src="{{ asset('back/js/tasks.js') }}" type="module"></script>
    <script>
        $('select[name="assignee_id"]').select2({
            placeholder: "@lang('dashboard.select.choose-option')",
            ajax: {
                url: '{{ route("dashboard.select2.users") }}', // Route to fetch users
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // Search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(user) {
                            console.log(user)
                            return {
                                id: user.id,
                                text: user.full_name,
                                image: user.display_image
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateResult: function(user) {
                if (!user.id) {
                    return user.text; // Return default text if no result found
                }
                var $user = $(
                    `<div class="d-flex align-items-center">
                        <img src="${user.image}" class="rounded-circle" style="width: 30px; height: 30px; margin-right: 10px;">
                        <span>${user.text}</span>
                    </div>`
                );
                return $user;
            },
            templateSelection: function(user) {
                if (!user.image) {
                    return user.text;
                }
                return $(
                    `<div class="d-flex align-items-center">
                        <img src="${user.image}" class="rounded-circle" style="width: 20px; height: 20px; margin-right: 5px;">
                        <span>${user.text}</span>
                    </div>`
                );
            },
        });
        
        let option = new Option("{{ $task->assignee->full_name }}", {{ $task->assignee->id }}, true, true);
        $('select[name="assignee_id"]').append(option).trigger('change');
        

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