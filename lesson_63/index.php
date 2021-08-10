<?php
    $current_year = date('Y', time());
    $x = rand( mktime(0,0,0,1,1,$current_year), mktime(0,0,-1,1,1,$current_year + 2) );
    $months = ['января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];
    $days = ['понедельник','вторник','среда','четверг','пятница','суббота','воскресенье'];
    $holidays = ['1-01','7-01','23-02','8-03','1-05','9-05','12-06','4-11'];
    $date = date('j', $x);
    $month = $months[date('n', $x) - 1];
    $year = date('Y', $x);
    $day_name = $days[date('N', $x) - 1];
    $weekend = ( (date('N', $x) == 7) || (date('N', $x) == 6) ) ? 'выходной ': '';
    $holiday = ( in_array( $date . '-' . date('m', $x), $holidays ) ) ? 'праздник': '';
    
    echo date(DATE_ATOM, $x) . "\n";
    echo $date . ' ' . $month . ' ' . $year . ' ' . $day_name . ' ' . $weekend . $holiday;
?>


