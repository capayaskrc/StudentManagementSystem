<?php include '../layout/header_student.php'; ?>

<div class="container mt-5">
    <section>
        <h1>Account Settings</h1>

        <div class="row">
            <div class="col-md-7">
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <h3 class="card-title">Personal Information</h3>

                        <form>
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullname" name="fullname" disabled>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="birthdate" class="form-label">Birthdate</label>
                                    <input type="text" class="form-control" id="birthdate" name="birthdate" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="sex" class="form-label">Sex</label>
                                    <input type="text" class="form-control" id="sex" name="sex" disabled>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" disabled>
                            </div>
                            <!-- Add other personal information fields as needed -->
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <h3 class="card-title">Account Information</h3>

                        <form>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" disabled>
                            </div>
                            <div class="mb-3">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    Change Password
                                </button>
                            </div>
                            <!-- Add other account information fields as needed -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form for changing password -->
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="handleChangePassword()">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>

<script>
    const userId = '<?php echo $_SESSION['user_id']; ?>';
    const auth_token = '<?php echo $_SESSION['auth_token']; ?>';
    function handleUserSettings() {
        fetch(`../auth/api.php?settings&user_id=${userId}`, {
            method: 'GET', // Keep the method as GET
            headers: {
                'Content-Type': 'application/json',
            },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch user settings');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('fullname').value = data.fullname;
                document.getElementById('birthdate').value = data.birthdate;
                document.getElementById('sex').value = data.sex;
                document.getElementById('address').value = data.address;
                document.getElementById('username').value = data.username;
                // Add code to update other form fields
            })
            .catch(error => {
                console.error('Error fetching user settings:', error);
                // Handle error as needed (e.g., display an error message to the user)
            });
    }
    handleUserSettings();

    // Submit handler for the change password form
    function handleChangePassword() {
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        // Validate form inputs (you can add more validation as needed)
        if (!currentPassword || !newPassword || !confirmPassword) {
            alert('Please fill in all fields');
            return;
        }

        // Check if the new password and confirm password match
        if (newPassword !== confirmPassword) {
            alert('New password and confirm password do not match');
            return;
        }
        // Add logic to send a request to the server to change the password
        fetch(`../auth/api.php?password&user_id=${userId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + auth_token,
            },
            body: JSON.stringify({
                currentPassword: currentPassword,
                newPassword: newPassword,
            }),
        })
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    // Handle successful password change (you can show a success message or perform additional actions)
                    console.log('Password change successful:', data.message);

                    // Close the change password modal
                    $('#changePasswordModal').modal('hide');
                } else {
                    // Handle error (you can show an error message to the user)
                    console.error('Error changing password:', data.message);
                }
            })
            .catch(error => {
                console.error('Error changing password:', error);
                // Handle error (you can show an error message to the user)
            });
    }

</script>
