import { request } from "./utils.js";

document.querySelector("form#create-task-form")?.addEventListener("submit", async function(e) {
    e.preventDefault()
    
    const form = this
    const formData = new FormData(form)

    dropzone.files?.forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });

    const submit_button = this.querySelector("button[type='submit']")
    submit_button.disabled = true

    const response = await request("/dashboard/tasks", "POST", formData)

    if(response.success) {
        location.href = response.data.redirectUrl
    }
    else
    {
        Swal.fire({
            text: response.message,
            icon: "error"
        });
    }

    submit_button.disabled = false
})

document.querySelectorAll("input.status_filter").forEach(input => {
    input.addEventListener('change', function(){
        table.ajax.reload();
    })
})

document.querySelector("body").addEventListener("click", function(e) {
    if(e.target.closest("button.remove_button"))
    {
        Swal.fire({
            title: "Do you really want to delete this request?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Delete",
            confirmButtonColor: "red",
        }).then((result) => {
            if (result.isConfirmed) {
                remove(e.target.closest("form"))
            }
        })
    }
})

const remove = async function(form) {
    const formData = new FormData(form);
    
    let id = form.getAttribute("data-id")
    
    const response = await request(`/dashboard/tasks/${id}`, "POST", formData)

    if(response.success) {
        Swal.fire({
            text: response.data.message,
            icon: "success"
        })
        table.ajax.reload(null, false)
    }
    else
    {
        Swal.fire({
            text: response.message,
            icon: "error"
        })
    }
}


document.querySelector("form#edit-task-form")?.addEventListener("submit", async function(e) {
    e.preventDefault()
    
    const form = this
    const formData = new FormData(form)

    dropzone.files?.forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });

    formData.append('_method', 'PUT')

    existingFiles.forEach((file, index) => {
        formData.append(`existingFiles[${index}]`, file.id)
    })

    const submit_button = this.querySelector("button[type='submit']")
    submit_button.disabled = true

    const task_id = this.getAttribute("data-id")

    const response = await request(`/dashboard/tasks/${task_id}`, "POST", formData)

    if(response.success) {

        document.querySelectorAll("#dropzone-preview li").forEach(element => {
            element.querySelector("button.btn-danger").click()
        })

        response.data.attachments_data.forEach(file => {
            let mockFile = { 
                name: file.file_name, 
                size: file.file_size, 
                dataURL: file.file_path, 
                id: file.id 
            };

            dropzone.emit("addedfile", mockFile);
            dropzone.emit("thumbnail", mockFile, file.display_image);
            dropzone.emit("complete", mockFile);
            mockFile.previewElement.classList.add('dz-complete');
        })

        existingFiles = response.data.attachments_data

        document.getElementById("noAttachments").style.display = dropzone.files.length + existingFiles.length === 0 ? "block" : "none"

        Swal.fire({
            text: response.data.message,
            icon: "success"
        });
    }
    else
    {
        Swal.fire({
            text: response.message,
            icon: "error"
        });
    }

    submit_button.disabled = false
})

document.querySelectorAll("input[name='status']").forEach(input => {
    input.addEventListener('change', async function(e) {
        const response = await request(`/dashboard/tasks/${task_id}/update-status`, "POST", {
            'status': this.value,
            '_method': 'PUT'
        })
    
        if(!response.success) {
            Swal.fire({
                text: response.message,
                icon: "error"
            });
    
            e.preventDefault()
        }
    })
})

let dropzone = null

document.querySelector("button#add_message_button")?.addEventListener("click", function(){
    const messageContainer = this.parentElement.parentElement.querySelector("#add_message_container");
    messageContainer.innerHTML = ``
    const messageElement = document.createElement("div")
    messageElement.innerHTML = `
        <div class="mb-3">
            <textarea class="form-control" id="message" name="message" placeholder="message"></textarea>
        </div>
        <div class="d-flex justify-content-end align-items-center gap-3">
            <div class="dropzone p-0 border-0" style="min-height:unset;">
                <div class="dz-message dz-clickable m-0" style="width:fit-content;height:fit-content;">
                    <i class="ri-attachment-line"></i>
                </div>
            </div>
            <button class="btn btn-success send_message_button">Send Message</button>
        </div>
        <ul class="list-unstyled mb-0" id="dropzone-preview">
            <li class="mt-2" id="dropzone-preview-list">
                <!-- This is used as the file preview template -->
                <div class="border rounded">
                    <div class="d-flex p-2">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm bg-light rounded">
                                <img data-dz-thumbnail class="img-fluid rounded d-block" src="/back/images/document.png" alt="Dropzone-Image" />
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
    `
    messageContainer.appendChild(messageElement);

    let existingFiles = []

    let dropzonePreviewNode = document.querySelector("#dropzone-preview-list");
    dropzonePreviewNode.id = "";
    let previewTemplate = dropzonePreviewNode.parentNode.innerHTML;
    dropzonePreviewNode.parentNode.removeChild(dropzonePreviewNode);
    dropzone = new Dropzone(".dropzone", {
        url: "#",
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 5,
        maxFilesize: 1024 * 2,
        acceptedFiles: ".jpg,.jpeg,.png,.gif,.zip,.rar,.pdf,.doc,.docx,.xls,.xlsx",
        previewTemplate: previewTemplate,
        previewsContainer: "#dropzone-preview",
    });
})

document.querySelector("body").addEventListener('click', async function(e){
    if(e.target.closest('.send_message_button'))
    {
        const submit_button = e.target.closest('.send_message_button')
        submit_button.disabled = true

        const formData = new FormData()

        const msg = document.querySelector("#add_message_container textarea#message").value
        formData.append("message", msg)

        formData.append("task_id", task_id)

        const files = {}
        dropzone.files?.forEach((file, index) => {
            formData.append(`files[${index}]`, file);
        });

        const response = await request("/dashboard/task-messages", "POST", formData)

        if(response.success) {
            dropzone = null
            document.querySelector("#add_message_container").innerHTML = ''
            Swal.fire({
                text: response.data.message,
                icon: "success"
            });
        }
        else
        {
            Swal.fire({
                text: response.message,
                icon: "error"
            });
        }

        submit_button.disabled = false
    }

    if(e.target.closest('.delete_message'))
    {
        const submit_button = e.target.closest('.delete_message')
        const id = submit_button.getAttribute("data-id")

        Swal.fire({
            title: "Do you really want to delete this request?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Delete",
            confirmButtonColor: "red",
        }).then(async (result) => {
            const response = await request(`/dashboard/task-messages/${id}`, "POST", {
                '_method': 'DELETE'
            })
    
            if(response.success) {
                const prev_hr = submit_button.parentElement.parentElement.parentElement.previousElementSibling
                if (prev_hr && prev_hr.tagName === "HR") {
                    prev_hr.remove()
                }
                else
                {
                    const next_hr = submit_button.parentElement.parentElement.parentElement.nextElementSibling
                    next_hr.remove()
                }
                submit_button.parentElement.parentElement.parentElement.remove();
                Swal.fire({
                    text: response.data.message,
                    icon: "success"
                });
            }
            else
            {
                Swal.fire({
                    text: response.message,
                    icon: "error"
                });
            }
        })
    }
})
