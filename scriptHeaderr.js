document.getElementById('hamburgerMenu').addEventListener('click', function() {
    const dropdownMenu = document.getElementById('dropdownMenu');

    // Toggle nav-links and dropdown menu
    dropdownMenu.classList.toggle('active');
});