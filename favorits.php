<?php include 'includes/header.php'; ?>

<style>
    .favorites-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .favorite-item {
        background: white;
        padding: 20px;
        margin: 15px 0;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .empty-favorites {
        text-align: center;
        padding: 60px 20px;
        color: #666;
        font-size: 18px;
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
</style>

<div class="favorites-container">
    <h1>❤️ Избранные товары</h1>
    <div id="favorites-content">
        <!--загружаются избранные товары -->
    </div>
</div>

<script>
function loadFavorites() {
    let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
    let html = '';
    
    if (favorites.length === 0) {
        html = '<div class="empty-favorites">';
        html += '<p>В избранном пока ничего нет</p>';
        html += '<p><a href="brands.php" style="color: #de978d; text-decoration: none;">Перейти к товарам →</a></p>';
        html += '</div>';
    } else {
        favorites.forEach((item, index) => {
            html += `<div class="favorite-item">`;
            html += `<div>`;
            html += `<h3>${item.name}</h3>`;
            html += `<p>Цена: ${item.price} ₸</p>`;
            html += `</div>`;
            html += `<div>`;
            html += `<button onclick="addToCartFromFavorites(${index})" style="background: #de978d; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-right: 10px;">+ В корзину</button>`;
            html += `<button onclick="removeFromFavorites(${index})" style="background: #ff4444; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">❌ Удалить</button>`;
            html += `</div>`;
            html += `</div>`;
        });
    }
    
    document.getElementById('favorites-content').innerHTML = html;
}

function removeFromFavorites(index) {
    let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
    favorites.splice(index, 1);
    localStorage.setItem('favorites', JSON.stringify(favorites));
    loadFavorites();
}

function addToCartFromFavorites(index) {
    let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
    let item = favorites[index];
    
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.push(item);
    localStorage.setItem('cart', JSON.stringify(cart));
    
    alert('✅ ' + item.name + ' добавлен в корзину!');
}

// Загружаем избранное при открытии страницы
loadFavorites();
</script>
<footer>
    <a href="makeup.php" class="footer-btn">← Продолжить покупки</a>
</footer>
<?php include 'includes/footer.php'; ?>