<?php
    mb_internal_encoding('utf-8');
    $storage_path = 'storage/'; // Основное хранилище файлов
    $errors = [];
    $success_messages = [];

    // Если передан параметр успеха
    if ( isset($_GET['success']) ) {
        switch ($_GET['success']) {
            case 'upload':
                $success_messages[] = 'Файл успешно загружен';
                break;
            case 'delete':
                $success_messages[] = 'Удаление прошло успешно';
                break;
            case 'rename':
                $success_messages[] = 'Переименование прошло успешно';
                break;
            case 'edit':
                $success_messages[] = 'Файл успешно отредактирован';
                break;
        }
    }

    // Если передан параметр на редактирование
    if ( isset($_POST['edit']) ) {
        $file_path = $_POST['edit'];
        $file_content = $_POST['new_text'];

        if ( file_put_contents($file_path, $file_content) ) {
            header('Location:' . basename(__FILE__) . '?success=edit');
        } else {
            $errors[] = 'Ошибка редактирования файла';
        }
    }

    // Если передан параметр на переименование
    if ( isset($_GET['new_name']) ) {
        if ( empty($_GET['new_name']) ) {
            $errors[] = 'Вы не указали новое имя';
        } else {
            $old_path = $_GET['rename'];

            if ( is_file($old_path) ) {
                $extension = substr( $old_path, strrpos($old_path, '.') );
            } else {
                $extension = '';
            }

            $new_path = dirname($old_path) . DIRECTORY_SEPARATOR . $_GET['new_name'] . $extension;

            if ( rename($old_path, $new_path) ) {
                header('Location:' . basename(__FILE__) . '?success=rename');
            } else {
                $errors[] = 'Ошибка переименования';
            }
            
        }
    }

    // Если передан параметр на удаление
    if ( isset($_GET['delete']) ) {
        if ( recursive_delete($_GET['delete']) ) {
            header('Location:' . basename(__FILE__) . '?success=delete');
        } else {
            $errors[] = 'Ошибка удаления файла';
        }
    }

    // Выбираем какой каталог показывать
    if ( isset($_GET['dir']) ) {
        $current_dir_path = $_GET['dir'] . '/';
    } else {
       $current_dir_path = $storage_path; 
    }

    // Если была отправлена форма на добавление файла
    if ( isset($_POST['add_file']) ) {
        if ($_FILES['file']['error'] == 4) {
            $errors[] = 'Файл не выбран';
        } else {
            $file_tmp_path = $_FILES['file']['tmp_name'];
            $new_file_path = $current_dir_path . $_FILES['file']['name'];  
            if ( move_uploaded_file($file_tmp_path, $new_file_path) ) {
                header('Location:' . basename(__FILE__) . '?success=upload');
            } else {
                $errors[] = 'Ошибка загрузки файла';
            }
        }
    }
   
    // Получаем список файлов и папок которые надо показать
    if ( !isset($_GET['rename']) and !isset($_GET['edit'])) {
        if ($current_dir_path == $storage_path) {
            $files = array_filter(scandir($current_dir_path), function($file) {
                return $file != '.' and $file != '..';
            });
        } else {
            $files = array_filter(scandir($current_dir_path), function($file) {
                return $file != '.';
            });
        }
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

    // Функция рекурсивного удаления файла или папки
    function recursive_delete($path) {
        if ( is_file($path) ) return unlink($path);
        if ( is_dir($path) ) {
            foreach (scandir($path) as $file) {
                if ( ($file != '.') && ($file != '..') ) {
                    recursive_delete($path . DIRECTORY_SEPARATOR . $file);
                }
            }
            return rmdir($path); 
        }

        return false;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 143</title>
        <style>
            body {
                padding: 20px;
            }

            h1 {
                text-align: center;
            }

            form {
                text-align: right;
                margin-bottom: 30px;
            }

            form.left-align {
                text-align: left;
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

            textarea {
                margin: 20px 0;
                width: 100%;
            }

            .text-center {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <!-- Форма переименования-->
        <?php if ( isset($_GET['rename']) ): ?>
            <h1>Переименование файла/папки</h1>
            <p>Вы собираетесь переименовать файл или папку: <?= $_GET['rename']; ?></p>

            <!-- Если есть ошибки -->
            <?php foreach ($errors as $error): ?>
                <div class="error-msg"><?= $error; ?></div>
            <?php endforeach; ?>

            <form method="GET" class="left-align">
                <label>
                    <span>Введите новое имя</span>
                    <input type="text" name="new_name">
                    <input type="hidden" name="rename" value="<?= $_GET['rename'] ?>">
                    <input type="submit" value="Переименовать">
                </label>
            </form>

            <a href="<?= basename(__FILE__); ?>">Вернуться в главный каталог</a>

        <!-- Форма редактирования-->
        <?php elseif ( isset($_GET['edit']) ): ?>
            <h1>Редактирование файла</h1>
            <p>Вы собираетесь отредактировать файл: <?= $_GET['edit']; ?></p>

            <form method="POST" class="left-align">
                <label>
                    <span>Отредактируйте текст файла</span><br>
                    <textarea name="new_text" rows="20"><?= file_get_contents($_GET['edit']); ?></textarea><br>
                    <input type="hidden" name="edit" value="<?= $_GET['edit'] ?>">
                    <input type="submit" value="Сохранить">
                </label>
            </form>

            <a href="<?= basename(__FILE__); ?>">Вернуться в главный каталог</a>

        <!-- Общий каталог-->
        <?php else: ?>
            <div class='text-center'>
                <img src="https://img.icons8.com/plasticine/50/000000/folder-invoices.png" alt="Картинка">
                <h1>Файловый менеджер</h1>
            </div>
            
            <!-- Если есть ошибки -->
            <?php foreach ($errors as $error): ?>
                <div class="error-msg"><?= $error; ?></div>
            <?php endforeach; ?>

            <!-- Если надо вывести сообщение об успешной операции -->
            <?php foreach ($success_messages as $message): ?>
                <div class="success-msg"><?= $message; ?></div>
            <?php endforeach; ?>

            <p>Текущая папка: <?= $current_dir_path; ?></p>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="file">
                <input type="submit" name="add_file" value="Загрузить в текущую папку">
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
                    <!-- Выводим список файлов и папок -->
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
                                    <a href="<?= basename(__FILE__) . "?rename=$current_dir_path$file"; ?>">Переименовать</a>
                                    <a href="<?= basename(__FILE__) . "?delete=$current_dir_path$file"; ?>">Удалить</a>
                                </td>
                            </tr>
                        <!-- Если файл -->
                        <?php else: ?>
                            <tr>
                                <td><?= $file; ?></td>
                                <td><?= get_file_size($current_dir_path . $file); ?></td>
                                <td>
                                    <a href="<?= basename(__FILE__) . "?rename=$current_dir_path$file"; ?>">Переименовать</a>
                                    <a href="<?= basename(__FILE__) . "?delete=$current_dir_path$file"; ?>">Удалить</a>
                                    <a href="<?= $current_dir_path . $file; ?>" download>Скачать</a>
                                    <!-- Определяем, можно ли редактировать файл -->
                                    <?php if ( preg_match('/.*\.(txt|doc|docx|odt)$/', $file) ):?>
                                        <a href="<?= basename(__FILE__) . "?edit=$current_dir_path$file"; ?>">Редактировать</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </body>
</html>