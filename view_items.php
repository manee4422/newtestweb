<?php
require_once 'db_connect.php';
require_once 'Item.php';
require_once 'Quotation.php';

$item = new Item($db);
$quotation = new Quotation($db);

$items = $item->getItems();
$message = $_GET['message'] ?? '';

// Fetch all unique supplier names
$allSuppliers = $db->query("SELECT DISTINCT id, name FROM suppliers ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Items - Quotation Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 text-gray-900 font-sans">
    <div class="container mx-auto px-4 py-8">
        <header class="mb-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-blue-600">Items List</h1>
                <img src="/quotation_management_system/public/images/logo.png" alt="Your Company Logo" class="h-12 w-auto">
            </div>
        </header>

        <?php if ($message): ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <?php if (empty($items)): ?>
            <p class="text-lg mb-4">No items found. <a href="add_modify_item.php" class="text-blue-600 hover:underline">Add a new item</a></p>
        <?php else: ?>
            <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-blue-500 text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Item Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Quantity</th>
                                <?php foreach ($allSuppliers as $supplier): ?>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider"><?php echo htmlspecialchars($supplier['name']); ?></th>
                                <?php endforeach; ?>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($items as $item): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <?php 
                                    $quotations = $quotation->getQuotations($item['id']);
                                    $quotationMap = [];
                                    foreach ($quotations as $q) {
                                        $quotationMap[$q['supplier_id']] = $q['cost'];
                                    }
                                    foreach ($allSuppliers as $supplier):
                                        $cost = $quotationMap[$supplier['id']] ?? 'N/A';
                                    ?>
                                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($cost); ?></td>
                                    <?php endforeach; ?>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="add_modify_item.php?id=<?php echo $item['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i data-lucide="edit" class="inline-block w-5 h-5 mr-1"></i>Edit
                                        </a>
                                        <form method="post" action="add_modify_item.php" class="inline-block">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this item?');">
                                                <i data-lucide="trash-2" class="inline-block w-5 h-5 mr-1"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="flex justify-between items-center">
            <a href="add_modify_item.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                Add New Item
            </a>
            <a href="index.php" class="text-blue-600 hover:underline inline-flex items-center">
                <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                Back to Main Menu
            </a>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

