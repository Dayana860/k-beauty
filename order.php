<?php
session_start();
// Предполагается, что 'includes/config.php' содержит подключение к БД ($conn)
require_once 'includes/config.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$order_placed = false;

// ОБРАБОТКА ОТПРАВКИ ЗАКАЗА ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $payment_method = $_POST['payment_method'] ?? 'cash'; // Получаем метод оплаты

    // Получаем данные корзины из скрытого поля 
    $order_details_json = $_POST['order_details_json'] ?? '[]';
    $total_amount = $_POST['total_amount'] ?? 0.00; 
    
    // Определяем статус заказа
    $order_status = 'new'; 

    // Вставляем заказ в базу данных (теперь 9 плейсхолдеров '?' в VALUES)
    $sql = "INSERT INTO orders (user_id, user_name, user_email, user_phone, user_address, order_details, total_amount, order_status, payment_method) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"; 

    $stmt = $conn->prepare($sql);
    
    // ИСПРАВЛЕНИЕ bind_param:
    // Типы: i, s, s, s, s, s, d, s, s (9 переменных)
    // Переменные: $user_id, $name, $email, $phone, $address, $order_details_json, $total_amount, $order_status, $payment_method
    $stmt->bind_param("isssssdss", $user_id, $name, $email, $phone, $address, $order_details_json, $total_amount, $order_status, $payment_method);

    if ($stmt->execute()) {
        $order_placed = true;
        
        // Очищаем корзину в браузере через JavaScript после перезагрузки страницы
        // (Это выполнится только после перезагрузки страницы, когда PHP-код будет завершен)
    } else {
        $error_message = "Ошибка при оформлении заказа: " . $stmt->error;
    }
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа</title>
    <style>
        /* Общие стили */
        body { 
            font-family: 'Times New Roman', Times, serif, sans-serif; 
            background: #f5e7e1; 
            padding: 0; 
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Заголовок и приветствие */
        h1 {
            color: #333;
            margin-top: 50px;
            font-size: 2.5em;
            text-align: center;
        }
        p {
            color: #555;
            text-align: center;
        }

        /* Контейнер формы */
        .order-form { 
            max-width: 600px; 
            width: 90%;
            margin: 20px auto; 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Элементы формы */
        .order-form input, 
        .order-form textarea, 
        .order-form select {
            width: 100%; 
            padding: 12px; 
            margin-bottom: 15px; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        .order-form input:focus, 
        .order-form textarea:focus, 
        .order-form select:focus {
            border-color: #de978d; /* Фокусировка цветом бренда */
            outline: none;
        }
        .order-form textarea {
            resize: vertical;
        }
        
        /* Заголовки внутри формы */
        .order-form h2, .order-form h3 {
            color: #de978d;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            margin-top: 25px;
            margin-bottom: 20px;
        }

        /* Секция заказа */
        #order-items ul {
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
        }
        #order-items li {
            padding: 10px 15px;
            border-bottom: 1px dashed #eee;
            font-size: 0.95em;
            display: flex;
            justify-content: space-between;
        }
        #order-items li:last-child {
            border-bottom: none;
        }
        
        /* Итоговая сумма */
        .order-form p:nth-of-type(1) { /* Стиль для "Итого к оплате" */
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
            text-align: left;
            padding-top: 10px;
            border-top: 2px solid #de978d;
        }
        #total-amount-display {
            color: #de978d;
            font-size: 1.3em;
            margin-left: 5px;
        }

        /* Кнопка */
        .order-form button {
            background-color: #de978d; 
            color: white; 
            border: none;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            margin-top: 20px;
            transition: background-color 0.3s ease, transform 0.1s;
        }
        .order-form button:hover {
            background-color: #a97a74;
            transform: translateY(-1px);
        }

        /* Сообщение об успехе */
        .success-message {
            background-color: #e6f7ef; /* Светло-зеленый */
            color: #0c6b3f; /* Темно-зеленый текст */
            border: 1px solid #b7e3d1; 
            padding: 20px; 
            border-radius: 10px; 
            margin: 40px auto;
            max-width: 600px;
            text-align: center;
            font-size: 1.1em;
        }
        .success-message a {
            color: #de978d;
            font-weight: bold;
            text-decoration: none;
        }
        .success-message a:hover {
            text-decoration: underline;
        }

        .empty-cart-message {
            text-align: center;
            padding: 30px;
            color: #666;
        }
        
    </style>
</head>
<body>
    <h1>📦 Оформление заказа</h1>
    <p>Добро пожаловать, <?php echo htmlspecialchars($user_name); ?>!</p>

    <?php if ($order_placed): ?>
        <div class="success-message">
            <p>✅ Ваш заказ успешно оформлен! Мы свяжемся с вами в ближайшее время.</p>
            <p>Вы можете вернуться к <a href="../index.php">главной странице</a> или <a href="my_orders.php">посмотреть свои заказы</a>.</p>
        </div>
        <script>
            // Это гарантирует, что корзина очистится и для браузеров, которые не всегда сразу обрабатывают PHP echo <script>
            localStorage.removeItem('cart');
        </script>
    <?php else: ?>
        <?php if (isset($error_message)): ?>
            <div style="color: red; margin-bottom: 15px; text-align: center;"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="order-form">
            <h2>Проверка заказа</h2>
            <div id="order-items"></div>
            <p>Итого к оплате: <span id="total-amount-display">0 тг</span></p>

            <form method="post">
                <h2>Контактные данные</h2>
                <p><input type="text" name="name" placeholder="Имя" value="<?php echo htmlspecialchars($user_name); ?>" required></p>
                <p><input type="email" name="email" placeholder="Email" required></p>
                <p><input type="tel" name="phone" placeholder="Телефон" required></p>
                <p><textarea name="address" placeholder="Адрес доставки" required></textarea></p>
                
                <h3>Способ оплаты</h3>
                <p>
                    <select name="payment_method" required>
                        <option value="cash">Наличными при получении</option>
                        <option value="card" disabled>Онлайн картой (в разработке)</option>
                    </select>
                </p>

                <input type="hidden" name="order_details_json" id="order-details-json">
                <input type="hidden" name="total_amount" id="total-amount-input">

                <button type="submit" id="submit-order-btn">✅ Подтвердить заказ</button>
            </form>
        </div>
    <?php endif; ?>

    <script>
    // Вспомогательная функция для форматирования числа
    function formatPrice(price) {
        const num = parseFloat(price);
        // Если число целое, убираем .00
        if (num % 1 === 0) {
            return num.toFixed(0); 
        } else {
            return num.toFixed(2); 
        }
    }
    
    // Загрузка корзины и расчет суммы
    function loadCart() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let html = '';
        let total = 0;
        
        const groupedCart = cart.reduce((acc, item) => {
            if (!acc[item.id]) {
                acc[item.id] = { ...item, count: 0, price: parseFloat(item.price) }; 
            }
            acc[item.id].count++;
            return acc;
        }, {});

        const submitButton = document.getElementById('submit-order-btn');
        
        if (cart.length === 0) {
            html = '<div class="empty-cart-message"><p>Корзина пуста. Пожалуйста, <a href="../index.php">добавьте</a> что-нибудь.</p></div>';
            if (submitButton) submitButton.disabled = true;
        } else {
            if (submitButton) submitButton.disabled = false;
            html = '<ul>';
            Object.values(groupedCart).forEach(item => {
                const subtotal = item.price * item.count;
                total += subtotal;
                
                const formattedSubtotal = formatPrice(subtotal);
                
                html += `<li><span>${item.name} (x${item.count})</span> <span>${formattedSubtotal} тг</span></li>`;
            });
            html += '</ul>';
        }
        
        const formattedTotal = formatPrice(total);
        document.getElementById('total-amount-display').textContent = formattedTotal + ' тг';
        document.getElementById('order-items').innerHTML = html;

        // Заполняем скрытые поля для отправки на сервер
        document.getElementById('order-details-json').value = JSON.stringify(groupedCart);
        document.getElementById('total-amount-input').value = total.toFixed(2); 
    }
    
    loadCart();
    </script>
</body>
</html>