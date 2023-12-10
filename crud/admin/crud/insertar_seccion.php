<?php
session_start(); // Start the session
$titulo = "Agregar Sección";

// Check if the user is logged in
if (!isset($_SESSION['admID'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit();
}

include_once("../../db_info.php");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form data (add section logic)
    // ...

    // After processing, you may redirect to the sections page or display a success message
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
        <h1>Agregar Sección</h1>

        <!-- Form to add a section -->
        <form action="add_section.php" method="post">
            <!-- Section form fields go here -->
            <label for="section_title">Título de la Sección:</label>
            <input type="text" id="section_title" name="section_title" required />

            <label for="capacity">Capacidad:</label>
            <input type="text" id="capacity" name="capacity" required />

            <!-- Add more fields as needed -->

            <input type="submit" class="formbutton" name="submit" value="Agregar Sección" />
        </form>
        
        <button onclick="location.href='index.php';">Volver a la Página Principal</button>
    </div>
</body>
</html>
