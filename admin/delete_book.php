<?php
include('../config.php');
if (!isAdmin()) { 
    header('Location: ../index.php'); 
    exit; 
}

$id = $_GET['id'] ?? 0;
if ($id > 0) {
    try {
        // 1. Delete from cart table FIRST (this has book_id)
        $pdo->prepare("DELETE FROM cart WHERE book_id = ?")->execute([$id]);
        
        // 2. Delete book (orders probably doesn't have book_id)
        $pdo->prepare("DELETE FROM books WHERE id = ?")->execute([$id]);
        
        header('Location: admin.php?success=1');
        exit;
        echo"Delete Book Successfull!";
    } catch (Exception $e) {
        echo "<h2 style='color:red;'>Delete Error: " . $e->getMessage() . "</h2>";
        echo "<a href='admin.php'>← Back to Admin</a>";
    }
} else {
    header('Location: admin.php');
    exit;
}
?>
