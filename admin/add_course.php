<?php
include '../layout/header_user.php';
?>

<body>
    <div class="addcourse">
        <h1>Add Course</h1>
        <form id="courseForm">
            <label for="courseName">Course Name:</label>
            <input type="text" id="courseName" name="courseName" required>

            <!-- Add more fields as needed for your course details -->

            <button type="submit">Add Course</button>
        </form>
    </div>

    <div class="enrollment" style="display: none;">
        <h1>Enrollment</h1>
        <form id="enrollmentForm">
            <!-- Hidden input fields to store course_id and student_id -->
            <input type="hidden" id="enrollmentCourseId" name="course_id">
            <label for="enrollmentStudentId">Student ID:</label>
            <input type="text" id="enrollmentStudentId" name="student_id" required>

            <!-- You can add more fields as needed for enrollment details -->
            <!-- For example, a date picker for date_enrolled -->

            <button type="submit">Enroll Student</button>
        </form>
    </div>

    <script>
        const courseForm = document.getElementById('courseForm');
        const enrollmentForm = document.getElementById('enrollmentForm');
        const enrollmentSection = document.querySelector('.enrollment');
        const enrollmentCourseIdInput = document.getElementById('enrollmentCourseId');

        courseForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Get form data
            const formData = new FormData(courseForm);
            const jsonData = {};

            // Convert FormData to JSON
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            // Send JSON data to the server to add the course
            fetch('../auth/api.php?course', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(jsonData),
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.message) {
                        alert(data.message);

                        // Show the enrollment section
                        enrollmentSection.style.display = 'block';

                        // Set the course_id in the enrollment form
                        enrollmentCourseIdInput.value = data.course_id;

                        // Reset the course form to clear input fields
                        courseForm.reset();
                    } else {
                        console.log(data);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        });

        enrollmentForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Get form data
            const formData = new FormData(enrollmentForm);
            const jsonData = {};

            // Convert FormData to JSON
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            // Send JSON data to the server to enroll the student
            fetch('../auth/api.php?enrollment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(jsonData),
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.message) {
                        alert(data.message);
                        // You can redirect to another page or perform additional actions after enrollment

                        // Reset the enrollment form to clear input fields
                        enrollmentForm.reset();
                    } else {
                        console.log(data);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        });
    </script>

    
</body>

<?php include '../layout/footer.php'; ?>