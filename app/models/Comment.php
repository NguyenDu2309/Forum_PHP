<?php
require_once __DIR__ . '/../../config/database.php';

class Comment {
    private $pdo;
    
    public function __construct() {
        $database = new Database();
        $this->pdo = $database->connect();
    }
    
    public function getCommentsByPostId($post_id) {
        $stmt = $this->pdo->prepare("SELECT comments.*, users.username 
                                     FROM comments 
                                     JOIN users ON comments.user_id = users.user_id 
                                     WHERE comments.post_id = ? 
                                     ORDER BY comments.created_at ASC");
        $stmt->execute([$post_id]);
        return $stmt->fetchAll();
    }
    
    public function createComment($user_id, $post_id, $content) {
        $stmt = $this->pdo->prepare("INSERT INTO comments (user_id, post_id, content, created_at) 
                                     VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$user_id, $post_id, $content]);
    }
    
    public function deleteComment($comment_id, $user_id) {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE comment_id = ? AND user_id = ?");
        return $stmt->execute([$comment_id, $user_id]);
    }
    
    public function countCommentsByPostId($post_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
        $stmt->execute([$post_id]);
        return $stmt->fetchColumn();
    }
    
    public function updateComment($comment_id, $user_id, $content) {
        $stmt = $this->pdo->prepare("UPDATE comments SET content = ? WHERE comment_id = ? AND user_id = ?");
        return $stmt->execute([$content, $comment_id, $user_id]);
    }

    
    //The query SELECT COUNT(*) returns a single value (the count)
    // fetchColumn() retrieves that single value directly
    // It's more efficient than using fetch() or fetchAll() when you only need one value
    // Some key points about fetchColumn():
    // It returns the first column from the next row of the result set
    // It's zero-indexed, so fetchColumn(0) gets the first column, fetchColumn(1) gets the second, etc.
    // It automatically moves the cursor to the next row
    // It returns false if there are no more rows
    // It's perfect for aggregate functions like COUNT(), SUM(), MAX(), etc.
}