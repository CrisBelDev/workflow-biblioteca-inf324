<?php
require 'conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'registro':
            // Procesar registro
            break;
            
        case 'prestamo':
            // Procesar préstamo
            break;
            
        default:
            header('Location: index.php');
            exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>