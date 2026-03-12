<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'supplier') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';
include '../includes/header.php'; // Header with logo, project name, logout button

// Fetch counts for supplier dashboard
$total_orders = $conn->query("SELECT COUNT(*) as total FROM purchase_orders")->fetch_assoc()['total'];
$pending_orders = $conn->query("SELECT COUNT(*) as total FROM purchase_orders WHERE status='pending'")->fetch_assoc()['total'];
$delivered_orders = $conn->query("SELECT COUNT(*) as total FROM purchase_orders WHERE status='delivered'")->fetch_assoc()['total'];
$returned_orders = $conn->query("SELECT COUNT(*) as total FROM purchase_orders WHERE status='returned'")->fetch_assoc()['total'];
?>

<div class="main-content">
    <h1 class="dashboard-title">SUPPLIER DASHBOARD</h1> 

    <div class="dashboard-cards">
        
        <a href="my_orders.php" class="card dashboard-card card-total">
            <div class="card-icon-wrapper" style="color: #3498db;">
                <div class="card-icon">🛒</div>
                <div class="card-content">
                    <p class="card-label">TOTAL ORDERS</p>
                    <p class="card-count"><?php echo $total_orders; ?></p>
                </div>
            </div>
            <div class="card-link" style="color: #3498db;">
                View All Orders <span style="font-size: 1.2em; margin-left: 5px;">→</span>
            </div>
        </a>

        <a href="delivered_orders.php?status=delivered" class="card dashboard-card card-delivered">
            <div class="card-icon-wrapper" style="color: #2ecc71;">
                <div class="card-icon">✅</div>
                <div class="card-content">
                    <p class="card-label">DELIVERED ORDERS</p>
                    <p class="card-count"><?php echo $delivered_orders; ?></p>
                </div>
            </div>
            <div class="card-link" style="color: #2ecc71;">
                View Delivered <span style="font-size: 1.2em; margin-left: 5px;">→</span>
            </div>
        </a>
        
        <a href="pending_orders.php?status=pending" class="card dashboard-card card-pending">
            <div class="card-icon-wrapper" style="color: #e67e22;">
                <div class="card-icon">⏳</div>
                <div class="card-content">
                    <p class="card-label">PENDING ORDERS</p>
                    <p class="card-count"><?php echo $pending_orders; ?></p>
                </div>
            </div>
            <div class="card-link" style="color: #e67e22;">
                View Pending <span style="font-size: 1.2em; margin-left: 5px;">→</span>
            </div>
        </a>

        <!-- NEW Returned Products Card -->
        <a href="returned_orders.php?status=returned" class="card dashboard-card card-returned">
            <div class="card-icon-wrapper" style="color: #e74c3c;">
                <div class="card-icon">↩️</div>
                <div class="card-content">
                    <p class="card-label">RETURNED PRODUCTS</p>
                    <p class="card-count"><?php echo $returned_orders; ?></p>
                </div>
            </div>
            <div class="card-link" style="color: #e74c3c;">
                View Returned <span style="font-size: 1.2em; margin-left: 5px;">→</span>
            </div>
        </a>

    </div>
</div>

<style>
/* Theme Colors */
:root {
    --color-bg-light: #f5f5f5;
    --color-card-bg: #fff;
    --color-text-dark: #333;
    --color-shadow: rgba(0, 0, 0, 0.05);
    --color-footer-dark: #2c3e50;
}

/* General Body Styles */
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif; 
    background: var(--color-bg-light);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    color: var(--color-text-dark);
}

/* Main Container */
.main-content {
    flex: 1;
    max-width: 1200px;
    width: 95%;
    margin: 40px auto; 
    padding: 20px;
    background: var(--color-card-bg); 
    border-radius: 8px;
    box-shadow: 0 0 15px var(--color-shadow);
    height: auto;
}

/* Dashboard Title */
.dashboard-title {
    font-weight: 800;
    font-size: 1.8rem;
    letter-spacing: 1px;
    color: var(--color-text-dark);
    margin: 0 0 40px 0;
    text-align: left; 
    padding-left: 20px;
}

/* Grid Layout */
.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* now 4 cards including returned products */
    gap: 25px;
    padding: 0 20px;
}

/* Individual Cards */
.card {
    background-color: var(--color-card-bg);
    border-radius: 6px;
    padding: 20px 20px;
    transition: box-shadow 0.3s;
    text-decoration: none;
    color: var(--color-text-dark);
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 3px var(--color-shadow);
    display: flex;
    flex-direction: column;
    justify-content: space-between; 
}

.card:hover {
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* Top Section of Card (Icon and Text) */
.card-icon-wrapper {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.card-icon {
    font-size: 30px;
    margin-right: 15px; 
}

/* Metric Count */
.card-count {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.1;
    color: var(--color-text-dark); 
}

/* Descriptive Label */
.card-label {
    font-size: 0.8rem;
    font-weight: 600;
    margin: 0;
    color: #777; 
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Link/Action area */
.card-link {
    font-size: 0.9rem;
    font-weight: 500;
    padding-top: 15px;
    border-top: 1px solid rgba(0, 0, 0, 0.08); 
    transition: color 0.3s;
    text-align: right;
}

.card:hover .card-link {
    text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .dashboard-cards { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .dashboard-cards { grid-template-columns: 1fr; }
    .main-content { margin: 20px auto; padding: 10px; }
}

/* Footer */
footer {
    background-color: var(--color-footer-dark);
    color: #fff;
    text-align: center;
    padding: 15px 0;
    font-size: 0.8rem;
    font-weight: 400;
}
</style>

<footer>
    <p>© <?php echo date("Y"); ?> Stock Management System. All rights reserved.</p>
</footer>
