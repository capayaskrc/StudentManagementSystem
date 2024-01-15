<?php
include '../layout/header_student.php';
?>

<div class="container">
    <h1>Student Dashboard</h1>

    <div class="container mt-5">
        <div class="row">
            <!-- Courses Box -->
            <div class="col-md-4">
                <div class="card border-primary mb-3">
                    <div class="card-body text-primary">
                        <h5 class="card-title">Courses</h5>
                    </div>
                </div>
            </div>

            <!-- Students Box -->
            <div class="col-md-4">
                <div class="card border-primary mb-3">
                    <div class="card-body text-primary">
                        <h5 class="card-title">Students</h5>
                    </div>
                </div>
            </div>
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