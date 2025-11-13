<?php
// Inicio y verificación de sesión
session_start();

// Evitar cache para que el botón "Atrás" no muestre contenido autenticado
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Si no hay usuario en sesión, redirigir al login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../sesion/login.php');
    exit;
}

require_once __DIR__ . "/../config/base_datos.php";

// Obtener todas las mesas
$result = $conn->query("SELECT * FROM reserva ORDER BY numero_mesa ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Reservas y Mesas</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        .container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.2); }
        h2 { margin-top: 0; }

        /* Contenedor de mesas en rejilla de 3 columnas */
        .mesas-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .mesa {
            background: #ddd;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }
        .libre { background: #4caf50; color: white; }
        .ocupada { background: #f44336; color: white; }
        .btn-liberar {
            margin-top: 6px;
            display: inline-block;
            padding: 4px 8px;
            background: white;
            color: #f44336;
            border: 1px solid #f44336;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
        }
        .btn-liberar:hover { background: #f44336; color: white; }
        .btn-borrar {
            margin-top: 6px;
            display: inline-block;
            padding: 4px 8px;
            background: white;
            color: #000000ff;
            border: 1px solid #000000ff;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
        }
        .btn-borrar:hover { background: #ff9800; color: white; }
        .msg { color: green; font-weight: bold; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
      
        <div class="card">
            <h2>Ingresar Reserva</h2>
            <form method="post" action="../config/base_datos.php" style="margin-top:15px;">
                <label>Apellido y Nombre:</label><br>
                <input type="text" name="nombre" required><br><br>

                <label>Número de personas:</label><br>
                <input type="number" name="personas" min="1" required><br><br>

                <button type="submit">Reservar</button>
            </form>
            <br>
            <form action="../public/index.php" method="post">
                <button type="submit">Cerrar Sesión</button>
            </form>
        </div>

        
        <div class="card">
            <h2>Estado de Mesas</h2>
            <?php if (isset($_GET['msg'])): ?>
                <p class="msg"><?= htmlspecialchars($_GET['msg']); ?></p>
            <?php endif; ?>

            <div class="mesas-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="mesa <?= $row['estado_mesa']; ?>">
                    <p><b>Mesa <?= $row['numero_mesa']; ?></b></p>
                    <p><?= ucfirst($row['estado_mesa']); ?></p>
                    <p><?= $row['capacidad_mesa']; ?> personas</p>
                    <?php if ($row['estado_mesa'] == 'ocupada'): ?>
                        <a class="btn-liberar" href="../actions/liberar_mesa.php?id=<?= $row['numero_mesa']; ?>">Liberar</a>
                    <?php elseif ($row['estado_mesa'] == 'libre'): ?>
                        <a class="btn-borrar" href="../actions/borrar_mesa.php?id=<?= $row['numero_mesa']; ?>" onclick="return confirm('¿Estás seguro de que deseas borrar esta mesa?');">Borrar</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            </div>

            <div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px; border: 1px solid #ddd;">
                <h3 style="margin-top: 0;">Agregar Nueva Mesa</h3>
                <form method="post" action="../actions/agregar_mesa.php">
                    <label>Número de Mesa:</label><br>
                    <input type="number" name="numero_mesa" min="1" required><br><br>

                    <label>Capacidad (personas):</label><br>
                    <input type="number" name="capacidad_mesa" min="1" required><br><br>

                    <button type="submit">Agregar Mesa</button>
                </form>
            </div>
        </div>
    </div>
    <br>
        


    </div>

    <script>
    // Intento de mitigar volver atrás: empuja estado y evita retroceso básico
    try {
        history.pushState(null, null, location.href);
        window.addEventListener('popstate', function () {
            history.pushState(null, null, location.href);
        });
    } catch (e) {
        // algunos navegadores o contextos pueden lanzar, ignorar
    }
    </script>
</body>
</html>

