<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Iniciar la sesión si aún no ha sido iniciada
}
function estadoAutenticado() : bool {
   
    // Check if the 'login' key is set and true in the session
    if (isset($_SESSION['login']) && $_SESSION['login']) {
        return true; // User is authenticated
    } 
        return false; // User is not authenticated
    
}

function cerrarSesion() {     
        session_unset(); // Liberar todas las variables de sesión
        session_destroy(); // Destruir la sesión
    
        // Por ejemplo, redirigir al usuario a la página de inicio
        header("Location: ../../index.php");
        exit();
    }
?>