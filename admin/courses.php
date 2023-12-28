<?php include '../layout/header_user.php'; ?>

<section id="courses">
    <h1>My Courses</h1>

    <!-- Table to display courses -->
    <table border="1">
        <thead>
            <tr>
                <th>Course ID</th>
                <th>Course Name</th>
                <!-- Add more columns if needed -->
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="courseTableBody">
            <!-- Course data will be displayed here dynamically -->
        </tbody>
    </table>

    <!-- Buttons for adding and deleting courses -->
    <button onclick="openAddCourseModal()">Add Course</button>
    <button onclick="deleteSelectedCourses()">Delete Selected Courses</button>

    <!-- Add Course Modal -->
    <div id="addCourseModal" style="display: none;">
        <!-- Add your form fields for adding a course -->
        <input type="text" id="courseName" placeholder="Enter Course Name">
        <button onclick="addCourse()">Add Course</button>
        <button onclick="closeAddCourseModal()">Close</button>
    </div>
</section>
<script>
    // Function to fetch and display courses from the API
function displayCourses() {
    // Make an API call to get the list of courses
    // Update the courseTableBody with the retrieved data
}

// Function to open the "Add Course" modal
function openAddCourseModal() {
    document.getElementById('addCourseModal').style.display = 'block';
}

// Function to close the "Add Course" modal
function closeAddCourseModal() {
    document.getElementById('addCourseModal').style.display = 'none';
}

// Function to add a new course
function addCourse() {
    // Make an API call to add a new course
    // Reload or update the table after adding the course
    // Close the "Add Course" modal
}

// Function to delete selected courses
function deleteSelectedCourses() {
    // Get the selected courses from the table
    // Make an API call to delete the selected courses
    // Reload or update the table after deleting the courses
}
</script>
<?php include '../layout/footer.php'; ?>