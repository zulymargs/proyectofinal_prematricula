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

        if (isset($_GET['section_id']) && is_numeric($_GET['section_id'])) {
            $section_id = $_GET['section_id'];

            $query = "SELECT * FROM section WHERE section_id = ?";

            try {
                $stmt = $dbc->prepare($query);
                $stmt->bind_param("i", $section_id);
                
                if ($stmt->execute()) {
                    $result = $stmt->get_result();

                    if ($result->num_rows == 1) {
                        $row = $result->fetch_assoc();

                        echo '<form action="eliminar_seccion.php" method="post">
                            <h3>¿Está seguro que desea eliminar la siguiente sección?: 
                            ' . htmlspecialchars($row['course_id']) . ' - ' . $row['section_id'] . ' - ' . $row['capacity'] . '?</h3>';

                        echo '<input type="hidden" name="section_id" value="' . $section_id . '" />';
                        echo '<div style="text-align:center;"><input type="submit" name="submit" value="Eliminar sección" /></div></form>';
                    } else {
                        echo '<h3 style="color:red;">Error, la sección no se encontró en la tabla</h3>';
                    }
                }
            } catch (Exception $e) {
                echo '<h3 style="color:red;">Error en el query: ' . $e->getMessage() . '</h3>';
            }
        } elseif (isset($_POST['section_id']) && is_numeric($_POST['section_id'])) {
            $section_id = $_POST['section_id'];

            // Start a transaction for atomicity
            $dbc->begin_transaction();

            try {
                // Delete enrolled students in the enrollment table
                $deleteEnrollmentsQuery = "DELETE FROM enrollment WHERE section_id = ?";
                $stmtEnrollments = $dbc->prepare($deleteEnrollmentsQuery);
                $stmtEnrollments->bind_param("i", $section_id);
                $stmtEnrollments->execute();

                // Delete the section
                $deleteSectionQuery = "DELETE FROM section WHERE section_id = ?";
                $stmtSection = $dbc->prepare($deleteSectionQuery);
                $stmtSection->bind_param("i", $section_id);
                $stmtSection->execute();

                // Commit the transaction
                $dbc->commit();

                echo '<h3 class="centro">La sección ha sido eliminada con éxito.</h3>';
                
                // Redirect after successful deletion
                header("Location: index.php");
                exit();
            } catch (Exception $e) {
                // Rollback the transaction if an error occurs
                $dbc->rollback();
                echo '<h3 class="centro" style="color:red;">No se pudo eliminar la sección porque: <br/>' . $e->getMessage() . '</h3>';
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
