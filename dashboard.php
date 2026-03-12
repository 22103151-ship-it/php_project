<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'staff') {
    header("Location: ../index.php");
    exit;
}

// NOTE: Ensure your paths for config.php and header.php are correct relative to this file.
include '../config.php';
include '../includes/header.php'; // Assuming your header includes the opening <html>, <head>, and <body> tags

// Check if $conn is successfully established before querying
if (isset($conn)) {
    // Fetch counts from database
    $user_count = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'] ?? 0;
    $supplier_count = $conn->query("SELECT COUNT(*) as total FROM suppliers")->fetch_assoc()['total'] ?? 0;
    $product_count = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'] ?? 0;
    $order_count = $conn->query("SELECT COUNT(*) as total FROM purchase_orders")->fetch_assoc()['total'] ?? 0;
    $delivered_count = $conn->query("SELECT COUNT(*) as total FROM purchase_orders WHERE status='delivered'")->fetch_assoc()['total'] ?? 0;
    $pending_count = $conn->query("SELECT COUNT(*) as total FROM purchase_orders WHERE status='pending'")->fetch_assoc()['total'] ?? 0;
    $returned_count = $conn->query("SELECT COUNT(*) as total FROM purchase_orders WHERE status='returned'")->fetch_assoc()['total'] ?? 0;
} else {
    // Fallback if database connection fails
    $user_count = $supplier_count = $product_count = $order_count = 0;
    $delivered_count = $pending_count = $returned_count = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* --- Modern Flat Design (Navy/Gold Theme) --- */

        /* CSS Variables */
        :root {
            --bg-color: #f4f7fc; /* Light background */
            --main-color: #1a2a47; /* Deep Navy Blue (Primary) */
            --accent-color: #ff9800; /* Vibrant Orange/Gold (Accent) */
            --card-bg: #ffffff;
            --text-color: #34495e; /* Darker text */
            --text-light: #7f8c8d; /* Muted secondary text */
            --border-color: #e6e9ed;
            --shadow-color: rgba(0, 0, 0, 0.08);
            --font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: var(--font-family);
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        .dashboard-header {
            margin-bottom: 35px;
            border-left: 5px solid var(--accent-color);
            padding-left: 15px;
        }

        .dashboard-header h1 {
            font-size: 2.4rem;
            font-weight: 800;
            color: var(--main-color);
            margin: 0;
        }

        .dashboard-header p {
            font-size: 1rem;
            color: var(--text-light);
            margin-top: 5px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .stat-card {
            background-color: var(--card-bg);
            border-radius: 8px; /* Slightly sharper corners */
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 10px var(--shadow-color);
            padding: 25px;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: var(--text-color);
            transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }

        .stat-card .card-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-card .card-icon {
            font-size: 1.8rem;
            height: 55px;
            width: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }
        
        .stat-card .card-details {
            display: flex;
            flex-direction: column;
        }

        .stat-card .card-details h3 {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .card-details .card-count {
            margin: 3px 0 0;
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--main-color);
        }

        .card-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed var(--border-color); /* Dashed separator for a modern touch */
            text-align: right;
        }
        
        .card-footer a {
            text-decoration: none;
            color: var(--accent-color);
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
        }
        
        .card-footer a i {
            margin-left: 5px;
            transition: transform 0.2s;
        }
        
        .stat-card:hover .card-footer a i {
            transform: translateX(3px);
        }


        /* Icon Colors - High contrast colors */
        .icon-users { background-color: #3498db; } /* Blue */
        .icon-suppliers { background-color: #9b59b6; } /* Amethyst */
        .icon-products { background-color: #f39c12; } /* Orange */
        .icon-orders { background-color: #2ecc71; } /* Emerald */
        .icon-delivered { background-color: #1abc9c; } /* Turquoise */
        .icon-pending { background-color: #e67e22; } /* Carrot */
        .icon-sell { background-color: #e74c3c; } /* Alizarin */
        .icon-returned { background-color: #95a5a6; } /* Concrete */

        /* Responsive */
        @media (max-width: 600px) {
            .main-content {
                padding: 15px;
            }
            .dashboard-header h1 {
                font-size: 2rem;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Footer */
        footer {
            background-color: var(--main-color);
            color: #fff;
            text-align: center;
            padding: 15px 0;
            margin-top: 30px;
            font-size: 0.85rem;
            font-weight: 400;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="dashboard-header">
        <h1>STAFF DASHBOARD</h1>
       
    </div>

    <div class="stats-grid">
    <a href="items.php" class="stat-card">
            <div class="card-content">
                <div class="card-icon icon-products"><i class="fa-solid fa-box-archive"></i></div>
                <div class="card-details">
                    <h3>Total Products</h3>
                    <p class="card-count"><?php echo $product_count; ?></p>
                </div>
            </div>
            <div class="card-footer">
                View Products <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <a href="suppliers.php" class="stat-card">
            <div class="card-content">
                <div class="card-icon icon-suppliers"><i class="fa-solid fa-truck-field"></i></div>
                <div class="card-details">
                    <h3>Total Suppliers</h3>
                    <p class="card-count"><?php echo $supplier_count; ?></p>
                </div>
            </div>
            <div class="card-footer">
                Manage Suppliers <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>
        
       

        <a href="sell_product.php" class="stat-card">
            <div class="card-content">
                <div class="card-icon icon-sell"><i class="fa-solid fa-dollar-sign"></i></div>
                <div class="card-details">
                    <h3>Sell a Product</h3>
                    <p class="card-count">&nbsp;</p> </div>
            </div>
            <div class="card-footer">
                Go to Sell Page <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

      
        
       
        
        
    
        
        
    </div>
</div>

<footer>
    <p>© <?php echo date("Y"); ?> Stock Management System. All rights reserved.</p>
</footer>

</body>
</html>