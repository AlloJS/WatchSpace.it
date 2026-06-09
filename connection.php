<?php
try {
    $dbPath = __DIR__ . '/database.db';

    $pdo = new PDO("sqlite:" . $dbPath);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // IMPORTANTISSIMO per SQLite
    $pdo->exec("PRAGMA foreign_keys = ON");

} catch (PDOException $e) {
    die("Errore di connessione al database: " . $e->getMessage());
}