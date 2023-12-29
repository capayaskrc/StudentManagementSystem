<?php include '../layout/header_user.php'; ?>

<section id="courses">
    <h1>My Courses</h1>

    <!-- Table to display courses -->


    <!-- Buttons for adding and deleting courses -->
    <button onclick="openAddCourseModal()">Add Course</button>
    <button onclick="deleteSelectedCourses()">Delete Selected Courses</button>

    <!-- Add Course Modal -->
    <div id="addCourseModal" style="display: none;">
        <!-- Add your form fields for adding a course -->
        <input type="text" id="courseName" placeholder="Enter Course Name">
        <button onclick="addCourses()">Add Course</button>
        <button onclick="closeAddCourseModal()">Close</button>
    </div>

    <table border="1" id="courseTable">
        <thead>
        <tr>
            <th>Course ID</th>
            <th>Course Name</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <!-- Course data will be displayed here dynamically -->
        </tbody>
    </table>

</section>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        fetchAllCourses();
    });
    // Function to fetch and display courses from the API
function displayCourses() {

}

// Function to open the "Add Course" modal
function openAddCourseModal() {
    document.getElementById('addCourseModal').style.display = 'block';
}

// Function to close the "Add Course" modal
function closeAddCourseModal() {
    document.getElementById('addCourseModal').style.display = 'none';
}

    function addCourses() {
        // Get the course name from the input field
        var courseName = document.getElementById("courseName").value;
        if (courseName.trim() === "") {
            alert("Please enter a course name");
            return;
        }

        var courseData = {
            courseName: courseName,
        };

        // Call the addCourse function with courseData
        addCourse(courseData);
    }
// Function to add a new course
function addCourse(courseData) {
    fetch('../auth/api.php?course', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            // Add any other headers as needed (e.g., authentication headers)
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
            closeAddCourseModal();
        })
        .catch(error => {
            console.error('Error adding course:', error);
            // Handle error as needed (e.g., display an error message to the user)
        });
}

    function fetchAllCourses() {
        // Make an API call to get all courses
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
                allCourses = data;
                populateCourseTable(allCourses);
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

            const updateButton = document.createElement('button');
            updateButton.textContent = 'Update';
            updateButton.onclick = function () {
                updateCourse(course);
            };

            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'Delete';
            deleteButton.onclick = function () {
                deleteCourse(course.course_id);
            };

            row.insertCell(2).appendChild(updateButton);
            row.insertCell(3).appendChild(deleteButton);
        });
    }

    function updateCourse(course) {
        // Implement logic for updating a course
        console.log('Update course:', course);
    }

    function deleteCourse(courseId) {
        // Implement logic for deleting a course
        console.log('Delete course with ID:', courseId);
    }

</script>
<?php include '../layout/footer.php'; ?>