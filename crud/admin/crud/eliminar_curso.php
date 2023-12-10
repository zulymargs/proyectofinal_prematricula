<?php

session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8">
        <title>Eliminar Estudiante</title>
        <link rel="stylesheet" href="php.css">
</head>
<body>
    <nav>
        <a href="#">NULL</a>
        <a href="../index.php">Home</a>
    </nav>
    <div id = "contenido">
     <h1>Estudiante de Honor UPRA</h1>
     <h2>Eliminar Estudiante</h2> 
     <?php
     if(isset($_SESSION['id']))
     {
            include_once("db_info.php");
            //echo "<p>Conexión exitosa al servidor.</p>";
            if(isset($_GET['estID']) && is_numeric($_GET['estID']))//vino del index
            {
                $query = "SELECT *
                FROM estudiante
                WHERE estID={$_GET['estID']}";
                try{
                    if($result = $dbc->query($query))
                    {
                        if($result->num_rows==1)
                        {
                            $row=$result->fetch_assoc();
                            print '<form action="eliminar_estudiante.php" method="post" >
                            <h3>Está seguro que desea eliminar al siguiente estudiante de honor:
                            '.$row['nombre'].''.$row['apellidoP'].''.$row['apellidoM'].';
                            '.$row['numEst'].' ?</h3>';
                            
                            print '<input type = "hidden" name="estID" value="' .$_GET['estID']. '" />';
                            print '<div style="text-align:center;"><input type="submit" name = "submit"
                            value="Eliminar estudiante" />
                            </div></form>';
                        }
                        else
                        print'<h3> style ="color:red;">Error, el estudainte no se encontró en la tabla</h3>';
                    }
                }
                catch (Exception $e){
                    print "<h3> style='color:red;'>Error en el query: ". $dbc->error . "</h3>";
                }
            
            }
            elseif(isset($_POST['estID']) && is_numeric($_POST['estID']))//vino del form
            {
                $query = "DELETE FROM estudiante WHERE estID={$_POST['estID']} LIMIT 1";
                if($dbc->query($query)===TRUE)
                echo'<h3 class ="centro">El récord del estudiante ha sido eliminado con éxito.</h3>';
                else
                print '<h3 class="centro" style ="color:red;">No se pudo eliminar al estudiante porque:<br/>' . $dbc->error. '</h3>';
            }
            else
            {
                print '<h3 class="centro" style="color:red;">Esta página ha sido accedida con error</h3>';
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