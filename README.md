# Student_Management_System

### NOTES
    * In getting the data in api use the following js:
       NOTE: change the login to specific function you need
        fetch('./auth/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
                // Add any additional headers if needed
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
                // Handle the successful response, e.g., redirect to dashboard
                console.log('Login successful:', responseData);
                Note: change the direction if it succeed
                window.location.href = 'dashboard.php';
            })
            .catch(error => {
                // Handle errors, e.g., display an error message
                console.error('Login failed:', error.message);
                document.getElementById('errorMessage').innerText = error.message;
            });
