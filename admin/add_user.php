<?php
include '../layout/header_user.php';
?>

<body>

    <header class="header">

        <a class="title" href="admin_dashboard.php">Dashboard</a>

        <div class="logout">

            <a href="../logout.php" class="btn btn-primary">Logout</a>

        </div>

    </header>


    <aside>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="add_user.php">Add User</a></li>
            <li><a href="view_student.php">View Student</a></li>
            <li><a href="add_course.php">Add Courses</a></li>
            <li><a href="">View Courses</a></li>
        </ul>
    </aside>

    <div class="addstud">
        <h1>Add Student</h1>
        <form id="userForm">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required>

            <label for="birthdate">Birthdate:</label>
            <input type="date" id="birthdate" name="birthdate" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <label for="sex">Sex:</label>
            <select id="sex" name="sex" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="instructor">Instructor</option>
                <option value="student">Student</option>
            </select>

            <!-- Year Level Dropdown -->
            <div id="yearLevelContainer" style="display: none;">
                <label for="yearLevel">Year Level:</label>
                <select id="yearLevel" name="yearLevel">
                    <option value="1">Freshman</option>
                    <option value="2">Sophomore</option>
                    <option value="3">Junior</option>
                    <option value="4">Senior</option>
                </select>
            </div>

            <button type="submit">Add User</button>
        </form>
    </div>

    <script>
        const userForm = document.getElementById('userForm');
        const roleDropdown = document.getElementById('role');
        const yearLevelContainer = document.getElementById('yearLevelContainer');

        // Show/hide Year Level dropdown based on selected role
        roleDropdown.addEventListener('change', function () {
            if (roleDropdown.value === 'student') {
                yearLevelContainer.style.display = 'block';
            } else {
                yearLevelContainer.style.display = 'none';
            }
        });

        userForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Get form data
            const formData = new FormData(userForm);
            const jsonData = {};

            // Convert FormData to JSON
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            // Send JSON data to the server
            fetch('../auth/api.php?user', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(jsonData),
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data); // Handle the response from the server
                    // Display a success message
                    alert(data.message);

                    // Reset the form to clear input fields
                    userForm.reset();
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        });
    </script>


    <?php include '../layout/footer.php'; ?>