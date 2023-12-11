<?php
session_start();
include_once("../../db_info.php");

// Check if the user is logged in
if (!isset($_SESSION['admID'])) {
    header("Location: ../../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input data
    // Note: You should add proper validation and sanitation here

    $course_id = $_POST['course_id'];
    $section_id = $_POST['section_id']; // Added section_id
    $capacity = $_POST['capacity'];

    // Validation 1: Los ID de sección deben constar de 3 caracteres.
    if (strlen($section_id) !== 3) {
        echo "Error: Los ID de sección deben constar de 3 caracteres.";
        exit();
    }

    // Validation 2: La capacidad debe ser un número natural.
    if (!ctype_digit($capacity) || $capacity <= 0) {
        echo "Error: La capacidad debe ser un número natural.";
        exit();
    }

    // Validation 3: No se puede insertar una sección a un curso que no exista en la tabla de cursos.
    $checkCourseQuery = "SELECT * FROM course WHERE course_id = '$course_id'";
    $checkCourseResult = $dbc->query($checkCourseQuery);

    if ($checkCourseResult->num_rows === 0) {
        echo "Error: No se puede insertar una sección a un curso que no exista en la tabla de cursos.";
        exit();
    }

    // Perform the insertion
    $insertQuery = "INSERT INTO section (course_id, section_id, capacity) VALUES ('$course_id', '$section_id', '$capacity')";
    $result = $dbc->query($insertQuery);

    if ($result) {
        header("Location: ../index.php");
        exit();
    } else {
        echo "Error: " . $dbc->error;
    }
}

// Fetch courses for dropdown in the form
$coursesQuery = "SELECT * FROM course";
$coursesResult = $dbc->query($coursesQuery);

// Close the database connection
$dbc->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insertar Sección</title>
    <link rel="stylesheet" href="../../php.css">
</head>
<body>
    <h1>Insertar Sección</h1>
    
    <form method="POST" action="">
        <label for="course_id">Course:</label>
        <select name="course_id" required>
            <?php while ($course = $coursesResult->fetch_assoc()) : ?>
                <option value="<?= $course['course_id']; ?>"><?= $course['title']; ?></option>
            <?php endwhile; ?>
        </select>

        <label for="section_id">Section ID:</label>
        <input type="text" name="section_id" maxlength="3" required> 

        <label for="capacity">Capacity:</label>
        <input type="text" name="capacity" required>

        <button type="submit">Insertar Sección</button>
    </form>
    
    <a href="../index.php">Ver Secciones</a>
</body>
</html>
