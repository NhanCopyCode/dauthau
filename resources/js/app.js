import "./bootstrap";
import Alpine from "alpinejs";
import initScrollToTop from "./scroll-to-top";

// Make Alpine globally available for inline x-data usage
window.Alpine = Alpine;

// Start Alpine
Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    initScrollToTop();
});
