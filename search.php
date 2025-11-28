<?php
session_start();
// Предполагается, что 'includes/config.php' содержит подключение к БД ($conn)
require_once 'includes/config.php';

// Получаем поисковый запрос
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Поиск товаров в базе данных
$found_products = [];
if (!empty($search_query)) {
    // Безопасное использование подготовленных выражений для предотвращения SQL-инъекций
    $sql = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term = "%" . $search_query . "%";
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($product = $result->fetch_assoc()) {
        $found_products[] = $product;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск товаров - K-beauty</title>
    <style>
        /* Основные стили */
        body, article, header {
            text-align: center;
        }
        body {
            font-family: 'Times New Roman', Times, serif, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5e7e1;
            color: #000;
            /* ✨ ИСПРАВЛЕНИЕ: Удален фиксированный margin-bottom, его устанавливает JS */
        }

        /* Навигация */
        .nav ul {
            display: flex;
            gap: 15px;
            margin: 0;
            padding: 0;
        }
        .nav ul li {
            list-style: none;
        }
        .nav ul li a {
            padding: 15px;
            color: #fff;
            font-size: 1em;
            text-decoration: none;
            transition: 0.3s;
        }
        .nav ul li a:hover {
            color: rgb(221, 158, 86);
        }

        /* Контейнер */
        .containerr {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 90%;
        }
        /* Кнопка меню */
        #menu-toggle {
            margin-top: 10px;
            display: none;
        }
        .menu-btn {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 10px;
        }
        .menu-btn span {
            background-color: #fff;
            width: 30px;
            height: 4px;
            margin: 5px 0;
            display: block;
            transition: 0.3s;
        } 
        /* Основной контент */
        .main {
            margin-top: 20px !important; 
            margin-bottom: 10px !important;
            
        }

        /* Заголовки */
        h2 {
            font-size: 30px;
            margin: 0 0 20px 0;
            padding-top: 100px; /* Обеспечивает отступ от хедера */
        }
        /* Сетка товаров */
        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 140px;
            padding: 0 150px 150px;
        }
        /* Кнопка "показать описание" */
        .toggle-btn {
            background: none;
            border: none;
            font-size: 1.2em;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .toggle-btn.active {
            transform: rotate(180deg);
        }
        /* Описание товара */
        .product-description {
            display: none;
            padding: 10px 0;
        }
        /* Стиль карточек товаров */
        article {
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            min-height: 300px;
        }
        article img {
            width: 100%;
            height: 250px; /* Фиксированная высота */
            object-fit: cover; /* Важно: обрезает изображение чтобы заполнить контейнер */
            border-radius: 10px;
        }

        .product-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 10px 0;
        }

        .product-name {
            margin: 0;
            font-size: 16px;
            text-align: left;
            flex-grow: 1;
        }
        .favorite-btn {
            background: white; 
            color: #333; 
            padding: 10px 15px; 
            border: 1px solid #000000ff; 
            border-radius: 8px;
            font-size: 16px; 
            font-weight: bold; 
            cursor: pointer;
            margin: 0 0px; 
            transition: all 0.3s ease;
        }

        .favorite-btn:hover {
            transform: scale(1.2);
        }

        .favorite-btn.active {
            color: #ff4444;
        }

        .add-btn {
            background: white; 
            color: #333; 
            padding: 10px 15px; 
            border: 1px solid #000000ff; 
            border-radius: 8px;
            font-size: 16px; 
            font-weight: bold; 
            cursor: pointer;
            margin: 0 0px; 
            transition: all 0.3s ease;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: auto; /* Прижимает к низу карточки */
            padding-top: 15px;
        }
        footer {
            position: fixed; 
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 1000; /* Чтобы был поверх контента */

            background-color: #de978d !important;
            display: flex;
            justify-content: center; /* Центрирует контейнер кнопок */
            align-items: center;
            padding: 20px 0; /* Вертикальный отступ внутри футера */
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .footer-buttons-container {
            display: flex;
            justify-content: center; /* Центрирует сами кнопки */
            gap: 20px; /* Расстояние между кнопками */
            width: 100%; 
        }

        .footer-btn {
            background-color: #fff;
            color: #333;
            padding: 12px 30px;
            cursor: pointer;
            border-radius: 8px;
            border: 2px solid transparent;
            font-weight: 500;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            font-family: 'Times New Roman', Times, serif;
        }

        .footer-btn:hover {
            background-color: #a97a74;
            color: #fff;
            border-color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        /* Стили для поиска */
        .search-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }

        .search-input {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid #de978d;
            border-radius: 25px;
            font-size: 16px;
            outline: none;
            background: white;
        }

        .search-btn {
            background: #de978d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }

        .search-results-info {
            text-align: center;
            margin: 15px 0;
            color: #333;
            font-size: 16px;
        }

        .search-results-info span {
            color: #de978d;
        }

        .no-results {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-size: 16px;
            grid-column: 1 / -1;
        }

        .image-placeholder {
            width: 100%;
            height: 150px;
            background: #f8f8f8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            margin-bottom: 12px;
        }

        /* Медиа-запросы */
        @media (min-width: 1200px) {
            h2 {
                font-size: 32px;
            }
        }
        @media (max-width: 768px) {
            h2 {
                font-size: 26px;
            }
            .products {
                padding: 10px;
            }
            .nav ul {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                background: rgba(0, 0, 0, 0.9);
                text-align: center;
                padding: 10px 0;
            }
            #menu-toggle:checked ~ .nav ul,
            .menu-btn {
                display: flex;
            }
        }
        @media (max-width: 480px) {
            h2 {
                font-size: 22px;
            }
        
            .products {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="search-container">
        <form method="GET">
            <input type="text" name="search" class="search-input" 
                   placeholder="🔍 Поиск товаров..." 
                   value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="search-btn">Найти</button>
        </form>
        
        <?php if (!empty($search_query)): ?>
            <div class="search-results-info">
                Результаты для "<?php echo htmlspecialchars($search_query); ?>"
                <span>(найдено: <?php echo count($found_products); ?>)</span>
            </div>
        <?php endif; ?>
    </div>

    <main class="main">
        <div class="products">
            <?php if (empty($search_query)): ?>
                <div class="no-results">
                    <p>Введите поисковый запрос чтобы найти товары</p>
                </div>
                
            <?php elseif (empty($found_products)): ?>
                <div class="no-results">
                    <p>По запросу "<?php echo htmlspecialchars($search_query); ?>" ничего не найдено</p>
                </div>
                
            <?php else: ?>
                <?php foreach ($found_products as $product): ?>
                    <article class="product-item">
                        <?php if (!empty($product['image_url'])): ?>
                            <img loading="lazy" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div class="image-placeholder">
                                Нет изображения
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-header">
                            <p class="product-name">
                                <?php echo htmlspecialchars($product['name']); ?>, 
                                <?php 
                                // ✨ ИСПРАВЛЕНИЕ PHP: Форматирование цены, чтобы убрать .00
                                $price = (float)$product['price'];
                                echo (floor($price) == $price) ? number_format($price, 0, '', '') : number_format($price, 2, '.', ''); 
                                ?>тг
                            </p>
                            <button class="toggle-btn">▼</button>
                        </div>
                        
                        <div class="product-description">
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                        </div>
                        
                        <div class="product-actions">
                            <button class="favorite-btn" data-id="<?php echo $product['id']; ?>">🤍</button>
                            <button class="add-btn" 
                                    data-id="<?php echo $product['id']; ?>" 
                                    data-name="<?php echo htmlspecialchars($product['name']); ?>" 
                                    data-price="<?php echo $product['price']; ?>">+</button>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    
<footer>
    <div class="footer-buttons-container">
        <button class="footer-btn" onclick="location.href='cart.php'">🛒 Корзина</button>
        <button class="footer-btn" onclick="location.href='favorits.php'">❤️ Избранное</button>
    </div>
</footer>

<script>
    // Функционал избранного
    document.addEventListener('DOMContentLoaded', function() {
        // Загружаем избранное из localStorage
        let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
        
        // Обновляем кнопки избранного
        updateFavoriteButtons();
        
        // Обработчик для кнопок избранного
        document.querySelectorAll('.favorite-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
                const productElement = this.closest('.product-item');
                // Получаем название и цену для добавления в избранное
                // Примечание: тут берется только название до первой запятой
                const productName = productElement.querySelector('.product-name').textContent.split(',')[0].trim();
                const productPrice = this.closest('.product-item').querySelector('.add-btn').dataset.price;
                
                toggleFavorite(productId, productName, productPrice, this);
            });
        });
        
        function toggleFavorite(id, name, price, button) {
            let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            const existingIndex = favorites.findIndex(item => item.id == id);
            
            if (existingIndex > -1) {
                // Удаляем из избранного
                favorites.splice(existingIndex, 1);
                button.innerHTML = '🤍';
                button.classList.remove('active');
                showNotification('❌ Удалено из избранного');
            } else {
                // Добавляем в избранное
                favorites.push({
                    id: id,
                    name: name,
                    price: price
                });
                button.innerHTML = '❤️';
                button.classList.add('active');
                showNotification('✅ Добавлено в избранное');
            }
            
            localStorage.setItem('favorites', JSON.stringify(favorites));
        }
        
        function updateFavoriteButtons() {
            let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            
            document.querySelectorAll('.favorite-btn').forEach(button => {
                const productId = button.dataset.id;
                const isFavorite = favorites.some(item => item.id == productId);
                
                if (isFavorite) {
                    button.innerHTML = '❤️';
                    button.classList.add('active');
                } else {
                    button.innerHTML = '🤍';
                    button.classList.remove('active');
                }
            });
        }
        
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #de978d;
                color: white;
                padding: 15px 25px;
                border-radius: 8px;
                z-index: 10000;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    });

    // Функционал корзины
    document.querySelectorAll('.add-btn').forEach(button => {
        button.addEventListener('click', function() {
            const product = {
                id: this.dataset.id,
                name: this.dataset.name, 
                price: this.dataset.price
            };
            
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Если вы хотите, чтобы каждый клик добавлял отдельный товар (без увеличения count)
            cart.push(product);

            // Если вы хотите считать количество:
            /*
            const existingProductIndex = cart.findIndex(item => item.id === product.id);

            if (existingProductIndex !== -1) {
                cart[existingProductIndex].quantity = (cart[existingProductIndex].quantity || 1) + 1;
            } else {
                cart.push({...product, quantity: 1});
            }
            */

            localStorage.setItem('cart', JSON.stringify(cart));
            
            alert('✅ ' + product.name + ' добавлен в корзину!');
        });
    });

    // ✨ КОД ДЛЯ ДИНАМИЧЕСКОГО ОТСТУПА (фиксирует проблему с футером)
    function setContentBottomMargin() {
        const footer = document.querySelector('footer');
        const body = document.body;
        
        // Получаем фактическую высоту футера
        const footerHeight = footer.offsetHeight;
        
        // Устанавливаем нижний отступ для body равным высоте футера + небольшой запас
        body.style.marginBottom = (footerHeight + 10) + 'px';
        
        console.log(`Высота футера: ${footerHeight}px. Установлен body margin-bottom: ${(footerHeight + 10)}px.`);
    }

    // Вызываем функцию при загрузке страницы и изменении размера окна
    window.addEventListener('load', setContentBottomMargin);
    window.addEventListener('resize', setContentBottomMargin);
    

    // JavaScript для скрытия/показа описания
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("toggle-btn")) {
            const toggleButton = event.target;
            const description = toggleButton.closest(".product-item").querySelector(".product-description");
            
            if (description.style.display === "block") {
                description.style.display = "none";
                toggleButton.classList.remove("active");
            } else {
                description.style.display = "block";
                toggleButton.classList.add("active");
            }
        }
    });
</script>

<?php 
// Закрываем соединение с базой
if (isset($conn)) {
    $conn->close();
}
?>
</body>
</html>