<?php
session_start();

// Verificar que hay sesión activa
if (!isset($_SESSION['usuario'])) {
    header('Location: ../sesion/login.php');
    exit;
}

require_once __DIR__ . "/../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numero_mesa = $_POST['numero_mesa'] ?? null;
    $capacidad_mesa = $_POST['capacidad_mesa'] ?? null;

    // Validar que los datos no estén vacíos
    if (!$numero_mesa || !$capacidad_mesa) {
        header('Location: ../pages/inicio.php?msg= Debes completar todos los campos.');
        exit;
    }

    // Validar que sean números
    if (!is_numeric($numero_mesa) || !is_numeric($capacidad_mesa)) {
        header('Location: ../pages/inicio.php?msg= El número de mesa y capacidad deben ser números.');
        exit;
    }

    $numero_mesa = intval($numero_mesa);
    $capacidad_mesa = intval($capacidad_mesa);

    // Verificar que la mesa no exista ya
    $checkMesa = "SELECT numero_mesa FROM reserva WHERE numero_mesa = ?";
    $stmtCheck = $conn->prepare($checkMesa);
    $stmtCheck->bind_param("i", $numero_mesa);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        header('Location: ../pages/inicio.php?msg= La mesa número ' . $numero_mesa . ' ya existe.');
        exit;
    }

    // Insertar la nueva mesa
    $sql = "INSERT INTO reserva (estado_mesa, capacidad_mesa, numero_mesa, numero_reserva) 
            VALUES ('libre', ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $capacidad_mesa, $numero_mesa);

    if ($stmt->execute()) {
        header('Location: ../pages/inicio.php?msg= Mesa número ' . $numero_mesa . ' agregada exitosamente.');
        exit;
    } else {
        header('Location: ../pages/inicio.php?msg= Error al agregar la mesa: ' . $stmt->error);
        exit;
    }

    $stmt->close();
    $stmtCheck->close();
} else {
    header('Location: ../pages/inicio.php');
    exit;
}

$conn->close();
?>
