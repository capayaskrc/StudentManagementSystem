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
    function deleteUser(userId) {
    // Confirm user deletion
    const confirmation = confirm('Are you sure you want to delete this user?');
    if (!confirmation) {
        return;
    }

    // Delete user through API
    fetch(`../auth/api.php?user&userID=${userId}`, {
        method: 'DELETE',
    })
        .then(response => {
            if (response.ok) {
                alert('User deleted successfully');
                window.location.reload();
            } else {
                throw new Error('Error deleting user');
            }
        })
        .catch(error => console.error('Error deleting user:', error));
}

    // Fetch user data from your API and populate the table
    // Example using fetch API:
    fetch('../auth/api.php?user')
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

<?php include '../layout/footer.php'; ?>
