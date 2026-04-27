/**
 * Common jQuery Validation Rules and Methods
 */

$.validator.addMethod(
    "lowercaseEmail",
    function (value, element) {
        return this.optional(element) || !/[A-Z]/.test(value);
    },
    "Email must not contain any uppercase letters.",
);

window.CommonValidationRules = {
    company_email: {
        required: true,
        email: true,
        maxlength: 100,
        lowercaseEmail: true,
    },
    website: {
        required: true,
        url: true,
        maxlength: 255,
    },
    license_number: {
        required: true,
        maxlength: 50,
    },
    address: {
        required: true,
        maxlength: 500,
    },
    city: {
        required: true,
        maxlength: 100,
    },
    state: {
        required: true,
        maxlength: 100,
    },
    country: {
        required: true,
        maxlength: 100,
    },
};
