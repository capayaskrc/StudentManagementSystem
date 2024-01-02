<?php include '../layout/header_user.php'; ?>

    <section id="courses" style="container-fluid justify-content-md-center col-md-8">
        <h1>Courses</h1>
        <!-- Bootstrap button for adding courses -->
        <button class="btn btn-primary" data-toggle="modal" data-target="#addCourseModal" onclick="openAddCourseModal()">Add Course</button>

        <!-- Bootstrap Modal -->
        <div class="modal fade" id="addCourseModal" tabindex="100" role="dialog" aria-labelledby="addCourseModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCourseModalLabel">Add Course</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Add your form fields for adding a course -->
                        <div class="form-group">
                            <label for="courseName">Course Name</label>
                            <input type="text" class="form-control" id="courseName" placeholder="Enter Course Name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="addCourse()">Add Course</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeAddCourseModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap Table -->
        <div class="table-responsive">
            <table class="table table-bordered" id="courseTable">
                <thead >
                <tr >
                    <th class="text-center align-middle" style="width: 10%">Course ID</th>
                    <th class="text-center align-middle" style="width: 80%">Course Name</th>
                    <th class="text-center align-middle" style="width: 10%">Action</th>
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
        function openAddCourseModal() {
            document.getElementById('addCourseModal').style.display = 'block';
        }

        // Function to close the "Add Course" modal
        function closeAddCourseModal() {
            document.getElementById('addCourseModal').style.display = 'none';
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
                    closeAddCourseModal();
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

                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.className = 'btn btn-danger btn-sm';
                deleteButton.onclick = function () {
                    deleteCourse(course.course_id);
                };
                const cell = row.insertCell(2);
                row.cells[2].classList.add('text-center', 'align-middle');
                cell.appendChild(deleteButton);
            });
        }

        function deleteCourse(courseId) {
            if (confirm("Are you sure you want to delete this course?")) {
                fetch(`../auth/api.php?course=${courseId}`, {
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
    </script>
<?php include '../layout/footer.php'; ?>