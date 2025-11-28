<?php
session_start();
// Предполагается, что 'config.php' находится на один уровень выше относительно 'admin/'
require_once '../includes/config.php';

// Проверяем авторизацию администратора
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>📊 Главная панель администратора</title>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5e7e1;
            color: #333;
        }
        h1 {
            color: #de978d;
            border-bottom: 2px solid #de978d;
            padding-bottom: 10px;
        }
        .menu { 
            margin-bottom: 30px; 
        }
        .menu a { 
            display: inline-block; 
            padding: 10px 15px; 
            background: #de978d; 
            color: white; 
            margin-right: 10px; 
            margin-bottom: 10px;
            text-decoration: none; 
            border-radius: 5px; 
            transition: background 0.3s;
        }
        .menu a:hover {
            background: #a97a74;
        }
        .stats-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            flex: 1;
            min-width: 250px;
        }
        .stat-box h3 {
            margin-top: 0;
            color: #555;
        }
        .stat-box .count {
            font-size: 2.5em;
            font-weight: bold;
            color: #de978d;
        }
    </style>
</head>
<body>
    <h1>📊 Панель администратора</h1>
    
    <div class="menu">
        <a href="product.php">📦 Управление товарами</a>
        <a href="orders.php">📜 Управление заказами</a>
        <a href="../index.php">← На сайт</a>
        <a href="logout.php">🚪 Выйти</a>
    </div>
    
    <h2>Статистика магазина</h2>
    <div class="stats-container">
        
        <?php
        $stats = [
            'products' => "SELECT COUNT(*) as total FROM products",
            'orders_total' => "SELECT COUNT(*) as total FROM orders",
            'orders_new' => "SELECT COUNT(*) as total FROM orders WHERE order_status = 'new'",
        ];

        foreach ($stats as $key => $sql) {
            $count = 0;
            if ($result = $conn->query($sql)) {
                $count = $result->fetch_assoc()['total'];
                $result->free();
            }
            
            $title = '';
            $icon = '';
            switch ($key) {
                case 'products':
                    $title = 'Товаров в базе';
                    $icon = '📦';
                    break;
                case 'orders_total':
                    $title = 'Всего заказов';
                    $icon = '🛒';
                    break;
                case 'orders_new':
                    $title = 'Новых заказов';
                    $icon = '🔔';
                    break;
            }
            
            echo "
            <div class='stat-box'>
                <h3>{$icon} {$title}</h3>
                <div class='count'>{$count}</div>
            </div>";
        }

        // Закрываем соединение с базой данных
        if (isset($conn)) {
            $conn->close();
        }
        ?>
    </div>
</body>
</html>