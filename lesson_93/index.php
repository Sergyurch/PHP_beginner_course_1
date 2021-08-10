<?php
    $file_size = rand(1, 1000000000000);
    $gigabyte = 1073741824;
    $megabyte = 1048576;
    $kilobyte = 1024;
    
    echo 'Размер файла в байтах ' . $file_size . "\n";
    
    if ($file_size % $gigabyte != $file_size) {
        $result = 'Это ' . round( $file_size / $gigabyte, 2) . 'Гб';
    } elseif ($file_size % $megabyte != $file_size) {
        $result = 'Это ' . round( $file_size / $megabyte, 2) . 'Мб';
    } elseif ($file_size % $kilobyte != $file_size) {
        $result = 'Это ' . round( $file_size / $kilobyte, 2) . 'Кб';
    } else {
        $result = 'Это ' . $file_size . 'б';
    }
    
    echo $result;
?>

