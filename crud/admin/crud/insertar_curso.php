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

    $new_course_id = $_POST['new_course_id'];
    $new_course_title = $_POST['new_course_title'];
    $new_course_credits = $_POST['new_course_credits'];

    // Validation 1: Los ID de los cursos deben constar de 4 letras mayúsculas seguidas de 4 dígitos.
    if (!preg_match('/^[A-Z]{4}\d{4}$/', $new_course_id)) {
        echo "Error: Los ID de los cursos deben constar de 4 letras mayúsculas seguidas de 4 dígitos.";
        exit();
    }

    // Validation 2: Los créditos deben ser un número natural.
    if (!ctype_digit($new_course_credits) || $new_course_credits <= 0) {
        echo "Error: Los créditos deben ser un número natural.";
        exit();
    }

    // Perform the insertion
    $insertCourseQuery = "INSERT INTO course (course_id, title, credits) VALUES ('$new_course_id', '$new_course_title', '$new_course_credits')";
    $result = $dbc->query($insertCourseQuery);

    if ($result) {
        header("Location: ../index.php");
        exit();
    } else {
        echo "Error: " . $dbc->error;
    }
}

// Close the database connection
$dbc->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insertar Curso</title>
    <link rel="stylesheet" href="../../php.css">
</head>
<body>
    <h1>Insertar Curso</h1>
    
    <form method="POST" action="">
        <label for="new_course_id">Course ID:</label>
        <input type="text" name="new_course_id" pattern="[A-Z]{4}\d{4}" title="Debe tener 4 letras mayúsculas seguidas de 4 dígitos" required>

        <label for="new_course_title">Course Title:</label>
        <input type="text" name="new_course_title" required>

        <label for="new_course_credits">Credits:</label>
        <input type="text" name="new_course_credits" required>

        <button type="submit">Insertar Curso</button>
    </form>
    
    <a href="../index.php">Ver Cursos</a>
</body>
</html>
