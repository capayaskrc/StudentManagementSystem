<?php include '../layout/header_user.php'; ?>

    <section id="users">
        <div id="opts" class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="add-user">
                    <a href="add_user.php" class="btn btn-primary mr-2">ADD USER</a>
                </div>
                <div class="sort-container">
                    <label for="sortType">Sort by:</label>
                    <select id="sortType" class="form-control" onchange="sortUsers()">
                        <option value="all">All Users</option>
                        <option value="student">Students</option>
                        <option value="teacher">Teachers</option>
                    </select>
                </div>
                <div class="input-group">
                    <input type="text" id="searchInputs" class="form-control" placeholder="Search by ID, Name, or Username" aria-label="Search by ID, Name, or Username" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="searchUsers()">Search</button>
                        <button class="btn btn-outline-secondary mr-1" type="button" onclick="clearSearch()">Clear</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="userTable" class="table">
                <thead>
                <tr>
                    <th style="width: 3%;" class="text-center align-middle">ID</th>
                    <th style="width: 15%;" class="text-center align-middle">Username</th>
                    <th style="width: auto;" class="text-center align-middle">Role</th>
                    <th style="width: 15%;" class="text-center align-middle">Full Name</th>
                    <th style="width: 5%;" class="text-center align-middle">Sex</th>
                    <th style="width: 10%;" class="text-center align-middle">Birthdate</th>
                    <th style="width: auto;" class="text-center align-middle">Address</th>

                    <th style="width: 12%;" colspan="2" class="text-center align-middle">Action</th>
                </tr>
                </thead>
                <tbody>
                <!-- User data will be inserted here dynamically -->
                </tbody>
            </table>
        </div>
        <p id="noResultsMessage" class="mt-3 text-center">No users found.</p>

        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit User Information</h5>
                        <button type="button" class="bg-danger" aria-label="Close" onclick="closeEditModal()" style="width: 30px; height: 30px; padding: 0; border-radius: 0;">
                            <span aria-hidden="true" style="font-size: 20px;">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <form>
                                <!-- Add a hidden input field for user ID -->
                                <input type="hidden" id="userId" name="userId">

                                <div class="form-group">
                                    <label for="fullname">Full Name:</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname">
                                </div>
                                <div class="form-group">
                                    <label for="sex">Sex:</label>
                                    <input type="text" class="form-control" id="sex" name="sex">
                                </div>
                                <div class="form-group">
                                    <label for="birthdate">Birthdate:</label>
                                    <input type="date" class="form-control" id="birthdate" name="birthdate">
                                </div>
                                <div class="form-group">
                                    <label for="address">Address:</label>
                                    <input type="text" class="form-control" id="address" name="address">
                                </div>
                            </form>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Close</button>
                        <button type="button" class="btn btn-primary" onclick="updateUser(document.getElementById('userId').value)">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function closeEditModal() {
                // Hide the modal
                $('#editUserModal').modal('hide');
                // Manually remove the modal backdrop
                document.body.classList.remove('modal-open');
                const modalBackdrops = document.getElementsByClassName('modal-backdrop');
                for (let backdrop of modalBackdrops) {
                    backdrop.parentNode.removeChild(backdrop);
                }
            }
            let currentUserId;
            let allUsers = []; // Store all users to avoid fetching data multiple times

            // Fetch all users initially
            fetch('../auth/api.php?users')
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
                    row.insertCell(1).textContent = user.username;
                    row.insertCell(2).textContent = user.role_name;
                    row.insertCell(3).textContent = user.fullname;
                    row.insertCell(4).textContent = user.sex;
                    row.insertCell(5).textContent = user.birthdate;
                    row.insertCell(6).textContent = user.address;
                    row.cells[0].classList.add('text-center', 'align-middle');
                    // Add Bootstrap classes to center text content
                    for (let i = 1; i <= 6; i++) {
                        row.cells[i].classList.add('align-middle');
                    }

                    const updateButton = document.createElement('button');
                    updateButton.innerHTML = '<i class="fas fa-edit"></i>';
                    updateButton.className = 'btn btn-warning btn-sm'; // Small size
                    updateButton.style.marginRight = '10px';
                    updateButton.style.width = '40px'; // Set a specific width
                    updateButton.style.height = '40px'; // Set a specific height
                    updateButton.onclick = async function () {
                        try {
                            userId = user.user_id;
                            const userData = await fetchUser(userId);
                            openEditModal(userData);
                        } catch (error) {
                            console.error('Error viewing user:', error.message);
                        }
                    };

                    const deleteButton = document.createElement('button');
                    deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
                    deleteButton.className = 'btn btn-danger btn-sm'; // Small size
                    deleteButton.style.width = '40px'; // Set a specific width
                    deleteButton.style.height = '40px'; // Set a specific height
                    deleteButton.onclick = function () {
                        deleteUser(user.user_id);
                    };

                    const actionCell = row.insertCell(7);
                    actionCell.classList.add('text-center', 'align-middle'); // Center align and middle vertically
                    actionCell.appendChild(updateButton);
                    actionCell.appendChild(deleteButton);
                });
            }

            function searchUsers() {
                const searchInputs = document.getElementById('searchInputs');
                const searchTerm = searchInputs.value.toLowerCase();

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
                document.getElementById('searchInputs').value = '';

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
                document.getElementById('searchInputs').value = '';

                // Hide the "No results" message
                document.getElementById('noResultsMessage').style.display = 'none';
            }

            // Add this event listener to trigger the sorting when the page loads
            document.addEventListener('DOMContentLoaded', function () {
                sortUsers();
            });

            async function fetchUser(userId) {
                try {
                    const response = await fetch(`../auth/api.php?getUser&userID=${userId}`, {
                        method: 'GET', // Use 'GET' method for fetching data
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    const data = await response.json();
                    // console.log(data); // You can handle the response data as needed
                    return data;
                } catch (error) {
                    console.error('Error fetching user:', error.message);
                    throw error;
                }
            }

            function openEditModal(user) {
                // Populate the modal with the user data
                document.getElementById('userId').value =  user.user_id;
                document.getElementById('fullname').value = user.fullname;
                document.getElementById('sex').value = user.sex;
                document.getElementById('birthdate').value = user.birthdate;
                document.getElementById('address').value = user.address;
                // Show the modal
                $('#editUserModal').modal('show');

            }
            // function saveChanges(currentUserId) {
            //     updateUser(currentUserId);
            //     console.log(updateUser(currentUserId));
            // }


            async function updateUser(userId) {
                try {
                    const updatedUserData = getUserFormData(); // Implement this function to collect form data

                    const response = await fetch(`../auth/api.php?user&userID=${userId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(updatedUserData),
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    const data = await response.json();
                    console.log(data); // You can handle the response data as needed
                    closeEditModal();

                    fetch('../auth/api.php?users')
                        .then(response => response.json())
                        .then(updatedUsers => {
                            allUsers = updatedUsers; // Update the allUsers array
                            populateUserTable(allUsers); // Populate the table with the updated users
                        })
                        .catch(error => console.error('Error fetching updated user data:', error));
                } catch (error) {
                    console.error('Error updating user:', error.message);
                    // Handle error as needed
                }
            }

            // Function to get the user data from the form
            function getUserFormData() {
                return {
                    user_id: document.getElementById('userId').value,
                    fullname: document.getElementById('fullname').value,
                    sex: document.getElementById('sex').value,
                    birthdate: document.getElementById('birthdate').value,
                    address: document.getElementById('address').value,
                };
            }


            function closeModal() {
                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.hide();
            }
        </script>
    </section>
<?php include '../layout/footer.php'; ?>