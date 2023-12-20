<?php
include '../layout/header_student.php';

include '../auth/db_connection.php';

// Total Students Query
$result_students = $conn->query("SELECT COUNT(*) AS total_students FROM student");
$row_students = $result_students->fetch_assoc();
$total_students = $row_students['total_students'];

// Total Teachers Query
$result_teachers = $conn->query("SELECT COUNT(*) AS total_teachers FROM user WHERE role_id = 2");
$row_teachers = $result_teachers->fetch_assoc();
$total_teachers = $row_teachers['total_teachers'];

// Total Courses Query
$result_courses = $conn->query("SELECT COUNT(*) AS total_courses FROM course");
$row_courses = $result_courses->fetch_assoc();
$total_courses = $row_courses['total_courses'];

// Close connection
$conn->close();
?>

<header class="header">

    <a class="title" href="admin_dashboard.php">Dashboard</a>

    <div class="logout">
        <a href="../logout.php" class="btn btn-primary">Logout</a>
    </div>

</header>


<aside>
    <ul>
        <li><a href="add_student.php">Add User</a></li>
        <li><a href="">View Student</a></li>
        <li><a href="">Add Courses</a></li>
        <li><a href="">View Courses</a></li>
    </ul>
</aside>

<div class="welcome">
    <h1>Welcome Admin!</h1>
</div>
<div class="content">
    <div class="statistics-box teachers-box">
        <h2>Teachers:</h2>
        <p class="total-numbers"><?php echo $total_teachers; ?></p>
    </div>

    <div class="statistics-box students-box">
        <h2>Students:</h2>
        <p class="total-numbers"><?php echo $total_students; ?></p>
    </div>

    <div class="statistics-box courses-box">
        <h2>Courses:</h2>
        <p class="total-numbers"><?php echo $total_courses; ?></p>
    </div>

</div>

<?php include '../layout/footer.php'; ?>