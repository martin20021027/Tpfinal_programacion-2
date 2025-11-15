<?php
session_start();

// Verificar que hay sesión activa
if (!isset($_SESSION['usuario'])) {
    header('Location: ../sesion/login.php');
    exit;
}

require_once __DIR__ . "/../config/conexion.php";

if (isset($_GET['id'])) {
    $numero_mesa = intval($_GET['id']);

    // Verificar que la mesa existe y está en estado 'libre'
    $checkMesa = "SELECT numero_mesa, estado_mesa FROM reserva WHERE numero_mesa = ?";
    $stmtCheck = $conn->prepare($checkMesa);
    $stmtCheck->bind_param("i", $numero_mesa);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows === 0) {
        header('Location: ../pages/inicio.php?msg= La mesa no existe.');
        exit;
    }

    $mesa = $resultCheck->fetch_assoc();

    if ($mesa['estado_mesa'] !== 'libre') {
        header('Location: ../pages/inicio.php?msg= Solo se pueden borrar mesas en estado libre.');
        exit;
    }

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Borrar la mesa
        $sqlDelete = "DELETE FROM reserva WHERE numero_mesa = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $numero_mesa);
        $stmtDelete->execute();

        // Reorganizar: renumerar todas las mesas después de la borrada
        // Obtener todas las mesas ordenadas por número_mesa
        $sqlGetMesas = "SELECT numero_mesa FROM reserva ORDER BY numero_mesa ASC";
        $resultMesas = $conn->query($sqlGetMesas);

        $nuevoNumero = 1;
        while ($row = $resultMesas->fetch_assoc()) {
            $sqlUpdate = "UPDATE reserva SET numero_mesa = ? WHERE numero_mesa = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ii", $nuevoNumero, $row['numero_mesa']);
            $stmtUpdate->execute();
            $stmtUpdate->close();
            $nuevoNumero++;
        }

        // También actualizar la tabla cliente para mantener consistencia
        $sqlGetClientes = "SELECT numero_mesa FROM cliente WHERE numero_mesa IS NOT NULL ORDER BY numero_mesa ASC";
        $resultClientes = $conn->query($sqlGetClientes);

        $nuevoNumero = 1;
        $mesasActualizadas = array();
        while ($row = $resultClientes->fetch_assoc()) {
            if (!in_array($row['numero_mesa'], $mesasActualizadas)) {
                $sqlUpdateCliente = "UPDATE cliente SET numero_mesa = ? WHERE numero_mesa = ?";
                $stmtUpdateCliente = $conn->prepare($sqlUpdateCliente);
                $stmtUpdateCliente->bind_param("ii", $nuevoNumero, $row['numero_mesa']);
                $stmtUpdateCliente->execute();
                $stmtUpdateCliente->close();
                $mesasActualizadas[] = $row['numero_mesa'];
                $nuevoNumero++;
            }
        }

        // Confirmar transacción
        $conn->commit();

        header('Location: ../pages/inicio.php?msg= Mesa eliminada y números reorganizados.');
        exit;
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        header('Location: ../pages/inicio.php?msg= Error al eliminar la mesa: ' . $e->getMessage());
        exit;
    }

    $stmtCheck->close();
} else {
    header('Location: ../pages/inicio.php');
    exit;
}

$conn->close();
?>
