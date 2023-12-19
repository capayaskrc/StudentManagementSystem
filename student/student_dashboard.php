<?php
include '../layout/header.php';

// Check if the user is logged in and has a name
$loggedInUser = '';  // Set a default value
if (isset($_SESSION['user']) && isset($_SESSION['user']['name'])) {
    $loggedInUser = $_SESSION['user']['name'];
}
?>

<!-- Student Dashboard Content -->
<div class="container">
    <h2>Welcome, <?php echo $loggedInUser; ?>, to your Dashboard!</h2>
    <p>This is the content of the student dashboard.</p>
</div>

<?php
include '../layout/footer.php';
?>
