<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../php.css">
    <title>Sistema de Matricula UPRA</title>
</head>
<body>
    <div>
    <img src="../imagenes/logo_upra.png" alt="Logo UPRA">
        <h1>Sistema de Matrícula UPRA</h1>
        

        <!-- PHP Code for Student Login Form -->
        <?php
        session_start(); // Start the session

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!empty($_POST['student_id']) && !empty($_POST['password'])) {
                include_once("../db_info.php");
                $student_id = $_POST['student_id'];
                $password = $_POST['password'];

                $query = "SELECT * FROM student WHERE student_id = '$student_id'";
                $result = $dbc->query($query);

                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();

                    if (password_verify($password, $row['password'])) {
                        $_SESSION['student_id'] = $student_id; // Store the student_id in the session
                        $_SESSION['estID'] = $row['estID'];
                        $_SESSION['user_name'] = $row['user_name'];
                        $_SESSION['user_lastnameP'] = $row['user_lastnameP'];
                        $_SESSION['user_lastnameM'] = $row['user_lastnameM'];
                        header('Location: index.php');
                        exit();
                    } else {
                        echo "<p>Contraseña incorrecta</p>";
                    }
                } else {
                    echo '<h3>Su Numero de estudiante no concuerda con nuestros archivos!<br />Vuelva a intentarlo...<a href="login.php"> Login </a></h3>';
                }

                $dbc->close();
            } else {
                echo '<h3>Asegúrese de entrar su student_id y contraseña. <br /> Vuelva a intentarlo...<a href="login.php"> Login </a></h3>';
            }
        } else {
            // No llegó por un submit, presentar el formulario
            echo '<form id="student-login-form" action="login.php" method="post">
                <label for="student_id">Numero de estudiante:</label>
                <input type="text" name="student_id" size="50" maxlength="60" required />

                <label for="password">Contraseña:</label>
                <input type="password" name="password" required />

                <button type="submit">Entrar!</button>
            </form>';
        }
        ?>
    </div>
    
    <footer>
        CCOM4019 - Programación Web con PHP/MYSQL <br>
        Creado por: Eddy Figueroa & Zulymar García
    </footer>
</body>
</html>
