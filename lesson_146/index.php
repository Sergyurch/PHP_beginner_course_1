<!DOCTYPE html>
<html>
  <head>
    <title>Lesson 146</title>
  </head>
  <body>
    <?php
      $n = rand(3,12);
      echo "n = $n<br>";

      $arr1 = [];
      $arr2 = [];
    
      for ($i = 1; $i <= $n; $i++) {
        $arr1[] = "$i<sup>$i</sup>";
        $arr2[] = pow($i,$i);
        echo implode(' + ', $arr1) . ' = ' . array_sum($arr2) . '<br>';
      }
    
    ?> 
  </body>
</html>
