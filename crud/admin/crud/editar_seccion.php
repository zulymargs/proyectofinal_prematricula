<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['admID'])) {
    echo "<h2>Bienvenido, {$_SESSION['admin_lastnameP']} {$_SESSION['admin_lastnameM']}, {$_SESSION['admin_name']}| <a href='../../logout.php'>Logout</a></h2>";
} else {
    echo "<p>Session not active <a href='../index.php'>Login</a></p>";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Editar Sección</title>
    <link rel="stylesheet" href="../../php.css">
</head>

<body>
    <div id="contenido">
        <h1>Curso de Honor UPRA</h1>
        <h2>Editar Sección</h2>

        <?php
        if (isset($_SESSION['admID'])) {
            include_once("../../db_info.php");

            if (isset($_GET['se_id'])) {
                $seId = $_GET['se_id'];

                $query = "SELECT * FROM section WHERE se_id = '$seId'";

                try {
                    if ($result = $dbc->query($query)) {
                        if ($result->num_rows == 1) {
                            $row = $result->fetch_assoc();

                            echo '<div>
                                <form action="editar_seccion.php" method="post">
                                    <table border="0">
                                        <tr>
                                            <td>Capacidad: </td>
                                            <td><input type="number" name="capacity" value="' . $row['capacity'] . '" required></td>
                                        </tr>
                                        <input type="hidden" name="se_id" value="' . $seId . '" />
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

            } elseif (isset($_POST['se_id'])) {
                $seId = $_POST['se_id'];
                // $section_id = htmlspecialchars($_POST['section_id'], ENT_QUOTES, 'UTF-8');
                $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);

                if ($capacity <= 0 || !is_numeric($capacity)) {
                    $capacity = 0;
                }

                $query = "UPDATE section 
                          SET  capacity='$capacity'
                          WHERE se_id='$seId'";

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
