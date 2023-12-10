<?php

session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Editar Estudiante</title>
    <link rel="stylesheet" href="template.css">
</head>

<body>
    <div id="contenido">
        <h1>Estudiante de Honor UPRA</h1>
        <h2>Editar Estudiante</h2>
        <?php
        if(isset($_SESSION['id']))
        {
            include_once("db_info.php");
            //echo "<p>Conexión exitosa al servidor.</p>";
            if (isset($_GET['estID']) && is_numeric($_GET['estID'])) //vino del index
            {
                $query = "SELECT *
                    FROM estudiante
                    WHERE estID={$_GET['estID']}";
                try {
                    if ($result = $dbc->query($query)) {
                        if ($result->num_rows == 1) {
                            $row = $result->fetch_assoc();

                            print '<div>
                                
                                <form action="editar_estudiante.php" method="post">
                                <table border=0>

                                <tr>
                                    <td>Apellido Paterno: </td><td>
                                    <input type="text" name="apellidoP" value="' . $row['apellidoP'] . '" required>
                                </tr>
                                <tr>
                                    <td>Apellido Materno: </td><td>
                                    <input type="text" name="apellidoM" value="' . $row['apellidoM'] . '" required>
                                </tr>
                                <tr>
                                    <td>Nombre: </td><td>
                                    <input type="text" name="nombre" value="' . $row['nombre'] . '" required>
                                </tr>
                                <tr>
                                    <td>Email: </td><td>
                                    <input type="text" name="email" value="' . $row['email'] . '" required>
                                </tr>
                                <tr>
                                <td> Departamento: </td><td>
                                <select name="deptoID">';

                            $query2 = "SELECT * FROM departamento";
                            $result2 = $dbc->query($query2);
                            while ($row2 = $result2->fetch_assoc()) {
                                print "<option value=" . $row2['deptoID'];
                                if ($row2['deptoID'] == $row['deptoID'])
                                    print " selected ->";
                                else
                                    print " > ";
                                print $row2['codigo'] . "</option>";
                            }
                            print '</select></td>
                                </tr>
                                <tr>
                                    <td>Promedio: </td><td>
                                    <input type="number" name="promedio" step=0.01 min=0.00 max=4.00 value="' . $row['promedio'] . '" required/></td>
                                </tr>
                                <tr>


                                <input type="hidden" name="estID" value="' . $_GET['estID'] . '" />

                                <td colspan=2>';

                            print '<div style="text-align:center;"><input type="submit" name="editar" id="Editar" value="Editar" /></div></td>
                                    </tr>
                                    </table>
                                    </form>
                                    </div>';
                        };
                    }
                } 
                    catch (Exception $e) 
                    {
                        print "<h3> style='color:red;'>Error en el query: " . $dbc->error . "</h3>";
                    }
            } 
            elseif (isset($_POST['estID']) && is_numeric($_POST['estID'])) //vino del form
            {

                $nombre = strip_tags($_POST['nombre']);

                $nombre = htmlspecialchars($nombre, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);

                $apellido_p = htmlspecialchars($_POST['apellidoP'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);

                $apellido_m = htmlspecialchars($_POST['apellidoM'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);

                $email = htmlspecialchars($_POST['email'], ENT_QUOTES | ENT_SUBSTITUTE,'UTF-8', false);

                $promedio = filter_input(INPUT_POST, 'promedio', FILTER_VALIDATE_FLOAT);

                if ($promedio <=0 or $promedio>4 or !is_numeric($promedio))

                    $promedio = 0;

                $id_dept = filter_input(INPUT_POST, 'deptoID', FILTER_VALIDATE_INT);

                if ($id_dept >13 or $id_dept < 1 or !is_numeric($id_dept))
                    $id_dept = 0;


                $query = "UPDATE estudiante 

                SET apellidoP='$apellido_p', 

                apellidoM='$apellido_m', 

                nombre='$nombre', 

                email='$email', 

                promedio=$promedio, 

                deptoID=$id_dept

                WHERE estID={$_POST['estID']}";

                //echo "<p>update query: ".$query."</p>";



                if ($dbc->query($query) === TRUE)

                    print '<h3>El estudiante ha sido actualizado exitosamente</h3>';

                else

                    print '<h3 style="color:red;">No se pudo actualizar el estudiante porque:<br />' . $dbc->error . '</h3>';
            } else {
                print '<h3 class="centro" style="color:red;">Esta página ha sido accedida con error</h3>';
                $dbc->close();
            }


            $dbc->close();
        }
        else
        print '<h3 style = "color:red;"> Esta pagina no ha sido accedida correctamente</h3>';

        ?>





        <h3><a href="index.php" class="centro">Ver estudiantes</a></h3>
    </div>
</body>

</html>