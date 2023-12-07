<?php
session_start();
$titulo = "Pre-Matrícula UPRA";


include_once("../db_info.php");

if ($dbc->connect_error) {
    die("Connection failed: " . $dbc->connect_error);
}


if (isset($_SESSION['admID'])) {
    echo "<h2>Bienvenido, {$_SESSION['admin_lastnameP']} {$_SESSION['admin_lastnameM']}, {$_SESSION['admin_name']}| <a href='../logout.php'>Logout</a></h2>";
} else {
    echo "<p>Session not active <a href='../logout.php'>Login</a></p>";
}

$adminID = $_SESSION['admID'];


function isValidCourseID($courseID) {
    return preg_match('/^[A-Z]{4}\d{4}$/', $courseID) === 1;
}


function isValidSectionID($sectionID) {
    return strlen($sectionID) === 3;
}


function isValidNaturalNumber($number) {
    return ctype_digit($number) && $number > 0;
}


function createCourse($courseName, $courseID) {
    global $dbc;

    if (!isValidCourseID($courseID)) {
        return false; 
    }

    $sql = "INSERT INTO courses (title, course_id) VALUES ('$courseName', '$courseID')";
    return $dbc->query($sql);
}


function createSection($sectionName, $courseID, $capacity, $credits) {
    global $dbc;

    if (!isValidCourseID($courseID)) {
        return false; 
    }

    if (!isValidSectionID($sectionName)) {
        return false; 
    }

    if (!isValidNaturalNumber($capacity)) {
        return false; 
    }
    if (!isValidNaturalNumber($credits)) {
        return false; 
    }

    $courseExists = $dbc->query("SELECT 1 FROM courses WHERE course_id = '$courseID'")->fetch_row();

    if (!$courseExists) {
        return false; 
    }

    $sql = "INSERT INTO sections (section_name, course_id, capacity, credits) VALUES ('$sectionName', '$courseID', '$capacity', '$credits')";
    return $dbc->query($sql);
}

function enrollStudent($studentID, $sectionID) {
    global $dbc;

    $enrollmentResult = $dbc->query("SELECT 1 FROM enrollment WHERE student_id = '$studentID' AND section_id = '$sectionID'");
    $isEnrolled = $enrollmentResult && $enrollmentResult->num_rows > 0;

    if (!$isEnrolled) {
        $insertResult = $dbc->query("INSERT INTO enrollment (student_id, section_id, status) VALUES ('$studentID', '$sectionID', 1)");

        if ($insertResult) {
            echo "Student enrolled successfully!";
        } else {
            echo "Error enrolling student. Please try again.";
        }
    } else {
        echo "Student is already enrolled in this section.";
    }
}

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
        $enrollmentID = $_POST["enrollmentID"];

        $enrollmentResult = $dbc->query("SELECT student_id FROM enrollment WHERE enrollment_id = '$enrollmentID'");
        if ($enrollmentResult && $enrollmentResult->num_rows > 0) {
            $enrollmentData = $enrollmentResult->fetch_assoc();
            $studentID = $enrollmentData['student_id'];

            enrollStudent($studentID, $sectionID);
        } else {
            echo "Error retrieving enrollment information.";
        }
    }
}

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

        <form action="index.php" method="post">
            <tr>
                <td width="140" align="right">Buscar curso para:</td>
                <td><input type="text" name="search_query" size="50" maxlength="255" /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" class="formbutton" name="submit" value="Buscar Curso" /></td>
            </tr>
        </form>

        <?php

    if (isset($result)) {
            if ($result === null) {
                echo "<p>Error executing the query: " . $dbc->error . "</p>";
            } elseif ($result->num_rows > 0) {
                echo "<h3>Search Results:</h3>";
                echo "<table border='1'>";
                echo "<tr><th>Course ID</th><th>Title</th><th>Availability</th><th>Enroll Waitinglist</th></tr>";
        
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['course_id'] . "-" . $row['section_id'] . "</td>";
                    echo "<td>" . $row['title'] . "</td>";
                    echo "<td>" . $row['capacity'] . "</td>";
                    $courseName = isset($row['title']) ? $row['title'] : 'N/A';
    echo "<td>" . $courseName . "</td>";

    echo "<td>" . $row['capacity'] . "</td>";
    $courseSectionId = $row['course_id'] . "-" . $row['section_id'];


                    if (isset($isEnrolled) && !$isEnrolled) {
                        echo "<td><form method='post' action='index.php'>
                                <input type='hidden' name='enrollStudent' value='enroll'>
                                <input type='hidden' name='sectionID' value='$courseSectionId'>
                                <input type='hidden' name='enrollmentID' value='{$row['enrollment_id']}'>
                                <input type='submit' class='enroll-button' value='OK'>
                              </form></td>";
                    } else {
                        echo "<td>Sección Llena</td>";
                    }
                
                    echo "</tr>"; 
                }

            echo "</table>";
        } else {
            echo "<p>No results found.</p>";
        }
    } else {
        echo "<p>No results found.</p>";
    }
        ?>
    </div>
</body>
</html>
