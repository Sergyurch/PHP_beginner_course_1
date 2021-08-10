<?php mb_internal_encoding('utf-8'); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Новая статья</title>
        <link href="style.css" rel="stylesheet">
    </head>
    <body>
        <!-- Показваем форму, если пустые поля -->
        <?php if ( !isset($_POST['article-title']) && !isset($_POST['article-text']) ): ?>
            <form method="POST">
                <div>
                    <label>
                        <p>Название статьи:</p>
                        <input type="text" name="article-title" required>
                    </label>
                </div>
                <div>
                    <label>
                        <p>Текст статьи:</p>
                        <textarea type="text" rows="10" name="article-text" required></textarea>
                    </label>
                </div>
                <input type="submit" value="Добавить">
            </form>
            <div class="text-right">
                <a href="index.php">Вернуться на главную</a>
            </div>

        <!-- Создаем новый файл, если поля были заполнены -->
        <?php else: ?>
            <?php 
                $file_content = $_POST['article-title'] . "\r\n" . $_POST['article-text'];// Контент будущего файла
                $articles = scandir('articles/');
                natsort($articles);
                $last_article = end($articles);
                $finish_cut = mb_strrpos($last_article, '.');
                $filename = mb_substr($last_article, 0, $finish_cut) + 1; // Имя будущего файла
                
                $new_file = fopen("articles/$filename.txt", 'a');
                fwrite($new_file, $file_content);
                fclose($new_file);

                // header('Location: index.php');
            ?>
            <p class="success-msg">Статья успешно добавлена.</p>
            <a href="index.php">Вернуться на Главную</a>
        <?php endif; ?>
    </body>
</html>