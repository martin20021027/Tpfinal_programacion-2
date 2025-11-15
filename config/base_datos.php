<?php
require_once __DIR__ . "/../config/conexion.php";

// Ingresar reserva
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = $_POST['nombre'] ?? null;
    $personas = $_POST['personas'] ?? null;

    if (!$nombre || !$personas) {
        die("Debes completar todos los campos.");
    }

    // Número de reserva aleatorio
    $numeroReserva = rand(1000, 9999);

    // Esto lo que hace es busca la mesa libre más pequeña posible que tenga espacio suficiente para las personas.
    $mesaQuery = "SELECT numero_mesa, capacidad_mesa 
                  FROM reserva 
                  WHERE estado_mesa='libre' AND capacidad_mesa >= ? 
                  ORDER BY capacidad_mesa ASC 
                  LIMIT 1";
    $stmtMesa = $conn->prepare($mesaQuery);
    $stmtMesa->bind_param("i", $personas);
    $stmtMesa->execute();
    $resultadoMesa = $stmtMesa->get_result();

    // Este if verifica si existe por lo menos una mesa que coincida con la búsqueda.
    if ($resultadoMesa->num_rows > 0) {
        $mesa = $resultadoMesa->fetch_assoc();
        $numeroMesa = $mesa['numero_mesa'];

        // Esto lo que hace es ingresar los datos del cliente en la base de datos.
        $sqlCliente = "INSERT INTO cliente (nombre_apellido, numero_reserva, numero_mesa) 
                       VALUES (?, ?, ?)";
        $stmtCliente = $conn->prepare($sqlCliente);
        $stmtCliente->bind_param("sii", $nombre, $numeroReserva, $numeroMesa);

         // Este if es para guardar al cliente
        if ($stmtCliente->execute()) {
            // Actualizar la mesa
            $updateReserva = "UPDATE reserva 
                              SET estado_mesa='ocupada', numero_reserva=? 
                              WHERE numero_mesa=?";
            $stmtUpdate = $conn->prepare($updateReserva);
            $stmtUpdate->bind_param("ii", $numeroReserva, $numeroMesa);
            $stmtUpdate->execute();

            // Este echo muestra el mensaje de confirmación en pantalla.
            echo "<div style='
                    font-family: Arial, sans-serif;
                    background-color: #f8f8f8;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;'> 

                    <div style='background: #fff; padding: 30px; border-radius: 10px;
                                box-shadow: 0 4px 8px rgba(0,0,0,0.2); width: 350px; text-align:center;'>

                        <h2>¡Reservación realizada!</h2>
                        <p><strong>Número de reserva:</strong> $numeroReserva</p>
                        <p><strong>Número de mesa:</strong> $numeroMesa</p>
                        <br>
                        <form action='../pages/inicio.php'>
                            <button type='submit' 
                                    style='padding:10px 20px; background:#007bff; 
                                           color:white; border:none; border-radius:5px; 
                                           cursor:pointer; font-size:16px;'>
                                Reserva Confirmada
                            </button>
                        </form>
                    </div>
                  </div>";
        } else {
            echo "Error al guardar el cliente: " . $stmtCliente->error;
        }

        $stmtCliente->close();
        // Este else es para decir que no hay mesas disponible.
    } else {
        echo "<div style='text-align:center; font-family:Arial,sans-serif;'>
                No hay mesas disponibles para $personas personas.
              </div>";
    }
    // Este $stmtMesa es para cerrar la consulta de la mesa.
    $stmtMesa->close();
}

// Esta funcion es para liberar mesa.
function liberarMesa($idMesa) {
    global $conn;

    // Liberar mesa de la base de datos de la tabla reserva.
    $sql = "UPDATE reserva SET estado_mesa='libre', numero_reserva=NULL WHERE numero_mesa=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idMesa);
    $ok = $stmt->execute();

    // Este if elimina al cliente asociado a la mesa.
    if ($ok) {
        $sqlDel = "DELETE FROM cliente WHERE numero_mesa=?";
        $stmtDel = $conn->prepare($sqlDel);
        $stmtDel->bind_param("i", $idMesa);
        $stmtDel->execute();
    }

    return $ok;
}
?>
