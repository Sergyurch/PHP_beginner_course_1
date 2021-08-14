<?php
    mb_internal_encoding('utf-8');

    function get_last_working_day($month_name, $year) {
        // Функция склоняет название месяца
        function format_month_name($month_name) {
            if ( in_array(mb_strtolower($month_name), ['март', 'август']) ) {
                return $month_name . 'а';
            } else {
                $length_to_cut = mb_strlen($month_name) - 1;
                return mb_substr($month_name, 0, $length_to_cut) . 'я';
            }
        }

        $months = [
            'январь',
            'февраль',
            'март',
            'апрель',
            'май',
            'июнь',
            'июль',
            'август',
            'сентябрь',
            'октябрь',
            'ноябрь',
            'декабрь'
        ];

        $year = $year;
        $month_number = array_keys( $months, mb_strtolower($month_name) )[0] + 1;

        $holidays = [
            '1-01',
            '2-01',
            '3-01',
            '4-01',
            '5-01',
            '6-01',
            '7-01',
            '8-01',
            '23-02',
            '8-03',
            '1-05',
            '9-05',
            '12-06',
            '4-11'
        ];
        
        // Определяем последний рабочий день
        $j = 0;

        do {
            $last_working_day = mktime(0, 0, 0, $month_number + 1, $j, $year);
            $day_number = date('N', $last_working_day);
            $date_month = date('j-m', $last_working_day);
            $j--;
        } while ( in_array($day_number, [6,7]) or in_array($date_month, $holidays) );
        
        $date = date('j', $last_working_day); 
        $month = format_month_name( $months[date('n', $last_working_day) - 1] );

        $full_date = $date . '-' . $month . '-' . $year; // Полная дата последнего рабочего дня
        
        echo "Месяц - {$months[$month_number-1]}.<br>";
        echo "Год - $year.<br>";
        echo "Последний рабочий день - {$full_date}г.";
           
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 150</title>
    </head>
    <body>
        <div><?= get_last_working_day('Октябрь', 2021); ?></div>
    </body>
</html>
