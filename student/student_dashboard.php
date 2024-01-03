<?php
include '../layout/header_student.php';
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
    // Fetch student information and enrolled courses from the API
    fetch('../auth/api.php?dashboard', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + yourAuthToken // Include your authentication token here
        }
    })
    .then(response => response.json())
    .then(data => {
        // Display student information
        document.getElementById('student-info').innerHTML = `
            <p><strong>Name:</strong> ${data.fullname}</p>
            <p><strong>Birthdate:</strong> ${data.birthdate}</p>
            <p><strong>Address:</strong> ${data.address}</p>
            <p><strong>Sex:</strong> ${data.sex}</p>
        `;

        // Display enrolled courses
        const coursesList = document.getElementById('courses-list');
        if (data.courses && data.courses.length > 0) {
            data.courses.forEach(course => {
                const courseElement = document.createElement('div');
                courseElement.className = 'course';
                courseElement.innerHTML = `<p><strong>Course Name:</strong> ${course.course_name}</p>
                                           <p><strong>Date Enrolled:</strong> ${course.date_enrolled}</p>
                                           <p><strong>Grade:</strong> ${course.grade}</p>`;
                coursesList.appendChild(courseElement);
            });
        } else {
            coursesList.innerHTML = '<p>No enrolled courses.</p>';
        }
    })
    .catch(error => console.error('Error fetching data:', error));
</script>


<?php include '../layout/footer.php'; ?>