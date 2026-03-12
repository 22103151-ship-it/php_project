<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'staff') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';
include '../includes/header.php'; // Header and sidebar

// ---------- Handle Search ----------
$search_query = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = $conn->real_escape_string(trim($_GET['search']));
    $products = $conn->query("
        SELECT * FROM products 
        WHERE name LIKE '%$search_query%' OR sku LIKE '%$search_query%'
        ORDER BY name ASC
    ");
} else {
    $products = $conn->query("SELECT * FROM products ORDER BY name ASC");
}
?>

<div class="main-container">
    <!-- Back Button -->
    <a href="dashboard.php" class="back-btn">Back</a>

    <h2 class="page-title">Item List</h2>

    <!-- Search Form -->
    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search by Name or SKU" 
               value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit" class="btn-primary">Search</button>
        <a href="items.php" class="reset-btn">Reset</a>
    </form>

    <!-- Items Table -->
    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    
                    <th>Price</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php if($products->num_rows > 0): ?>
                    <?php while($p = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['name']); ?></td>
                       
                        <td><?php echo number_format($p['price'], 2); ?></td>
                        <td><?php echo $p['stock']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .main-container {
        max-width: 1000px;
        margin: 40px auto;
        background: #fff;
        padding: 20px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .page-title {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        padding: 8px 15px;
        background: #555;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .back-btn:hover {
        background: #333;
    }

    .search-form {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .search-form input[type="text"] {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .btn-primary {
        padding: 8px 15px;
        background: #007BFF;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn-primary:hover {
        background: #0056b3;
    }

    .reset-btn {
        padding: 8px 15px;
        background: #6c757d;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .reset-btn:hover {
        background: #555;
    }

    .table-container {
        overflow-x: auto;
    }

    .styled-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0 auto;
        font-size: 15px;
        border-radius: 5px;
        overflow: hidden;
    }

    .styled-table thead tr {
        background-color: #007BFF;
        color: #ffffff;
        text-align: left;
    }

    .styled-table th, .styled-table td {
        padding: 12px 15px;
        border: 1px solid #ddd;
    }

    .styled-table tbody tr:nth-child(even) {
        background-color: #f3f3f3;
    }

    .styled-table tbody tr:hover {
        background-color: #e9f5ff;
    }
</style>


<!-- <footer style="
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: pending;
    color: white;
    text-align: center;
    padding: 15px 0;
">
    <p>© 2025 Stock Management System. All rights reserved.</p>
</footer> -->
