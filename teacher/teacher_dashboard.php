<?php
include '../layout/header_student.php';
print_r($_SESSION);


?>

    <div class="container">
        <h1>Student Dashboard</h1>

        <div id="student-info">
            <!-- Student information will be displayed here dynamically using JavaScript -->
        </div>

        <div class="courses-container">
            <h2>Enrolled Courses</h2>
            <div id="courses-list">
                <!-- Enrolled courses will be displayed here dynamically using JavaScript -->
            </div>
        </div>
    </div>

    <script>
        const userRole = '<?php echo $_SESSION['role']; ?>';
        const auth_token = '<?php echo $_SESSION['auth_token']; ?>';
        const userId = '<?php echo $_SESSION['user_id']; ?>';

        // Append the user_id to the apiUrl
        const apiUrl = `http://localhost/StudentManagementSystem/auth/api.php?dashboard&userRole=${userRole}&user_id=${userId}`;

        // Fetch student information and enrolled courses from the API
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + auth_token
            }
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById('student-info').innerHTML = `
            <p style="color: black"><strong>Name:</strong> ${data.fullname}</p>
            <p style="color: black"><strong>Birthdate:</strong> ${data.birthdate}</p>
            <p style="color: black"><strong>Address:</strong> ${data.address}</p>
            <p style="color: black"><strong>Sex:</strong> ${data.sex}</p>
        `;

                // Display enrolled courses
                const coursesList = document.getElementById('courses-list');
                if (data.courses && data.courses.length > 0) {
                    data.courses.forEach(course => {
                        const courseElement = document.createElement('div');
                        courseElement.className = 'course';

                        // Create elements for course details
                        const courseNameElement = document.createElement('p');
                        const dateEnrolledElement = document.createElement('p');
                        const gradeElement = document.createElement('p');

                        // Apply styles directly to the elements
                        courseNameElement.innerHTML = `<strong>Course Name:</strong> ${course.course_name}`;
                        dateEnrolledElement.innerHTML = `<strong>Date Enrolled:</strong> ${course.date_enrolled}`;
                        gradeElement.innerHTML = `<strong>Grade:</strong> ${course.grade}`;

                        // Set color property to black
                        courseNameElement.style.color = 'black';
                        dateEnrolledElement.style.color = 'black';
                        gradeElement.style.color = 'black';

                        // Append elements to courseElement
                        courseElement.appendChild(courseNameElement);
                        courseElement.appendChild(dateEnrolledElement);
                        courseElement.appendChild(gradeElement);

                        // Append courseElement to coursesList
                        coursesList.appendChild(courseElement);
                    });
                } else {
                    coursesList.innerHTML = '<p>No enrolled courses.</p>';
                }

            })
            .catch(error => console.error('Error fetching data:', error));


    </script>


<?php include '../layout/footer.php'; ?>