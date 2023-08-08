<?php
  // Cargamos variables, le damos un nombre a la sesion (por si quisieramos identificarla)
  session_name("BANKero");
  session_start(); // iniciamos sesiones
  session_unset(); // destruimos todas las variables de la sesion.
  session_destroy(); // destruimos la sesion del usuario actual.
  setcookie(session_name("BANKero"), "", time() - 3600, "/");
  header ("Location:../index.php");
?>
