<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    exit("Unauthorized");
}

if(isset($_POST['product_id'], $_POST['supplier_id'], $_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $supplier_id = intval($_POST['supplier_id']);
    $quantity = intval($_POST['quantity']);
    $status = "Pending";
    $created_at = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO purchase_orders (product_id, supplier_id, quantity, status, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $product_id, $supplier_id, $quantity, $status, $created_at);
    if($stmt->execute()){
        echo "success";
    } else {
        echo "Failed to create order";
    }
    $stmt->close();
} else {
    echo "Missing data";
}
?>



