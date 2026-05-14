

$(document).ready(function() {
    const form = $('#edit-company-form');

    form.validate({
        onfocusout: function(element) {
            $(element).valid();
        },
        errorElement: "span",
        rules: {
            ...window.CommonValidationRules,
            status: {
                required: true
            }
        },
        messages: {
            company_email: {
                email: "Please enter a valid business email."
            }
        }
    });
});
