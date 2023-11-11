<?php 
    $titulo = "Estudiantes de Honor UPRA";
    if (isset($_GET['ordenID']))
    {
        $ordenID=$_GET['ordenID'];
        switch($ordenID)
        {
            case 1: $orden = 'apellidoP, apellidoM, nombre ASC';
                    break;
            case 2: $orden = 'numEst ASC';
                    break;
            case 3: $orden = 'email ASC';
                    break;
            case 4: $orden = 'nombreDepto ASC';
                    break;
            case 0: $orden = 'promedio DESC';
                    break;        
        }
    }
    else{
        $ordenID=0;
        $orden = 'promedio DESC';
    }
    $limite=5;

    if (!isset($_GET['desde']))

    {

        $desde=0;

    }

    else

    {

        $desde=$_GET['desde'];

    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $titulo; ?></title>
        <link rel="stylesheet" href="php.css">
    </head>
    
    <body>
    	<div>
            <h1>Busqueda de cursos</h1>
			<?php
                /* código */
                include_once("../db_info.php");
                $query =    "SELECT COUNT(estID) as contador
                            FROM estudiante e, departamento d
                            WHERE e.promedio>=3.30 AND
                            e.deptoID = d.deptoID";

                echo "<p>Query del count: $query</p>";
                $result = $dbc->query($query);
                $row = $result->fetch_assoc();
                $contador = $row['contador'];
                $total_pags = ceil($contador/$limite);
                $pag_actual = ceil($desde/$limite)+1;
                echo "<p>La cantidad de récords es $contador</p>";          
                echo "<p>La cantidad de páginas es $total_pags</p>";
                // $query =    "SELECT *
                //             FROM estudiante e, departamento d
                //             WHERE d.deptoID=e.deptoID
                //             ORDER BY $orden";
                $query = "SELECT *

                            FROM estudiante e, departamento d
                            WHERE e.promedio>=3.30 AND
                            e.deptoID = d.deptoID
                            ORDER BY $orden 
                            LIMIT $limite OFFSET $desde";
                echo "<p>query: ".$query."</p>";

                if($result = $dbc->query($query))
                {
                    echo "<table border=1>";
                    echo "<tr>
                        <th>editar</th>
                        <th>eliminar</th>
                        <th><a href='index.php?ordenID=1'>Nombre</a></th>
                        <th><a href='index.php?ordenID=2'>Número Estudiante</a></th>
                        <th><a href='index.php?ordenID=3'>email</a></th>
                        <th><a href='index.php?ordenID=4'>Departamento</a></th>
                        <th><a href='index.php?ordenID=0'>Promedio</a></th>
                    </tr>";
                    while($row = $result->fetch_assoc())
                    {
                        $numEstMask=substr($row['numEst'],0,3)."-".substr($row['numEst'],3,2)."-".substr($row['numEst'],5,4);
                        print'<tr>
                        <td>
                        <a href = "editar_estudiante.php?estID='.$row['estID']. '">editar</a></td>
                        <td>
                        <a href = "eliminar_estudiante.php?estID='.$row['estID']. '">eliminar</a></td>
                        <td>'.$row['apellidoP']." ".$row['apellidoM'].", ".$row['nombre'].
                            "</td> <td>".$numEstMask.
                            "</td> <td>".$row['email'].
                            "</td> <td>".$row['codigo'].
                            "</td> <td>".$row['promedio']."</td>";
                        echo "</tr>";
                    
                    }
                    echo"</table>";
                    //Imprimir los números de páginas
                    echo "<h2>";
                    for ($i=1; $i<=$total_pags; $i++)
                        echo "<a  class='btn' href='index.php?desde=".(($i-1)*$limite)."&limite=$limite&ordenID=$ordenID'> $i </a>&nbsp;&nbsp;";
                    echo "</h2>";
                    print '<h3><a href="insertar_estudiante_de_honor.php">Insertar récord de estudiante de honor</a></h3>';
                }
                else{
                    echo'<h3 style="color:red;">Error en el query: '.$dbc->error.'<h3>';
                }
                $dbc->close();

            ?>
    	</div> 
    </body>
</html>