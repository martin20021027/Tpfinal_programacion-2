<?php
session_start();
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["pass"];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario["pass"])) {
            $_SESSION["usuario"] = $usuario["nombre"];
            header("Location: inicio.php");
            exit;
        } else {
            echo "<p style='color:red; text-align:center;'>❌ Contraseña incorrecta.</p>";
        }
    } else {
        echo "<p style='color:red; text-align:center;'>❌ Usuario no encontrado.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <style>
        /* Fondo con imagen */
        body {
            font-family: Arial, sans-serif;
            background-image: url('Imagenes/foto1.jpg'); /* Ruta del fondo */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;

            /* Centrar el contenido */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Cuadro blanco */
        form {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            width: 300px;
            text-align: center;
        }

        /* Campos de texto */
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

        h2 {
            margin-bottom: 15px;
            color: #333;
        }
    </style>
</head>
<body>
    <!-- Formulario centrado -->
    <form method="POST">
        <h2>Iniciar Sesión</h2>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="pass" placeholder="Contraseña" required><br>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>




