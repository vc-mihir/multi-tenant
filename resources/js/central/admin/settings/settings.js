/**
 * Central Admin Settings
 */
$(document).ready(function() {
    const form = $('#settings-form');

    form.validate({
        onfocusout: function(element) {
            $(element).valid();
        },
        errorElement: "span",
        rules: {
            name: {
                required: true,
                minlength: 3
            },
            email: {
                required: true,
                email: true
            },
            password: {
                minlength: 8
            },
            password_confirmation: {
                equalTo: "#password"
            }
        }
    });
});
