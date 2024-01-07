<?php
include '../layout/header_student.php';
print_r($_SESSION);


?>

    <div class="container">
        <h1>Student Dashboard</h1>

        <div id="student-info">
            <!-- Student information will be displayed here dynamically using JavaScript -->
        </div>

        <div class="courses-container">
            <h2>Enrolled Courses</h2>
            <div id="courses-list">
                <!-- Enrolled courses will be displayed here dynamically using JavaScript -->
            </div>
        </div>
    </div>

    <script>
        const userRole = '<?php echo $_SESSION['role']; ?>';
        const auth_token = '<?php echo $_SESSION['auth_token']; ?>';
        const userId = '<?php echo $_SESSION['user_id']; ?>';

        // Append the user_id to the apiUrl
        const apiUrl = `http://localhost/StudentManagementSystem/auth/api.php?dashboard&userRole=${userRole}&user_id=${userId}`;

        // Fetch student information and enrolled courses from the API
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + auth_token
            }
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
            })
            .catch(error => console.error('Error fetching data:', error));


    </script>


<?php include '../layout/footer.php'; ?>