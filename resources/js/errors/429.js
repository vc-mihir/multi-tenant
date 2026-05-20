const el = document.getElementById("countdown-value");
const label = document.getElementById("countdown-label");
const ring = document.getElementById("countdown-display");

if (el) {
    let seconds = parseInt(el.dataset.seconds, 10);

    const tick = setInterval(() => {
        seconds--;
        if (seconds <= 0) {
            clearInterval(tick);
            el.textContent = "0";
            ring.classList.replace("border-teal-100", "border-emerald-300");
            ring.classList.replace("bg-teal-50", "bg-emerald-50");
            el.classList.replace("text-teal-600", "text-emerald-600");
            label.textContent = "You can try again now!";
            label.classList.add("font-bold", "text-emerald-600");
        } else {
            el.textContent = seconds;
        }
    }, 1000);
}
