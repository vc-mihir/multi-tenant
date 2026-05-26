

$(document).ready(function() {
    const form = $('#edit-company-form');

    form.validate({
        onfocusout: false,
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
