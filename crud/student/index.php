<?php
session_start(); // Start the session
$titulo = "Pre-Matrícula UPRA";

// Check if the user is logged in
if (isset($_SESSION['student_id'])) {
    echo "<h2>Welcome, {$_SESSION['user_lastnameP']} {$_SESSION['user_lastnameM']}, {$_SESSION['user_name']}| <a href='../logout.php'>Logout</a></h2>";
} else {
    echo "<p>Session not active <a href='../index.php'>Login</a></p>";
}

// Now you can use $_SESSION['student_id'] to get the logged-in student's ID
$student_id = $_SESSION['student_id'];

function checkEnrollment($student_id, $course_id) {
    global $dbc;
    $query = "SELECT * FROM enrollment WHERE student_id = '$student_id' AND course_id = '$course_id'";
    $result = $dbc->query($query);
    return $result->num_rows > 0;
}
// Function to get a label based on the status
function getStatusLabel($status) {
    switch ($status) {
        case 0:
            return "Pendiente";
        case 1:
            return "Matriculado";
        case 2:
            return "Cancelado por cupo";
        default:
            return "Unknown";
    }
}
function checkStatus($course_id, $section_id, $allowedStatusArray) {
    global $dbc;

    $query = "SELECT status FROM enrollment WHERE course_id = '$course_id' AND section_id = '$section_id' LIMIT 1";
    $result = $dbc->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return in_array($row['status'], $allowedStatusArray);
    }

    return false;
}


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="../php.css">
</head>

<body>
    <div>
        <h1>Pre-Matrícula UPRA</h1>

        <!-- Existing form fields -->
        <form action="index.php" method="post">
            <!-- ... existing form fields ... -->
            <tr>
                <td width="140" align="right" class="sub">Buscar curso:</td>
                <td><input type="text" name="search_query" size="50" maxlength="255" /></td>
            </tr>
            <!-- ... existing form fields ... -->
            <tr>
                <td></td>
                <td><input type="submit" class="sub" name="submit" value="Buscar Curso" /></td>
            </tr>
        </form>

        <?php
        /* código */
        include_once("../db_info.php");

        // Check if the connection was successful
        if ($dbc->connect_error) {
            die("Connection failed: " . $dbc->connect_error);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['search_query'])) {
                $search_query = $_POST['search_query'];
                // Modify your SQL query to include the search condition
                $query = "SELECT * FROM course NATURAL JOIN section
                          WHERE course_id LIKE '%$search_query%'
                          ORDER BY course_id ASC, section_id ASC";

                // ... rest of the code ...
                $result = $dbc->query($query);
                if ($result) {
                    // ... existing code ...
                } else {
                    die("Error executing the query: " . $dbc->error);
                }
            }
        }

        if ($result->num_rows > 0) {
            echo "<h3>Search Results:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>Course ID</th><th>Title</th><th>Availability</th><th>Enroll?</th></tr>";

            // Loop through the results and display them in a table
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['course_id'] . "-" . $row['section_id'] . "</td>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>" . $row['capacity'] . "</td>";

                
                // Check if the student is already enrolled in the course
                $isEnrolled = checkEnrollment($student_id, $row['course_id']);
                

                // Display the "Enroll" button only if the student is not already enrolled
                if (!$isEnrolled) {
                    echo "<td>
                            <form action='index.php' method='post'>
                                <input type='hidden' name='enroll_course_id' value='{$row['course_id']}' />
                                <input type='hidden' name='enroll_section_id' value='{$row['section_id']}' />
                                <input type='submit' class='enroll-button' value='Enroll' />
                            </form>
                        </td>";
                } else {
                    echo "<td>Already Enrolled</td>";
                }

                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No results found.</p>";
        }
        // Check if the form was submitted for enrollment
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['enroll_course_id']) && isset($_POST['enroll_section_id'])) {
                $enroll_course_id = $_POST['enroll_course_id'];
                $enroll_section_id = $_POST['enroll_section_id'];

                // Check if the student is not already enrolled in the course
                $isEnrolled = checkEnrollment($student_id, $enroll_course_id, $enroll_section_id);

                if (!$isEnrolled) {
                    // Perform the enrollment
                    $enroll_query = "INSERT INTO enrollment (student_id, course_id, section_id, status) VALUES ('$student_id', '$enroll_course_id', '$enroll_section_id', 0)";

                    if ($dbc->query($enroll_query)) {
                        echo "<p>Enrolled successfully!</p>";
                    } else {
                        echo "<p>Error enrolling: " . $dbc->error . "</p>";
                    }
                } else {
                    echo "<p>You are already enrolled in this course.</p>";
                }
            }
        }

        // Display enrolled courses for the current user
        $enrolled_courses_query = "SELECT *
        FROM enrollment
        NATURAL JOIN course
        NATURAL JOIN section
        WHERE student_id = '$student_id'";


        $enrolled_courses_result = $dbc->query($enrolled_courses_query);

        if ($enrolled_courses_result->num_rows > 0) {
        echo "<h3>Enrolled Courses:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Course ID</th><th>Title</th><th>Status</th><th>Disenroll?</th></tr>";

        while ($enrolled_row = $enrolled_courses_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $enrolled_row['course_id'] . "-" . $enrolled_row['section_id'] . "</td>";
        echo "<td>" . $enrolled_row['title'] . "</td>";
        echo "<td>" . getStatusLabel($enrolled_row['status']) . "</td>";
        // Check if the status is pending or canceled for disenroll button
        if ($enrolled_row['status'] == 0 || $enrolled_row['status'] == 2) {
            // Display the "Disenroll" button
            echo "<td>
                    <form action='index.php' method='post'>
                        <input type='hidden' name='disenroll_course_id' value='{$enrolled_row['course_id']}' />
                        <input type='hidden' name='disenroll_section_id' value='{$enrolled_row['section_id']}' />
                        <input type='submit' class='disenroll-button' value='Disenroll' />
                    </form>
                </td>";
        } else {
            // Display a message or an empty cell if not pending
            echo "<td>No puede hacer cambios</td>";
        }
        echo "</tr>";
        }
        echo "</table><br>";
        } else {
        echo "<p>You are not enrolled in any courses.</p>";
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['disenroll_course_id']) && isset($_POST['disenroll_section_id'])) {
            $disenroll_course_id = $_POST['disenroll_course_id'];
            $disenroll_section_id = $_POST['disenroll_section_id'];

            $allowedStatusArray = array(0, 2);
        
            // Check if the status is pending for disenrollment
            $isPending = checkStatus($disenroll_course_id, $disenroll_section_id, $allowedStatusArray);
        
            if ($isPending) {
                // Perform the disenrollment
                $disenroll_query = "DELETE FROM enrollment WHERE student_id = '$student_id' AND course_id = '$disenroll_course_id' AND section_id = '$disenroll_section_id'";
        
                if ($dbc->query($disenroll_query)) {
                    echo "<p>Disenrolled successfully!</p>";
                    header("Location: index.php");
                    exit();
                } else {
                    echo "<p>Error disenrolling: " . $dbc->error . "</p>";
                }
            } else {
                echo "<p>Cannot disenroll. The course is not pending.</p>";
            }
        }
        

        ?>
    </div>
    <footer>
        CCOM4019 - Programación Web con PHP/MYSQL <br>
        Creado por: Eddy Figueroa & Zulymar García
    </footer>
</body>
</html>