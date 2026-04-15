export default function initScrollToTop() {
    const button = document.getElementById("scrollToTopBtn");

    if (!button) return;

    const SCROLL_THRESHOLD = 300;
    let isVisible = false;

    const toggleVisibility = () => {
        const shouldShow = window.scrollY > SCROLL_THRESHOLD;

        if (shouldShow === isVisible) return;

        isVisible = shouldShow;

        if (shouldShow) {
            button.classList.remove("hidden");
            button.classList.add("flex");
        } else {
            button.classList.remove("flex");
            button.classList.add("hidden");
        }
    };

    const scrollToTop = () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    };

    let ticking = false;

    window.addEventListener("scroll", () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                toggleVisibility();
                ticking = false;
            });
            ticking = true;
        }
    });

    button.addEventListener("click", scrollToTop);
}
