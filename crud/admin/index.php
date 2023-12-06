<?php
session_start();
$titulo = "Pre-Matrícula UPRA";

// Include the database connection code
include_once("../db_info.php");

if ($dbc->connect_error) {
    die("Connection failed: " . $dbc->connect_error);
}
// Check if the user is logged in
if (isset($_SESSION['admID'])) {
    echo "<h2>Bienvenido, {$_SESSION['admin_lastnameP']} {$_SESSION['admin_lastnameM']}, {$_SESSION['admin_name']}| <a href='../logout.php'>Logout</a></h2>";
} else {
    echo "<p>Session not active <a href='../logout.php'>Login</a></p>";
}

$studentID = $_SESSION['admID'];

// Function to validate a course ID
function isValidCourseID($courseID) {
    return preg_match('/^[A-Z]{4}\d{4}$/', $courseID) === 1;
}

// Function to validate a section ID
function isValidSectionID($sectionID) {
    return strlen($sectionID) === 3;
}

// Function to validate a natural number
function isValidNaturalNumber($number) {
    return ctype_digit($number) && $number > 0;
}

// Function to create a new course with validation
function createCourse($courseName, $courseID) {
    global $dbc;

    if (!isValidCourseID($courseID)) {
        return false; // Invalid course ID
    }

    $sql = "INSERT INTO courses (course_name, course_id) VALUES ('$courseName', '$courseID')";
    return $dbc->query($sql);
}

// Function to create a new section with validation
function createSection($sectionName, $courseID, $capacity, $credits) {
    global $dbc;

    if (!isValidCourseID($courseID)) {
        return false; // Invalid course ID
    }

    if (!isValidSectionID($sectionName)) {
        return false; // Invalid section ID
    }

    if (!isValidNaturalNumber($capacity)) {
        return false; // Invalid capacity
    }

    if (!isValidNaturalNumber($credits)) {
        return false; // Invalid credits
    }

    // Check if the course exists
    $courseExists = $dbc->query("SELECT 1 FROM courses WHERE course_id = '$courseID'")->fetch_row();

    if (!$courseExists) {
        return false; // Course does not exist
    }

    $sql = "INSERT INTO sections (section_name, course_id, capacity, credits) VALUES ('$sectionName', '$courseID', '$capacity', '$credits')";
    return $dbc->query($sql);
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["createCourse"])) {
        $courseName = $_POST["courseName"];
        $courseID = $_POST["courseID"];

        if (createCourse($courseName, $courseID)) {
            echo "Course created successfully!";
        } else {
            echo "Error creating course. Please check your input.";
        }
    } elseif (isset($_POST["createSection"])) {
        $sectionName = $_POST["sectionName"];
        $courseID = $_POST["courseID"];
        $capacity = $_POST["capacity"];
        $credits = $_POST["credits"];

        if (createSection($sectionName, $courseID, $capacity, $credits)) {
            echo "Section created successfully!";
        } else {
            echo "Error creating section. Please check your input.";
        }
    } elseif (isset($_POST["enrollStudent"])) {
        $sectionID = $_POST["sectionID"];

        // Add your enrollment logic here
        enrollStudent($studentID, $sectionID);
    }
}

// Retrieve search results
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search_query"])) {
    $search_query = $_POST["search_query"];
    $query = "SELECT * FROM courses NATURAL JOIN sections
              WHERE course_id LIKE '%$search_query%'
              ORDER BY course_id ASC, section_id ASC";

    $result = $dbc->query($query);

    if (!$result) {
        die("Error executing the query: " . $dbc->error);
    }
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
                <td width="140" align="right">Buscar curso para:</td>
                <td><input type="text" name="search_query" size="50" maxlength="255" /></td>
            </tr>
            <!-- ... existing form fields ... -->
            <tr>
                <td></td>
                <td><input type="submit" class="formbutton" name="submit" value="Buscar Curso" /></td>
            </tr>
        </form>

        <?php
        // Display the search results
        if ($result->num_rows > 0) {
            echo "<h3>Search Results:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>Course ID</th><th>Title</th><th>Availability</th><th>Enroll Waitinglist</th></tr>";

            // Loop through the results and display them in a table
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['course_id'] . "-" . $row['section_id'] . "</td>";
                echo "<td>" . $row['course_name'] . "</td>";
                echo "<td>" . $row['capacity'] . "</td>";
                $courseSectionId = $row['course_id'] . "-" . $row['section_id'];

                // Display the "Enroll" button only if the student is not already enrolled
                if (!$isEnrolled) {
                    echo "<td><form method='post' action='index.php'>
                            <input type='hidden' name='enrollStudent' value='enroll'>
                            <input type='hidden' name='sectionID' value='$courseSectionId'>
                            <input type='submit' class='enroll-button' value='OK'>
                          </form></td>";
                } else {
                    echo "<td>Sección Llena</td>";
                }

                echo "</tr>";
                // ... display other relevant information ...
            }

            echo "</table>";
        } else {
            echo "<p>No results found.</p>";
        }
        ?>
    </div>
</body>
</html>
