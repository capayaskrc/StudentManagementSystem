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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="../css/dashboard.css">
    <title>SMS</title>
</head>
<body>
<?php
include '../layout/header.php';
include '../layout/navbar.php';
?>
<div class="container" style="max-width: 1200px;">
    <!-- Your content goes here -->

