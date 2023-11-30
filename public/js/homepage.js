// Wait for the DOM to be ready
document.addEventListener('DOMContentLoaded', function () {
    
    // Get the alert element
    var alertElement = document.querySelector('.alert');

    // Permet de cacher l'alerte 5 secondes après qu'elle soit apparue sur la page
    if (alertElement) {
        // Set a timeout to fade out after 5 seconds
        setTimeout(function () {
            alertElement.style.transition = '.5s';
            alertElement.style.opacity = '0';
            alertElement.style.visibility = 'hidden';
        }, 5000);
        // Set a timeout to hide the alert after 5.5 seconds
        setTimeout(function () {
            alertElement.style.display = 'none';
        }, 5500);
    }

    var cooldown = document.getElementById('cooldown');
    if(cooldown)
    {
        // Update the countdown every second (1000 milliseconds)
        var countdownInterval = setInterval(function () {
            // Get the current value
            var currentValue = parseInt(cooldown.innerText);

            // Update the value if it's greater than 0
            if (currentValue > 0) {
                cooldown.innerText = currentValue - 1;
            } else {
                // If the countdown reaches 0, clear the interval
                clearInterval(countdownInterval);
                // Refresh text
                document.getElementById('refresh-inventory').innerText='Refresh inventory';
            }
        }, 1000);
    }
});