<?php include 'includes/header.php'; ?>

<style>
    main {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
        min-height: calc(100vh - 240px); /* Чтобы контент занимал всю высоту */
    }
    
    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 30px;
    }
    
    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .cart-table th {
        background: #de978d;
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }
    
    .cart-table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .cart-table tr:last-child td {
        border-bottom: none;
    }
    
    .total-row {
        background: #f5e7e1 !important;
        font-weight: bold;
        font-size: 18px;
    }
    
    .btn-remove {
        background: #ff4444;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    
    .btn-remove:hover {
        background: #cc0000;
    }
    
    .cart-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
        flex-wrap: wrap;
    }
    
    .btn-clear {
        background: #ff4444;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s ease;
    }
    
    .btn-clear:hover {
        background: #cc0000;
    }
    
    .btn-checkout {
        background: #00aa00;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s ease;
    }
    
    .btn-checkout:hover {
        background: #008800;
    }
    
    .empty-cart {
        text-align: center;
        padding: 40px;
        color: #666;
        font-size: 18px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    footer {
        background: #de978d;
        padding: 15px;
        text-align: center;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        border-top: 2px solid #a97a74;
        z-index: 1000;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .footer-btn {
        background: white;
        color: #333;
        padding: 10px 25px;
        border: 2px solid #000000;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .footer-btn:hover {
        background: #a97a74;
        color: white;
        border-color: #fff;
    }
</style>

<main>
    <h1>🛒 Ваша корзина</h1>

    <div id="cart-content">
        </div>

    <div class="cart-actions">
        <button class="btn-clear" onclick="clearCart()">🗑️ Очистить корзину</button>
        <button class="btn-checkout" onclick="checkout()">💳 Оформить заказ</button>
    </div>
</main>

<script>
// Вспомогательная функция для форматирования числа как целого, 
// если нет дробной части.
function formatPrice(price) {
    const num = parseFloat(price);
    // Проверяем, является ли число целым
    if (num % 1 === 0) {
        return num.toFixed(0); // Оставляем 0 знаков после запятой
    } else {
        return num.toFixed(2); // Оставляем 2 знака, если есть дробная часть
    }
}

// Загружаем корзину из localStorage
function loadCart() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let html = '';
    
    if (cart.length === 0) {
        html = '<div class="empty-cart">';
        html += '<p>🛒 Корзина пуста</p>';
        html += '<p><a href="makeup.php" style="color: #de978d; text-decoration: none;">Перейти к покупкам →</a></p>';
        html += '</div>';
    } else {
        html = '<table class="cart-table">';
        html += '<tr>';
        html += '<th>Товар</th>';
        html += '<th style="text-align: right;">Цена</th>';
        html += '<th style="text-align: center;">Действие</th>';
        html += '</tr>';
        
        let total = 0;
        
        cart.forEach((item, index) => {
            // ** Используем formatPrice для отображения цены **
            const formattedPrice = formatPrice(item.price); 

            html += `<tr>`;
            html += `<td>${item.name}</td>`;
            html += `<td style="text-align: right;">${formattedPrice} ₸</td>`;
            html += `<td style="text-align: center;">`;
            html += `<button class="btn-remove" onclick="removeFromCart(${index})">❌ Удалить</button>`;
            html += `</td>`;
            html += `</tr>`;
            
            // Расчет общей суммы всегда должен идти от полных значений, 
            // чтобы избежать ошибок округления
            total += parseFloat(item.price);
        });
        
        // ** Используем formatPrice для отображения итоговой суммы **
        const formattedTotal = formatPrice(total);

        html += `<tr class="total-row">`;
        html += `<td><strong>Итого:</strong></td>`;
        html += `<td style="text-align: right;"><strong>${formattedTotal} ₸</strong></td>`;
        html += `<td></td>`;
        html += `</tr>`;
        html += '</table>';
    }
    
    document.getElementById('cart-content').innerHTML = html;
}

// Удаляем товар из корзины
function removeFromCart(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCart();
}

// Очищаем корзину
function clearCart() {
    if (confirm('Очистить всю корзину?')) {
        localStorage.removeItem('cart');
        loadCart();
    }
}

// Оформление заказа
function checkout() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart.length === 0) {
        alert('Корзина пуста!');
        return;
    }
    
    // Сохраняем корзину для страницы заказа
    localStorage.setItem('cartForOrder', JSON.stringify(cart));
    
    // Переходим на страницу оформления заказа
    window.location.href = 'order.php';
}

// Загружаем корзину при открытии страницы
loadCart();
</script>

<footer>
    <a href="makeup.php" class="footer-btn">← Продолжить покупки</a>
</footer>

<?php include 'includes/footer.php'; ?>