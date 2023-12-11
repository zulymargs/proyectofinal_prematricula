<?php
session_start();

// Check if the user is logged in as an administrator
if (!isset($_SESSION['admID'])) {
    header("Location: ../../index.php");
    exit();
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
    <div id="contenido">
        <?php
        include_once("../../db_info.php");

        if (isset($_GET['se_id'])) {
            $se_id = $_GET['se_id'];

            $query = "SELECT * FROM section WHERE se_id = $se_id";

            try {
                if ($result = $dbc->query($query)) {
                    if ($result->num_rows == 1) {
                        $row = $result->fetch_assoc();

                        echo '<form action="eliminar_seccion.php" method="post">
                            <h3>¿Está seguro que desea eliminar la siguiente sección?: <br>
                            ' . htmlspecialchars($row['course_id']) . ' - ' . $row['section_id'] . '?</h3>';

                        echo '<input type="hidden" name="se_id" value="' . $se_id . '" />';
                        echo '<div style="text-align:center;"><input type="submit" name="submit" value="Eliminar sección" /></div></form>';
                    } else {
                        echo '<h3 style="color:red;">Error, la sección no se encontró en la tabla</h3>';
                    }
                }
            } catch (Exception $e) {
                echo '<h3 style="color:red;">Error en el query: ' . $e->getMessage() . '</h3>';
            }
        } elseif (isset($_POST['se_id'])) {
            $se_id = $_POST['se_id'];
            $query = "SELECT * FROM section WHERE se_id = $se_id";
            if ($result = $dbc->query($query)) {
                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $se_id = $_POST['se_id'];
                    $section_id = $row['section_id'];
                    $course_id = $row['course_id'];

                    // Start a transaction for atomicity
                    $dbc->begin_transaction();

                    $deleteEnrollmentsQuery = "DELETE FROM enrollment WHERE course_id = ? AND section_id = ?";
                    $stmtEnrollments = $dbc->prepare($deleteEnrollmentsQuery);
                    $stmtEnrollments->bind_param("ss", $course_id, $section_id);
                    $stmtEnrollments->execute();

                    $deleteSectionQuery = "DELETE FROM section WHERE se_id = ?";
                    $stmtSection = $dbc->prepare($deleteSectionQuery);
                    $stmtSection->bind_param("i", $se_id);
                    $stmtSection->execute();

                    try {
                        // Commit the transaction
                        $dbc->commit();

                        echo '<h3 class="centro">La sección ha sido eliminada con éxito.</h3>';
                        // Redirect after successful deletion
                        header("Location: index.php");
                        exit();
                    } catch (Exception $e) {
                        // Rollback the transaction if an error occurs
                        $dbc->rollback();
                        echo "<h3 style='color:red;'>Error in the query: " . $dbc->errno . "</h3>";
                    }
                }
            }
        } else {
            echo '<h3 class="centro" style="color:red;">Esta página ha sido accedida con error</h3>';
            $dbc->close();
        }
        ?>

        <h3><a href="index.php" class="centro">Ver secciones</a></h3>
    </div>
</body>

</html>
