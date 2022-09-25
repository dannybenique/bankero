<?php
  // Cargamos variables, le damos un nombre a la sesion (por si quisieramos identificarla)
  session_name("BANKero");
  session_start(); // iniciamos sesiones
  session_unset(); // destruimos todas las variables de la sesion.
  session_destroy(); // destruimos la sesion del usuario actual.
  session_start();
  session_regenerate_id(true);
  Header ("Location:../index.php");
?>
