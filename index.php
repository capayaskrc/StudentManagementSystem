<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <form id="loginForm">
        <h2>Login</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="button" onclick="login()">Login</button>
    </form>
    <p id="errorMessage"></p>
</div>

<script src="scripts.js"></script>
</body>
</html>

<script>
    function login() {
        var username = document.getElementById('username').value;
        var password = document.getElementById('password').value;

        // Prepare the data to be sent in the request body
        var data = {
            username: username,
            password: password
        };

        // Make a POST request to the login endpoint
        fetch('./auth/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Invalid username or password');
                }
                return response.json();
            })
            .then(responseData => {
                // Check if responseData has the expected structure
                if (responseData && responseData.token) {
                    // Handle the successful response, e.g., redirect to dashboard
                    console.log('Login successful:', responseData);
                    var role = responseData.role;
                    switch (role) {
                        case 'student':
                            window.location.href = 'student_dashboard';
                            break;
                        case 'teacher':
                            window.location.href = 'teacher_dashboard';
                            break;
                        case 'admin':
                            window.location.href = 'admin_dashboard';
                            break;
                        default:
                            console.error('Invalid user role');
                    }
                } else {
                    // Unexpected response format
                    throw new Error('Unexpected response format');
                }
            })
            .catch(error => {
                // Handle errors, e.g., display an error message
                console.error('Login failed:', error.message);
                document.getElementById('errorMessage').innerText = error.message;
            });
    }

</script>
