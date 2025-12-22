<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'supplier') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

$supplier_id = $_SESSION['user_id']; // Supplier ID from session

// Check if order_id is provided
if (isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];

    // Ensure the order belongs to this supplier and is pending
    $order_check = $conn->query("SELECT * FROM purchase_orders WHERE id=$order_id AND supplier_id=$supplier_id AND status='pending'");

    if ($order_check->num_rows > 0) {
        // Update status to delivered
        $conn->query("UPDATE purchase_orders SET status='delivered' WHERE id=$order_id");
    }
}

// Redirect back to supplier dashboard
header("Location: dashboard.php");
exit;
