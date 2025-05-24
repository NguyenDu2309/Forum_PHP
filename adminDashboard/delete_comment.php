<?php
session_start();
include '../Partials/db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

if (isset($_GET['comment_id'])) {
    $comment_id = intval($_GET['comment_id']);

    // Xóa reply của comment này
    $sql = "DELETE FROM replies WHERE comment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $stmt->close();

    // Xóa comment
    $sql = "DELETE FROM comments WHERE comment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $stmt->close();

    // Quay lại user_activity
    $redirect = 'user_activity.php';
    if (isset($_GET['user'])) {
        $redirect .= '?user=' . urlencode($_GET['user']);
    }
    header('Location: ' . $redirect);
    exit();
} else {
    header('Location: user_activity.php');
    exit();
}
?>