<?php
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

require_once 'db_connect.php';

// Initialize variables
$id = $name = $email = $contact = $address = '';
$message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($action === 'add' || $action === 'update') {
        if (empty($name)) {
            $message = "Supplier name cannot be empty.";
        } else {
            if ($action === 'add') {
                $stmt = $db->prepare("INSERT INTO suppliers (name, email, contact, address) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $contact, $address);
                $successMessage = "Supplier added successfully.";
            } else {
                $stmt = $db->prepare("UPDATE suppliers SET name = ?, email = ?, contact = ?, address = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $name, $email, $contact, $address, $id);
                $successMessage = "Supplier updated successfully.";
            }

            if ($stmt->execute()) {
                $message = $successMessage;
                $id = $name = $email = $contact = $address = ''; // Clear the form
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM suppliers WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Supplier deleted successfully.";
        } else {
            $message = "Error deleting supplier: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch existing suppliers
$result = $db->query("SELECT * FROM suppliers ORDER BY name");
$suppliers = $result->fetch_all(MYSQLI_ASSOC);

// Fetch supplier details for editing
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM suppliers WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($supplier = $result->fetch_assoc()) {
        $id = $supplier['id'];
        $name = $supplier['name'];
        $email = $supplier['email'];
        $contact = $supplier['contact'];
        $address = $supplier['address'];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Suppliers</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        h1, h2 { color: #333; }
        form { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"] { width: 300px; padding: 5px; margin-bottom: 10px; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
        .message { color: #31708f; background-color: #d9edf7; border: 1px solid #bce8f1; padding: 15px; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .action-links a { margin-right: 10px; }
    </style>
</head>
<body>
    <h1>Manage Suppliers</h1>
    
    <?php if ($message): ?>
        <div class="message"><?php echo e($message); ?></div>
    <?php endif; ?>
    
    <form method="post">
        <input type="hidden" name="id" value="<?php echo e($id); ?>">
        <input type="hidden" name="action" value="<?php echo e($id ? 'update' : 'add'); ?>">
        
        <label for="name">Supplier Name:</label>
        <input type="text" id="name" name="name" value="<?php echo e($name); ?>" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo e($email); ?>">
        
        <label for="contact">Contact:</label>
        <input type="text" id="contact" name="contact" value="<?php echo e($contact); ?>">
        
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo e($address); ?>">
        
        <input type="submit" value="<?php echo e($id ? 'Update' : 'Add'); ?> Supplier">
    </form>
    
    <h2>Existing Suppliers</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($suppliers as $supplier): ?>
            <tr>
                <td><?php echo e($supplier['id']); ?></td>
                <td><?php echo e($supplier['name']); ?></td>
                <td><?php echo e($supplier['email']); ?></td>
                <td><?php echo e($supplier['contact']); ?></td>
                <td><?php echo e($supplier['address']); ?></td>
                <td class="action-links">
                    <a href="?edit=<?php echo e($supplier['id']); ?>">Edit</a>
                    <a href="#" onclick="deleteSupplier(<?php echo e($supplier['id']); ?>, '<?php echo json_encode(e($supplier['name'])); ?>')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <p><a href="index.php">Back to Main Menu</a></p>

    <script>
    function deleteSupplier(id, name) {
        if (confirm('Are you sure you want to delete supplier "' + name + '"?')) {
            var form = document.createElement('form');
            form.method = 'post';
            form.innerHTML = '<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="' + id + '">';
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>

