import { request } from "./utils.js";

document.querySelector("form#create-task-form")?.addEventListener("submit", async function(e) {
    e.preventDefault()
    
    const form = this
    const formData = new FormData(form)

    dropzone.files.forEach((file, index) => {
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