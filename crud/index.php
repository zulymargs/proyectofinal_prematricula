<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>LOGIN - Matricula UPRA</title>
    <link rel="stylesheet" href="styles.css" type="text/css" />
</head>

<body>
<div id="contenido">
    <h1>UPRA-SIS</h1>
    <h2>Autenticarse</h2>
<?php
//if (isset($_POST['submit']))
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if( (!empty($_POST['student_id'])) && (!empty($_POST['password'])) )
        { //conectarme a ver si existe ese estudiante de honor    
            include_once("db_info.php");
            $student_id = $_POST['student_id'];
            $password = $_POST['password'];
           
            $query = "SELECT * FROM student
                      WHERE student_id = '$student_id'";
           
            $result = $dbc->query($query);
            if ($result->num_rows==1)
            {  
                $row = $result->fetch_assoc();
                echo "password de la base de datos: ".$row['password'];

                //  Redirigir el usuario a la página correspondiente
                if (password_verify($password,$row['password'])&&$row['year_of_study']==0)
                {
                    echo "<p>password correcto</p>";
                    header('Location: admin/index.php');
                   
                }
                elseif (password_verify($password,$row['password'])&&$row['year_of_study']>=1)
                {
                    echo "<p>password correcto</p>";
                    header('Location: user/index.php');
                }
                else
                    echo "<p>password incorrecto</p>";
            }
            else
            {
                print '<h3>Su Numero de estudiante no concuerda con nuestros archivos!<br />Vuelva a intentarlo...<a href="login.php"> Login </a></h3>';
            }
            $dbc->close();
        }
        else
        {   // No entró uno de los campos

          print '<h3>Asegúrese de entrar su student_id y número de estudiante. <br /> Vuelva a intentarlo...<a href="login.php"> Login </a></h3>';
        }
}
else // No llegó por un submit, por lo tanto hay que presentar el formulario
{  
    print '<form action="index.php" method="post">
        <table border="0">
          <tr>
            <td width="140" align="right">Numero de estudiante:</td>
            <td><input type="student_id" name="student_id" size="50" maxlength="60" required /></td>
          </tr>
          <tr>
            <td width="255" align="right">password:</td>
            <td><input type="password" name="password" ></td>
          </tr>
         
          <tr>
            <td></td>
            <td><input type="submit" class="formbutton" name="submit" value="Entrar!" /></td>
          </tr></table></form>';
}

?>
</div>
</body>
</html>