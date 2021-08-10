<?php
    mb_internal_encoding('utf-8');
 ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 128</title>
        <style>
            input[type="number"] {
                width: 40px;
            }

            .error {
                background-color: red;
                padding: 20px;
            }

            .success {
                background-color: green;
                padding: 20px;
            }
        </style>
    </head>
    <body>
        <?php if ( isset($_POST['submit']) ): ?>
            <?php if ( $_FILES['file']['error'] == 4 ): ?>
                <p class="error">Не выбран файл для загрузки!</p>
            <?php else: ?>
                <?php
                    //Определяем название будущего файла
                    switch ( $_POST['name-style'] ) {
                        case '1':
                            $filename = date('Y-m-d');
                            break;
                        case '2':
                            $filename = date('Y-m-d_H-i');
                            break;
                        case '3':
                            $symbols = range('a','z');
                            $filename = '';

                            for ($i = 1; $i <= $_POST['length']; $i++) {
                                $filename .= $symbols[rand(0, count($symbols) - 1 )];
                            }

                            break;
                    }

                    $file_tmp_name = $_FILES['file']['tmp_name']; //Временное название файла
                    $file_origin = basename($_FILES['file']['name']); //Ориинальное имя с расширением
                    $dot_position = mb_strrpos($file_origin, '.'); 
                    $file_extension = mb_substr($file_origin, $dot_position); //Расширение оригинального файла
                ?>
                <!-- Загружаем файл --> 
                <?php if ( move_uploaded_file($file_tmp_name, $filename . $file_extension) ): ?>
                    <p class="success">Файл успешно загружен. Имя файла <?= $filename; ?>. Скачать.</p>
                <?php else: ?>
                    <p class="error">Ошибка во время загрузки файла!</p>
                <?php endif; ?>

            <?php endif; ?>
        <?php endif; ?>
        <div>
            <h1>Загрузить файл</h1>
            <form method="POST" enctype="multipart/form-data">
                <div>
                    <input type="file" name="file">
                </div>
                <h2>Имя файла:</h2>
                <label>
                    <input type="radio" name="name-style" value="1" checked>
                    <span>текущая дата в формате ГГГГ-ММ-ДД</span>
                </label><br>
                <label>
                    <input type="radio" name="name-style" value="2">
                    <span>текущая дата в формате ГГГГ-ММ-ДД_ЧЧ-ММ</span>
                </label><br>
                <label>
                    <input type="radio" name="name-style" value="3">
                    <span>случайная строка длиной <input type="number" name="length" value="5"> символов</span>
                </label><br><br>
                <input type="submit" name="submit" value="Сохранить">
            </form>
        </div>
    </body>
</html>