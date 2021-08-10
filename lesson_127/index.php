<?php 
    mb_internal_encoding('utf-8');
    $text = 'Пиву очень пиво, береза пива пиве пиву пивом пивомдлинный текст очень длинный пивами текст очень длинный текст пиво';
    
    if ( isset($_GET['word']) ) {
        //Если слова в кавычках
        if ( mb_ereg_match("^\".*\"$", $_GET['word']) ) {
            $word = mb_substr($_GET['word'], 1, mb_strlen($_GET['word']) - 2);
            $word_to_find = modify_word($word);
            $result_text = modify_text($text, $word_to_find);
        //Если несколько слов через пробел
        } elseif ( mb_ereg_match(".* .*", $_GET['word']) ) {
            $result_text = $text;

            foreach(explode(' ', $_GET['word']) as $word) {
                $word_to_find = modify_word($word);
                $result_text = modify_text($result_text, $word_to_find);
            }
        //Если просто одно слово
        } else {
            $word = $_GET['word'];
            $word_to_find = modify_word($word);
            $result_text = modify_text($text, $word_to_find);
        }
                 
    } else {
        $result_text = $text;
    }

    //Функция возвращает часть слова без падежа
    function modify_word($word) {
        if ( mb_ereg_match(".*(ом|ой|ей|ям|ями|ами|ьми|ь|у|е|ы|а|ом|э|и|ю|о)$", $word) ) {
            return mb_eregi_replace("(ом|ой|ей|ям|ями|ами|ьми|ь|у|е|ы|а|ом|э|и|ю|о)$", "", $word);
        } else {
            return $word;
        }
    }

    //Функция ищет слово с учетом падежей
    function modify_text($text, $word) {
        $result = $text;

        if ( mb_stripos($text, $word) !== false ) {
            $result = mb_eregi_replace("(\\W|^)($word(ом|ой|ей|ям|ями|ами|ьми|ь|у|е|ы|а|ом|э|и|ю|о))(\\W|$)", "\\1<span style=\"background-color: yellow;\">\\2</span>\\4", $result);
        } else {
            $result = $text;
        }
        
        return $result;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 127</title>
    </head>
    <body>
        <p><?= $result_text; ?></p>
        <form method="GET">
            <label>
                <span>Введите текст для поиска: </span>
                <input type="text" name="word">
            </label>
            <input type="submit" value="Поиск">
        </form>
    </body>
</html>
