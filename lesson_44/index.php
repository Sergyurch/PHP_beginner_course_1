<?php
    mb_internal_encoding('UTF-8');
    $text = 'Текст фпроизвольной Ф длины. Необходимо Фподсчитать количество количество символов в тексте, количество пробелов и русской буквы';
    $spaces_quantity = mb_substr_count($text, ' ');
    $no_spaces_quantity = mb_strlen($text) - $spaces_quantity;
    $F_quantity = mb_substr_count($text, 'Ф');
    $f_quantity = mb_substr_count($text, 'ф');
    if ($F_quantity == 0) $F_quantity = 'нет';
    if ($f_quantity == 0) $f_quantity = 'нет';
    $result = str_replace(['Ф','ф'], ['<span style="background: yellow;">Ф</span>','<span style="background: yellow;">ф</span>'], $text);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PHP Lesson 44</title>
</head>
<body>
    <p>Количество символов: <?= $no_spaces_quantity; ?></p>
    <p>Количество пробелов: <?= $spaces_quantity; ?></p>
    <p>Количество букв Ф: <?= $F_quantity; ?></p>
    <p>Количество букв ф: <?= $f_quantity; ?></p>
    <p><?= $result; ?></p>
    
</body>
</html>
