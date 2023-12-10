<?php
session_start(); // Start the session
$titulo = "Pre-Matrícula UPRA";

// Check if the user is logged in
if (isset($_SESSION['admID'])) {
    echo "<h2>Bienvenido, {$_SESSION['admin_lastnameP']} {$_SESSION['admin_lastnameM']}, {$_SESSION['admin_name']}| <a href='../logout.php'>Logout</a></h2>";
} else {
    echo "<p>Session not active <a href='login.php'>Login</a></p>";
}

// Now you can use $_SESSION['admin_id'] to get the logged-in admin's ID
$admin_id = $_SESSION['admID'];
function checkAvailability($course_id, $section_id) {
    global $dbc;

    // Count the number of students enrolled in the specified course and section with status set to 1 (Matriculado)
    $query = "SELECT COUNT(*) as enrolled_count FROM enrollment WHERE course_id = '$course_id' AND section_id = '$section_id' AND status = 1";
    $result = $dbc->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $enrolledCount = $row['enrolled_count'];

        // Get the capacity of the section from the section table
        $sectionCapacityQuery = "SELECT capacity FROM section WHERE course_id = '$course_id' AND section_id = '$section_id'";
        $sectionCapacityResult = $dbc->query($sectionCapacityQuery);

        if ($sectionCapacityResult && $sectionCapacityResult->num_rows > 0) {
            $sectionCapacityRow = $sectionCapacityResult->fetch_assoc();
            $sectionCapacity = $sectionCapacityRow['capacity'];

            // Compare the number of enrolled students with the section capacity
            return $enrolledCount < $sectionCapacity;
        }
    }

    // If any query fails or no data is found, consider the section as unavailable
    return false;
}

function checkStatus($course_id, $section_id, $expectedStatus) {
    global $dbc;

    $query = "SELECT status FROM enrollment WHERE course_id = '$course_id' AND section_id = '$section_id' LIMIT 1";
    $result = $dbc->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['status'] == $expectedStatus;
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

                
                // Check if the section is still available for enrollment
                $isAvailable = checkAvailability($row['course_id'], $row['section_id']);
                

                  // Display appropriate message based on availability
                    if ($isAvailable) {
                        echo "<td>
                                <form action='index.php' method='post'>
                                    <input type='hidden' name='enroll_course_id' value='{$row['course_id']}' />
                                    <input type='hidden' name='enroll_section_id' value='{$row['section_id']}' />
                                    <input type='submit' class='enroll-button' value='Enroll' />
                                </form>
                            </td>";
                    } else {
                        echo "<td>Sección Llena</td>";
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

                // Loop until the capacity is reached or no more availability
                while ($isAvailable = checkAvailability($enroll_course_id, $enroll_section_id)) {
                    // Update the status for the next enrolled student in the course and section
                    $update_status_query = "UPDATE enrollment NATURAL JOIN student
                                            SET status = 1
                                            WHERE course_id = '$enroll_course_id'
                                            AND section_id = '$enroll_section_id'
                                            AND status = 0
                                            ORDER BY year_of_study DESC, timestamp ASC
                                            LIMIT 1";

                    $result = $dbc->query($update_status_query);

                    if ($result) {
                        if ($result->num_rows > 0) {
                            echo "<p>Status updated successfully!</p>";
                        } else {
                            // No more rows to update, exit the loop
                            break;
                        }
                    } else {
                        echo "<p>Error updating status: " . $dbc->error . "</p>";
                        break; // Break the loop if an error occurs
                    }
                }

                // Update remaining status: 0 enrollments to status: 2
                $update_remaining_query = "UPDATE enrollment
                                        SET status = 2
                                        WHERE course_id = '$enroll_course_id'
                                        AND section_id = '$enroll_section_id'
                                        AND status = 0";
                $dbc->query($update_remaining_query);

            }
        }else {
        die("Error executing the query: " . $dbc->error);
    }
        ?>
          <button><a href="crud/index.php" class="admin-button">Administrar Cursos y Secciones</a></button>
    </div>
    <footer>
        CCOM4019 - Programación Web con PHP/MYSQL <br>
        Creado por: Eddy Figueroa & Zulymar García
    </footer>
</body>
</html>