<?php
include '../layout/header_student.php';
?>

<body>

    <header class="header">

        <a class="title" href="admin_dashboard.php">Dashboard</a>

        <div class="logout">

            <a href="logout.php" class="btn btn-primary">Logout</a>

        </div>

    </header>


    <aside>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="add_student.php">Add User</a></li>
            <li><a href="">View Student</a></li>
            <li><a href="">Add Courses</a></li>
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
            <option value="teacher">Teacher</option>
            <option value="student">Student</option>
        </select>

        <button type="submit">Add User</button>
    </form>
    </div>

    <script>
        const userForm = document.getElementById('userForm');

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
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        });

    </script>


    <?php include '../layout/footer.php'; ?>