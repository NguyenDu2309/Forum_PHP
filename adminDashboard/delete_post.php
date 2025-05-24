<?php
session_start();
include '../Partials/db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

if (isset($_GET['thread_id'])) {
    $thread_id = intval($_GET['thread_id']);

    // Xóa reply của các comment thuộc thread này
    $sql = "DELETE FROM replies WHERE comment_id IN (SELECT comment_id FROM comments WHERE thread_comment_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $thread_id);
    $stmt->execute();
    $stmt->close();

    // Xóa comment thuộc thread này
    $sql = "DELETE FROM comments WHERE thread_comment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $thread_id);
    $stmt->execute();
    $stmt->close();

    // Xóa thread
    $sql = "DELETE FROM thread WHERE thread_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $thread_id);
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