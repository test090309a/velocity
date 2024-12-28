// Funktion zum Schließen des Hamburger-Menüs
function closeNavbar() {
    const navbarCollapse = document.getElementById("navbarNav");
    const navbarToggler = document.querySelector(".navbar-toggler");

    // Schließt das Menü, indem die Bootstrap-Klasse entfernt wird
    navbarCollapse.classList.remove("show");

    // Setzt den Zustand des Toggler-Buttons zurück
    navbarToggler.setAttribute("aria-expanded", "false");
}

// Event-Listener für Klicks auf die Navigationslinks
document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
    link.addEventListener('click', () => {
        closeNavbar(); // Schließt das Menü, wenn ein Link geklickt wird
    });
});

// Event-Listener für Klicks außerhalb des Menüs
document.addEventListener('click', (event) => {
    const navbarCollapse = document.getElementById("navbarNav");
    const navbarToggler = document.querySelector(".navbar-toggler");

    // Überprüft, ob der Klick außerhalb des Menüs erfolgt ist
    if (!navbarCollapse.contains(event.target) && !navbarToggler.contains(event.target)) {
        closeNavbar(); // Schließt das Menü
    }
});

// Triangular-Button anzeigen beim Scrollen nach unten
let lastScrollTop = 0;
const triangleButtons = document.querySelectorAll('.triangle-button');

window.addEventListener("scroll", function () {
    console.log("Scrolling");
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > lastScrollTop) {
        triangleButtons.forEach(button => {
            button.style.display = 'block';
        });
    } else {
        triangleButtons.forEach(button => {
            button.style.display = 'none';
        });
    }

    lastScrollTop = scrollTop;
});