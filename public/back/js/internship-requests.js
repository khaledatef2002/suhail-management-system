import { request } from './utils.js';

const remove = async function(form) {
    const formData = new FormData(form);
    
    let id = form.getAttribute("data-id")
    
    const response = await request(`/dashboard/internship-requests/${id}`, "POST", formData)

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

const accept = async function(id) {
    const response = await request(`/dashboard/internship-request/${id}/accept`, "POST", {
        '_method': 'PUT'
    })
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

const accept_and_create = async function(id) {
    const response = await request(`/dashboard/internship-request/${id}/accept_and_create`, "POST", {
        '_method': 'PUT'
    })
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

const reject = async function(id) {
    const response = await request(`/dashboard/internship-request/${id}/reject`, "POST", {
        '_method': 'PUT'
    })
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
    if(e.target.closest("a.accept"))
    {
        Swal.fire({
            title: "Do you really want to accept this request?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Accept",
            confirmButtonColor: "green",
        }).then((result) => {
            if (result.isConfirmed) {
                accept(e.target.closest("a.accept").getAttribute("data-id"))
            }
        });
    }
    if(e.target.closest("a.accept_and_create"))
    {
        Swal.fire({
            title: "Do you really want to accept this request creating a user and send the account info in email?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "accept and create",
            confirmButtonColor: "blue",
        }).then((result) => {
            if (result.isConfirmed) {
                accept_and_create(e.target.closest("a.accept_and_create").getAttribute("data-id"))
            }
        });
    }
    if(e.target.closest("a.reject"))
    {
        Swal.fire({
            title: "Do you really want to reject this request?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Reject",
            confirmButtonColor: "red",
        }).then((result) => {
            if (result.isConfirmed) {
                reject(e.target.closest("a.reject").getAttribute("data-id"))
            }
        });
    }
})