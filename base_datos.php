<?php
$servername = "localhost";
$username = "root";  
$password = "";      
$database = "basededatos1"; 

// Conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $personas = $_POST['personas'];

    // Número de reserva aleatorio
    $numeroReserva = rand(1000, 9999);

    // Número de mesa libre aleatorio. Primero buscamos mesas libres
    $mesaQuery = "SELECT numero_mesa, capacidad_mesa FROM reserva WHERE estado_mesa='libre' AND capacidad_mesa >= ? ORDER BY capacidad_mesa ASC LIMIT 1";
    $stmtMesa = $conn->prepare($mesaQuery);
    $stmtMesa->bind_param("i", $personas);
    $stmtMesa->execute();
    $resultadoMesa = $stmtMesa->get_result();

    if ($resultadoMesa->num_rows > 0) {
        $mesa = $resultadoMesa->fetch_assoc();
        $numeroMesa = $mesa['numero_mesa'];
        $capacidadMesa = $mesa['capacidad_mesa'];

        // Insertar en la tabla cliente
        $sqlCliente = "INSERT INTO cliente (nombre_apellido, numero_reserva, numero_mesa) VALUES (?, ?, ?)";
        $stmtCliente = $conn->prepare($sqlCliente);
        $stmtCliente->bind_param("sii", $nombre, $numeroReserva, $numeroMesa);

        if ($stmtCliente->execute()) {
            // Actualizar la reserva
            $updateReserva = "UPDATE reserva SET estado_mesa='ocupada', numero_reserva=? WHERE numero_mesa=?";
            $stmtUpdate = $conn->prepare($updateReserva);
            $stmtUpdate->bind_param("ii", $numeroReserva, $numeroMesa);
            $stmtUpdate->execute();

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
                        <form action='index.html'>
                            <button type='submit' 
                                    style='padding:10px 20px; background:#007bff; 
                                           color:white; border:none; border-radius:5px; 
                                           cursor:pointer; font-size:16px;'>
                                Volver al Menú Principal
                            </button>
                        </form>
                    </div>
                  </div>";
        } else {
            echo "Error al guardar el cliente: " . $stmtCliente->error;
        }

        $stmtCliente->close();
    } else {
        echo "<div style='text-align:center; font-family:Arial,sans-serif;'>
                No hay mesas disponibles para $personas personas.
              </div>";
    }

    $stmtMesa->close();
}

$conn->close();
?>
