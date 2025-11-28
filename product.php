<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';
$edit_mode = false;
$edit_product = null;

// Обработка добавления новой категории
if (isset($_POST['add_category'])) {
    $new_category = trim($conn->real_escape_string($_POST['new_category']));
    
    if (!empty($new_category)) {
        // Проверяем, существует ли уже такая категория в таблице категорий
        $check_sql = "SELECT * FROM categories WHERE name = '$new_category' LIMIT 1";
        $result = $conn->query($check_sql);
        
        if ($result && $result->num_rows > 0) {
            $error = "Категория '$new_category' уже существует!";
        } else {
            // Добавляем новую категорию в таблицу категорий
            $insert_sql = "INSERT INTO categories (name) VALUES ('$new_category')";
            if ($conn->query($insert_sql)) {
                $success = "Категория '$new_category' успешно добавлена!";
            } else {
                $error = "Ошибка при добавлении категории: " . $conn->error;
            }
        }
    } else {
        $error = "Введите название категории!";
    }
}

// Обработка добавления товара
if (isset($_POST['add_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $description = $conn->real_escape_string($_POST['description']);
    
    $sql = "INSERT INTO products (name, price, category, image_url, description) VALUES ('$name', $price, '$category', '$image_url', '$description')";
    
    if ($conn->query($sql)) {
        $success = "Товар успешно добавлен!";
    } else {
        $error = "Ошибка: " . $conn->error;
    }
}

// Обработка обновления товара
if (isset($_POST['update_product'])) {
    $id = intval($_POST['product_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $description = $conn->real_escape_string($_POST['description']);
    
    $sql = "UPDATE products SET name='$name', price=$price, category='$category', image_url='$image_url', description='$description' WHERE id=$id";
    
    if ($conn->query($sql)) {
        $success = "Товар успешно обновлен!";
        $edit_mode = false;
        $edit_product = null;
    } else {
        $error = "Ошибка обновления: " . $conn->error;
    }
}

// Обработка отмены редактирования
if (isset($_POST['cancel_edit'])) {
    $edit_mode = false;
    $edit_product = null;
}

// Обработка удаления товара
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM products WHERE id = $id";
    
    if ($conn->query($sql)) {
        $success = "Товар удален!";
    } else {
        $error = "Ошибка удаления: " . $conn->error;
    }
    
    header('Location: products.php');
    exit;
}

// Обработка удаления категории
if (isset($_GET['delete_category'])) {
    $category_id = intval($_GET['delete_category']);
    $sql = "DELETE FROM categories WHERE id = $category_id";
    
    if ($conn->query($sql)) {
        $success = "Категория удалена!";
    } else {
        $error = "Ошибка удаления категории: " . $conn->error;
    }
    
    header('Location: products.php');
    exit;
}

// Режим редактирования
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM products WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $edit_product = $result->fetch_assoc();
        $edit_mode = true;
    }
}

// Получаем все товары
$products = [];
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
if ($result) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}

// Получаем все категории из таблицы категорий
$categories = [];
$category_result = $conn->query("SELECT * FROM categories ORDER BY name");
if ($category_result) {
    while ($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Если таблица категорий пуста, создаем основные категории
if (empty($categories)) {
    $default_categories = ['Макияж', 'Уход за волосами', 'Уход за лицом', 'Парфюмерия'];
    foreach ($default_categories as $cat) {
        $conn->query("INSERT IGNORE INTO categories (name) VALUES ('$cat')");
    }
    // Снова получаем категории
    $category_result = $conn->query("SELECT * FROM categories ORDER BY name");
    if ($category_result) {
        while ($row = $category_result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Управление товарами</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #de978d; color: white; }
        .form-container { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 10px; }
        .category-form { background: #e6f7ff; padding: 15px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #de978d; }
        .categories-table { margin: 20px 0; }
        .categories-table th { background-color: #de978d; }
        input, textarea, select { 
            width: 100%; 
            padding: 8px; 
            margin: 5px 0; 
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button { 
            background: #de978d; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer;
            margin: 5px;
        }
        button:hover { background: #d1887e; }
        .cancel-btn { 
            background: #6c757d; 
        }
        .cancel-btn:hover { 
            background: #5a6268; 
        }
        .category-btn { 
            background: #de978d; 
        }
        .category-btn:hover { 
            background: #de978d; 
        }
        .danger-btn { 
            background: #ff4d4f; 
            padding: 5px 10px;
            font-size: 12px;
        }
        .danger-btn:hover { 
            background: #d9363e; 
        }
        .success { color: green; background: #e6ffe6; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .actions { display: flex; gap: 10px; }
        .edit-form { background: #fff3cd; border-left: 4px solid #ffc107; }
        .category-list { margin: 10px 0; padding: 10px; background: #f0f0f0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Управление товарами</h1>
    <a href="index.php">← Назад в админку</a>

    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Форма добавления новой категории -->
    <div class="category-form">
        <h3>📁 Добавить новую категорию</h3>
        <form method="POST">
            <input type="text" name="new_category" placeholder="Введите название новой категории" required>
            <button type="submit" name="add_category" class="category-btn">➕ Добавить категорию</button>
        </form>
    </div>

    <!-- Список существующих категорий -->
    <div class="categories-table">
        <h3>📋 Существующие категории</h3>
        <?php if (!empty($categories)): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Название категории</th>
                    <th>Действия</th>
                </tr>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?php echo $cat['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                    <td>
                        <a href="?delete_category=<?php echo $cat['id']; ?>" 
                           onclick="return confirm('Удалить категорию <?php echo addslashes($cat['name']); ?>?')"
                           class="danger-btn" style="color: white; text-decoration: none;">❌ Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Категорий нет в базе данных</p>
        <?php endif; ?>
    </div>

    <!-- Форма добавления/редактирования товара -->
    <div class="form-container <?php echo $edit_mode ? 'edit-form' : ''; ?>">
        <h3><?php echo $edit_mode ? '✏️ Редактировать товар' : '➕ Добавить новый товар'; ?></h3>
        <form method="POST">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
            <?php endif; ?>
            
            <input type="text" name="name" placeholder="Название товара" 
                   value="<?php echo $edit_mode ? htmlspecialchars($edit_product['name']) : ''; ?>" required>
            
            <input type="number" name="price" placeholder="Цена" step="0.01" 
                   value="<?php echo $edit_mode ? $edit_product['price'] : ''; ?>" required>
            
            <select name="category" required>
                <option value="">Выберите категорию</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['name']; ?>" <?php echo ($edit_mode && $edit_product['category'] == $cat['name']) ? 'selected' : ''; ?>>
                        <?php echo $cat['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" name="image_url" placeholder="Имя файла картинки (например: m.jpg)" 
                   value="<?php echo $edit_mode ? htmlspecialchars($edit_product['image_url']) : ''; ?>" required>
            
            <textarea name="description" placeholder="Описание товара" rows="3"><?php echo $edit_mode ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
            
            <div>
                <?php if ($edit_mode): ?>
                    <button type="submit" name="update_product">💾 Сохранить изменения</button>
                    <button type="submit" name="cancel_edit" class="cancel-btn">❌ Отменить</button>
                <?php else: ?>
                    <button type="submit" name="add_product">Добавить товар</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Список товаров -->
    <h3>📦 Список товаров в базе (<?php echo count($products); ?>)</h3>
    
    <?php if (empty($products)): ?>
        <p>Товаров нет в базе данных</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Категория</th>
                <th>Картинка</th>
                <th>Описание</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo $product['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                <td>
    <?php 
    $price = (float)$product['price'];
    
    // Если число целое (дробная часть .00), форматируем без дробной части (0 знаков)
    if (floor($price) == $price) {
        echo number_format($price, 0, '', ''); 
    } else {
        // Иначе форматируем с двумя знаками после запятой
        echo number_format($price, 2, '.', '');
    }
    ?>тг.
</td>
                <td><?php echo $product['category']; ?></td>
                <td><?php echo $product['image_url']; ?></td>
                <td><?php echo htmlspecialchars(mb_strimwidth($product['description'], 0, 50, '...')); ?></td>
                <td>
                    <div class="actions">
                        <a href="?edit=<?php echo $product['id']; ?>" 
                           style="color: #007bff; text-decoration: none;">✏️ Редактировать</a>
                        <a href="?delete=<?php echo $product['id']; ?>" 
                           onclick="return confirm('Удалить товар <?php echo addslashes($product['name']); ?>?')"
                           style="color: red; text-decoration: none;">❌ Удалить</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <script>
        // Прокрутка к форме при редактировании
        <?php if ($edit_mode): ?>
            document.querySelector('.form-container').scrollIntoView({ behavior: 'smooth' });
        <?php endif; ?>
        
        // Автозаполнение новой категории в форме товара после её создания
        <?php if (isset($_POST['add_category']) && !empty($_POST['new_category']) && empty($error)): ?>
            const newCategory = '<?php echo $_POST['new_category']; ?>';
            const categorySelect = document.querySelector('select[name="category"]');
            if (categorySelect) {
                categorySelect.value = newCategory;
                document.querySelector('.form-container').scrollIntoView({ behavior: 'smooth' });
            }
        <?php endif; ?>
    </script>
</body>
</html>