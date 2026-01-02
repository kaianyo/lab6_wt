<?php
// Налаштування часового поясу (щоб час відображався коректно)
date_default_timezone_set('Europe/Kiev');

// Змінна для виведення повідомлень користувачу
$resultMessage = "";

// Перевіряємо, чи була відправлена форма методом POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Отримуємо дані з форми
    // Використовуємо htmlspecialchars для безпеки (захист від XSS атак)
    $name = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    
    // Отримання відповідей на питання
    $bestLang = isset($_POST['best_lang']) ? $_POST['best_lang'] : 'Не обрано';
    
    // Обробка чекбоксів (оскільки це масив)
    if (isset($_POST['tools'])) {
        $tools = implode(", ", $_POST['tools']); // Перетворюємо масив у рядок через кому
    } else {
        $tools = "Нічого не обрано";
    }
    
    $comment = htmlspecialchars($_POST['comment']);

    // 2. Фіксуємо дату та час [cite: 33]
    $currentDate = date("Y-m-d H:i:s");

    // 3. Формуємо текст для запису у файл
    $content = "Дата заповнення: $currentDate\n";
    $content .= "Ім'я: $name\n";
    $content .= "Email: $email\n";
    $content .= "---------------------------------\n";
    $content .= "Питання 1 (Улюблена мова): $bestLang\n";
    $content .= "Питання 2 (Інструменти): $tools\n";
    $content .= "Питання 3 (Коментар): $comment\n";
    $content .= "=================================\n";

    // 4. Генеруємо ім'я файлу з датою та часом [cite: 32]
    // Наприклад: response_2023-10-25_14-30-05.txt
    $filename = "survey/response_" . date("Y-m-d_H-i-s") . ".txt";

    // 5. Перевіряємо наявність папки survey і зберігаємо файл [cite: 31]
    if (!is_dir('survey')) {
        mkdir('survey', 0777, true); // Створити папку, якщо її немає
    }

    if (file_put_contents($filename, $content)) {
        // Якщо файл створено успішно, виводимо повідомлення та час [cite: 33]
        $resultMessage = "<div class='success'>
                            <h3>Дякуємо, $name!</h3>
                            <p>Ваша відповідь збережена.</p>
                            <p>Час відправки: <strong>$currentDate</strong></p>
                            <a href='index.php'>Заповнити ще раз</a>
                          </div>";
    } else {
        $resultMessage = "<div class='error'>Помилка збереження файлу. Перевірте права доступу до папки survey.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторна робота №6</title>
    <style>
        /* Трохи стилів для гарного вигляду */
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 20px auto; padding: 20px; background-color: #f4f4f4; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #218838; }
        .success { background-color: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 4px; text-align: center; }
        .error { background-color: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 4px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Опитування: Веб-розробка</h2>

    <?php if ($resultMessage != ""): ?>
        <?php echo $resultMessage; ?>
    <?php else: ?>

    <form action="index.php" method="POST">
        
        <div class="form-group">
            <label for="username">Ваше ім'я:</label>
            <input type="text" id="username" name="username" required placeholder="Введіть ім'я">
        </div>

        <div class="form-group">
            <label for="email">Ваш Email:</label>
            <input type="email" id="email" name="email" required placeholder="email@example.com">
        </div>

        <hr>

        <div class="form-group">
            <label>Яка мова програмування вам подобається найбільше?</label>
            <div>
                <input type="radio" id="php" name="best_lang" value="PHP" checked>
                <label for="php" style="display:inline; font-weight:normal;">PHP</label>
            </div>
            <div>
                <input type="radio" id="python" name="best_lang" value="Python">
                <label for="python" style="display:inline; font-weight:normal;">Python</label>
            </div>
            <div>
                <input type="radio" id="js" name="best_lang" value="JavaScript">
                <label for="js" style="display:inline; font-weight:normal;">JavaScript</label>
            </div>
        </div>

        <div class="form-group">
            <label>Якими інструментами ви користуєтесь? (можна декілька)</label>
            <div>
                <input type="checkbox" id="git" name="tools[]" value="Git">
                <label for="git" style="display:inline; font-weight:normal;">Git</label>
            </div>
            <div>
                <input type="checkbox" id="docker" name="tools[]" value="Docker">
                <label for="docker" style="display:inline; font-weight:normal;">Docker</label>
            </div>
            <div>
                <input type="checkbox" id="storm" name="tools[]" value="PhpStorm">
                <label for="storm" style="display:inline; font-weight:normal;">PhpStorm</label>
            </div>
        </div>

        <div class="form-group">
            <label for="comment">Чому ви обрали веб-розробку?</label>
            <textarea id="comment" name="comment" rows="4" placeholder="Ваша відповідь..."></textarea>
        </div>

        <button type="submit">Надіслати відповідь</button>
    </form>
    <?php endif; ?>
</div>

</body>
</html>