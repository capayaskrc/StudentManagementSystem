<?php
include '../layout/header_student.php';
?>

<section>
<div class="container-fluid justify-content-md-center">
<!-- Bootstrap Modal for Enrollment -->
<div class="modal fade" id="enrollModal" tabindex="-1" role="dialog" aria-labelledby="enrollModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollModalLabel">Enroll Student</h5>
                <button type="button" class="bg-danger" aria-label="Close" onclick="closeEnrollModal()"
                    style="width: 30px; height: 30px; padding: 0; border-radius: 0;">
                    <span aria-hidden="true" style="font-size: 20px;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Add your form fields for enrolling a student -->
                <form onsubmit="enrollStudent(event)">
                    <input type="hidden" id="enrollModalCourseId" name="courseId"> <!-- Add this line -->
                    <div class="form-group">
                        <label for="studentId">Student ID</label>
                        <input type="text" class="form-control" id="studentId" placeholder="Enter Student ID">
                    </div>
                    <!-- You can add more fields for student information if needed -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="enrollStudent(event)">Enroll</button>

                <button type="button" class="btn btn-secondary" data-dismiss="modal"
                    onclick="closeEnrollModal()">Close</button>
            </div>
        </div>
    </div>
</div>
</div>
    <div class="container d-flex align-items-center justify-content-between mt-4 ml-2 mr-2">
        <h1>Courses</h1>
        <button type="button" class="btn btn-primary" onclick="goToCourseClass()">Go to Course Class</button>
    </div>

<!-- Bootstrap Table -->
<div class="table-responsive">
    <table class="table table-bordered" id="courseTable">
        <thead>
            <tr>
                <th class="text-center align-middle" style="width: 10%">Course ID</th>
                <th class="text-center align-middle" style="width: 60%">Course Name</th>
                <th class="text-center align-middle" style="width: 30%">Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Course data will be displayed here dynamically -->
        </tbody>
    </table>
</div>
    </div>
</section>

<script>
    const userRole = '<?php echo $_SESSION['role']; ?>';
    const auth_token = '<?php echo $_SESSION['auth_token']; ?>';
    const userId = '<?php echo $_SESSION['user_id']; ?>';

    document.addEventListener("DOMContentLoaded", function () {
        fetchAllCourses();
    });


    function fetchAllCourses() {
        fetch(`../auth/api.php?dashboard&userRole=${userRole}&user_id=${userId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch courses');
                }
                return response.json();
            })
            .then(data => {
                populateCourseTable(data);
            })
            .catch(error => {
                console.error('Error fetching courses:', error);
                // Handle error as needed (e.g., display an error message to the user)
            });
    }

    function populateCourseTable(courses) {
        const courseTable = document.getElementById('courseTable');
        const tbody = courseTable.getElementsByTagName('tbody')[0];
        tbody.innerHTML = ''; // Clear existing rows

        courses.courses_taught.forEach(course => {
            const row = tbody.insertRow();
            row.insertCell(0).textContent = course.course_id;
            row.insertCell(1).textContent = course.course_name;
            row.cells[0].classList.add('text-center', 'align-middle');
            row.cells[1].classList.add('align-middle');

            // Add "Enroll" button
            const enrollButton = document.createElement('button');
            enrollButton.textContent = 'Enroll';
            enrollButton.className = 'btn btn-success btn-sm';
            enrollButton.onclick = function () {
                event.stopPropagation(); // Stop event propagation here
                openEnrollModal(course.course_id);
            };

            const cell = row.insertCell(2);
            row.cells[2].classList.add('text-center', 'align-middle');
            cell.appendChild(enrollButton);

            row.addEventListener('click', () => {
                const settingsUrl = './course_class.php?course_id=' + course.course_id;
                // const settingsUrl = './course_class.php?user_role='+ userRole +'&course_id=' + course.course_id;
                window.location.href = settingsUrl;
            });
            row.style.cursor = 'pointer';
        });
    }


    function enrollStudent(event) {
        event.preventDefault(); // Prevent the default form submission
        event.stopPropagation(); // Stop event propagation here

        const courseId = document.getElementById("enrollModalCourseId").value;
        const studentId = document.getElementById("studentId").value;

        // Validate student ID (you can add more validation as needed)
        if (studentId.trim() === "") {
            alert("Please enter a student ID");
            return;
        }

        // Prepare enrollment data
        const enrollmentData = {
            student_id: parseInt(studentId),
            course_id: parseInt(courseId),
        };

        // Send enrollment request to the backend
        fetch('../auth/api.php?enrollment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(enrollmentData),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to enroll student');
                }
                return response.json();
            })
            .then(data => {
                // Handle successful enrollment (you can show a success message or perform additional actions)
                console.log('Enrollment successful:', data);

                // Refresh the course list or perform any other necessary actions
                fetchAllCourses();
            })
            .catch(error => {
                console.error('Error enrolling student:', error);
                // Handle error (you can show an error message to the user)
            })
            .finally(() => {
                // Close the enrollment modal
                closeEnrollModal();
            });
    }
    // Function to open the "Enroll Student" modal
    function openEnrollModal(courseId) {
        // Set the course ID in the modal (if needed)
        document.getElementById('enrollModalCourseId').value = courseId;

        // Show the modal
        $('#enrollModal').modal('show');
    }

    // Function to close the "Enroll Student" modal
    function closeEnrollModal() {
        // Hide the modal
        $('#enrollModal').modal('hide');
        // Manually remove the modal backdrop
        document.body.classList.remove('modal-open');
        const modalBackdrops = document.getElementsByClassName('modal-backdrop');
        for (let backdrop of modalBackdrops) {
            backdrop.parentNode.removeChild(backdrop);
        }
    }

    function goToCourseClass() {
        // You can customize the URL as needed
        const courseClassUrl = './course_class.php';
        window.location.href = courseClassUrl;
    }

</script>


<?php include '../layout/footer.php'; ?>