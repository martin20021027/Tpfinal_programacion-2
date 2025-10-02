<?php
session_start();
session_unset();
session_destroy();
echo "👋 Has cerrado sesión. <a href='login.php'>Inicia sesión</a> o <a href='signup.php'>registrate</a>";
?>
