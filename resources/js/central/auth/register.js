/**
 * Central Registration Multi-step Form
 */
$(document).ready(function() {
    const form = $('#registration-form');
    const phases = ['phase-1', 'phase-2', 'phase-3'];
    const dots = ['step-1-dot', 'step-2-dot', 'step-3-dot'];
    const labels = ['', 'step-2-label', 'step-3-label'];
    const progressLine = $('#progress-line');
    const nextBtn = $('#next-btn');
    const prevBtn = $('#prev-btn');
    const btnText = $('#btn-text');
    const nextIcon = $('#next-icon');

    let currentPhase = 0;

    $.validator.addMethod("strongPassword", function(value, element) {
        return this.optional(element) ||
            (value.length >= 8 && value.length <= 16 &&
                /[A-Z]/.test(value) &&
                /[a-z]/.test(value) &&
                /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(value));
    }, "Use 8-16 chars with Upper, Lower & Symbol.");

    const validator = form.validate({
        onfocusout: false,
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
            },
            password_confirmation: {
                equalTo: "Passwords do not match."
            }
        }
    });

    function updateUI() {
        phases.forEach((id, index) => {
            $(`#${id}`).toggleClass('hidden', index !== currentPhase);
        });

        dots.forEach((id, index) => {
            const dot = $(`#${id}`);
            if (index <= currentPhase) {
                dot.removeClass('bg-slate-100 text-slate-400').addClass(
                    'bg-[#DD7F61] text-white shadow-lg shadow-[#DD7F61]/20');
            } else {
                dot.removeClass('bg-[#DD7F61] text-white shadow-lg shadow-[#DD7F61]/20').addClass(
                    'bg-slate-100 text-slate-400');
            }
        });

        labels.forEach((id, index) => {
            if (!id) return;
            const label = $(`#${id}`);
            if (index <= currentPhase) {
                label.removeClass('text-slate-400').addClass('text-[#DD7F61]');
            } else {
                label.removeClass('text-[#DD7F61]').addClass('text-slate-400');
            }
        });

        progressLine.css('width', `${(currentPhase / (phases.length - 1)) * 100}%`);

        prevBtn.toggleClass('hidden', currentPhase === 0);
        btnText.text(currentPhase === phases.length - 1 ? 'Register Now' : 'Next Step');
        nextIcon.toggleClass('hidden', currentPhase === phases.length - 1);
    }

    nextBtn.on('click', function() {
        const currentPhaseId = phases[currentPhase];
        let isValid = true;

        $(`#${currentPhaseId} :input`).each(function() {
            if (!$(this).valid()) {
                isValid = false;
            }
        });

        if (isValid) {
            if (currentPhase < phases.length - 1) {
                currentPhase++;
                updateUI();
            } else {
                form.submit();
            }
        }
    });

    prevBtn.on('click', function() {
        if (currentPhase > 0) {
            currentPhase--;
            updateUI();
        }
    });
});
