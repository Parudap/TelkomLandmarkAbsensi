<?php
// Set password for ketua.it@telkom.com
$host = '127.0.0.1';
$port = 3306;
$db = 'telkom_landmark_absensi';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['error' => 'db_connect_error', 'message' => $e->getMessage()]);
    exit(1);
}

$email = 'ketua.it@telkom.com';
$newPassword = 'KetuaIT123!';
$hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $pdo->prepare('UPDATE users SET password = :hash WHERE email = :email');
$updated = $stmt->execute(['hash' => $hash, 'email' => $email]);

if ($updated) {
    echo json_encode(['updated' => true]);
} else {
    echo json_encode(['updated' => false]);
}
