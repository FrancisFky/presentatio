<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

verifyCsrf();

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');
$subject = trim($_POST['subject'] ?? 'Contact request');

if ($name === '' || $email === '' || $message === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email']);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO contact_messages (sender_name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([$name, $email, $_POST['phone'] ?? '', $subject, $message, 'unread']);

echo json_encode(['success' => true, 'message' => 'Message received']);
