<?php
header("Content-Type: application/json");
session_start();

require_once 'db_connection.php';

$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
    case 'POST':
        if (isset($_GET['login'])) {
            handle_login();
        } elseif (isset($_GET['enrollment'])) {
            handle_enrollment();
        } elseif (isset($_GET['performance'])) {
            handle_performance();
        } elseif (isset($_GET['course'])) {
            handle_addCourse();
        } elseif (isset($_GET['user'])) {
            handle_addUser();
        } elseif (isset($_GET['create_course'])) {
            handle_create_course();
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Invalid request"]);
        }
        break;
    case 'GET':
//        authenticate_user();

        if (isset($_GET['dashboard'])) {
            switch ($_SESSION['role']) {
                case 'student':
                    handle_student_dashboard();
                    break;
                case 'teacher':
                    handle_teacher_dashboard();
                    break;
                case 'admin':
                    handle_admin_dashboard();
                    break;
                default:
                    http_response_code(403);
                    echo json_encode(["error" => "Invalid user role"]);
            }
        } elseif (isset($_GET['getUser']) && isset($_GET['userID'])) {
            handle_view_user($_GET['userID']);
        } elseif (isset($_GET['users'])) {
            handle_view_user_all();
        } elseif (isset($_GET['courses'])) {
            handle_getAllCourses();
        }else {
            http_response_code(400);
            echo json_encode(["error" => "Invalid request"]);
        }
        break;
    case 'PUT':
        authenticate_user();

        if (isset($_GET['password'])) {
            handle_update_password();
        } elseif (isset($_GET['course']) && isset($_GET['courseID']) && isset($_GET['instructorID'])) {
            handle_assign_instructor($_GET['courseID'], $_GET['instructorID']);
        } elseif (isset($_GET['course']) && isset($_GET['courseID'])) {
            handle_update_course($_GET['courseID']);
        } elseif (isset($_GET['user']) && isset($_GET['userID'])) {
            handle_update_user($_GET['userID']);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Invalid request"]);
        }
        break;
    case 'DELETE':
        if (isset($_GET['user']) && isset($_GET['userID'])) {
            handle_delete_user($_GET['userID']);
        } elseif (isset($_GET['course']) && isset($_GET['courseID'])) {
            handle_delete_course($_GET['courseID']);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Invalid request"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Invalid request method"]);
}

function handle_login()
{
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data['username'];
    $password = $data['password'];

    // Fetch the user from the database based on the username
    $sql = "SELECT User.user_id, User.role_id, User.password, Role.role_name FROM User 
            JOIN Role ON User.role_id = Role.role_id 
            WHERE User.username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            // Verify the password using MD5
            if (md5($password) === $row['password']) {
                // Password is correct
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['role'] = $row['role_id'];

                $token = session_id();
                $response = [
                    "token" => $token,
                    "role" => $row['role_name'],
                    "message" => "Login successful"
                ];
                echo json_encode($response);
            } else {
                // Password is incorrect
                http_response_code(401); // Unauthorized
                echo json_encode(["error" => "Invalid credentials"]);
            }
        } else {
            // User not found
            http_response_code(401); // Unauthorized
            echo json_encode(["error" => "Invalid credentials"]);
        }
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Error querying the database"]);
    }
    $stmt->close();
}

function authenticate_user()
{
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401); // Unauthorized
        echo json_encode(["error" => "Unauthorized"]);
        exit();
    }
}

function handle_addUser()
{
    global $conn;

    // Check if the user making the request is an admin or teacher
    // $allowedRoles = ['admin', 'teacher'];
    // $userRole = $_SESSION['role'];

    // Uncomment the code below if you want to restrict access based on roles
    // if (!in_array($userRole, $allowedRoles)) {
    //     http_response_code(403); // Forbidden
    //     echo json_encode(["error" => "Unauthorized access"]);
    //     exit();
    // }

    // Get the data from the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract user information
    $fullName = mysqli_real_escape_string($conn, $data['fullname']);
    $birthdate = mysqli_real_escape_string($conn, $data['birthdate']);
    $address = mysqli_real_escape_string($conn, $data['address']);
    $sex = mysqli_real_escape_string($conn, $data['sex']);
    $username = mysqli_real_escape_string($conn, $data['username']);
    $roleName = mysqli_real_escape_string($conn, $data['role']);

    // Check if the user with the same name and birthdate already exists
    $sqlCheckUser = "SELECT user_id FROM user WHERE fullname = ? AND birthdate = ?";
    $stmtCheckUser = $conn->prepare($sqlCheckUser);
    $stmtCheckUser->bind_param("ss", $fullName, $birthdate);
    $stmtCheckUser->execute();
    $resultCheckUser = $stmtCheckUser->get_result();

    if ($resultCheckUser && $resultCheckUser->num_rows > 0) {
        // User with the same name and birthdate already exists
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "User with the same name already exists"]);
        exit();
    }

    // Fetch the corresponding RoleID from the "role" table
    $sqlRole = "SELECT role_id FROM role WHERE role_name = ?";
    $stmtRole = $conn->prepare($sqlRole);
    $stmtRole->bind_param("s", $roleName);
    $stmtRole->execute();
    $resultRole = $stmtRole->get_result();

    if ($resultRole && $resultRole->num_rows == 1) {
        // Role found, get the RoleID
        $rowRole = $resultRole->fetch_assoc();
        $roleId = $rowRole['role_id'];

        // Insert the user into the "user" table
        $sqlInsertUser = "INSERT INTO user (fullname, birthdate, address, sex, username, password, role_id)
                              VALUES ('$fullName', '$birthdate', '$address', '$sex', '$username', MD5('default123'), $roleId)";

        if ($conn->query($sqlInsertUser)) {
            http_response_code(201); // Created
            echo json_encode(["message" => "User added successfully"]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["error" => "Error adding user to the database"]);
        }
    } else {
        // Role not found
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Invalid role"]);
    }

    // Close the role and check user statements
    $stmtRole->close();
    $stmtCheckUser->close();
}

function handle_delete_user($userId)
{
    global $conn;

    // Check if the user making the request is authorized (e.g., admin)
    // Add your authorization logic here

    // Delete the user from the "user" table
    $sqlDeleteUser = "DELETE FROM user WHERE user_id = ?";
    $stmtDeleteUser = $conn->prepare($sqlDeleteUser);
    $stmtDeleteUser->bind_param("i", $userId);

    if ($stmtDeleteUser->execute()) {
        http_response_code(200); // OK
        echo json_encode(["message" => "User deleted successfully"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Error deleting user"]);
    }

    // Close the statement
    $stmtDeleteUser->close();
}

function handle_update_user($userId)
{
    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize the input data (customize based on your requirements)
    $updatedFullName = mysqli_real_escape_string($conn, $data['fullname']);
    $updatedBirthdate = mysqli_real_escape_string($conn, $data['birthdate']);
    $updatedAddress = mysqli_real_escape_string($conn, $data['address']);
    $updatedSex = mysqli_real_escape_string($conn, $data['sex']);

    // Update the user information in the database for the specific user ID
    $sql = "UPDATE user
            SET fullname = '$updatedFullName',
                birthdate = '$updatedBirthdate',
                address = '$updatedAddress',
                sex = '$updatedSex'
            WHERE user_id = $userId";

    if ($conn->query($sql)) {
        http_response_code(200); // OK
        echo json_encode(["message" => "User information updated successfully"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Error updating user information: " . $conn->error]);
    }
}


function handle_enrollment()
{
    global $conn;

    // Check if the user making the request is a teacher or admin
//        $allowedRoles = ['teacher', 'admin'];
//        $userRole = $_SESSION['role'];
//
//        if (!in_array($userRole, $allowedRoles)) {
//            http_response_code(403); // Forbidden
//            echo json_encode(["error" => "Unauthorized access"]);
//            exit();
//        }

    // Get the enrollment data from the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize the input data (customize based on your requirements)
    $studentId = mysqli_real_escape_string($conn, $data['student_id']);
    $courseId = mysqli_real_escape_string($conn, $data['course_id']);

    // Check if the student is already enrolled in the course
    $checkEnrollmentSql = "SELECT * FROM Enrollment WHERE student_id = $studentId AND course_id = $courseId";
    $checkEnrollmentResult = $conn->query($checkEnrollmentSql);

    if ($checkEnrollmentResult->num_rows > 0) {
        http_response_code(400);
        echo json_encode(["error" => "Student is already enrolled in the course"]);
        exit();
    }

    // Enroll the student in the course
    $enrollmentSql = "INSERT INTO Enrollment (student_id, course_id, date_enrolled)
                          VALUES ($studentId, $courseId, NOW())";

    if ($conn->query($enrollmentSql)) {
        http_response_code(200);
        echo json_encode(["message" => "Enrollment successful"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error enrolling student in the course"]);
    }
}




function handle_student_dashboard()
{
    global $conn;

    // Fetch student details
    $userId = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = $userId";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $student = $result->fetch_assoc();

        // Fetch enrolled courses and grades
        $sqlCourses = "SELECT Course.course_name, Enrollment.date_enrolled, Performance.grade
                           FROM Enrollment
                           JOIN Course ON Enrollment.course_id = Course.course_id
                           JOIN Performance ON Enrollment.enrollment_id = Performance.enrollment_id
                           WHERE Enrollment.student_id = $userId";

        $resultCourses = $conn->query($sqlCourses);

        if ($resultCourses->num_rows > 0) {
            $courses = [];
            while ($row = $resultCourses->fetch_assoc()) {
                $courses[] = $row;
            }
            $student['courses'] = $courses;
        }

        echo json_encode($student);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "Student not found"]);
    }
}


function handle_teacher_dashboard()
{
    global $conn;

    // Fetch teacher details
    $userId = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = $userId";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $teacher = $result->fetch_assoc();

        // Fetch courses taught by the teacher
        $sqlCourses = "SELECT Course.course_name, Course.course_id
                       FROM Course
                       WHERE Course.user_id = $userId";

        $resultCourses = $conn->query($sqlCourses);

        if ($resultCourses->num_rows > 0) {
            $courses = [];
            while ($row = $resultCourses->fetch_assoc()) {
                $courses[] = $row;
            }
            $teacher['courses_taught'] = $courses;

            // Fetch students enrolled in each course taught by the teacher
            foreach ($courses as &$course) {
                $courseId = $course['course_id'];
                $sqlEnrollments = "SELECT User.fullname, Enrollment.date_enrolled
                                   FROM Enrollment
                                   JOIN User ON Enrollment.student_id = User.user_id
                                   WHERE Enrollment.course_id = $courseId";

                $resultEnrollments = $conn->query($sqlEnrollments);

                if ($resultEnrollments->num_rows > 0) {
                    $enrollments = [];
                    while ($row = $resultEnrollments->fetch_assoc()) {
                        $enrollments[] = $row;
                    }
                    $course['enrollments'] = $enrollments;
                }
            }

            echo json_encode($teacher);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["error" => "Teacher not assigned to any courses"]);
        }
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "Teacher not found"]);
    }
}

function handle_create_course()
{
    global $conn;

    // Check if the user making the request is a teacher
    if ($_SESSION['role'] !== 'teacher') {
        http_response_code(403); // Forbidden
        echo json_encode(["error" => "Unauthorized access"]);
        exit();
    }

    // Get the data from the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract course information
    $courseName = mysqli_real_escape_string($conn, $data['course_name']);

    // Insert the new course into the "Course" table
    $sqlInsertCourse = "INSERT INTO Course (course_name, user_id) VALUES ('$courseName', {$_SESSION['user_id']})";

    if ($conn->query($sqlInsertCourse)) {
        http_response_code(201); // Created
        echo json_encode(["message" => "Course created successfully"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Error creating course"]);
    }
}



function handle_admin_dashboard()
{
    global $conn;

    // Fetch admin details
    $userId = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = $userId";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();

        // Fetch user management data
        $sqlUsers = "SELECT User.user_id, User.fullname, User.role_id, Role.role_name
                     FROM User
                     JOIN Role ON User.role_id = Role.role_id";

        $resultUsers = $conn->query($sqlUsers);

        if ($resultUsers->num_rows > 0) {
            $users = [];
            while ($row = $resultUsers->fetch_assoc()) {
                $users[] = $row;
            }
            $admin['user_management'] = $users;
        }

        // Fetch course management data
        $sqlCourses = "SELECT Course.course_id, Course.course_name, Course.user_id, User.fullname
                       FROM Course
                       JOIN User ON Course.user_id = User.user_id";

        $resultCourses = $conn->query($sqlCourses);

        if ($resultCourses->num_rows > 0) {
            $courses = [];
            while ($row = $resultCourses->fetch_assoc()) {
                $courses[] = $row;
            }
            $admin['course_management'] = $courses;
        }

        echo json_encode($admin);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "Admin not found"]);
    }
}
function handle_view_user($userId)
{
    global $conn;

    // Check if the user making the request is authorized (e.g., admin)
    // Add your authorization logic here

    // Fetch the user with the specified ID along with their role name
    $sql = "SELECT User.user_id, User.fullname, User.birthdate, User.address, User.sex, User.username, Role.role_name
            FROM user
            LEFT JOIN Role ON User.role_id = Role.role_id
            WHERE User.user_id = $userId";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "User not found"]);
    }
}
function handle_view_user_all()
{
    global $conn;

    // Check if the user making the request is authorized (e.g., admin)
    // Add your authorization logic here

    // Fetch all users with their role names
    $sql = "SELECT User.user_id, User.fullname, User.birthdate, User.address, User.sex, User.username, Role.role_name
            FROM user
            LEFT JOIN Role ON User.role_id = Role.role_id";  // Use LEFT JOIN to include users without a role

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode($users);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "No users found"]);
    }
}





function handle_performance()
{
    global $conn;

    if ($_SESSION['role'] !== 'teacher') {
        http_response_code(403);
        echo json_encode(["error" => "Unauthorized access"]);
        exit();
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $enrollmentId = mysqli_real_escape_string($conn, $data['enrollment_id']);
    $grade = mysqli_real_escape_string($conn, $data['grade']);

    // Check if the enrollment exists
    $checkEnrollmentSql = "SELECT * FROM Enrollment WHERE enrollment_id = $enrollmentId";
    $checkEnrollmentResult = $conn->query($checkEnrollmentSql);

    if ($checkEnrollmentResult->num_rows !== 1) {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Invalid enrollment ID"]);
        exit();
    }

    $updateGradeSql = "UPDATE Performance SET Grade = $grade WHERE enrollment_id = $enrollmentId";

    if ($conn->query($updateGradeSql)) {
        http_response_code(200);
        echo json_encode(["message" => "Grade entry successful"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error entering grade"]);
    }
}

function handle_update_password()
{
    global $conn;

    // Check if the user making the request is authenticated
    authenticate_user();

    // Get the user ID from the session
    $userId = $_SESSION['user_id'];

    // Get the new password from the request body
    $data = json_decode(file_get_contents("php://input"), true);
    $newPassword = mysqli_real_escape_string($conn, $data['new_password']);

    // Update the user's password in the database
    $hashedPassword = md5($newPassword); // You may want to use a stronger hashing algorithm
    $updatePasswordSql = "UPDATE User SET Password = ? WHERE user_id = ?";
    $stmt = $conn->prepare($updatePasswordSql);
    $stmt->bind_param("si", $hashedPassword, $userId);

    if ($stmt->execute()) {
        http_response_code(200); // OK
        echo json_encode(["message" => "Password updated successfully"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Error updating password"]);
    }

    $stmt->close();
}
    function handle_getAllCourses() {
    global $conn;

    $sql = "SELECT course_id, course_name FROM course";
    $result = $conn->query($sql);

    $courses = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $courses[] = $row;
            }
            echo json_encode($courses);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["error" => "No users found"]);
        }

        return $courses;
    }
function handle_addCourse()
{
    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);

    $courseName = mysqli_real_escape_string($conn, $data['courseName']);

    $sqlCheckCourse = "SELECT course_id FROM course WHERE course_name = ?";
    $stmtCheckCourse = $conn->prepare($sqlCheckCourse);
    $stmtCheckCourse->bind_param("s", $courseName);
    $stmtCheckCourse->execute();
    $resultCheckCourse = $stmtCheckCourse->get_result();

    if ($resultCheckCourse && $resultCheckCourse->num_rows > 0) {
        // Course with the same name already exists
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Course with the same name already exists"]);
        exit();
    }

    // Insert the course into the "course" table
    $sqlInsertCourse = "INSERT INTO course (course_name) VALUES (?)";
    $stmtInsertCourse = $conn->prepare($sqlInsertCourse);
    $stmtInsertCourse->bind_param("s", $courseName);

    if ($stmtInsertCourse->execute()) {
        http_response_code(201); // Created
        echo json_encode(["message" => "Course added successfully"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Error adding course to the database"]);
    }

    // Close the statement
    $stmtInsertCourse->close();
}


function handle_delete_course($courseID)
{
    global $conn;

    // Check if the user making the request is authorized (e.g., admin)
    // Add your authorization logic here

    // Delete the course from the "course" table
    $sqlDeleteCourse = "DELETE FROM course WHERE course_id = ?";
    $stmtDeleteCourse = $conn->prepare($sqlDeleteCourse);
    $stmtDeleteCourse->bind_param("i", $courseID);

    if ($stmtDeleteCourse->execute()) {
        http_response_code(200); // OK
        echo json_encode(["message" => "Course deleted successfully"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Error deleting course"]);
    }

    // Close the statement
    $stmtDeleteCourse->close();
}

function handle_update_course($courseID)
{
    global $conn;

    // Check if the user making the request is authorized (e.g., admin)
    // Add your authorization logic here

    // Get the updated course data from the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize the input data (customize based on your requirements)
    $updatedCourseName = mysqli_real_escape_string($conn, $data['courseName']);
    // Add more fields as needed for your course details

    // Update the course information in the database
    $sqlUpdateCourse = "UPDATE course SET course_name = ? WHERE course_id = ?";
    $stmtUpdateCourse = $conn->prepare($sqlUpdateCourse);
    $stmtUpdateCourse->bind_param("si", $updatedCourseName, $courseID);

    if ($stmtUpdateCourse->execute()) {
        http_response_code(200); // OK
        echo json_encode(["message" => "Course information updated successfully"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Error updating course information"]);
    }

    // Close the statement
    $stmtUpdateCourse->close();
}

function handle_assign_instructor($courseID, $instructorID)
{
    global $conn;

    // Check if the user making the request is authorized (e.g., admin)
    // Add your authorization logic here

    // Assign the instructor to the course
    $sqlAssignInstructor = "UPDATE course SET instructor_id = ? WHERE course_id = ?";
    $stmtAssignInstructor = $conn->prepare($sqlAssignInstructor);
    $stmtAssignInstructor->bind_param("ii", $instructorID, $courseID);

    if ($stmtAssignInstructor->execute()) {
        http_response_code(200); // OK
        echo json_encode(["message" => "Instructor assigned to the course successfully"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Error assigning instructor to the course"]);
    }

    // Close the statement
    $stmtAssignInstructor->close();
}


?>