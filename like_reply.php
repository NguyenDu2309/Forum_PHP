<?php
session_start();
include('Partials/db_connection.php');
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) exit;
$user_id = $_SESSION['user_id'];
$reply_id = intval($_POST['reply_id']);

$check_query = "SELECT 1 FROM reply_likes WHERE reply_id = ? AND user_id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ii", $reply_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $delete_query = "DELETE FROM reply_likes WHERE reply_id = ? AND user_id = ?";
    $stmt_delete = $conn->prepare($delete_query);
    $stmt_delete->bind_param("ii", $reply_id, $user_id);
    $stmt_delete->execute();
    $stmt_delete->close();
    $liked = 0;
} else {
    $insert_query = "INSERT INTO reply_likes (reply_id, user_id) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param("ii", $reply_id, $user_id);
    $stmt_insert->execute();
    $stmt_insert->close();
    $liked = 1;
}
$stmt->close();

$count_query = "SELECT COUNT(*) FROM reply_likes WHERE reply_id = ?";
$stmt_count = $conn->prepare($count_query);
$stmt_count->bind_param("i", $reply_id);
$stmt_count->execute();
$stmt_count->bind_result($newLikes);
$stmt_count->fetch();
$stmt_count->close();

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'newLikes' => $newLikes
]);
?>