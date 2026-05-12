(function () {
    const originalSubmit = HTMLFormElement.prototype.submit;

    async function refreshAndSubmit(form) {
        if (form.dataset.refreshed) {
            return originalSubmit.call(form);
        }

        try {
            const response = await fetch("/refresh-csrf");
            const data = await response.json();
            if (data.token) {
                const tokenInput = form.querySelector('input[name="_token"]');
                if (tokenInput) tokenInput.value = data.token;
            }
        } catch (e) {
            console.error("CSRF refresh failed", e);
        }

        form.dataset.refreshed = "true";
        originalSubmit.call(form);
    }

    document.addEventListener(
        "submit",
        function (e) {
            if (
                e.target.dataset.refreshed ||
                !e.target.querySelector('input[name="_token"]')
            ) {
                return;
            }
            e.preventDefault();
            refreshAndSubmit(e.target);
        },
        true,
    );

    HTMLFormElement.prototype.submit = function () {
        refreshAndSubmit(this);
    };
})();
