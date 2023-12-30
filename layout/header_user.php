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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/dashboard.css">
    <title>SMS</title>
</head>
<body>
<?php
include '../layout/header.php';
include '../layout/navbar.php';
?>
<div class="container" style="max-width: 800px;">
    <!-- Your content goes here -->

