<?php include '../layout/header_user.php'; ?>

<section id="users">
    <h2>Users</h2>
    <div class="add-user">
            <a href="add_user.php" class="btn btn-primary">ADD USER</a>
        </div>
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
    function addUser() {
    alert('Implement logic to add user');
}

function deleteUser(userId) {
    const confirmation = confirm('Are you sure you want to delete this user?');
    if (confirmation) {
        // Implement the logic to delete the user with the given ID
        alert('Implement logic to delete user with ID ' + userId);
    }
}

async function fetchUserData() {
    try {
        const response = await fetch('../auth/api.php?user');
        const data = await response.json();

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
            deleteButton.onclick = function () {
                deleteUser(user.user_id);
            };

            row.insertCell(7).appendChild(deleteButton);
        });
    } catch (error) {
        console.error('Error fetching user data:', error);
        // Display an error message on the page
    }
}

// Fetch user data when the page loads
fetchUserData();
</script>

<?php include '../layout/footer.php'; ?>
