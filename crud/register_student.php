<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="php.css">
    <title>Registro de Estudiantes UPRA</title>
</head>

<body>
    <div>
        <img src="imagenes/logo_upra.png" alt="Logo UPRA">
        <h1>Registro de Estudiantes UPRA</h1>

        <?php
        function validateStudentID($dbc, $studentID)
        {
            // Check if the student ID is 9 characters long
            if (strlen($studentID) !== 9) {
                return false;
            }

            // Check if the student ID already exists in the database
            $query = "SELECT * FROM student WHERE student_id = '$studentID'";
            $result = $dbc->query($query);

            return ($result->num_rows == 0); // Returns true if the student ID is unique
        }

        function validateYearOfStudy($yearOfStudy)
        {
            return ($yearOfStudy >= 1 && $yearOfStudy <= 5);
        }

        function validatePassword($password)
        {
            return (strlen($password) >= 8);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            include_once("db_info.php");

            $studentID = $_POST['student_id'];
            $password = $_POST['password'];
            $user_name = $_POST['user_name'];
            $user_lastnameP = $_POST['user_lastnameP'];
            $user_lastnameM = $_POST['user_lastnameM'];
            $yearOfStudy = $_POST['year_of_study'];

            // Validate student ID
            if (!validateStudentID($dbc, $studentID)) {
                echo "<p>El ID del estudiante debe constar de 9 dígitos y ser único. | <a href='register_student.php'>Volver a intentar</a></p>";
                exit();
            }

            // Validate year of study
            if (!validateYearOfStudy($yearOfStudy)) {
                echo "<p>El año de estudio debe estar entre 1 y 5 para estudiantes. | <a href='register_student.php'>Volver a intentar</a></p>";
                exit();
            }

            // Validate password
            if (!validatePassword($password)) {
                echo "<p>La contraseña debe tener al menos 8 caracteres. | <a href='register_student.php'>Volver a intentar</a></p>";
                exit();
            }

            // Insert into the database if all validations pass
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO student (student_id, password, user_name, user_lastnameP, user_lastnameM, year_of_study) 
                      VALUES ('$studentID', '$hashedPassword', '$user_name', '$user_lastnameP', '$user_lastnameM', '$yearOfStudy')";

            if ($dbc->query($query)) {
                echo "<p>Registro exitoso. Ahora puedes <a href='index.php'>iniciar sesión</a> como estudiante.</p>";
                exit(); // Stop further execution to prevent displaying the form again
            } else {
                echo "<p>Error en el registro. Por favor, inténtalo de nuevo.</p>";
            }

            $dbc->close();
        } else {
            // No llegó por un submit, presentar el formulario
            echo '<form id="student-registration-form" action="register_student.php" method="post">
                <label for="student_id">Número de estudiante:</label>
                <input type="text" name="student_id" size="50" maxlength="60" required />

                <label for="password">Contraseña:</label>
                <input type="password" name="password" required />

                <label for="user_name">Nombre:</label>
                <input type="text" name="user_name" required />

                <label for="user_lastnameP">Apellido Paterno:</label>
                <input type="text" name="user_lastnameP" required />

                <label for="user_lastnameM">Apellido Materno:</label>
                <input type="text" name="user_lastnameM" required />

                <label for="year_of_study">Año de estudio:</label>
                <input type="number" name="year_of_study" min="1" max="5" required />

                <button type="submit">Registrarse</button>
            </form>';
        }
        ?>

        <p>¿Ya tienes una cuenta? <a href="index.php">Inicia sesión aquí</a>.</p>
    </div>

    <footer>
        CCOM4019 - Programación Web con PHP/MYSQL <br>
        Creado por: Eddy Figueroa & Zulymar García
    </footer>
</body>

</html>
