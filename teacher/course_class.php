<?php
include '../layout/header_student.php';
?>

<section id="courses" class="container-fluid justify-content-md-center col-md-8">
    <h1>Courses</h1>

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
                        <input type="hidden" id="unenrollModalCourseId" name="courseId"> <!-- Add this line -->
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


    <!-- Bootstrap Table -->
    <div class="table-responsive">
        <table class="table table-bordered" id="courseTable">
            <thead>
                <tr>
                    <th class="text-center align-middle" style="width: 10%">Student ID</th>
                    <th class="text-center align-middle" style="width: 60%">Student Name</th>
                    <th class="text-center align-middle" style="width: 60%">Year Level</th>
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
        const userRole = '<?php echo $_SESSION['role']; ?>';
        const auth_token = '<?php echo $_SESSION['auth_token']; ?>';
        const userId = '<?php echo $_SESSION['user_id']; ?>';

        const urlParams = new URLSearchParams(window.location.search);
        const courseId = parseInt(urlParams.get('course_id'));

        document.addEventListener("DOMContentLoaded", function () {
        fetchAllCourses();
    });


    function fetchAllCourses() {
        fetch(`../auth/api.php?class&userRole=${userRole}&user_id=${userId}&courseId=${courseId}`, {
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

    function populateCourseTable(students) {
        const courseTable = document.getElementById('courseTable');
        const tbody = courseTable.getElementsByTagName('tbody')[0];
        tbody.innerHTML = ''; // Clear existing rows

        students.forEach(student => {
            const row = tbody.insertRow();
            row.insertCell(0).textContent = student.student_id;
            row.insertCell(1).textContent = student.fullname;
            row.insertCell(2).textContent = student.year_lvl;
            row.cells[0].classList.add('text-center', 'align-middle');
            row.cells[1].classList.add('align-middle');
            row.cells[2].classList.add('align-middle');

            // Add "Enroll" button
            const enrollButton = document.createElement('button');
            enrollButton.textContent = 'Unenroll';
            enrollButton.className = 'btn btn-success btn-sm';
            enrollButton.onclick = function () {
                unenrollStudent(student.student_id, );
            };

            const cell = row.insertCell(3);
            row.cells[2].classList.add('text-center', 'align-middle');
            cell.appendChild(enrollButton);

            row.addEventListener('click', () => {
                const settingsUrl = '../teacher/courseClass.php';
                window.location.href = settingsUrl;
            });
            row.style.cursor = 'pointer';
        });
    }


    function unenrollStudent(studentId) {
        // Prepare enrollment data
        const unenrollData = {
            student_id: parseInt(studentId),
            course_id: parseInt(courseId),
        };

        // Send enrollment request to the backend
        fetch(`../auth/api.php?unenroll&courseID=${courseId}&studentID=${studentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(unenrollData),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to unenroll student');
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

    </script>


<?php include '../layout/footer.php'; ?>