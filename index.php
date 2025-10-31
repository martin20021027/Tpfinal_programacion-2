<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante - Inicio</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            background-image: url('Imagenes/foto2.jpg');   
            background-size: cover;       
            background-position: center; 
            background-repeat: no-repeat;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            text-align: center;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.2);
        }
        h1 {
            margin-bottom: 30px;
            color: #333;
        }
        .btn {
            display: inline-block;
            margin: 10px;
            padding: 12px 25px;
            font-size: 16px;
            text-decoration: none;
            color: #fff;
            background: #007BFF;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #28a745;
        }
        .btn-secondary:hover {
            background: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍴 Bienvenido</h1>
        <a href="login.php" class="btn">Iniciar Sesión</a>
        <a href="signup.php" class="btn btn-secondary">Registrarse</a>
    </div>
</body>
</html>

