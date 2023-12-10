<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Insertar Estudiante</title>
    <link rel="stylesheet" href="template.css">
</head>

<body>
    <nav>
        <a href="#">NULL</a>
        <a href="../index.php">Home</a>
    </nav>
    <div id="contenido">
        <h1>Estudiante de Honor UPRA</h1>
        <h2>Insertar Estudiante</h2>
        <?php
        if(isset($_SESSION['id']))
        {
            include_once("db_info.php");

            print '<div>
                        <form action="insertar_estudiante_de_honor.php" method="post">
                            <table border="0">
                                <tr>
                                    <td>Apellido Paterno:</td>
                                    <td><input type="text" name="apellidoP" value="" required></td>
                                </tr>
                                <tr>
                                    <td>Apellido Materno:</td>
                                    <td><input type="text" name="apellidoM" value="" required></td>
                                </tr>
                                <tr>
                                    <td>Nombre:</td>
                                    <td><input type="text" name="nombre" value="" required></td>
                                </tr>
                                <tr>
                                    <td>Email:</td>
                                    <td><input type="text" name="email" value="" required></td>
                                </tr>
                                <tr>
                                    <td>Numero Estudiante:</td>
                                    <td><input type="text" name="numEst" value="" required></td>
                                </tr>
                                <tr>
                                    <td> Departamento: </td><td>
                                    <select name="deptoID">';
                                    $query2 = "SELECT * FROM departamento";
                                    $result2 = $dbc->query($query2);
                                    while ($row2 = $result2->fetch_assoc()) {
                                        print "<option value=" . $row2['deptoID'];

                                            print " selected ->";

                                        print $row2['codigo'] . "</option>";
                                    }
                                    print '</select></td>
                                </tr>
                                <tr>
                                    <td>Promedio: </td><td>
                                    <input type="number" name="promedio" step=0.01 min=0.00 max=4.00 value="" required/></td>
                                </tr>
                                <tr>
                                    <input type="hidden" name="estID" value="" />
                                    <td colspan=2>';
                    print ' </table>
                        <div style="text-align: center;"><input type="submit" name="insertar" id="Insertar" value="Insertar" /></div>
                        </tr>
                    </form>
                </div>';
            
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["insertar"])) 
            {

                $nombre = htmlspecialchars($_POST['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
                $apellido_p = htmlspecialchars($_POST['apellidoP'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
                $apellido_m = htmlspecialchars($_POST['apellidoM'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
                $email = htmlspecialchars($_POST['email'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
                $numEst = htmlspecialchars($_POST['numEst'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
                $promedio = filter_input(INPUT_POST, 'promedio', FILTER_VALIDATE_FLOAT);
                
                if ($promedio <=0 or $promedio>4 or !is_numeric($promedio))
                    $promedio = 0;

                $id_dept = filter_input(INPUT_POST, 'deptoID', FILTER_VALIDATE_INT);
                if ($id_dept >13 or $id_dept < 1 or !is_numeric($id_dept))
                    $id_dept = 0;


                $query = "INSERT INTO estudiante 
                        (estID, apellidoP, apellidoM, nombre, email, promedio, deptoID, numEst)
                        VALUES (NULL, '$apellido_p', '$apellido_m', '$nombre', '$email', $promedio, $id_dept, $numEst)";


                if($dbc->query($query))
                echo'<h3 class ="centro">El récord del estudiante ha sido insertado con éxito.</h3>';
                else
                print '<h3 class="centro" style ="color:red;">No se pudo insertar al estudiante porque:<br/>' . $dbc->error. '</h3>';
                $dbc->close();
            }



        }
        else
        print '<h3 style = "color:red;"> Esta pagina no ha sido accedida correctamente</h3>';

        ?>





        <h3><a href="index.php" class="centro">Ver estudiantes</a></h3>
    </div>
</body>

</html>