<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../sesion/login.php');
    exit;
}

require_once __DIR__ . "/../Objetos/Mesas.php";
require_once __DIR__ . "/../config/conexion.php";

$mesaObj = new Mesa($conn);

// Determine action
$action = $_GET['action'] ?? ($_SERVER['REQUEST_METHOD'] == 'POST' ? 'agregar' : null);

switch ($action) {
    case 'agregar':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $numero_mesa = $_POST['numero_mesa'] ?? null;
            $capacidad_mesa = $_POST['capacidad_mesa'] ?? null;
            $res = $mesaObj->agregar($numero_mesa, $capacidad_mesa);
            header('Location: ../pages/inicio.php?msg=' . urlencode($res['msg']));
            exit;
        }
        break;
    case 'borrar':
        if (isset($_GET['id'])) {
            $res = $mesaObj->borrar($_GET['id']);
            header('Location: ../pages/inicio.php?msg=' . urlencode($res['msg']));
            exit;
        }
        break;
    case 'liberar':
        if (isset($_GET['id'])) {
            $res = $mesaObj->liberar($_GET['id']);
            header('Location: ../pages/inicio.php?msg=' . urlencode($res['msg']));
            exit;
        }
        break;
    default:
        header('Location: ../pages/inicio.php');
        exit;
}

?>
