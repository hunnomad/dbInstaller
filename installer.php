<?php
# Fejlesztői cucc, hibajelentés be/ki ----------------------------------------------------
error_reporting(E_ALL);
ini_set("display_errors", 1);
# Fejlesztői cucc, hibajelentés be/ki ----------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'] ?? 'localhost';
    $port = $_POST['port'] ?? 3306;
    $username = $_POST['username'] ?? 'root';
    $password = $_POST['password'] ?? '';
    $dbname = $_POST['dbname'] ?? '';

    // Ellenőrzés: PDO kiterjesztés
    if (!extension_loaded('pdo') || !extension_loaded('pdo_mysql')) {
        die('PDO vagy PDO_MYSQL kiterjesztés nincs engedélyezve.');
    }

    // Ellenőrzés: init.sql fájl létezik-e
    $sqlFile = __DIR__ . '/init.sql';
    if (!file_exists($sqlFile)) {
        die('Hiba: Az init.sql fájl nem található.');
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

        echo "<p>Sikeresen létrehoztuk az adatbázist, a táblákat és az alapadatokat!</p>";
    } catch (PDOException $e) {
        die("Hiba az adatbázis kapcsolat során: " . htmlspecialchars($e->getMessage()));
    } catch (Exception $e) {
        die("Hiba: " . htmlspecialchars($e->getMessage()));
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
                                <label for="host" class="form-label">Hostnév</label>
                                <input type="text" name="host" id="host" class="form-control" value="localhost" required>
                            </div>
                            <div class="mb-3">
                                <label for="port" class="form-label">Port</label>
                                <input type="number" name="port" id="port" class="form-control" value="3306" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Felhasználónév</label>
                                <input type="text" name="username" id="username" class="form-control" value="root" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Jelszó</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="dbname" class="form-label">Adatbázis neve</label>
                                <input type="text" name="dbname" id="dbname" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Telepítés</button>
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
