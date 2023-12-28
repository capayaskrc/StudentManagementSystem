<?php
session_start();
// Check if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); // Redirect to the login page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/dashboard.css">
    <title>SMS</title>
</head>

<?php
include '../layout/header.php';
include '../layout/navbar.php';
?>