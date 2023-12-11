<?php
session_start();
include_once("../../db_info.php");
// Check if the user is logged in
if (isset($_SESSION['admID'])) {
    echo "<h2>Bienvenido, {$_SESSION['admin_lastnameP']} {$_SESSION['admin_lastnameM']}, {$_SESSION['admin_name']}| <a href='../logout.php'>Logout</a></h2>";
} else {
    echo "<p>Session not active <a href='../../index.php'>Login</a></p>";
}
$limite = 4; // 4 records at a time
$desde = isset($_GET['desde']) ? $_GET['desde'] : 0;

$coursesQuery = "SELECT * FROM course LIMIT $limite OFFSET $desde";
$sectionsQuery = "SELECT section.*, course.title AS course_title FROM section JOIN course ON section.course_id = course.course_id LIMIT $limite OFFSET $desde";

$coursesResult = $dbc->query($coursesQuery);
$sectionsResult = $dbc->query($sectionsQuery);

// Count query for sections
$query = "SELECT COUNT(section_id) as contador
          FROM section s
          JOIN course c ON s.course_id = c.course_id";
$result = $dbc->query($query);
$row = $result->fetch_assoc();
$contador = $row['contador'];
$total_pags = ceil($contador / $limite);
$pag_actual = ceil($desde / $limite) + 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <img src="../../imagenes/logo_upra.png" alt="Logo UPRA">

    <link rel="stylesheet" href="../../php.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h1 {
            text-align: center;
        }

        .action-links {
            display: flex;
            gap: 10px;
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div style="padding-right: 20px;">
        <h1>Administrar Cursos y Secciones</h1>

        <h2>Cursos</h2>
        <table>
            <tr>
                <th>Course ID</th>
                <th>Title</th>
               
                <th>Actions</th>
            </tr>
            <?php while ($course = $coursesResult->fetch_assoc()) : ?>
                <tr>
                    <td><?= $course['course_id']; ?></td>
                    <td><?= $course['title']; ?></td>
                    
                    <td class="action-links">
                        <a href="editar_curso.php?course_id=<?= $course['course_id']; ?>">Edit</a>
                        <a href="eliminar_curso.php?course_id=<?= $course['course_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <br>
        <h2>Secciones</h2>
        <table>
            <tr>
                <th>Section ID</th>
                
                <th>Capacity</th>
                <th>Course Title</th>
                <th>Actions</th>
            </tr>
            <?php while ($section = $sectionsResult->fetch_assoc()) : ?>
                <tr>
                    <td><?= $section['section_id']; ?></td>
                    
                    <td><?= $section['capacity']; ?></td>
                    <td><?= $section['course_title']; ?></td>
                    <td class="action-links">
                        <a href="editar_seccion.php?se_id=<?= $section['se_id']; ?>">Edit</a>
                        <a href="eliminar_seccion.php?se_id=<?= $section['se_id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Pagination Links -->
        <div class="pagination">
            <?php if ($desde > 0) : ?>
                <a href="?desde=<?= max(0, $desde - $limite); ?>">Previous</a>
            <?php endif; ?>

            <?php if ($total_pags > 1) : ?>
                <span>Page <?= $pag_actual; ?> of <?= $total_pags; ?></span>
            <?php endif; ?>

            <?php if ($desde + $limite < $contador) : ?>
                <a href="?desde=<?= min($contador - $limite, $desde + $limite); ?>">Next</a>
            <?php endif; ?>
            <button><a href="insertar_curso.php" class="admin-button">Insertar curso</a></button>
            <button><a href="insertar_seccion.php" class="admin-button">Insertar sección</a></button>
            
            <button><a href="../index.php" class="admin-button">Búsqueda de cursos</a></button>

        </div>
    </div>
</body>
</html>

<?php
$dbc->close();
?>
