

$(document).ready(function() {
    const form = $('#create-company-form');

    $.validator.addMethod("strongPassword", function(value, element) {
        return this.optional(element) ||
            (value.length >= 8 && value.length <= 16 &&
                /[A-Z]/.test(value) &&
                /[a-z]/.test(value) &&
                /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(value));
    }, "Use 8-16 chars with Upper, Lower & Symbol.");

    form.validate({
        onfocusout: function(element) {
            $(element).valid();
        },
        errorElement: "span",
        errorPlacement: function(error, element) {
            const wrapper = element.parent('.relative');
            (wrapper.length ? wrapper : element).after(error);
        },
        rules: {
            ...window.CommonValidationRules,
            password: {
                required: true,
                strongPassword: true
            },
            password_confirmation: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            company_email: {
                email: "Please enter a valid business email."
            }
        }
    });
});
