<?php
include '../layout/header_student.php';
?>

<header class="header">

    <a class="title" href="">Dashboard</a>

    <div class="logout">

        <a href="logout.php" class="btn btn-primary">Logout</a>

    </div>

</header>


<aside>
    <ul>
        <li><a href="">Add Student</a></li>
        <li><a href="">View Student</a></li>
        <li><a href="">Add Teacher</a> </li>
        <li><a href="">View Teacher</a></li>
        <li><a href="">Add Courses</a></li>
        <li><a href="">View Courses</a></li>
    </ul>
</aside>


<div class="content">
    <h1>Hello World</h1>
    <p>In this example, we have added an accordion and a dropdown menu inside the side navigation.</p>
</div>

</body>

<?php include '../layout/footer.php'; ?>