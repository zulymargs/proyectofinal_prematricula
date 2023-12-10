<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="php.css">
    <title>Sistema de Matricula UPRA</title>
</head>

<body>
    <div>
        <img src="imagenes/logo_upra.png" alt="Logo UPRA">
        <h1>Sistema de Matrícula UPRA</h1>

        <!-- PHP Code for Combined Login Form -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            include_once("db_info.php");

            $username = $_POST['username'];
            $password = $_POST['password'];

            // Check in the student table
            $queryStudent = "SELECT * FROM student WHERE student_id = '$username'";
            $resultStudent = $dbc->query($queryStudent);

            // Check in the admin table
            $queryAdmin = "SELECT * FROM admin WHERE admID = '$username'";
            $resultAdmin = $dbc->query($queryAdmin);

            if ($resultStudent->num_rows == 1) {
                $row = $resultStudent->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    // Student login successful
                    handleLogin($row, 'student');
                    header('Location: student/index.php');
                    exit();
                } else {
                    echo "<p>Contraseña incorrecta para estudiantes</p>";
                }
            } elseif ($resultAdmin->num_rows == 1) {
                $row = $resultAdmin->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    // Admin login successful
                    handleLogin($row, 'admin');
                    header('Location: admin/index.php');
                    exit();
                } else {
                    echo "<p>Contraseña incorrecta para administradores</p>";
                }
            } else {
                echo "<p>Usuario no encontrado</p>";
            }

            $dbc->close();
        } else {
            // No llegó por un submit, presentar el formulario
            echo '<form id="combined-login-form" action="index.php" method="post">
                <label for="username">Usuario (Número de estudiante o administrador):</label>
                <input type="text" name="username" size="50" maxlength="60" required />

                <label for="password">Contraseña:</label>
                <input type="password" name="password" required />

                <button type="submit">Entrar!</button>
            </form>';
        }

        function handleLogin($row, $role)
        {
            session_start();
            if ($role === 'student') {
                $_SESSION['student_id'] = $row['student_id'];
                $_SESSION['estID'] = $row['estID'];
                $_SESSION['user_name'] = $row['user_name'];
                $_SESSION['user_lastnameP'] = $row['user_lastnameP'];
                $_SESSION['user_lastnameM'] = $row['user_lastnameM'];
            } elseif ($role === 'admin') {
                $_SESSION['admID'] = $row['admID'];
                $_SESSION['admin_name'] = $row['admin_name'];
                $_SESSION['admin_lastnameP'] = $row['admin_lastnameP'];
                $_SESSION['admin_lastnameM'] = $row['admin_lastnameM'];
            }
            echo "<p>Contraseña correcta</p>";
        }
        ?>
    </div>

    <footer>
        CCOM4019 - Programación Web con PHP/MYSQL <br>
        Creado por: Eddy Figueroa & Zulymar García
    </footer>
</body>

</html>
