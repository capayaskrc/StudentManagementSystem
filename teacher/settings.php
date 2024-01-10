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
                            <!-- Add other account information fields as needed -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include '../layout/footer.php'; ?>

<script>
    const userId = '<?php echo $_SESSION['user_id']; ?>';

    function handleUserSettings() {
        console.log('Fetching user settings...');

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
                console.log(data);
                // Update your form fields with the received data
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
</script>
