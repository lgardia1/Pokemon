<?php
require('Class/Connection.php');

// Configuración de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();

// Verificar si hay un usuario en sesión
if (!isset($_SESSION['user'])) {
    header('Location:..');
    exit;
}



// Obtener instancia de conexión
$connection = Connection::getInstance();

// Consulta para obtener las columnas de la tabla 'pokemon'
$showColumnsSQL = 'SHOW COLUMNS FROM pokemon';
$param = Connection::executeSentence($showColumnsSQL);

$values = [];
$isValid = true;



// Recoger información de las columnas
while ($parameter = $param->fetch(PDO::FETCH_ASSOC)) {
    $columnName = $parameter['Field'];

    // Verificar si los campos han sido enviados por POST
    if (!isset($_POST[$columnName])) {
        Connection::clearConnection();
        header('Location:..');
        exit;
    }

    $value = trim($_POST[$columnName]);
    $values[$columnName] = $value;

    // Validar si los valores son numéricos y están en el rango permitido
    if (empty($value) || (is_numeric($value) && ($value < 0 || $value > 1000000))) {
        $isValid = false;
    }
}

// Si la validación falla, redirigir y guardar los valores en la sesión
if (!$isValid) {
    $_SESSION['old'] = $values;
    header('Location: edit.php?op=editpokemon&result=0');
    exit;
}



$setParts = [];
$param = array_slice($values, 1);

foreach ($param as $nameValues => $value) {
    $setParts[] = $nameValues . '=:' . $nameValues;
}

$updateSQL = 'UPDATE pokemon SET ' . implode(', ', $setParts) . ' WHERE id=:id';


$update = Connection::prepareStatements($updateSQL);

var_dump($values);

try {
    // Asignar los valores a los parámetros de la consulta
    foreach ($values as $nameValues => $value) {
        $update->bindValue(':' . $nameValues, $value);
    }
    // Ejecutar la consulta
    $update->execute();

    // Obtener el último ID insertado
    $lastId = $update->rowCount();

    // Redirigir con el resultado de la inserción
    header('Location: index.php?op=updatepokemon&result=' . $lastId);
} catch (PDOException $ex) {
    Connection::clearConnection();
    exit;
}

// Limpiar la conexión
Connection::clearConnection();