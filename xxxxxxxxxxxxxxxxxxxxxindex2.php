<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --background-light: #f0f4f8;
            --text-dark: #2c3e50;
            --card-bg: #ffffff;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: var(--background-light);
            color: var(--text-dark);
        }
        .container {
            width: 95%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            text-align: center;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            border-radius: 0 0 10px 10px;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .dashboard-item {
            background-color: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            position: relative;
            border-top: 4px solid transparent;
        }
        .dashboard-item:nth-child(1) { border-top-color: #3498db; }
        .dashboard-item:nth-child(2) { border-top-color: #2ecc71; }
        .dashboard-item:nth-child(3) { border-top-color: #e74c3c; }
        .dashboard-item:nth-child(4) { border-top-color: #f39c12; }
        .dashboard-item:nth-child(5) { border-top-color: #9b59b6; }
        
        .dashboard-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 22px rgba(0,0,0,0.15);
        }
        .dashboard-item a {
            text-decoration: none;
            color: var(--text-dark);
            padding: 20px;
            display: block;
            text-align: center;
            flex-grow: 1;
        }
        .dashboard-item h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--primary-color);
            transition: color 0.3s ease;
        }
        .dashboard-item:hover h3 {
            color: var(--secondary-color);
        }
        .dashboard-item p {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        .dashboard-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        }
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        @media (max-width: 480px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Quotation System Management Dashboard</h1>
    </header>

    <div class="container">
        <?php
        // Define your system pages
        $pages = [
            [
                'name' => 'Add/Modify Item',
                'file' => 'add_modify_item.php',
                'description' => 'Add or Modify Items in the System List'
            ],
            [
                'name' => 'Item List',
                'file' => 'Item.php',
                'description' => 'Create a New Item'
            ],
            [
                'name' => 'Quotation',
                'file' => 'Quotation.php',
                'description' => 'Create and manage quotations'
            ],
            [
                'name' => 'View Items',
                'file' => 'view_items.php',
                'description' => 'Detailed view of system items'
            ]
        ];
        ?>

        <div class="dashboard-grid">
            <?php foreach ($pages as $page): ?>
                <div class="dashboard-item">
                    <a href="<?php echo htmlspecialchars($page['file']); ?>">
                        <h3><?php echo htmlspecialchars($page['name']); ?></h3>
                        <p><?php echo htmlspecialchars($page['description']); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>