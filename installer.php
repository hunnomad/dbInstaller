<?php
# Developer settings, error report on/off ------------------------------------------------
error_reporting(E_ALL);
ini_set("display_errors", 1);
# Developer settings, error report on/off ------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'] ?? 'localhost';
    $port = $_POST['port'] ?? 3306;
    $username = $_POST['username'] ?? 'root';
    $password = $_POST['password'] ?? '';
    $dbname = $_POST['dbname'] ?? '';

    // Ellenőrzés: PDO kiterjesztés
    if (!extension_loaded('pdo') || !extension_loaded('pdo_mysql')) {
        die('PDO or PDO_MYSQL extension is not enabled.');
    }

    // Ellenőrzés: init.sql fájl létezik-e
    $sqlFile = __DIR__ . '/init.sql';
    if (!file_exists($sqlFile)) {
        die('Error: The init.sql file cannot be found.');
    }

    try {
        // Kapcsolódás az adatbázishoz
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Adatbázis létrehozása, ha nem létezik
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        $pdo->exec("USE `$dbname`");

        // SQL utasítások végrehajtása
        $sqlCommands = file_get_contents($sqlFile);
        $pdo->exec($sqlCommands);

        echo "<p>We have successfully created the database, tables, and master data!</p>";
    } catch (PDOException $e) {
        die("Error during database connection: " . htmlspecialchars($e->getMessage()));
    } catch (Exception $e) {
        die("Error: " . htmlspecialchars($e->getMessage()));
    }
} else {
    // Form megjelenítése
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Installer</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Adatbázis Telepítő</h3>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="host" class="form-label">Hostname</label>
                                <input type="text" name="host" id="host" class="form-control" value="localhost" required>
                            </div>
                            <div class="mb-3">
                                <label for="port" class="form-label">Port</label>
                                <input type="number" name="port" id="port" class="form-control" value="3306" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control" value="root" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="dbname" class="form-label">Database name</label>
                                <input type="text" name="dbname" id="dbname" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Install</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
}
?>
