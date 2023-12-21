<?php
include '../layout/header_user.php';
?>

<header class="header">
    <a class="title" href="">Dashboard</a>
    <div class="logout">
        <a href="logout.php" class="btn btn-primary">Logout</a>
    </div>
</header>

<aside>
    <ul>
        <li>
            <a href="">Enrolled Subjects</a>
        </li>
        <li>
            <a href="">View Courses</a>
        </li>
    </ul>
</aside>

<div class="content">
    <h1>Hello World</h1>
    <p>In this example, we have added an accordion and a dropdown menu inside the side navigation.
        Click on both to understand how they differ from each other. The accordion will push down the content, while the
        dropdown lays over the content.</p>
</body>

<?php include '../layout/footer.php'; ?>