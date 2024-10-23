<?php
require('Class/Connection.php');

// Configuración de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();

// Verificar si hay un usuario en sesión
$user = $_SESSION['user'] ?? null;

Connection::GetInstance();


// Consultas SQL
$selectSQL = 'SELECT * FROM pokemon ORDER BY id, name';
$showColumnsSQL = 'SHOW COLUMNS FROM pokemon';

$select = Connection::ExecuteSentence($selectSQL);
$param =Connection::ExecuteSentence($showColumnsSQL);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokedex</title>
    <link rel="stylesheet" href="../styles/indexPokemons.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>

<body>
    <section class="hero-section">
        <header>
            <div class="box-menu">
                <div class="logo">
                    <img src="../pokeball.png" alt="Pokeball" class="logo-image">
                </div>
                <div class="content-container">
                    <div class="title">
                        <h1>Poke<span>dex</span></h1>
                    </div>
                    <nav class="toggle-nav">
                        <ul class="toggle-nav_list">
                            <li class="toggle-nav_item animation1">
                                <a href="..">Home</a>
                                <div class="animation-underline"></div>
                            </li>
                            <li class="toggle-nav_item animation2">
                                <a href=".">Pokemons</a>
                                <div class="animation-underline"></div>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="box-triangle">
                <div class="triangle"></div>
            </div>
        </header>

        <main>
            <div class="section">
                <h1>Pokemon</h1>
            </div>
            <div class="container">
                <?php if (isset($_GET['op']) && isset($_GET['result'])): ?>
                    <div class="<?= $_GET['result'] > 0 ? 'message' : 'danger' ?>">
                        result: <?= htmlspecialchars($_GET['op']) . ' ' . htmlspecialchars($_GET['result']) ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <h3>Pokemons List</h3>
                </div>
                <table class="table table-striped table-hover" id="tablaProducto">
                    <thead>
                        <tr>
                            <?php 
                            // Obtener nombres de columnas
                            $parameter = [];
                            while ($fila = $param->fetch()) {
                                $parameter[] = $fila[0];
                                echo "<td>{$fila[0]}</td>";
                            }
                            ?>
                            <?php if ($user !== null): ?>
                                <th>Delete</th>
                                <th>Edit</th>
                            <?php endif; ?>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $select->fetch()): ?>
                            <tr>
                                <?php foreach ($parameter as $p): ?>
                                    <td><?= htmlspecialchars($fila[$p]) ?></td>
                                <?php endforeach; ?>

                                <?php if ($user !== null): ?>
                                    <td><a href="destroy.php?id=<?= $fila[$parameter[0]] ?>" class="borrar">Delete</a></td>
                                    <td><a href="edit.php?id=<?= $fila[$parameter[0]] ?>">Edit</a></td>
                                <?php endif; ?>
                                <td><a href="show.php?id=<?= $fila[$parameter[0]] ?>">View</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="row">
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="create.php">Add Pokemon</a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </section>
</body>
</html>
<?php
// Cerrar la conexión
Connection::ClearConnection();
