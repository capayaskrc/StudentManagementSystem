<?php
include '../layout/header_student.php';
?>

<div class="container mt-5">
    <section>
        <h1>Account Settings</h1>

        <div class="row">
            <div class="col-md-7">
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <h3 class="card-title">Personal Information</h3>

                        <form>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" value="John Doe" readonly>
                            </div>

                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" id="address" value="123 Main St" readonly>
                            </div>

                            <div class="form-group">
                                <label for="sex">Sex</label>
                                <input type="text" class="form-control" id="sex" value="Male" readonly>
                            </div>

                            <div class="form-group">
                                <label for="bday">Birthday</label>
                                <input type="text" class="form-control" id="bday" value="1990-01-01" readonly>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <h3 class="card-title">Account Information</h3>

                        <form>
                            <div class="form-group">
                                <label for="username">Username</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="username" value="<?php echo $_SESSION['username']; ?>" readonly>
                                    <div class="input-group-append">
                                        <button id="editUsernameBtn" class="btn btn-primary" type="button">Edit</button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" placeholder="********" readonly>
                                <a href="change_password.php" class="btn btn-link mt-2">Change Password</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include '../layout/footer.php'; ?>
<script>


</script>