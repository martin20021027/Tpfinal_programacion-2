<?php
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $email = $_POST["email"];
    $password = password_hash($_POST["pass"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, apellido, email, pass) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $apellido, $email, $password);

    if ($stmt->execute()) {
        header("Location: inicio.html");
    } else {
        echo "❌ Error: ";
    }
}
?>

<!-- Formulario simple -->
<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required><br>
    <input type="text" name="apellido" placeholder="Apellido" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="pass" placeholder="Contraseña" required><br>
    <button type="submit">Registrarse</button>
</form>

