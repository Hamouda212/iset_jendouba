// script.js - Interactive Features

document.addEventListener("DOMContentLoaded", () => {
    // Reveal animations
    const reveals = document.querySelectorAll(".reveal");
    
    const revealOnScroll = () => {
        reveals.forEach(el => {
            const windowHeight = window.innerHeight;
            const elementTop = el.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < windowHeight - elementVisible) {
                el.classList.add("active");
            }
        });
    };
    
    window.addEventListener("scroll", revealOnScroll);
    revealOnScroll();
    
    // Mobile menu toggle
    const menuToggle = document.querySelector(".menu-toggle");
    if (menuToggle) {
        menuToggle.addEventListener("click", () => {
            document.querySelector(".nav-links").classList.toggle("show");
        });
    }
});

// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).style.display = "flex";
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
}