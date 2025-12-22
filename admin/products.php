<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Include config.php and check DB connection
include '../config.php';
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

include '../includes/header.php'; // Keep your header

// ---------------- Add Product ----------------
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $supplier_id = $_POST['supplier_id'] ?? 0;
    $created_at = date('Y-m-d H:i:s');

    // Case-insensitive check for duplicate product name
    $check = $conn->prepare("SELECT id FROM products WHERE LOWER(name) = LOWER(?)");
    $check->bind_param("s", $name);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<p style='color:red;'>Product already exists!</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, price, stock, supplier_id, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdiis", $name, $price, $stock, $supplier_id, $created_at);

        if($stmt->execute()){
            echo "<p style='color:green;'>Product added successfully!</p>";
        } else {
            echo "<p style='color:red;'>Product add not possible. ".$stmt->error."</p>";
        }

        $stmt->close();
    }

    $check->close();
}

// ---------------- Edit Product ----------------
if (isset($_POST['edit_product'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $supplier_id = $_POST['supplier_id'] ?? 0;

    $check = $conn->prepare("SELECT id FROM products WHERE LOWER(name) = LOWER(?) AND id <> ?");
    $check->bind_param("si", $name, $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<p style='color:red;'>Another product with the same name already exists!</p>";
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, stock=?, supplier_id=? WHERE id=?");
        $stmt->bind_param("sdiii", $name, $price, $stock, $supplier_id, $id);

        if($stmt->execute()){
            echo "<p style='color:green;'>Product updated successfully!</p>";
        } else {
            echo "<p style='color:red;'>Product update failed. ".$stmt->error."</p>";
        }

        $stmt->close();
    }

    $check->close();
}

// ---------------- Delete Product ----------------
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        echo "<p style='color:red;'>Product deleted successfully!</p>";
    } else {
        echo "<p style='color:red;'>Delete failed. ".$stmt->error."</p>";
    }
    $stmt->close();
}

// ---------------- Fetch Products ----------------
$result = $conn->query("SELECT p.*, s.name AS supplier_name FROM products p LEFT JOIN suppliers s ON p.supplier_id = s.id ORDER BY p.id DESC");

// ---------------- If editing, fetch product details ----------------
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_product = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<div style="max-width:900px; margin:20px auto; padding:20px; background:#f8f8f8; border-radius:8px;">

    <!-- Back Button -->
    <a href="dashboard.php" style="display:inline-block; margin-bottom:20px; padding:8px 15px; background:#555; color:white; border-radius:5px; text-decoration:none;">Back </a>

    <h2>Manage Products</h2>

    <!-- Add / Edit Product Form -->
    <form method="POST" autocomplete="off" style="margin-bottom: 30px;">
        <h3><?php echo $edit_product ? "Edit Product" : "Add New Product"; ?></h3>

        <input type="hidden" name="id" value="<?php echo $edit_product['id'] ?? ''; ?>">

        <input type="text" name="name" placeholder="Product Name" required 
               value="<?php echo $edit_product['name'] ?? ''; ?>" 
               style="width:100%; padding:8px; margin:5px 0;" autocomplete="off">

        <input type="number" step="0.01" name="price" placeholder="Price" required 
               value="<?php echo $edit_product['price'] ?? ''; ?>" 
               style="width:100%; padding:8px; margin:5px 0;" autocomplete="off">

        <input type="number" name="stock" placeholder="Stock" required 
               value="<?php echo $edit_product['stock'] ?? ''; ?>" 
               style="width:100%; padding:8px; margin:5px 0;" autocomplete="off">

        <!-- Supplier Dropdown -->
        <select name="supplier_id" style="width:100%; padding:8px; margin:5px 0;">
            <option value="0">Select Supplier</option>
            <?php 
            $suppliers = $conn->query("SELECT * FROM suppliers ORDER BY name ASC");
            while($supplier = $suppliers->fetch_assoc()): ?>
                <option value="<?php echo $supplier['id']; ?>" <?php 
                    if(isset($edit_product['supplier_id']) && $edit_product['supplier_id'] == $supplier['id']) echo 'selected'; 
                ?>>
                    <?php echo htmlspecialchars($supplier['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit" name="<?php echo $edit_product ? 'edit_product' : 'add_product'; ?>" 
                style="padding:10px 20px; background:#28a745; color:white; border:none; border-radius:5px; cursor:pointer;">
            <?php echo $edit_product ? 'Update Product' : 'Add Product'; ?>
        </button>
        <?php if ($edit_product): ?>
            <a href="products.php" style="margin-left:10px; color:#555;">Cancel</a>
        <?php endif; ?>
    </form>

    <!-- Products Table -->
    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse:collapse; background:white; text-align:left;">
        <tr style="background:#ddd;">
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Supplier</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo number_format($row['price'],2); ?></td>
            <td><?php echo $row['stock']; ?></td>
            <td><?php echo htmlspecialchars($row['supplier_name'] ?? 'N/A'); ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <a href="products.php?edit=<?php echo $row['id']; ?>">Edit</a> |
                <a href="products.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
