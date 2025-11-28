<?php
session_start();
// Путь к config.php может отличаться, используйте правильный путь
require_once '../includes/config.php'; 

// Проверяем авторизацию администратора
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// ------------------------------------------
// 1. ОБРАБОТКА СМЕНЫ СТАТУСА
// ------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['new_status'];

    $allowed_statuses = ['new', 'processing', 'shipped', 'completed', 'cancelled'];
    
    if (in_array($new_status, $allowed_statuses)) {
        $sql_update = "UPDATE orders SET order_status = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $order_id);
        $stmt_update->execute();
        $stmt_update->close();
        
        // Перенаправляем, чтобы избежать повторной отправки формы при обновлении
        header('Location: orders.php');
        exit;
    }
}
// ------------------------------------------

// Получаем список всех заказов
$orders = [];
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

/**
 * Функция для форматирования цены: убирает .00 для целых чисел.
 * @param float $amount Сумма.
 * @return string Отформатированная строка.
 */
function format_price($amount) {
    $amount = (float)$amount;
    if (floor($amount) == $amount) {
        return number_format($amount, 0, '', ' '); // Целое число
    }
    return number_format($amount, 2, '.', ' '); // С десятичными, если есть
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Управление заказами</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5e7e1; }
        h1, h2 { color: #333; }
        .menu a { display: inline-block; padding: 10px 15px; background: #de978d; color: white; margin-right: 10px; text-decoration: none; border-radius: 5px; }
        hr { border: 0; height: 1px; background: #ccc; margin: 20px 0; }
        
        .order-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        .order-table th, .order-table td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: top; }
        .order-table th { background-color: #f2f2f2; }
        
        /* Цветовое оформление статусов */
        .order-status-new { background-color: #fff3cd; color: #856404; font-weight: bold; } /* Желтый */
        .order-status-processing { background-color: #cce5ff; color: #004085; } /* Синий */
        .order-status-shipped { background-color: #d1ecf1; color: #0c5460; } /* Голубой */
        .order-status-completed { background-color: #d4edda; color: #155724; font-weight: bold; } /* Зеленый */
        .order-status-cancelled { background-color: #f8d7da; color: #721c24; } /* Красный */

        /* Стили для деталей заказа */
        .order-details-list { margin: 0; padding-left: 20px; font-size: 0.9em; list-style-type: square; }
        .order-details-list li { margin-bottom: 5px; }

        /* Форма смены статуса */
        .status-form { display: flex; flex-direction: column; gap: 5px; }
        .status-form select { padding: 5px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.9em; }
        .status-form button { padding: 5px; background: #de978d; color: white; border: none; border-radius: 4px; cursor: pointer; transition: background 0.2s; }
        .status-form button:hover { background: #a97a74; }
    </style>
</head>
<body>
    <h1>Панель администратора</h1>
    <div class="menu">
        <a href="index.php">📊 Главная</a>
        <a href="product.php">📦 Управление товарами</a>
        <a href="../index.php">← На сайт</a>
        <a href="logout.php">🚪 Выйти</a>
    </div>

    <hr>
    
    <h2>📜 Управление заказами (Всего: <?php echo count($orders); ?>)</h2>

    <?php if (empty($orders)): ?>
        <p>На данный момент активных заказов нет.</p>
    <?php else: ?>
        <table class="order-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Дата</th>
                    <th>Клиент & Адрес</th>
                    <th>Телефон/Email</th>
                    <th>Метод оплаты</th>
                    <th>Сумма</th>
                    <th>Статус & Действие</th>
                    <th>Детали заказа</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr class="order-status-<?php echo strtolower($order['order_status']); ?>">
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($order['user_name']); ?><br><small><?php echo htmlspecialchars($order['user_address']); ?></small></td>
                    <td><?php echo htmlspecialchars($order['user_phone']); ?><br><?php echo htmlspecialchars($order['user_email']); ?></td>
                    <td><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></td>
                    
                    <td><?php echo format_price($order['total_amount']); ?> тг</td>
                    
                    <td>
                        <div class="status-indicator">
                            <strong><?php echo htmlspecialchars(ucfirst($order['order_status'])); ?></strong>
                        </div>
                        <form method="post" class="status-form">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="new_status">
                                <option value="new" <?php echo ($order['order_status'] == 'new') ? 'selected' : ''; ?>>Новый</option>
                                <option value="processing" <?php echo ($order['order_status'] == 'processing') ? 'selected' : ''; ?>>Обработка</option>
                                <option value="shipped" <?php echo ($order['order_status'] == 'shipped') ? 'selected' : ''; ?>>Отправлен</option>
                                <option value="completed" <?php echo ($order['order_status'] == 'completed') ? 'selected' : ''; ?>>Завершен</option>
                                <option value="cancelled" <?php echo ($order['order_status'] == 'cancelled') ? 'selected' : ''; ?>>Отменен</option>
                            </select>
                            <button type="submit">Обновить</button>
                        </form>
                    </td>
                    <td>
                        <ul class="order-details-list">
                            <?php
                            // Декодируем JSON-строку с деталями заказа
                            $details = json_decode($order['order_details'], true);
                            if (is_array($details)) {
                                foreach ($details as $item) {
                                    // ИСПРАВЛЕНИЕ: Используем format_price для цены товара
                                    $item_price = format_price($item['price'] ?? 0);
                                    echo "<li>{$item['name']} (x{$item['count']}) / Цена: {$item_price} тг</li>";
                                }
                            } else {
                                echo "<li>Ошибка чтения деталей</li>";
                            }
                            ?>
                        </ul>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>