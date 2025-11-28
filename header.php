<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K-beauty</title>
    <link rel="stylesheet" href="index.css">
<style>
   /* Хедер */
header {
    background-color: #de978d;
    text-align: center;
    padding: 5px 20px;
    color: #fff;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    min-height: 150px;
    position: relative;
}

header h1 {
    font-size: 45px;
    margin: 0;
    text-align: center;
    width: 100%;
    position: absolute;
    top: 35%;
    left: 50%;
    transform: translate(-50%, -50%);
}

/* Верхняя строка */
.header-top {
    display: flex;
    justify-content: flex-end;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 20px;
    width: 100%;
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 100;
}

.admin-btn, .auth-btn {
    background: #a16a59;
    color: white;
    padding: 10px 15px;
    border-radius: 50px;
    text-decoration: none;
    font-size: 15px;
    transition: 0.3s ease;
    white-space: nowrap;
    margin-top: 15px;
    border: none;
    cursor: pointer;
}

.admin-btn:hover, .auth-btn:hover {
    background: #8a5a4a;
    transform: translateY(-2px);
}

.auth-section {
    display: flex;
    align-items: center;
    gap: 8px;
}

.user-greeting {
    color: #57301c;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
}

/* Навигация */
nav {
    margin-top: 80px;
    width: 100%;
    order: 5;
}

.nav ul {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.nav ul li {
    list-style: none;
}

.nav ul li a {
    padding: 12px 15px;
    color: #fff;
    font-size: 1em;
    text-decoration: none;
    transition: 0.3s;
    font-weight: 500;
    border-radius: 5px;
}

.nav ul li a:hover {
    color: rgba(0, 0, 0, 1);
    background-color: rgba(255, 255, 255, 0.2);
}

/* Мобильное меню */
#menu-toggle {
    display: none;
}

.menu-btn {
    display: none;
    flex-direction: column;
    cursor: pointer;
    padding: 10px;
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 1000;
}

.menu-btn span {
    width: 30px;
    height: 3px;
    background-color: #fff;
    margin: 4px 0;
    display: block;
    transition: 0.3s;
    border-radius: 2px;
}

/* Анимация гамбургера */
#menu-toggle:checked + .menu-btn span:nth-child(1) {
    transform: rotate(45deg) translate(6px, 6px);
}

#menu-toggle:checked + .menu-btn span:nth-child(2) {
    opacity: 0;
}

#menu-toggle:checked + .menu-btn span:nth-child(3) {
    transform: rotate(-45deg) translate(6px, -6px);
}

/* Планшетная версия */
@media (max-width: 1024px) {
    .nav ul {
        gap: 25px;
    }
    
    .nav ul li a {
        font-size: 0.9em;
        padding: 10px 12px;
    }
    
    header h1 {
        font-size: 38px;
    }
}

/* Мобильная версия */
@media (max-width: 768px) {
    header {
        min-height: 120px;
        padding: 5px 15px;
    }
    
    header h1 {
        font-size: 32px;
        top: 40%;
    }
    
    .header-top {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        top: 5px;
        right: 5px;
        left: 5px;
        padding: 5px 10px;
    }
    
    .admin-btn, .auth-btn {
        padding: 8px 12px;
        font-size: 14px;
        margin-top: 10px;
    }
    
    .user-greeting {
        font-size: 11px;
    }
    
    /* Гамбургер меню */
    .menu-btn {
        display: flex;
        top: 15px;
        left: 15px;
    }
    
    /* Основная навигация */
    nav {
        margin-top: 60px;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: rgba(222, 151, 141, 0.98);
        z-index: 999;
        display: none;
    }
    
    #menu-toggle:checked ~ nav {
        display: block;
    }
    
    .nav ul {
        flex-direction: column;
        gap: 0;
        padding: 10px 0;
    }
    
    .nav ul li {
        width: 100%;
        text-align: center;
    }
    
    .nav ul li a {
        display: block;
        padding: 15px 20px;
        font-size: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .nav ul li:last-child a {
        border-bottom: none;
    }
    
    .nav ul li a:hover {
        background-color: rgba(255, 255, 255, 0.3);
    }
}

/* Маленькие мобильные */
@media (max-width: 480px) {
    header {
        min-height: 110px;
    }
    
    header h1 {
        font-size: 28px;
        top: 45%;
    }
    
    .header-top {
        flex-direction: column;
        gap: 5px;
        align-items: flex-end;
    }
    
    .admin-btn, .auth-btn {
        padding: 6px 10px;
        font-size: 12px;
        margin-top: 5px;
    }
    
    .auth-section {
        flex-direction: column;
        gap: 5px;
        align-items: flex-end;
    }
    
    .user-greeting {
        font-size: 10px;
    }
    
    .menu-btn {
        top: 10px;
        left: 10px;
    }
    
    .menu-btn span {
        width: 25px;
        height: 2px;
        margin: 3px 0;
    }
    
    nav {
        margin-top: 50px;
    }
    
    .nav ul li a {
        padding: 12px 15px;
        font-size: 15px;
    }
}

/* Очень маленькие экраны */
@media (max-width: 360px) {
    header h1 {
        font-size: 24px;
    }
    
    .nav ul li a {
        font-size: 14px;
        padding: 10px 12px;
    }
}
</style>
</head>
<body>
    <header>
        <!-- Верхняя строка: Админка, Вход, Поиск -->
        <div class="header-top">
            <!-- Админка -->
            <a href="admin/login.php" class="admin-btn"> Админ</a>
            
            <!-- Блок авторизации -->
            <div class="auth-section">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="user-greeting">Привет, <?php echo $_SESSION['user_name']; ?>!</span>
                    <a href="login.php?action=logout" class="auth-btn">Выйти</a>
                <?php else: ?>
                    <a href="login.php" class="auth-btn">Войти</a>
                <?php endif; ?>
            </div>
            
            <!-- Поиск -->
            <a class="auth-btn" href="search.php" class="search-link"> Поиск</a>
        </div>

        <h1>K-beauty</h1>

        <input type="checkbox" id="menu-toggle">
        <label for="menu-toggle" class="menu-btn">
            <span></span>
            <span></span>
            <span></span>
        </label>
        
        <!-- Основное меню -->
        <nav class="nav">
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="brands.php">Товары</a></li>
                <li><a href="hair.php">Продукты для волос</a></li>
                <li><a href="makeup.php">Продукты для макияжа</a></li>
                <li><a href="skin-care.php">Продукты для ухода за лицом</a></li>
            </ul>
        </nav>
        <script>
// Закрытие меню при клике на ссылку (для мобильных)
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const navLinks = document.querySelectorAll('.nav ul li a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                menuToggle.checked = false;
            }
        });
    });
    
    // Закрытие меню при клике вне области
    document.addEventListener('click', function(event) {
        const isClickInsideNav = event.target.closest('nav');
        const isClickInsideMenuBtn = event.target.closest('.menu-btn');
        
        if (window.innerWidth <= 768 && menuToggle.checked && 
            !isClickInsideNav && !isClickInsideMenuBtn) {
            menuToggle.checked = false;
        }
    });
});
</script>
    </header>