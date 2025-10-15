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
        } else {
            echo "❌ Contraseña incorrecta.";
        }
    } else {
        echo "❌ Usuario no encontrado.";
    }
}
?>
<form method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="pass" placeholder="Contraseña" required><br>
    <button type="submit">Iniciar sesión</button>
</form>

