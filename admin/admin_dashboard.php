<?php
include '../layout/header_user.php';

include '../auth/db_connection.php';


// Total Students Query
$result_students = $conn->query("SELECT COUNT(*) AS total_students FROM user WHERE role_id = 3");
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

<main>
        <section id="welcome">
            <h2>Welcome Admin</h2>
        </section>

        <section id="status">
            <div class="status-item">
                <h3>Students</h3>
                <p id="student-count"><?php echo $total_students; ?></p>
            </div>

            <div class="status-item">
                <h3>Teachers</h3>
                <p id="teacher-count"><?php echo $total_teachers; ?></p>
            </div>

            <div class="status-item">
                <h3>Courses</h3>
                <p id="course-count"><?php echo $total_courses; ?></p>
            </div>
        </section>
    </main>

<?php include '../layout/footer.php'; ?>