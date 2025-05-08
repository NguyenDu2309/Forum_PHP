<?php
require_once __DIR__ . '/../../config/database.php';

class Post {
    private $pdo;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->connect();
    }

    public function getAllPosts() {
        $stmt = $this->pdo->query("SELECT posts.*, users.username, modules.module_name 
                                    FROM posts 
                                    JOIN users ON posts.user_id = users.user_id 
                                    JOIN modules ON posts.module_id = modules.module_id 
                                    ORDER BY posts.created_at DESC");
        return $stmt->fetchAll();
    }

    public function getPostsByModule($module_id) {
        $stmt = $this->pdo->prepare("SELECT posts.*, users.username, modules.module_name 
                                    FROM posts 
                                    JOIN users ON posts.user_id = users.user_id 
                                    JOIN modules ON posts.module_id = modules.module_id 
                                    WHERE posts.module_id = ?
                                    ORDER BY posts.created_at DESC");
        $stmt->execute([$module_id]);
        return $stmt->fetchAll();
    }

    public function getPostById($post_id) {
        $stmt = $this->pdo->prepare("SELECT posts.*, users.username, modules.module_name 
                                     FROM posts 
                                     JOIN users ON posts.user_id = users.user_id 
                                     JOIN modules ON posts.module_id = modules.module_id 
                                     WHERE posts.post_id = ?");
        $stmt->execute([$post_id]);
        return $stmt->fetch();
    }
    public function getUserPost($user_id) {
            $stmt = $this->pdo->prepare("SELECT posts.*, users.username, modules.module_name
            FROM posts
            JOIN users ON posts.user_id = users.user_id
            JOIN modules ON posts.module_id = modules.module_id
            WHERE posts.user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
        
    // this is for user CRUD
    public function createPost($user_id, $module_id, $title, $content, $image = null) {
        $stmt = $this->pdo->prepare("INSERT INTO posts (user_id, module_id, title, content, image, created_at) 
                                     VALUES (?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([$user_id, $module_id, $title, $content, $image]);
    }
    

    public function updatePost($post_id, $title, $content, $module_id, $image_path, $user_id) {
        $stmt = $this->pdo->prepare("UPDATE posts SET title = ?, content = ?, module_id = ?, image = ? WHERE post_id = ? AND user_id = ?");
        return $stmt->execute([$title, $content, $module_id, $image_path, $post_id, $user_id]);
    }

    
    public function deletePost($post_id, $user_id) {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE post_id = ? AND user_id = ?");
        return $stmt->execute([$post_id, $user_id]);
    }
    

    
    public function countAllPosts() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM posts");
        return $stmt->fetchColumn();
    }

    public function countAllPostsByUser($user_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }
    
    public function countPostsByModule($module_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM posts WHERE module_id = ?");
        $stmt->execute([$module_id]);
        return $stmt->fetchColumn();
    }
    
    // THIS IS FOR admin Crud
    public function deletePostFromAdmin($post_id) {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE post_id = ?");
        return $stmt->execute([$post_id]);
    }

    public function searchPosts($search) {
        $searchTerm = '%' . $search . '%';
        $query = "SELECT posts.*, users.username, modules.module_name 
                FROM posts 
                JOIN users ON posts.user_id = users.user_id 
                JOIN modules ON posts.module_id = modules.module_id 
                WHERE posts.title LIKE ? OR posts.content LIKE ?
                ORDER BY posts.created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }

    // Get unassigned posts (posts that need to be assigned to users and modules)
    public function getUnassignedPosts() {
        $stmt = $this->pdo->prepare("SELECT * FROM posts 
                                    WHERE user_id IS NULL OR module_id IS NULL
                                    ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Assign a post to a user and module
    public function assignPost($post_id, $user_id, $module_id) {
        $stmt = $this->pdo->prepare("UPDATE posts SET user_id = ?, module_id = ? WHERE post_id = ?");
        return $stmt->execute([$user_id, $module_id, $post_id]);
    }
}
?>
