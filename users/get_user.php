<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';

if(!isset($_GET['id'])) {
    die(json_encode(['error' => 'Invalid ID']));
}

$id = (int)$_GET['id'];
$sql = "SELECT * FROM user WHERE id = $id";
$result = $conn->query($sql);

if($user = $result->fetch_assoc()) {
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User not found']);
}