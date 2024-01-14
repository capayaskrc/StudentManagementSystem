<?php include '../layout/header_user.php'; ?>

<section id="courses" style="container-fluid justify-content-md-center col-md-8">
    <h1>Courses</h1>
    <!-- Bootstrap button for adding courses -->
    <button class="btn btn-primary" data-toggle="modal" data-target="#addCourseModal" onclick="openAddCourseModal()">Add
        Course</button>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1" role="dialog" aria-labelledby="addCourseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCourseModalLabel">Add Course</h5>
                    <button type="button" class="bg-danger" aria-label="Close" onclick="closeEditModal()"
                        style="width: 30px; height: 30px; padding: 0; border-radius: 0;">
                        <span aria-hidden="true" style="font-size: 20px;">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Add your form fields for adding a course -->
                    <form>
                        <!-- Add a hidden input field for course ID -->
                        <input type="hidden" id="courseId" name="courseId">

                        <div class="form-group">
                            <label for="courseName">Course Name</label>
                            <input type="text" class="form-control" id="courseName" placeholder="Enter Course Name">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="addCourse()">Add Course</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="closeEditModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

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
                    <form>
                        <input type="hidden" id="enrollModalCourseId" name="courseId"> <!-- Add this line -->
                        <div class="form-group">
                            <label for="studentId">Student ID</label>
                            <input type="text" class="form-control" id="studentId" placeholder="Enter Student ID">
                        </div>
                        <!-- You can add more fields for student information if needed -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="enrollStudent()">Enroll</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="closeEnrollModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal for Assigning Instructor -->
    <div class="modal fade" id="assignInstructorModal" tabindex="-1" role="dialog" aria-labelledby="assignInstructorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignInstructorModalLabel">Assign Instructor</h5>
                    <button type="button" class="bg-danger" aria-label="Close" onclick="closeAssignInstructorModal()"
                        style="width: 30px; height: 30px; padding: 0; border-radius: 0;">
                        <span aria-hidden="true" style="font-size: 20px;">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Add your form fields for enrolling a student -->
                    <form>
                        <input type="hidden" id="assignInstructorModalCourseId" name="courseId"> <!-- Add this line -->
                        <div class="form-group">
                            <label for="teacherId">Teacher ID</label>
                            <input type="text" class="form-control" id="teacherId" placeholder="Enter Teacher ID">
                        </div>
                        <!-- You can add more fields for student information if needed -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="assignInstructor()">Assign</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="closeAssignInstructorModal()">Close</button>
                </div>
            </div>
        </div>
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
</section>
<script>

    document.addEventListener("DOMContentLoaded", function () {
        fetchAllCourses();
    });

    // Function to open the "Add Course" modal
    function openAddCourseModal(course) {
        // Populate the modal with the course data if needed
        document.getElementById('courseId').value = course ? course.courseId : '';
        document.getElementById('courseName').value = course ? course.courseName : '';

        // Show the modal
        $('#addCourseModal').modal('show');
    }


    // Function to close the "Add Course" modal
    function closeEditModal() {
        // Hide the modal
        $('#addCourseModal').modal('hide');
        // Manually remove the modal backdrop
        document.body.classList.remove('modal-open');
        const modalBackdrops = document.getElementsByClassName('modal-backdrop');
        for (let backdrop of modalBackdrops) {
            backdrop.parentNode.removeChild(backdrop);
        }
    }

    function addCourse() {
        const courseName = document.getElementById("courseName").value;
        if (courseName.trim() === "") {
            alert("Please enter a course name");
            return;
        }
        const courseData = {
            courseName: courseName,
        };
        fetch('../auth/api.php?course', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(courseData),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to add course');
                }
                return response.json();
            })
            .then(data => {
                fetchAllCourses(); // Refresh the course list after adding a new course
                closeEditModal()
            })
            .catch(error => {
                console.error('Error adding course:', error);
                // Handle error as needed (e.g., display an error message to the user)
            });
    }

    function fetchAllCourses() {
        fetch('../auth/api.php?courses', {
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

        courses.forEach(course => {
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
                openEnrollModal(course.course_id);
            };

            // Add "Assign Teacher" button
            const assignTeacherButton = document.createElement('button');
            assignTeacherButton.className = 'btn btn-info btn-sm';
            assignTeacherButton.onclick = function () {
                openAssignInstructorModal(course.course_id);
            };

            if (course.user_name) {
                // If a teacher is assigned, display the teacher's name on the button
                assignTeacherButton.textContent = `Assigned to: ${course.user_name}`;
                assignTeacherButton.classList.add('assigned'); // Optional: Add a CSS class for styling
            } else {
                assignTeacherButton.textContent = 'Assign Teacher';
            }

            // Add "Delete" button
            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'Delete';
            deleteButton.className = 'btn btn-danger btn-sm';
            deleteButton.onclick = function () {
                deleteCourse(course.course_id);
            };

            const cell = row.insertCell(2);
            row.cells[2].classList.add('text-center', 'align-middle');
            cell.appendChild(enrollButton);
            cell.appendChild(assignTeacherButton);
            cell.appendChild(deleteButton);
        });
    }

    function deleteCourse(courseId) {
        if (confirm("Are you sure you want to delete this course?")) {
            fetch(`../auth/api.php?course&courseID=${courseId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to delete course');
                    }
                    return response.json();
                })
                .then(data => {
                    fetchAllCourses(); // Refresh the course list after deleting a course
                })
                .catch(error => {
                    console.error('Error deleting course:', error);
                    // Handle error as needed (e.g., display an error message to the user)
                });
        }
    }

    function enrollStudent() {
    const courseId = document.getElementById("enrollModalCourseId").value;
    const studentId = document.getElementById("studentId").value;

    // Validate student ID (you can add more validation as needed)
    if (studentId.trim() === "") {
        alert("Please enter a student ID");
        return;
    }

    // Prepare enrollment data
    const enrollmentData = {
        student_id: studentId,
        course_id: courseId,
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

    function assignInstructor() {
        const courseId = document.getElementById("assignInstructorModalCourseId").value;
        const teacherId = document.getElementById("teacherId").value;

        // Validate Teacher ID (you can add more validation as needed)
        if (teacherId.trim() === "") {
            alert("Please enter a student ID");
            return;
        }

        // Prepare enrollment data
        const enrollmentData = {
            teacher_id: teacherId,
            course_id: courseId,
        };

        // Send enrollment request to the backend
        fetch(`../auth/api.php?course&courseID=${courseId}&instructorID=${teacherId}`, {
            method: 'PUT',
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
            // Handle successful instructor assignment (you can show a success message or perform additional actions)
            console.log('Enrollment successful:', data);

            // Refresh the course list or perform any other necessary actions
            fetchAllCourses();
        })
        .catch(error => {
            console.error('Error enrolling student:', error);
            // Handle error (you can show an error message to the user)
        })
        .finally(() => {
            // Close the instructor assigning modal
            closeAssignInstructorModal();
        });
    }


    // Function to open the "Assign Instructor" modal
    function openAssignInstructorModal(courseId) {
        // Set the course ID in the modal (if needed)
        document.getElementById('assignInstructorModalCourseId').value = courseId;

        // Show the modal
        $('#assignInstructorModal').modal('show');
    }

    // Function to close the "Assign Instructor" modal
    function closeAssignInstructorModal() {
        // Hide the modal
        $('#assignInstructorModal').modal('hide');
        // Manually remove the modal backdrop
        document.body.classList.remove('modal-open');
        const modalBackdrops = document.getElementsByClassName('modal-backdrop');
        for (let backdrop of modalBackdrops) {
            backdrop.parentNode.removeChild(backdrop);
        }
    }

</script>
<?php include '../layout/footer.php'; ?>