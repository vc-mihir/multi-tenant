/**
 * Post-verification provisioning loader.
 *
 * Polls the provisioning status endpoint until the tenant is ready, then
 * forwards to the tenant site. If it is not ready within the timeout, falls back
 * to the registration page (the account is already created and verified, so this
 * is not an error). URLs are read from data-* attributes on #provisioning.
 */
const el = document.getElementById("provisioning");

if (el) {
    const statusUrl = el.dataset.statusUrl;
    const tenantUrl = el.dataset.tenantUrl;
    const fallbackUrl = el.dataset.fallbackUrl;
    const intervalMs = 1500;
    const deadline = Date.now() + 10000; // give up and fall back after ~10s

    // replace() so this interstitial is not left in the back/forward history.
    const go = (url) => window.location.replace(url);

    const poll = () => {
        fetch(statusUrl, { headers: { Accept: "application/json" }, cache: "no-store" })
            .then((res) => (res.ok ? res.json() : { state: "pending" }))
            .then((data) => {
                if (data.state === "ready") return go(tenantUrl);
                if (Date.now() >= deadline) return go(fallbackUrl);
                setTimeout(poll, intervalMs);
            })
            .catch(() => {
                // Network hiccup while polling — keep trying until the deadline.
                if (Date.now() >= deadline) return go(fallbackUrl);
                setTimeout(poll, intervalMs);
            });
    };

    poll();
}
