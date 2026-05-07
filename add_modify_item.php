<?php
require_once 'Item.php';
require_once 'Quotation.php';
require_once 'db_connect.php';

$item = new Item($db);
$quotation = new Quotation($db);

$itemId = $_GET['id'] ?? null;
$searchTerm = $_GET['search'] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $itemId = $_POST['item_id'] ?? null;
    $itemName = $_POST['item_name'] ?? '';
    $quantity = $_POST['quantity'] ?? '';

    if ($action === 'delete') {
        if ($item->deleteItem($itemId)) {
            header("Location: view_items.php?message=Item deleted successfully");
            exit();
        } else {
            $message = "Error deleting item";
        }
    } elseif ($action === 'save') {
        if ($itemId) {
            $item->updateItem($itemId, $itemName, $quantity);
        } else {
            $itemId = $item->addItem($itemName, $quantity);
        }

        // Handle supplier quotations
        $supplierIds = $_POST['supplier_id'] ?? [];
        $costs = $_POST['cost'] ?? [];

        $quotation->deleteQuotationsForItem($itemId);

        foreach ($supplierIds as $index => $supplierId) {
            if (!empty($supplierId) && isset($costs[$index])) {
                $quotation->addQuotation($itemId, $supplierId, $costs[$index]);
            }
        }

        header("Location: view_items.php?message=Item saved successfully");
        exit();
    }
}

// Fetch item details if editing
$itemToEdit = null;
if ($itemId) {
    $itemToEdit = $item->getItem($itemId);
    $existingQuotations = $quotation->getQuotations($itemId);
}

// Fetch suppliers for dropdown
$suppliers = $db->query("SELECT * FROM suppliers ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Search functionality
$searchResults = [];
if (!empty($searchTerm)) {
    $searchResults = $item->searchItems($searchTerm);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add/Modify Item</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        h1, h2 { color: #333; }
        form { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="number"], select { width: 300px; padding: 5px; margin-bottom: 10px; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
        .message { color: #31708f; background-color: #d9edf7; border: 1px solid #bce8f1; padding: 15px; margin-bottom: 20px; }
        .supplier-row { margin-bottom: 10px; }
        #add-supplier { margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Add/Modify Item</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="get" action="">
        <label for="search">Search Item:</label>
        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
        <input type="submit" value="Search">
    </form>

    <?php if (!empty($searchResults)): ?>
        <h2>Search Results</h2>
        <table>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
            <?php foreach ($searchResults as $result): ?>
                <tr>
                    <td><?php echo htmlspecialchars($result['name']); ?></td>
                    <td><?php echo htmlspecialchars($result['quantity']); ?></td>
                    <td>
                        <a href="?id=<?php echo $result['id']; ?>">Edit</a>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="item_id" value="<?php echo $result['id']; ?>">
                            <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this item?');">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="item_id" value="<?php echo $itemToEdit ? $itemToEdit['id'] : ''; ?>">
        
        <label for="item_name">Item Name:</label>
        <input type="text" id="item_name" name="item_name" value="<?php echo $itemToEdit ? htmlspecialchars($itemToEdit['name']) : ''; ?>" required>
        
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo $itemToEdit ? htmlspecialchars($itemToEdit['quantity']) : ''; ?>" required>
        
        <h2>Supplier Quotations</h2>
        <div id="supplier-quotations">
            <?php
            if ($itemToEdit && !empty($existingQuotations)) {
                foreach ($existingQuotations as $index => $quotation) {
                    echo '<div class="supplier-row">';
                    echo '<select name="supplier_id[]">';
                    foreach ($suppliers as $supplier) {
                        $selected = ($supplier['id'] == $quotation['supplier_id']) ? 'selected' : '';
                        echo '<option value="' . $supplier['id'] . '" ' . $selected . '>' . htmlspecialchars($supplier['name']) . '</option>';
                    }
                    echo '</select>';
                    echo '<input type="number" step="0.01" name="cost[]" value="' . htmlspecialchars($quotation['cost']) . '" required>';
                    echo '<button type="button" onclick="removeSupplier(this)">Remove</button>';
                    echo '</div>';
                }
            } else {
                echo '<div class="supplier-row">';
                echo '<select name="supplier_id[]">';
                foreach ($suppliers as $supplier) {
                    echo '<option value="' . $supplier['id'] . '">' . htmlspecialchars($supplier['name']) . '</option>';
                }
                echo '</select>';
                echo '<input type="number" step="0.01" name="cost[]" required>';
                echo '<button type="button" onclick="removeSupplier(this)">Remove</button>';
                echo '</div>';
            }
            ?>
        </div>
        <button type="button" id="add-supplier">Add Another Supplier</button>
        
        <input type="submit" value="Save Item">
    </form>

    <script>
    function addSupplier() {
        var container = document.getElementById('supplier-quotations');
        var newRow = document.createElement('div');
        newRow.className = 'supplier-row';
        newRow.innerHTML = `
            <select name="supplier_id[]">
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?php echo $supplier['id']; ?>"><?php echo htmlspecialchars($supplier['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" step="0.01" name="cost[]" required>
            <button type="button" onclick="removeSupplier(this)">Remove</button>
        `;
        container.appendChild(newRow);
    }

    function removeSupplier(button) {
        button.parentElement.remove();
    }

    document.getElementById('add-supplier').addEventListener('click', addSupplier);
    </script>

    <p><a href="view_items.php">Back to Items List</a></p>
</body>
</html>

