function remove(form) {
    var formData = new FormData(form);
    
    var submit_button = $(form).find("button[type='submit']")
    submit_button.prop("disabled", true)
    
    let tenant_id = $(form).attr("data-id")
    
    $.ajax({
        url: "/dashboard/tenants/" + tenant_id,  // Laravel route to handle name change
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            Swal.fire({
                text: "This tenant has been deleted successfully!",
                icon: "success"
            });
            submit_button.prop("disabled", false)
            table.ajax.reload(null, false)
        },
        error: function(xhr) {
            var errors = xhr.responseJSON.errors;
            var firstKey = Object.keys(errors)[0];
            Swal.fire({
                text: errors[firstKey][0],
                icon: "error"
            });
            submit_button.prop("disabled", false)
        }
    });
}

$("#create-tenant-form").submit(function(e){
    e.preventDefault()

    var formData = new FormData(this)
    var submit_button = $(this).find("button[type='submit']")
    submit_button.prop("disabled", true)

    $.ajax({
        url: "/dashboard/tenants",
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            window.location = response.redirectUrl
        },
        error: function(xhr) {
            var errors = xhr.responseJSON.errors;
            var firstKey = Object.keys(errors)[0];
            Swal.fire({
                text: errors[firstKey][0],
                icon: "error"
            });
            submit_button.prop("disabled", false)
        }
    });
})

$("#edit-tenant-form").submit(function(e){
    e.preventDefault()

    var formData = new FormData(this)
    var submit_button = $(this).find("button[type='submit']")
    submit_button.prop("disabled", true)

    const tenant_id = $(this).attr("data-id")

    $.ajax({
        url: "/dashboard/tenants/" + tenant_id,
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            Swal.fire({
                text: "Your changes has been saved successfully",
                icon: "success"
            });
            submit_button.prop("disabled", false)
        },
        error: function(xhr) {
            var errors = xhr.responseJSON.errors;
            var firstKey = Object.keys(errors)[0];
            Swal.fire({
                text: errors[firstKey][0],
                icon: "error"
            });
            submit_button.prop("disabled", false)
        }
    });
})