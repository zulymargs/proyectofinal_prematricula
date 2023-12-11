<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['admID'])) {
    echo "<h2>Bienvenido, {$_SESSION['admin_lastnameP']} {$_SESSION['admin_lastnameM']}, {$_SESSION['admin_name']}| <a href='../logout.php'>Logout</a></h2>";
} else {
    echo "<p>Session not active <a href='../index.php'>Login</a></p>";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Editar Curso</title>
    <link rel="stylesheet" href="../../php.css">
</head>

<body>
    <div id="contenido">
        <h1>Curso de Honor UPRA</h1>
        <h2>Editar Curso</h2>

        <?php
        if (isset($_SESSION['admID'])) {
            include_once("../../db_info.php");

            if (isset($_GET['course_id'])) {
                $courseId = $_GET['course_id'];

                $query = "SELECT * FROM course WHERE course_id = '$courseId'";

                try {
                    if ($result = $dbc->query($query)) {
                        if ($result->num_rows == 1) {
                            $row = $result->fetch_assoc();

                            echo '<div>
                                <form action="editar_curso.php" method="post">
                                    <table border="0">
                                        <tr>
                                            <td>Titulo: </td>
                                            <td><input type="text" name="title" value="' . $row['title'] . '" required></td>
                                        </tr>
                                        <tr>
                                            <td>Creditos: </td>
                                            <td><input type="number" name="credits" value="' . $row['credits'] . '" required></td>
                                        </tr>
                                        <input type="hidden" name="course_id" value="' . $courseId . '" />
                                        <tr>
                                            <td colspan="2" style="text-align:center;">
                                                <input type="submit" name="editar" id="Editar" value="Editar" />
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </div>';
                        } else {
                            echo '<p style="color:red;">Course not found.</p>';
                        }
                    }
                } catch (Exception $e) {
                    echo "<h3 style='color:red;'>Error en el query: " . $dbc->error . "</h3>";
                }

            } elseif (isset($_POST['course_id'])) {
                $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
                $credits = filter_input(INPUT_POST, 'credits', FILTER_VALIDATE_INT);

                if ($credits <= 0 || !is_numeric($credits)) {
                    $credits = 0;
                }

                $query = "UPDATE course 
                          SET title='$title', 
                              credits='$credits'
                          WHERE course_id={$_POST['course_id']}";

                try {
                    if ($dbc->query($query) === TRUE) {
                        echo '<h3>El curso ha sido actualizado exitosamente</h3>';
                    } else {
                        echo '<h3 style="color:red;">No se pudo actualizar el curso porque:<br />' . $dbc->error . '</h3>';
                    }
                } catch (Exception $e) {
                    echo "<h3 style='color:red;'>Error en el query: " . $dbc->error . "</h3>";
                }

            } else {
                echo '<h3 class="centro" style="color:red;">Esta página ha sido accedida con error</h3>';
                $dbc->close();
            }

            $dbc->close();
        } else {
            echo '<h3 style="color:red;">Esta página no ha sido accedida correctamente</h3>';
        }
        ?>

        <h3><a href="index.php" class="centro">Ver cursos</a></h3>
    </div>
</body>

</html>
