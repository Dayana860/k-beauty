<?php 
session_start();
require_once 'includes/config.php';
?>
<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Продукты для макияжа</title>
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
  }  /* Основной контент */
.main {
    margin-top: 120px; 
    margin-bottom: 30px;
    
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
/* Подвал */
footer {
    background-color: #de978d;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 20px;
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

   <main class="main">
    <div class="products">
        <?php
        // Получаем товары для категории "makeup" из базы данных
        $category = 'makeup';
        $sql = "SELECT * FROM products WHERE category = ? ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($product = $result->fetch_assoc()) {
                $product_id = $product['id'];
                $product_name = htmlspecialchars($product['name']);
                $product_price = $product['price'];
                $product_image = htmlspecialchars($product['image_url']);
                $product_description = htmlspecialchars($product['description']);
        ?>
        <!-- Товар из базы данных -->
        <article class="product-item">
            <img loading="lazy" src="<?php echo $product_image; ?>" alt="<?php echo $product_name; ?>">
            <div class="product-header">
                <p class="product-name">
    <?php echo $product_name; ?>, 
    <?php 
    $price = (float)$product_price;
    // Если число целое (нет дробной части, отличной от нуля), форматируем без дробной части
    if (floor($price) == $price) {
        echo number_format($price, 0, '', ''); 
    } else {
        // Иначе выводим с двумя знаками после запятой
        echo number_format($price, 2, '.', '');
    }
    ?>тг.
</p>
                <button class="toggle-btn">▼</button>
            </div>
            <div class="product-description">
                <p><?php echo $product_description; ?></p>
            </div>
            <div class="product-actions">
                <button class="favorite-btn" data-id="<?php echo $product_id; ?>">🤍</button>
                <button class="add-btn" 
                        data-id="<?php echo $product_id; ?>" 
                        data-name="<?php echo $product_name; ?>" 
                        data-price="<?php echo $product_price; ?>">+</button>
            </div>
        </article>
        <?php
            }
        } else {
            echo '<p style="text-align: center; grid-column: 1 / -1; font-size: 18px; color: #666;">Товары для макияжа не найдены</p>';
            // Для отладки - покажем какие категории есть в базе
            $debug_sql = "SELECT DISTINCT category FROM products";
            $debug_result = $conn->query($debug_sql);
            if ($debug_result && $debug_result->num_rows > 0) {
                echo '<p style="text-align: center;">Доступные категории в базе: ';
                $categories = [];
                while ($row = $debug_result->fetch_assoc()) {
                    $categories[] = $row['category'];
                }
                echo implode(', ', $categories);
                echo '</p>';
            }
        }
        
        if (isset($stmt)) {
            $stmt->close();
        }
        ?>
    </div>
</main>

<footer>
    <button class="footer-btn" onclick="location.href='cart.php'">🛒 Корзина</button>
    <button class="footer-btn" onclick="location.href='favorits.php'">❤️ Избранное</button>
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
                const productName = productElement.querySelector('.product-name').textContent;
                const productPrice = productElement.querySelector('.add-btn').dataset.price;
                
                toggleFavorite(productId, productName, productPrice, this);
            });
        });
        
        function toggleFavorite(id, name, price, button) {
            let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            const existingIndex = favorites.findIndex(item => item.id === id);
            
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
                const isFavorite = favorites.some(item => item.id === productId);
                
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
            cart.push(product);
            localStorage.setItem('cart', JSON.stringify(cart));
            
            alert('✅ ' + product.name + ' добавлен в корзину!');
        });
    });

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