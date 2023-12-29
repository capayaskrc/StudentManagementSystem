<?php include '../layout/header_user.php'; ?>

<section id="users">
    <div id="opts">
        <div class="add-user">
            <a href="add_user.php" class="btn btn-primary">ADD USER</a>
        </div>
        <div class="sort-container">
            <label for="sortType">Sort by:</label>
            <select id="sortType" onchange="sortUsers()">
                <option value="all">All Users</option>
                <option value="student">Students</option>
                <option value="teacher">Teachers</option>
            </select>
        </div>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search by ID, Name, or Username">
            <button onclick="searchUsers()">Search</button>
            <button onclick="clearSearch()">Clear Search</button>
        </div>
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

    <p id="noResultsMessage">No users found.</p>
</section>

<script>
    let allUsers = []; // Store all users to avoid fetching data multiple times

    // Fetch all users initially
    fetch('../auth/api.php?user')
        .then(response => response.json())
        .then(data => {
            allUsers = data; // Store all users
            populateUserTable(allUsers); // Populate the table initially
        })
        .catch(error => console.error('Error fetching user data:', error));

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
                    // Remove the deleted user from the local data
                    allUsers = allUsers.filter(user => user.user_id !== userId);
                    // Repopulate the table
                    populateUserTable(allUsers);
                } else {
                    throw new Error('Error deleting user');
                }
            })
            .catch(error => console.error('Error deleting user:', error));
    }

    function populateUserTable(users) {
        const userTable = document.getElementById('userTable');
        const tbody = userTable.getElementsByTagName('tbody')[0];
        tbody.innerHTML = ''; // Clear existing rows

        users.forEach(user => {
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
    }

    function searchUsers() {
        const searchInput = document.getElementById('searchInput');
        const searchTerm = searchInput.value.toLowerCase();

        // Filter users based on the search term
        const filteredUsers = allUsers.filter(user =>
            user.user_id.toString().includes(searchTerm) ||
            user.fullname.toLowerCase().includes(searchTerm) ||
            user.username.toLowerCase().includes(searchTerm)
        );

        // Repopulate the table with the filtered users
        populateUserTable(filteredUsers);

        // Display or hide the "No results" message based on search results
        const noResultsMessage = document.getElementById('noResultsMessage');
        noResultsMessage.style.display = (filteredUsers.length === 0) ? 'block' : 'none';
    }

    function clearSearch() {
        // Clear the search input
        document.getElementById('searchInput').value = '';

        // Repopulate the table with all users
        populateUserTable(allUsers);

        // Hide the "No results" message
        document.getElementById('noResultsMessage').style.display = 'none';
    }

    let currentSortType = 'all'; // Default to showing all users

function sortUsers() {
    const sortType = document.getElementById('sortType').value;
    currentSortType = sortType;

    if (sortType === 'all') {
        // Show all users
        populateUserTable(allUsers);
    } else {
        // Filter users based on the selected role
        const sortedUsers = allUsers.filter(user => user.role_name.toLowerCase() === sortType.toLowerCase());
        populateUserTable(sortedUsers);
    }

    // Clear the search input
    document.getElementById('searchInput').value = '';

    // Hide the "No results" message
    document.getElementById('noResultsMessage').style.display = 'none';
}

// Add this event listener to trigger the sorting when the page loads
document.addEventListener('DOMContentLoaded', function () {
    sortUsers();
});
</script>

<?php include '../layout/footer.php'; ?>