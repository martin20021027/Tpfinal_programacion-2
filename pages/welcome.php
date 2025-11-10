<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../sesion/login.php");
    exit();
}
?>

<h2>Bienvenido, <?php echo $_SESSION['usuario']; ?> 🎉</h2>
<a href="../sesion/logout.php">Cerrar Sesión</a>
