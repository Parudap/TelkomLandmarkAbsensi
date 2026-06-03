<?php
// Simple DB check script for ketua.it@telkom.com
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
$checkPassword = 'KetuaIT123!';

$stmt = $pdo->prepare('SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$row = $stmt->fetch();

if (!$row) {
    echo json_encode(['exists' => false]);
    exit(0);
}

$hash = $row['password'];
$matches = false;
if ($hash) {
    // Laravel bcrypt uses password_hash compatible with password_verify
    $matches = password_verify($checkPassword, $hash);
}

echo json_encode([
    'exists' => true,
    'id' => $row['id'],
    'email' => $row['email'],
    'name' => $row['name'],
    'password_hash' => $hash,
    'password_matches' => $matches,
]);
