<?php
session_start();
include_once("../../db_info.php");
// Check if the user is logged in
if (isset($_SESSION['admID'])) {
    echo "<h2>Bienvenido, {$_SESSION['admin_lastnameP']} {$_SESSION['admin_lastnameM']}, {$_SESSION['admin_name']}| <a href='../../logout.php'>Logout</a></h2>";
} else {
    echo "<p>Session not active <a href='login.php'>Login</a></p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Eliminar Sección</title>
    <link rel="stylesheet" href="../../php.css">
</head>
<body>
    <!-- <nav>
        <a href="#">NULL</a>
        <a href="../index.php">Home</a>
    </nav> -->
    <div id="contenido">
        <h1>Estudiante de Honor UPRA</h1>
        <h2>Eliminar Sección</h2>
        <?php
        if (isset($_SESSION['id'])) {
            include_once("../../db_info.php");

            if (isset($_GET['section_id']) && is_numeric($_GET['section_id'])) {
                $section_id = $_GET['section_id'];

                // Fetch section details
                $query = "SELECT * FROM section WHERE section_id = $section_id";

                try {
                    if ($result = $dbc->query($query)) {
                        if ($result->num_rows == 1) {
                            $row = $result->fetch_assoc();
                            echo '<form action="eliminar_seccion.php" method="post">
                                <h3>Está seguro que desea eliminar la siguiente sección:
                                ' . $row['title'] . '?</h3>';

                            echo '<input type="hidden" name="section_id" value="' . $section_id . '" />';
                            echo '<div style="text-align:center;"><input type="submit" name="submit"
                                value="Eliminar sección" />
                                </div></form>';
                        } else {
                            echo '<h3 style ="color:red;">Error, la sección no se encontró en la tabla</h3>';
                        }
                    }
                } catch (Exception $e) {
                    echo '<h3 style="color:red;">Error en el query: ' . $dbc->error . '</h3>';
                }
            } elseif (isset($_POST['section_id']) && is_numeric($_POST['section_id'])) {
                $section_id = $_POST['section_id'];

                // Delete section and cascade delete waiting list
                $delete_query = "DELETE section, enrollment
                                FROM section
                                LEFT JOIN enrollment ON section.section_id = enrollment.section_id
                                WHERE section.section_id = $section_id";

                if ($dbc->query($delete_query) === TRUE) {
                    echo '<h3 class="centro">La sección y la lista de espera han sido eliminadas con éxito.</h3>';
                } else {
                    echo '<h3 class="centro" style="color:red;">No se pudo eliminar la sección porque:<br/>' . $dbc->error . '</h3>';
                }
            } else {
                echo '<h3 class="centro" style="color:red;">Esta página ha sido accedida con error</h3>';
            }

            $dbc->close();
        } else {
            echo '<h3 style="color:red;">Esta página no ha sido accedida correctamente</h3>';
        }
        ?>
        <h3><a href="index.php" class="centro">Ver secciones</a></h3>
    </div>
</body>
</html>
