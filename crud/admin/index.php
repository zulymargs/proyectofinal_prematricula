<?php
session_start(); // Start the session
$titulo = "Pre-Matrícula UPRA";

// Check if the user is logged in
if (isset($_SESSION['admID'])) {
    echo "<h2>Bienvenido, {$_SESSION['admin_lastnameP']} {$_SESSION['admin_lastnameM']}, {$_SESSION['admin_name']}| <a href='../logout.php'>Logout</a></h2>";
} else {
    echo "<p>Session not active <a href='login.php'>Login</a></p>";
}

// Now you can use $_SESSION['student_id'] to get the logged-in student's ID
$student_id = $_SESSION['admID'];
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
                <td width="140" align="right">Buscar curso:</td>
                <td><input type="text" name="search_query" size="50" maxlength="255" /></td>
            </tr>
            <!-- ... existing form fields ... -->
            <tr>
                <td></td>
                <td><input type="submit" class="formbutton" name="submit" value="Buscar Curso" /></td>
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
            // ... existing code ...

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
            echo "<tr><th>Course ID</th><th>Title</th><th>Availability</th><th>Enroll Waitinglist</th></tr>";

            // Loop through the results and display them in a table
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['course_id'] . "-" . $row['section_id'] . "</td>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>" . $row['capacity'] . "</td>";
                $courseSectionId = $row['course_id'] . "-" . $row['section_id'];
                // Display the "Enroll" button only if the student is not already enrolled
                if (!$isEnrolled) {
                    echo "<td><button class='enroll-button' onclick='enrollCourse(\"$courseSectionId\")'>OK</button></td>";
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
        // Process enrollment when the button is clicked
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll_course'])) {
            $courseSectionId = $_POST['enroll_course'];
            list($courseId, $sectionId) = explode("-", $courseSectionId);

            // Step 1: Update Enrollment Status
            $updateQuery = "UPDATE enrollment SET status = 1 WHERE course_id = '$courseId' AND section_id = '$sectionId'";
            $dbc->query($updateQuery);

            // Step 2: Priority Logic
            $priorityQuery = "SELECT e.student_id, s.year_of_study, e.timestamp
                            FROM enrollment e
                            JOIN student s ON e.student_id = s.student_id
                            WHERE e.course_id = '$courseId' AND e.section_id = '$sectionId'
                            ORDER BY s.year_of_study DESC, e.timestamp ASC";
            $priorityResult = $dbc->query($priorityQuery);

            // Step 3: Capacity Constraint
            $sectionQuery = "SELECT capacity FROM section WHERE course_id = '$courseId' AND section_id = '$sectionId'";
            $sectionResult = $dbc->query($sectionQuery);
            $sectionRow = $sectionResult->fetch_assoc();
            $capacity = $sectionRow['capacity'];

            // Update the enrollment status based on priority and capacity
            $count = 0;
            while ($row = $priorityResult->fetch_assoc()) {
                $studentId = $row['student_id'];
                $updateQuery = "UPDATE enrollment SET status = 1 WHERE course_id = '$courseId' AND section_id = '$sectionId' AND student_id = '$studentId'";
                $dbc->query($updateQuery);
                $count++;

                // Check if the maximum capacity is reached
                if ($count >= $capacity) {
                    break;
                }
            }

            // Redirect or display a success message
            header("Location: index.php");
            exit();
        }


        ?>
    </div>
</body>
</html>
