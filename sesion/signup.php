<?php
require_once __DIR__ . "/../config/conexion.php";

// Variable en donde se guarda el mensaje para mostrar despues.
$mensaje = "";

// Este if es para crear un usuario y guardarlo en la base de datos.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $email = $_POST["email"];
    $password = password_hash($_POST["pass"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, apellido, email, pass) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $apellido, $email, $password);

// Este if es para ejecutar la consulta y te dice si se ingreso correctamente o no.
    if ($stmt->execute()) {
        $mensaje = "<p style='color: green; font-size:14px;'>Usuario registrado correctamente. <a href='login.php'>Inicia sesión</a></p>";
    } else {
        $mensaje = "<p style='color: red; font-size:14px;'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <style>
        /* Fondo general */
        body {
            font-family: Arial, sans-serif;
            background-image: url('../fotos/foto1.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;  
            align-items: center;      
        }

        /* Cuadro del formulario */
        form {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            width: 300px;
            text-align: center;
        }

        /* Inputs */
        input {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        /* Botón */
        button {
            width: 95%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }


    </style>
</head>
<body>

    <form method="POST">
        <h2>Registrarse</h2>

        <input type="text" name="nombre" placeholder="Nombre" required><br>
        <input type="text" name="apellido" placeholder="Apellido" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="pass" placeholder="Contraseña" required><br>
        <button type="submit">Registrarse</button>

         <!-- Mensaje dentro del cuadro -->
         <?= $mensaje ?>
    
    </form>

</body>
</html>
