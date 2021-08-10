<?php
    mb_internal_encoding('utf-8');
    date_default_timezone_set('Europe/Kaliningrad');
    
    $files_dir = 'uploads';
    $year = date('Y');
    $month = date('m');
    $day = date('d');
    $hours = date('H');
    $minutes = date('i');
    $seconds = date('s');
    $upload_success = false;
    $upload_error = false;
    $no_file_error = false;
    $wrong_file_extension = false;
    
    // Если была отправлена форма
    if ( isset($_FILES['file']) ) {
        if ( $_FILES['file']['error'] == 4 ) { // Если файл не выбран
            $no_file_error = true;
        } else {
            $file_tmp_name = $_FILES['file']['tmp_name']; //Временное название файла
            $file_origin = basename($_FILES['file']['name']); //Ориинальное имя с расширением
            $dot_position = mb_strrpos($file_origin, '.'); 
            $file_extension = mb_strtolower( mb_substr($file_origin, $dot_position + 1) ); //Расширение оригинального файла
            $new_filename = "$year-$month-{$day}_$hours-$minutes-$seconds";

            if ( !in_array($file_extension, ['doc','docx','odt']) ) { // Если недопустимый тип файла
                $wrong_file_extension = true;
            } else {
                @mkdir("$files_dir/$year/$month/$day", 0777, true);
                if ( move_uploaded_file($file_tmp_name, "$files_dir/$year/$month/$day/" . "$new_filename.$file_extension") ) {
                    $upload_success = true;
                } else {
                    $upload_error = true; 
                }
            }
             
        }
    }

    // Если есть запрос на удаление файла
    if ( isset($_GET['delete']) ) {
        $finish_cut = strpos($_GET['delete'], '_');
        $filepath_tmp = str_replace( '-', '/', substr($_GET['delete'], 0, $finish_cut) );
        $filepath = $files_dir . '/' . $filepath_tmp . '/';
        unlink($filepath . $_GET['delete']);
        header('Location: index.php');
    }

    // Функция выводит на экран содержимое папки
    function show_dir_content($dir) {
        $dir_name = basename($dir);
        echo "<ul><li>$dir_name<ul>";
        
        foreach ( scandir($dir, SCANDIR_SORT_NONE) as $dir_item ) {
            if ( ($dir_item != '.') && ($dir_item != '..') ) {
                if ( is_dir("$dir/$dir_item") ) {
                    show_dir_content("$dir/$dir_item");
                } else {
                    echo "<li class=\"flex\">$dir_item <a href=\"index.php?delete=$dir_item\"><i class=\"material-icons\" style=\"font-size:26px\">close</i></a></li>";
                }
            }
        }
        echo "</ul></li></ul>";
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <title>Lesson 130</title>
        <style>
            .container {
                display: flex;
                justify-content: space-between;
            }

            .container div {
                width: 50%;
            }

            .error-msg {
                padding: 20px;
                background-color: red;
            }

            .success-msg {
                padding: 20px;
                background-color: green;
            }

            ul {
                list-style-type: none;
            }

            li.flex {
                display: flex;
                align-items: center;
            }

            li i {
                padding-left: 15px;
                color: red;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div>
                <h2>Форма загрузки файлов</h2>
                
                <!-- Сообщение, если файл не выбран -->
                <?php if ($no_file_error): ?>
                    <p class="error-msg">Файл не выбран</p>
                <?php endif; ?>

                <!-- Сообщение, если недопустимый тип файла -->
                <?php if ($wrong_file_extension): ?>
                    <p class="error-msg">Недопустимый тип файла. Разрешено загружать только DOC, DOCX и ODT.</p>
                <?php endif; ?>

                <!-- Сообщение, если ошибка загрузки файла -->
                <?php if ($upload_error): ?>
                    <p class="error-msg">Ошибка загрузки файла.</p>
                <?php endif; ?>

                <!-- Сообщение, если файл успешно загружен -->
                <?php if ($upload_success): ?>
                    <p class="success-msg">Файл успешно загружен.</p>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="file">
                    <input type="submit" name="file-upload" value="Загрузить файл">
                </form>
            </div>
            <div>
                <?php
                    show_dir_content($files_dir);
                ?>
            </div>
        </div>
    </body>
</html>