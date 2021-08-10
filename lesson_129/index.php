<?php
    mb_internal_encoding('utf-8');
    $articles_dir = 'text/'; //Путь к папке со статьями
    $is_new_article_added = false;
    $is_new_file_added = false;
    $file_upload_error = false;
    $articles_per_page = 5;
    $current_page = ( isset($_GET['page']) ) ? $_GET['page'] : 1;

    //Если получен запрос на удаление статьи
    if ( isset($_GET['delete']) ) {
        unlink($articles_dir . $_GET['delete']);
        header('Location: index.php');
    }

    // Добавлям новую статью, если поля были заполнены
    if ( !empty($_POST['article-title']) && !empty($_POST['article-text']) ) {
        $is_new_article_added = true;
        $file_content = $_POST['article-title'] . "\r\n" . date('Y-m-d') . "\r\n" . $_POST['article-text'];// Контент будущего файла
        $new_filename = create_new_filename( scandir($articles_dir) );
        $new_file = fopen("$articles_dir" . "$new_filename.txt", 'a');
        fwrite($new_file, $file_content);
        fclose($new_file);
    }

    // Добавлям новый файл со статьей, если был загружен файл
    if ( isset($_FILES['article-file']) && $_FILES['article-file']['error'] != 4) {
        $file_tmp_name = $_FILES['article-file']['tmp_name']; //Временное название файла
        $new_filename = create_new_filename( scandir($articles_dir) );
                
        // Загружаем файл
        if ( move_uploaded_file($file_tmp_name, $articles_dir . $new_filename . '.txt') ) {
            $is_new_file_added = true;
        } else {
            $file_upload_error = true;
        }
            
    }
    
    // Функция создает имя для нового файла
    function create_new_filename($articles) {
        natsort($articles);
        $last_article = end($articles);
        $finish_cut = mb_strrpos($last_article, '.');
        return mb_substr($last_article, 0, $finish_cut) + 1;
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 129</title>
        <link href="style.css" rel="stylesheet">
        <style>
            * {
                box-sizing: border-box;
            }

            body {
                padding: 30px;
            }

            img {
                margin: 0 20px;
            }

            h1 {
                padding-left: 150px;
            }

            h3 {
                margin-top: 50px;
            }

            .active-link {
                text-decoration: none;
                color: white;
                background-color: blue;
            }

            .btn-container {
                text-align: right;
                padding-top: 77px;
            }

            .btn-container a {
                color: black;
                text-decoration: none;
                padding: 10px 30px;
                border: 1px solid black;
                background-color: lightgrey;
            }

            .article-list div {
                margin-bottom: 20px;
            }

            .text-bold {
                font-weight: bold;
            }

            .page-number {
                margin: 0 10px;
            }

            .article-text-container {
                display: flex;
                justify-content: space-between;
                margin-bottom: 100px;
            }

            .article-text {
                width: 60%;
            }

            .article-info {
                width: 40%;
                text-align: right;
            }

            .text-right {
                text-align: right;
            }

            input[type="text"], textarea {
                width: 100%;
            }

            .delete-link {
                margin-left: 30px;
            }

            .success-msg {
                background-color: green;
                padding: 30px;
                color: white;
            }

            .error-msg {
                background-color: red;
                padding: 30px;
            }
        </style>
    </head>
    <body>

        <!-- Показываем конкретную статью -->
        <?php if ( isset($_GET['article']) ): ?>
            <?php
                $finish_cut = mb_strrpos($_GET['article'], '.');
                $article_number = mb_substr($_GET['article'], 0, $finish_cut);
                $article_created = date('Y-m-d', filectime($articles_dir . $_GET['article']) );
                $article_updated = date('Y-m-d', filemtime($articles_dir . $_GET['article']) );
                $handle = fopen($articles_dir . $_GET['article'], 'r');
                $article_title = fgets($handle);
                $article_date = fgets($handle);
                $article_text = fgets($handle);
                fclose($handle);
            ?>
            <h1>Статья № <?= $article_number; ?></h1>
            <p><a href="index.php">Главная</a> > Статья № <?= $article_number; ?>: <?= $article_title; ?></p>
            <p>Дата публикации: <?= $article_date; ?></p>
            <div class="article-text-container">
                <div class="article-text">
                    <p><?= $article_text; ?></p>
                </div>
                <div class="article-info">
                    <p>Дата изменения статья: <?= $article_updated; ?></p>
                    <p>Дата создания статья: <?= $article_created; ?></p>
                </div>
            </div>
            <div class="text-right">
                <a href="index.php">Вернуться на главную</a>
            </div>
        
        <!-- Или показываем список всех статей -->
        <?php else: ?>
            <?php
                $files = []; // Здесь будут все файлы со  статьями
                
                foreach(scandir($articles_dir) as $file) {
                    if ($file != "." && $file != "..") {
                        $files[filemtime($articles_dir . $file)] = $file; // Ключем будет время обновления файла
                    }
                }

                krsort($files);
                $articles_amont = count($files);
                $pages_amount = ceil($articles_amont / $articles_per_page);
                $start = ($current_page - 1) * $articles_per_page;
                $array_to_show = array_slice($files, $start, $articles_per_page); // Список статей, который надо показать
            ?>
            <div class="container">
                <h1>Статьи</h1>
                <div class="center">
                    <div class="article-list">
                        <?php foreach($array_to_show as $article): ?>
                            <?php
                                $finish_cut = mb_strrpos($article, '.');
                                $filename = mb_substr($article, 0, $finish_cut);
                                $handle = fopen($articles_dir . $article, 'r');
                                $article_title = fgets($handle);
                                $article_date = fgets($handle);
                                fclose($handle);
                            ?>
                            <div>
                                <a href="index.php?article=<?= $article; ?>">Статья <?= $filename; ?>: <?= $article_title; ?></a>
                                <img src="https://cdn0.iconfinder.com/data/icons/feather/96/591276-arrow-right-16.png" alt="arrow">
                                <span>Дата публикации: <?= $article_date; ?></span>
                                <a href="index.php?delete=<?= $article; ?>" class="delete-link">Удалить статью</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p>
                        <span class="text-bold">Статьи:</span>
                        <?php for ($i = 1; $i <= $pages_amount; $i++): ?>
                            <?php if ($i == $current_page): ?>
                                <a href="index.php?page=<?= $i; ?>" class="active-link page-number"><?= $i; ?></a>
                            <?php else: ?>
                                <a href="index.php?page=<?= $i; ?>" class="page-number"><?= $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </p>
                    <p>Всего статей: <span class="text-bold"><?= $articles_amont; ?></span></p>
                </div>
                
                <!-- Форма для добавления новой статьи -->
                <h3>Добавить статью</h3>

                <!-- Сообщение, если не заполнены все поля-->
                <?php if ( isset($_POST['article-add']) ):  ?>
                    <?php if ( empty($_POST['article-title']) || empty($_POST['article-text']) ): ?>
                        <p class="error-msg">Заполнены не все поля.</p>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Сообщение, если статья добавлена-->
                <?php if ( $is_new_article_added ): ?>
                    <?php
                        $is_new_article_added = false;
                        unset($_POST['article-title']);
                        unset($_POST['article-text']);
                    ?>
                    <p class="success-msg">Статья успешно добавлена.</p>
                <?php endif; ?>

                <form method="POST">
                    <div>
                        <label>
                            <p>Название статьи:</p>
                            <input type="text" name="article-title" value="<?php if( isset($_POST['article-title']) ) echo $_POST['article-title']; ?>">
                        </label>
                    </div>
                    <div>
                        <label>
                            <p>Текст статьи:</p>
                            <textarea type="text" rows="10" name="article-text"><?php if( isset($_POST['article-text']) ) echo $_POST['article-text']; ?></textarea>
                        </label>
                    </div>
                    <input type="submit" value="Добавить статью" name="article-add">
                </form>
            

                <!-- Форма для загрузки статьи в виде файла -->
                <h3>Загрузить статью в виде файла</h3>

                <!-- Сообщение, если файл не выбран -->
                <?php if ( isset($_POST['file-add']) ):  ?>
                    <?php if ( $_FILES['article-file']['error'] == 4 ): ?>
                        <p class="error-msg">Файл не выбран.</p>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Сообщение, если ошибка загрузки файла -->
                <?php if ($file_upload_error == true):  ?>
                    <p class="error-msg">Ошибка загрузки файла.</p>
                <?php endif; ?>
                
                <!-- Сообщение, если файл загружен успешно -->
                <?php if ($is_new_file_added): ?>
                    <?php
                        $is_new_file_added = false;
                        $file_upload_error = false;
                    ?>
                    <p class="success-msg">Файл успешно добавлен.</p>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="article-file">
                    <input type="submit" name="file-add" value="Загрузить файл">
                </form>
            </div>
        <?php endif; ?>
    </body>
</html>