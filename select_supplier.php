<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php';
require_once 'Item.php';
require_once 'Quotation.php';

$item = new Item($db);
$quotation = new Quotation($db);

// Validate item_id
if (!isset($_GET['item_id']) || !is_numeric($_GET['item_id'])) {
    die("Invalid item ID");
}

$itemId = intval($_GET['item_id']);
$quotations = $quotation->getQuotations($itemId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['supplier_id']) || !isset($_POST['cost'])) {
        die("Missing supplier_id or cost");
    }
    $selectedSupplierId = intval($_POST['supplier_id']);
    $selectedCost = floatval($_POST['cost']);
    
    $result = $quotation->selectQuotation($itemId, $selectedSupplierId, $selectedCost);
    if ($result) {
        // Delete the item after selecting the supplier
        if ($item->deleteItem($itemId)) {
            header("Location: view_items.php?message=Item+selected+and+removed");
            exit();
        } else {
            echo "Error deleting item: " . $db->error;
        }
    } else {
        echo "Error selecting quotation: " . $db->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Supplier</title>
</head>
<body>
    <h1>Select Supplier</h1>
    <?php if (empty($quotations)): ?>
        <p>No quotations found for this item.</p>
    <?php else: ?>
        <form method="post">
            <select name="supplier_id" required>
                <?php foreach ($quotations as $q): ?>
                    <option value="<?php echo htmlspecialchars($q['supplier_id']); ?>" data-cost="<?php echo htmlspecialchars($q['cost']); ?>">
                        <?php echo htmlspecialchars($q['supplier_name']); ?> - <?php echo htmlspecialchars($q['cost']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="cost" id="selected_cost">
            <input type="submit" value="Select Supplier">
        </form>

        <script>
            document.querySelector('select[name="supplier_id"]').addEventListener('change', function() {
                document.getElementById('selected_cost').value = this.options[this.selectedIndex].dataset.cost;
            });
            // Set initial value
            document.getElementById('selected_cost').value = document.querySelector('select[name="supplier_id"]').options[0].dataset.cost;
        </script>
    <?php endif; ?>
    <p><a href="view_items.php">Back to Items List</a></p>
</body>
</html>

