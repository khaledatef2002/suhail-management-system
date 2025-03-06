function remove(form) {
    var formData = new FormData(form);
    
    var submit_button = $(form).find("button[type='submit']")
    submit_button.prop("disabled", true)
    
    let user_id = $(form).attr("data-id")
    
    $.ajax({
        url: "/dashboard/users/" + user_id,  // Laravel route to handle name change
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            Swal.fire({
                text: "This user has been deleted successfully!",
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

$("#create-user-form").submit(function(e){
    e.preventDefault()

    var formData = new FormData(this)
    var submit_button = $(this).find("button[type='submit']")
    submit_button.prop("disabled", true)

    $.ajax({
        url: "/dashboard/users",
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

$("#edit-user-form").submit(function(e){
    e.preventDefault()

    var formData = new FormData(this)
    var submit_button = $(this).find("button[type='submit']")
    submit_button.prop("disabled", true)

    const user_id = $(this).attr("data-id")

    $.ajax({
        url: "/dashboard/users/" + user_id,
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