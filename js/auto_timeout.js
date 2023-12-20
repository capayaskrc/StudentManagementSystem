var timeout;

function startTimer() {
    timeout = setTimeout(logout, 600000); // 600000 milliseconds = 10 minutes
}

function resetTimer() {
    clearTimeout(timeout);
    startTimer();
}

function logout() {
    window.location.href = '../logout.php'; // Redirect to the logout script
}

// Start the timer on page load
document.onload = startTimer();

// Reset the timer on user activity (e.g., mouse move or keypress)
document.onmousemove = resetTimer;
document.onkeypress = resetTimer;