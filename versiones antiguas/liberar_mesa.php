<?php
require_once "base_datos.php";

//Estos if lo que hacen es buscar el id de la mesa para asi liberar la mesa para otra reserva.
if (isset($_GET['id'])) {
    $idMesa = intval($_GET['id']);
    if (liberarMesa($idMesa)) {
        header("Location: inicio.php?msg=Mesa liberada correctamente");
        exit;
    } else {
        echo "❌ Error al liberar la mesa.";
    }
} else {
    echo "❌ No se recibió el ID de la mesa.";
}
?>
