<?php
require_once __DIR__ . "/../config/conexion.php";

class Mesa {
    private $conn;

    public function __construct($conn = null) {
        if ($conn) {
            $this->conn = $conn;
        } else {
            // fallback to global conexion.php
            global $conn;
            $this->conn = $conn ?? null;
        }
    }

    // Agregar nueva mesa
    public function agregar($numero_mesa, $capacidad_mesa) {
        if (!$numero_mesa || !$capacidad_mesa) {
            return ['success' => false, 'msg' => 'Debes completar todos los campos.'];
        }

        if (!is_numeric($numero_mesa) || !is_numeric($capacidad_mesa)) {
            return ['success' => false, 'msg' => 'El número de mesa y capacidad deben ser números.'];
        }

        $numero_mesa = intval($numero_mesa);
        $capacidad_mesa = intval($capacidad_mesa);

        // Verificar que la mesa no exista ya
        $checkMesa = "SELECT numero_mesa FROM reserva WHERE numero_mesa = ?";
        $stmtCheck = $this->conn->prepare($checkMesa);
        $stmtCheck->bind_param("i", $numero_mesa);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            return ['success' => false, 'msg' => 'La mesa número ' . $numero_mesa . ' ya existe.'];
        }

        $sql = "INSERT INTO reserva (estado_mesa, capacidad_mesa, numero_mesa, numero_reserva) 
                VALUES ('libre', ?, ?, 0)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $capacidad_mesa, $numero_mesa);

        if ($stmt->execute()) {
            return ['success' => true, 'msg' => 'Mesa número ' . $numero_mesa . ' agregada exitosamente.'];
        } else {
            return ['success' => false, 'msg' => 'Error al agregar la mesa: ' . $stmt->error];
        }
    }

    // Borrar mesa y reorganizar numeración
    public function borrar($numero_mesa) {
        $numero_mesa = intval($numero_mesa);

        // Verificar que la mesa existe y está en estado 'libre'
        $checkMesa = "SELECT numero_mesa, estado_mesa FROM reserva WHERE numero_mesa = ?";
        $stmtCheck = $this->conn->prepare($checkMesa);
        $stmtCheck->bind_param("i", $numero_mesa);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows === 0) {
            return ['success' => false, 'msg' => 'La mesa no existe.'];
        }

        $mesa = $resultCheck->fetch_assoc();

        if ($mesa['estado_mesa'] !== 'libre') {
            return ['success' => false, 'msg' => 'Solo se pueden borrar mesas en estado libre.'];
        }

        $this->conn->begin_transaction();
        try {
            $sqlDelete = "DELETE FROM reserva WHERE numero_mesa = ?";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->bind_param("i", $numero_mesa);
            $stmtDelete->execute();

            $sqlGetMesas = "SELECT numero_mesa FROM reserva ORDER BY numero_mesa ASC";
            $resultMesas = $this->conn->query($sqlGetMesas);

            $nuevoNumero = 1;
            while ($row = $resultMesas->fetch_assoc()) {
                $sqlUpdate = "UPDATE reserva SET numero_mesa = ? WHERE numero_mesa = ?";
                $stmtUpdate = $this->conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("ii", $nuevoNumero, $row['numero_mesa']);
                $stmtUpdate->execute();
                $stmtUpdate->close();
                $nuevoNumero++;
            }

            // Actualizar la tabla cliente para mantener consistencia
            $sqlGetClientes = "SELECT numero_mesa FROM cliente WHERE numero_mesa IS NOT NULL ORDER BY numero_mesa ASC";
            $resultClientes = $this->conn->query($sqlGetClientes);

            $nuevoNumero = 1;
            $mesasActualizadas = array();
            while ($row = $resultClientes->fetch_assoc()) {
                if (!in_array($row['numero_mesa'], $mesasActualizadas)) {
                    $sqlUpdateCliente = "UPDATE cliente SET numero_mesa = ? WHERE numero_mesa = ?";
                    $stmtUpdateCliente = $this->conn->prepare($sqlUpdateCliente);
                    $stmtUpdateCliente->bind_param("ii", $nuevoNumero, $row['numero_mesa']);
                    $stmtUpdateCliente->execute();
                    $stmtUpdateCliente->close();
                    $mesasActualizadas[] = $row['numero_mesa'];
                    $nuevoNumero++;
                }
            }

            $this->conn->commit();
            return ['success' => true, 'msg' => 'Mesa eliminada y números reorganizados.'];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'msg' => 'Error al eliminar la mesa: ' . $e->getMessage()];
        }
    }

    // Liberar mesa y eliminar cliente asociado
    public function liberar($idMesa) {
        $idMesa = intval($idMesa);
        $sql = "UPDATE reserva SET estado_mesa='libre', numero_reserva=NULL WHERE numero_mesa=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idMesa);
        $ok = $stmt->execute();

        if ($ok) {
            $sqlDel = "DELETE FROM cliente WHERE numero_mesa=?";
            $stmtDel = $this->conn->prepare($sqlDel);
            $stmtDel->bind_param("i", $idMesa);
            $stmtDel->execute();
        }

        if ($ok) {
            return ['success' => true, 'msg' => 'Mesa liberada correctamente'];
        }

        return ['success' => false, 'msg' => 'Error al liberar la mesa.'];
    }

    // Obtener todas las mesas
    public function obtenerMesas() {
        $sql = "SELECT * FROM reserva ORDER BY numero_mesa ASC";
        return $this->conn->query($sql);
    }
}

?>
