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
            } elseif (isset($_GET['user'])) {
                handle_add_user();
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Invalid request"]);
            }
            break;
        case 'GET':
            authenticate_user();

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
            }
            break;
        case 'PUT':
            authenticate_user();

            if (isset($_GET['user']) && isset($_GET['userID'])) {
                handle_update_user($_GET['userId']);
            } elseif (isset($_GET['password'])) {
                handle_update_password();
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Invalid request"]);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Invalid request method"]);
    }

    function handle_login() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data['username'];
    $password = $data['password'];

    // Fetch the user from the database based on the username
    $sql = "SELECT User.UserID, User.RoleID, User.Password, Role.RoleName FROM User 
            JOIN Role ON User.RoleID = Role.RoleID 
            WHERE User.Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            // Verify the password using MD5
            if (md5($password) === $row['Password']) {
                // Password is correct
                $_SESSION['user_id'] = $row['UserID'];
                $_SESSION['role'] = $row['RoleID'];

                $token = session_id();
                $response = [
                    "token" => $token,
                    "role" => $row['RoleName'],
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

    function authenticate_user() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401); // Unauthorized
            echo json_encode(["error" => "Unauthorized"]);
            exit();
        }
    }

    function handle_add_user() {
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
        $fullName = mysqli_real_escape_string($conn, $data['full_name']);
        $birthdate = mysqli_real_escape_string($conn, $data['birthdate']);
        $address = mysqli_real_escape_string($conn, $data['address']);
        $sex = mysqli_real_escape_string($conn, $data['sex']);
        $username = mysqli_real_escape_string($conn, $data['username']);
        $roleName = mysqli_real_escape_string($conn, $data['role']);

        // Check if the user with the same name and birthdate already exists
        $sqlCheckUser = "SELECT UserID FROM user WHERE FullName = ? AND Birthdate = ?";
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
        $sqlRole = "SELECT RoleID FROM role WHERE RoleName = ?";
        $stmtRole = $conn->prepare($sqlRole);
        $stmtRole->bind_param("s", $roleName);
        $stmtRole->execute();
        $resultRole = $stmtRole->get_result();

        if ($resultRole && $resultRole->num_rows == 1) {
            // Role found, get the RoleID
            $rowRole = $resultRole->fetch_assoc();
            $roleId = $rowRole['RoleID'];

            // Insert the user into the "user" table
            $sqlInsertUser = "INSERT INTO user (FullName, Birthdate, Address, Sex, Username, Password, RoleID)
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

    function handle_enrollment() {
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
        $checkEnrollmentSql = "SELECT * FROM Enrollment WHERE StudentID = $studentId AND CourseID = $courseId";
        $checkEnrollmentResult = $conn->query($checkEnrollmentSql);

        if ($checkEnrollmentResult->num_rows > 0) {
            http_response_code(400);
            echo json_encode(["error" => "Student is already enrolled in the course"]);
            exit();
        }

        // Enroll the student in the course
        $enrollmentSql = "INSERT INTO Enrollment (StudentID, CourseID, DateEnrolled)
                          VALUES ($studentId, $courseId, NOW())";

        if ($conn->query($enrollmentSql)) {
            http_response_code(200);
            echo json_encode(["message" => "Enrollment successful"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error enrolling student in the course"]);
        }
    }




function handle_student_dashboard() {
        global $conn;

        // Fetch student details
        $userId = $_SESSION['user_id'];
        $sql = "SELECT * FROM users WHERE id = $userId";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $student = $result->fetch_assoc();

            // Fetch enrolled courses and grades
            $sqlCourses = "SELECT Course.CourseName, Enrollment.DateEnrolled, Performance.Grade
                           FROM Enrollment
                           JOIN Course ON Enrollment.CourseID = Course.CourseID
                           JOIN Performance ON Enrollment.EnrollmentID = Performance.EnrollmentID
                           WHERE Enrollment.StudentID = $userId";

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


function handle_teacher_dashboard() {
    global $conn;

    // Fetch teacher details
    $userId = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = $userId";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $teacher = $result->fetch_assoc();

        // Fetch courses taught by the teacher
        $sqlCourses = "SELECT Course.CourseName, Course.CourseID
                       FROM Course
                       WHERE Course.UserID = $userId";

        $resultCourses = $conn->query($sqlCourses);

        if ($resultCourses->num_rows > 0) {
            $courses = [];
            while ($row = $resultCourses->fetch_assoc()) {
                $courses[] = $row;
            }
            $teacher['courses_taught'] = $courses;

            // Fetch students enrolled in each course taught by the teacher
            foreach ($courses as &$course) {
                $courseId = $course['CourseID'];
                $sqlEnrollments = "SELECT User.FullName, Enrollment.DateEnrolled
                                   FROM Enrollment
                                   JOIN User ON Enrollment.StudentID = User.UserID
                                   WHERE Enrollment.CourseID = $courseId";

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


function    handle_admin_dashboard() {
    global $conn;

    // Fetch admin details
    $userId = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = $userId";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();

        // Fetch user management data
        $sqlUsers = "SELECT User.UserID, User.FullName, User.RoleID, Role.RoleName
                     FROM User
                     JOIN Role ON User.RoleID = Role.RoleID";

        $resultUsers = $conn->query($sqlUsers);

        if ($resultUsers->num_rows > 0) {
            $users = [];
            while ($row = $resultUsers->fetch_assoc()) {
                $users[] = $row;
            }
            $admin['user_management'] = $users;
        }

        // Fetch course management data
        $sqlCourses = "SELECT Course.CourseID, Course.CourseName, Course.UserID, User.FullName
                       FROM Course
                       JOIN User ON Course.UserID = User.UserID";

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


function handle_update_user($userId) {
    global $conn;

    // Check if the user making the request is an administrator
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403); // Forbidden
        echo json_encode(["error" => "Unauthorized access"]);
        exit();
    }

    // Get the updated user data from the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize the input data (customize based on your requirements)
    $updatedFullName = mysqli_real_escape_string($conn, $data['full_name']);
    $updatedBirthdate = mysqli_real_escape_string($conn, $data['birthdate']);
    $updatedAddress = mysqli_real_escape_string($conn, $data['address']);
    $updatedSex = mysqli_real_escape_string($conn, $data['sex']);

    // Update the user information in the database
    $sql = "UPDATE User
            SET FullName = '$updatedFullName',
                Birthdate = '$updatedBirthdate',
                Address = '$updatedAddress',
                Sex = '$updatedSex'
            WHERE UserID = $userId";

    if ($conn->query($sql)) {
        http_response_code(200); // OK
        echo json_encode(["message" => "User information updated successfully"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Error updating user information"]);
    }
}

    function handle_performance() {
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
        $checkEnrollmentSql = "SELECT * FROM Enrollment WHERE EnrollmentID = $enrollmentId";
        $checkEnrollmentResult = $conn->query($checkEnrollmentSql);

        if ($checkEnrollmentResult->num_rows !== 1) {
            http_response_code(400); // Bad Request
            echo json_encode(["error" => "Invalid enrollment ID"]);
            exit();
        }

        $updateGradeSql = "UPDATE Performance SET Grade = $grade WHERE EnrollmentID = $enrollmentId";

        if ($conn->query($updateGradeSql)) {
            http_response_code(200);
            echo json_encode(["message" => "Grade entry successful"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error entering grade"]);
        }
    }

    function handle_update_password() {
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
        $updatePasswordSql = "UPDATE User SET Password = ? WHERE UserID = ?";
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

?>
