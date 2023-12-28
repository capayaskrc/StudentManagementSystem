<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <style>
        /* Add your custom styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        nav {
            background-color: #eee;
            padding: 10px;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<header>
    <h1>Student Management System</h1>
</header>

<nav>
    <a href="#users">USERS</a> |
    <a href="#courses">COURSES</a>
</nav>

<section id="users">
    <h2>Users</h2>
    <button onclick="addUser()">Add User</button>
    <table id="userTable">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Birthdate</th>
                <th>Address</th>
                <th>Sex</th>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- User data will be inserted here dynamically -->
        </tbody>
    </table>
</section>

<script>
    // Add your JavaScript code here
    function addUser() {
        // Implement the logic to open a form or redirect to the user creation page
        alert('Implement logic to add user');
    }

    function deleteUser(userId) {
        // Implement the logic to delete the user with the given ID
        alert('Implement logic to delete user with ID ' + userId);
    }

    // Fetch user data from your API and populate the table
    // Example using fetch API:
    fetch('api_url_for_getting_users')
        .then(response => response.json())
        .then(data => {
            const userTable = document.getElementById('userTable');
            const tbody = userTable.getElementsByTagName('tbody')[0];
            
            data.forEach(user => {
                const row = tbody.insertRow();
                row.insertCell(0).textContent = user.user_id;
                row.insertCell(1).textContent = user.fullname;
                row.insertCell(2).textContent = user.birthdate;
                row.insertCell(3).textContent = user.address;
                row.insertCell(4).textContent = user.sex;
                row.insertCell(5).textContent = user.username;
                row.insertCell(6).textContent = user.role_name;

                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.onclick = function() {
                    deleteUser(user.user_id);
                };

                row.insertCell(7).appendChild(deleteButton);
            });
        })
        .catch(error => console.error('Error fetching user data:', error));
</script>

</body>
</html>
