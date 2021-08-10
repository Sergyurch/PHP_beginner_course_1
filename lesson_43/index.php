<?php
    mb_internal_encoding('UTF-8');
    $file_name = '/home/users/downloads/реферат.pdf';
 
    $dot_position = mb_strripos($file_name, '.');
    $slash_position = mb_strripos($file_name, '/');
 
	$extension = mb_substr($file_name, $dot_position + 1);
	$name = mb_substr($file_name, $slash_position + 1, $dot_position - $slash_position - 1);
	$name_length = mb_strlen($name);
	$folder = mb_substr($file_name, 0, $slash_position);
	$folder = mb_substr( $folder, mb_strripos($folder, '/') + 1 );
 
	echo 'Расширение файла: ' . $extension . "\n";
	echo 'Имя файла: ' . $name . "\n";
	echo 'Длина имени файла: ' . $name_length . "\n";
	echo 'Родительская папка: ' . $folder . "\n";
 
?>
