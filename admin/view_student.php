<?php include '../layout/header_user.php'; ?>

<body>
    <header class="header">
        <a class="title" href="admin_dashboard.php">Dashboard</a>

        <div class="logout">

            <a href="logout.php" class="btn btn-primary">Logout</a>

        </div>
    </header>

    <aside>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="add_user.php">Add User</a></li>
            <li><a href="view_student.php">View Student</a></li>
            <li><a href="add_course.php">Add Courses</a></li>
            <li><a href="">View Courses</a></li>
        </ul>
    </aside>

    <div class="viewstudents">
        <h1>View Students</h1>
        <!-- Add a table to display students' information -->
        <table id="studentsTable">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Birthdate</th>
                    <th>Address</th>
                    <!-- Add more columns as needed -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Student data will be populated here dynamically -->
            </tbody>
        </table>

        <!-- Button to delete selected students -->
        <button id="deleteSelected">Delete Selected</button>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        $(document).ready(function () {
            // Fetch student data from your API
            $.ajax({
                url: '../auth/api.php?user',
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    // Check if the response is successful
                    if (response.hasOwnProperty('error')) {
                        console.error('Error fetching student data:', response.error);
                        return;
                    }

                    // Populate the table with student data
                    populateStudentsTable(response);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching student data:', error);
                }
            });

            // Function to populate the table with student data
            function populateStudentsTable(students) {
                var tbody = $('#studentsTable tbody');

                // Loop through the student data and append rows to the table
                students.forEach(function (student) {
                    var row = $('<tr>');
                    row.append('<td><input type="checkbox" class="selectCheckbox"></td>');
                    row.append('<td>' + student.user_id + '</td>');
                    row.append('<td>' + student.fullname + '</td>');
                    row.append('<td>' + student.birthdate + '</td>');
                    row.append('<td>' + student.address + '</td>');
                    // Add more columns as needed

                    // Add an action button (you can customize this part)
                    row.append('<td><button class="actionButton" data-userid="' + student.user_id + '">Action</button></td>');

                    tbody.append(row);
                });
            }

            // Example: Handle click on an action button
            $('#studentsTable').on('click', '.actionButton', function () {
                var userId = $(this).data('userid');
                alert('Perform an action for user ID ' + userId);
            });

            // Example: Handle click on the deleteSelected button
            $('#deleteSelected').on('click', function () {
                var selectedUserIds = [];

                // Collect selected user IDs
                $('.selectCheckbox:checked').each(function () {
                    selectedUserIds.push($(this).closest('tr').find('td:nth-child(2)').text());
                });

                // Perform the delete operation or show a confirmation dialog
                if (selectedUserIds.length > 0) {
                    // Example: Call your API to delete selected users
                    console.log('Delete the following user IDs:', selectedUserIds);
                } else {
                    alert('No users selected for deletion.');
                }
            });
        });
    </script>
</body>

<?php include '../layout/footer.php'; ?>