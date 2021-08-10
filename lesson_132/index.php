<?php
    $no_file_error = false;
    $upload_success = false;
    $upload_error = false;
    
    if ( isset($_POST['files']) ) { // Если форма отправлена
        if ( $_FILES['main-photo']['error'] == 4 || $_FILES['other-photos'] == 4 ) { // Если не выбраны файлы
            $no_file_error = true;
        } else {
            // Очищаем папки с изображениями
            clear_dir('images/main');
            clear_dir('images/other');
            
            // Загружаем основное фото
            $main_photo_path = 'images/main/' . $_FILES['main-photo']['name'];
            move_uploaded_file( $_FILES['main-photo']['tmp_name'], $main_photo_path ); 
            
            // Загружаем остальные фотографии
            foreach( $_FILES['other-photos']['tmp_name'] as $key => $tmp_path )  {
                $path = 'images/other/' . $_FILES['other-photos']['name'][$key];    
                $other_photos_paths[] = $path;
                move_uploaded_file( $tmp_path, $path );
            }

            // Смотрим на кол-во файлов в папке
            if ( count(scandir('images/main')) > 2 && count(scandir('images/other')) > 2) {
                $upload_success = true;
            } else {
                $upload_error = true;
            }
        }
    }

    // Функция удаления всех файлов из папки
    function clear_dir($dir_path) {
        $files = glob("$dir_path/*");
        foreach ($files as $file) {
            if (is_file($file) ) unlink($file);
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 132</title>
        <style>
            .container {
                display: flex;
                justify-content: space-between;
            }

            .form-container {
                width: 40%;
            }

            .photos-container {
                width: 60%;
            }

            input[type="submit"] {
                margin-top: 20px;
            }

            .error-msg {
                padding: 20px;
                background-color: red;
            }

            .success-msg {
                padding: 20px;
                background-color: green;
            }

            .main-photo {
                width: 300px;
                height: 300px;
                margin: auto;
                overflow: hidden;
                position: relative;
                margin-bottom: 20px;
            }

            .main-photo img, .other-photos-item img {
                position: absolute;
                top: 50%;
                left: 50%;
                transform:translate(-50%,-50%);
            }

            .other-photos-container {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .other-photos-item {
                width: 150px;
                height: 150px;
                overflow: hidden;
                position: relative;
                margin-bottom: 10px;
            }

        </style>
    </head>
    <body>
        <div class="container">
            <div class="form-container">
                <!-- Если не выбран файл -->
                <?php if ($no_file_error): ?>
                    <p class="error-msg">Не выбрано главное фото или другие фотографии.</p>
                <?php endif; ?>

                <!-- Если ошибка загрузки -->
                <?php if ($upload_error): ?>
                    <p class="error-msg">Ошибка загрузки файлов.</p>
                <?php endif; ?>

                <!-- Если загрузка прошла успешно -->
                <?php if ($upload_success): ?>
                    <p class="success-msg">Файлы успешно загружены.</p>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <p>Главное фото</p>
                    <input type="file" name="main-photo">
                    <p>Другие фото</p>
                    <input type="file" name="other-photos[]" multiple><br>
                    <input type="submit" name="files" value="Загрузить">
                </form>
            </div>
            <div class="photos-container">
                <div class="main-photo-container">
                    <div class="main-photo">
                        <!-- Выводи основное фото -->
                        <?php if ( count(scandir('images/main')) > 2 ): ?>
                            <?php
                                $files = glob('images/main/*');
                                $main_photo_path = '';
                                foreach ($files as $file) {
                                    if (is_file($file) ) $main_photo_path = $file;
                                }
                            ?>
                                
                            <img src="<?= $main_photo_path; ?>">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="other-photos-container">
                    <!-- Выводим остальные фото -->
                    <?php if ( count(scandir('images/other')) > 2 ): ?>
                        <?php
                            $files = glob('images/other/*');
                            $other_photos_paths = [];
                            foreach ($files as $file) {
                                if (is_file($file) ) $other_photos_paths[] = $file;
                            }

                        ?>
                        <?php foreach ($other_photos_paths as $path): ?>
                            <div class="other-photos-item">
                                <img src="<?= $path; ?>">
                            </div>  
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </body>
</html>