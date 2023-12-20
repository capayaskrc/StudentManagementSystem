<?php
include '../layout/header_student.php';
// Include the database connection file
include '../auth/db_connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $fullname = $_POST['fullname'];
    $birthdate = $_POST['birthdate'];
    $address = $_POST['address'];
    $sex = $_POST['sex'];
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Using md5 for simplicity, consider using more secure methods

    // Insert data into the user table
    $insertUserQuery = "INSERT INTO `user` (`fullname`, `birthdate`, `address`, `sex`, `username`, `password`, `role_id`) 
                        VALUES ('$fullname', '$birthdate', '$address', '$sex', '$username', '$password', 3)"; // Assuming role_id 3 is for students

    if ($conn->query($insertUserQuery)) {
        // Get the user_id of the newly inserted user
        $newUserId = $conn->insert_id;

        // Insert data into the student table
        $insertStudentQuery = "INSERT INTO `student` (`user_id`) VALUES ($newUserId)";
        if ($conn->query($insertStudentQuery)) {
            echo "Student added successfully!";
        } else {
            echo "Error adding student: " . $conn->error;
        }
    } else {
        echo "Error adding user: " . $conn->error;
    }
}
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
            <li><a href="add_student.php">Add Student</a></li>
            <li><a href="">View Student</a></li>
            <li><a href="">Add Teacher</a> </li>
            <li><a href="">View Teacher</a></li>
            <li><a href="">Add Courses</a></li>
            <li><a href="">View Courses</a></li>
        </ul>
    </aside>

    <div class="addstud">
        <h1>Add Student</h1>
        <form action="add_student.php" method="post">
            <!-- Add your form fields here -->
            <label for="fullname">Full Name:</label>
            <input type="text" name="fullname" required>

            <label for="birthdate">Birthdate:</label>
            <input type="date" name="birthdate" required>

            <label for="address">Address:</label>
            <input type="text" name="address" required>

            <label for="sex">Sex:</label>
            <select name="sex" required>
                <option value="M">Male</option>
                <option value="F">Female</option>
            </select>

            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>
         
            <button type="submit">Add Student</button>
        </form>
    </div>

<script>
    const userForm = document.getElementById('userForm');

    userForm.addEventListener('submit', function (event) {
        event.preventDefault();

        // Get form data
        const formData = new FormData(userForm);
        const jsonData = {

        };

        // Convert FormData to JSON
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        // Send JSON data to the server
        fetch('./auth/api.php?addUser', {
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