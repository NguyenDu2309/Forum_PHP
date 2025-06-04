<?php
// like_comment.php (Simplified - Not Recommended for Production)
session_start();
include('Partials/db_connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để thích bình luận.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$comment_id = intval($_POST['comment_id']);

// Kiểm tra đã like chưa
$check_query = "SELECT * FROM comment_likes WHERE comment_id = ? AND user_id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ii", $comment_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Nếu đã like, thì xóa like (unlike)
    $delete_query = "DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?";
    $stmt_delete = $conn->prepare($delete_query);
    $stmt_delete->bind_param("ii", $comment_id, $user_id);
    $stmt_delete->execute();
    $stmt_delete->close();
    $liked = 0;
} else {
    // Nếu chưa like, thì thêm like
    $insert_query = "INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param("ii", $comment_id, $user_id);
    $stmt_insert->execute();
    $stmt_insert->close();
    $liked = 1;
}
$stmt->close();

// Đếm lại tổng số like cho comment này
$count_query = "SELECT COUNT(*) AS total FROM comment_likes WHERE comment_id = ?";
$stmt_count = $conn->prepare($count_query);
$stmt_count->bind_param("i", $comment_id);
$stmt_count->execute();
$stmt_count->bind_result($newLikes);
$stmt_count->fetch();
$stmt_count->close();

// Cập nhật lại số like trong bảng comments (nếu có cột likes)
$update_query = "UPDATE comments SET likes = ? WHERE comment_id = ?";
$stmt_update = $conn->prepare($update_query);
$stmt_update->bind_param("ii", $newLikes, $comment_id);
$stmt_update->execute();
$stmt_update->close();

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'newLikes' => $newLikes
]);
?>