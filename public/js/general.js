document.addEventListener("DOMContentLoaded", (event) => {
    const avatarImage = document.getElementById('avatar-image');
    const userDropdown = document.getElementById('user-dropdown-wrapper'); // Move it here

    // Dropdown when clicking on the photo of the user
    avatarImage.addEventListener("click", (event) => {
        userDropdown.classList.toggle('active');
        event.stopPropagation(); // Prevent the click event from reaching the document
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (event) => {
        const isClickInsideDropdown = userDropdown.contains(event.target);

        if (!isClickInsideDropdown) {
            userDropdown.classList.remove('active');
        }
    });

    const crossIcon = document.getElementById('search-cross-icon');
    crossIcon.addEventListener("click", (event) => {
        document.getElementById('search-input').value = '';
    });
});