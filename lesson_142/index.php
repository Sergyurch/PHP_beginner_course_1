<?php
    $storage_path = 'storage/'; // Основное хранилище файлов
    $errors = [];

    // Выбираем какой каталог показывать
    if ( isset($_GET['dir']) ) {
        $current_dir_path = $_GET['dir'] . '/';
    } else {
       $current_dir_path = $storage_path; 
    }

    // Если форма была отправлена
    if ( isset($_POST['submit']) ) {
        if ($_FILES['file']['error'] == 4) {
            $errors[] = 'Файл не выбран';
        } else {
            $file_tmp_path = $_FILES['file']['tmp_name'];
            $new_file_path = $current_dir_path . $_FILES['file']['name'];  
            if ( move_uploaded_file($file_tmp_path, $new_file_path) ) {
                header('Location:' . basename(__FILE__) . '?upload_success=1');
            } else {
                $errors[] = 'Ошибка загрузки файла';
            }
        }
    }
   
    // Получаем список файлов и папок которые надо показать
    if ($current_dir_path == $storage_path) {
        $files = array_filter(scandir($current_dir_path), function($file) {
            return $file != '.' and $file != '..';
        });
    } else {
        $files = array_filter(scandir($current_dir_path), function($file) {
            return $file != '.';
        });
    }
    
    // Функция возвращает размер файла в привычных единицах
    function get_file_size($file_path) {
        $gigabyte = 1073741824;
        $megabyte = 1048576;
        $kilobyte = 1024;
        $file_size = filesize($file_path);
           
        if ($file_size % $gigabyte != $file_size) {
            $result = round( $file_size / $gigabyte, 2) . 'Гб';
        } elseif ($file_size % $megabyte != $file_size) {
            $result = round( $file_size / $megabyte, 2) . 'Мб';
        } elseif ($file_size % $kilobyte != $file_size) {
            $result = round( $file_size / $kilobyte, 2) . 'Кб';
        } else {
            $result = $file_size . 'б';
        }

        return $result;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 142</title>
        <style>
            body {
                padding: 20px;
            }

            h1 {
                text-align: center;
            }

            form {
                text-align: right;
            }

            table {
                width: 100%;
                margin-top: 20px;
                border-collapse: collapse;
            }

            th, td {
                padding: 10px;
                border: 1px solid black;
            }

            .error-msg {
                padding: 20px;
                background-color: red;
                margin: 20px 0;
            }

            .success-msg {
                padding: 20px;
                background-color: green;
                margin: 20px 0;
            }
        </style>
    </head>
    <body>
        <h1>Файловый менеджер</h1>

        <!-- Если есть ошибки -->
        <?php foreach ($errors as $error): ?>
            <div class="error-msg"><?= $error; ?></div>
        <?php endforeach; ?>

        <!-- Если файл загружен успешно -->
        <?php if ( isset($_GET['upload_success']) ): ?>
            <div class="success-msg">Файл успешно загружен</div>
        <?php endif; ?>

        <p>Текущая папка: <?= $current_dir_path; ?></p>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file">
            <input type="submit" name="submit" value="Загрузить в текущую папку">
        </form>
        <table>
            <thead>
                <tr>
                    <th>Имя файла или папки</th>
                    <th>Размер</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $files as $file ): ?>
                    <!-- Если папка -->
                    <?php if ( is_dir( $current_dir_path . $file ) ): ?>
                        <tr>
                            <td>
                                <?php if ($file == '..'): ?>
                                    <a href="<?= basename(__FILE__) . "?dir=" . basename(dirname($current_dir_path)); ?>"><?= $file; ?></a>
                                <?php else: ?>
                                    <a href="<?= basename(__FILE__) . "?dir=$current_dir_path$file"; ?>"><?= $file; ?></a>
                                <?php endif; ?>
                            </td>
                            <td>---</td>
                            <td>
                                <a href="#">Переименовать</a>
                                <a href="#">Удалить</a>
                            </td>
                        </tr>
                    <!-- Если файл -->
                    <?php else: ?>
                        <tr>
                            <td><?= $file; ?></td>
                            <td><?= get_file_size($current_dir_path . $file); ?></td>
                            <td>
                                <a href="#">Переименовать</a>
                                <a href="#">Удалить</a>
                                <a href="#">Скачать</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>