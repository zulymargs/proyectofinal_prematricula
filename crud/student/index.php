<?php
session_start(); // Start the session
$titulo = "Pre-Matrícula UPRA";

// Check if the user is logged in
if (isset($_SESSION['student_id'])) {
    echo "<h2>Welcome, {$_SESSION['user_lastnameP']} {$_SESSION['user_lastnameM']}, {$_SESSION['user_name']}| <a href='../logout.php'>Logout</a></h2>";
} else {
    echo "<p>Session not active <a href='login.php'>Login</a></p>";
}

// Now you can use $_SESSION['student_id'] to get the logged-in student's ID
$student_id = $_SESSION['student_id'];
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
            echo "<tr><th>Course ID</th><th>Title</th><th>Availability</th><th>Enroll</th></tr>";

            // Loop through the results and display them in a table
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['course_id'] . "-" . $row['section_id'] . "</td>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>" . $row['capacity'] . "</td>";
                // Display the "Enroll" button only if the student is not already enrolled
                if (!$isEnrolled) {
                    echo "<td><button class='enroll-button' onclick='enrollCourse(\"$courseSectionId\")'>Enroll</button></td>";
                } else {
                    echo "<td>Already Enrolled</td>";
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
