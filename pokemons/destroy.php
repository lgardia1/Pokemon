<?php
require('Class/Connection.php');

// Configuración de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();

// Redirigir si no hay un usuario en sesión
if (!isset($_SESSION['user'])) {
    header('Location:..');
    exit;
}

// Verificar si existe el ID en la solicitud GET
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: .?op=destroyproduct&result=noid');
    exit;
}

// Obtener la instancia de la conexión
$connection = Connection::GetInstance();

try {
    // Preparar la consulta de eliminación
    $deleteSQL = 'DELETE FROM pokemon WHERE id = :id';
    $delete = $connection->prepare($deleteSQL);
    $delete->bindValue(':id', $id, PDO::PARAM_INT);
    
    // Ejecutar la consulta
    $delete->execute();

    // Determinar si la eliminación fue exitosa
    $lastId = $connection->lastInsertId();
} catch (PDOException $e) {
    $lastId = 0;
    // Redirigir en caso de error
    header('Location: ?op=deleteproduct&result=' . $lastId);
    exit;
}

// Redirigir con el resultado
header('Location: ?op=deleteproduct&result=' . $lastId);

// Cerrar la conexión
Connection::ClearConnection();
