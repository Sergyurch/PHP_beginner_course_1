<?php mb_internal_encoding('utf-8'); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 124</title>
        <link href="style.css" rel="stylesheet">
    </head>
    <body>

        <!-- Показываем конкретную статью -->
        <?php if ( isset($_GET['article']) ): ?>
            <?php
                $finish_cut = mb_strrpos($_GET['article'], '.');
                $article_number = mb_substr($_GET['article'], 0, $finish_cut);
                $article_created = date('Y-m-d', filectime('articles/' . $_GET['article']) );
                $article_updated = date('Y-m-d', filemtime('articles/' . $_GET['article']) );
                $handle = fopen('articles/' . $_GET['article'], 'r');
                $article_title = fgets($handle);
                $article_text = fgets($handle);
                fclose($handle);
            ?>
            <h1>Статья № <?= $article_number; ?></h1>
            <p><a href="index.php">Главная</a> > Статья № <?= $article_number; ?>: <?= $article_title; ?></p>
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
                if ( isset($_GET['delete']) ) {
                    unlink('articles/' . $_GET['delete']);
                    header('Location: index.php');
                }

                if ( isset($_GET['page']) ) {
                    $page = $_GET['page'];
                } else {
                    $page = 1;
                }

                $articles_per_page = 5;
                $files = []; // Здесь будут все файлы со  статьями
                
                foreach(scandir('articles/') as $file) {
                    if ($file != "." && $file != "..") {
                        $files[filemtime('articles/' . $file)] = $file; // Ключем будет время обновления файла
                    }
                }

                krsort($files);
                $articles_amont = count($files);
                $pages_amount = ceil($articles_amont / $articles_per_page);
                $start = ($page - 1) * $articles_per_page;
                $array_to_show = array_slice($files, $start, $articles_per_page); // Список статей, который надо показать
            ?>
            <div class="container">
                <div class="inner-container">
                    <h1>Статьи</h1>
                    <div class="article-list">
                        <?php foreach($array_to_show as $article): 
                            $finish_cut = mb_strrpos($article, '.');
                            $filename = mb_substr($article, 0, $finish_cut);
                            $handle = fopen('articles/' . $article, 'r');
                            $title = fgets($handle);
                            fclose($handle);
                        ?>
                            <div>
                                <a href="index.php?article=<?= $article; ?>">Статья <?= $filename; ?>: <?= $title; ?></a>
                                <a href="index.php?delete=<?= $article; ?>" class="delete-link">Удалить статью</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p>
                        <span class="text-bold">Статьи:</span>
                        <?php for ($i = 1; $i <= $pages_amount; $i++): ?>
                            <?php if ($i == $page): ?>
                                <a href="index.php?page=<?= $i; ?>" class="active-link page-number"><?= $i; ?></a>
                            <?php else: ?>
                                <a href="index.php?page=<?= $i; ?>" class="page-number"><?= $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </p>
                    <p>Всего статей: <span class="text-bold"><?= $articles_amont; ?></span></p>
                </div>
                <div class="inner-container btn-container">
                    <a href="new_article.php">Добавить статью</a>
                </div>
            </div>
        <?php endif; ?>
    </body>
</html>