<html>
  <head>
    <title>PHP Test</title>
  </head>
  <body>
    <?php
      $user_agent = $_SERVER['HTTP_USER_AGENT'];
      $os_array = ['Windows' => 'Windows NT', 'Linux' => 'Linux', 'Mac OS' => 'Mac OS', 'Android', 'iOS' => 'OS'];
      $result = 'Неизвестная операционная система';
      
      foreach ($os_array as $os => $value) {
        if ( $os === 'Linux' && stripos($user_agent, 'Android') ) continue;
        
        if ($os === 'Mac OS') {
          if ( stripos($user_agent, 'iPhone') || stripos($user_agent, 'iPad') ) continue;
        }

        if ($os === 'iOS') {
          if ( !stripos($user_agent, 'iPhone') && !stripos($user_agent, 'iPad') ) continue;
        }

        if ( stripos($user_agent, $value) ) {
          $result = 'У вас ' . get_os($user_agent, $value);
          break;
        }
      }

      echo "<p>$result</p>"; 

      function get_os($user_agent, $sub_string) {
        $start = stripos($user_agent, $sub_string);
        $finish1 = stripos($user_agent, ';', $start);
        $finish2 = stripos($user_agent, ')', $start);
        $finish = ( ($finish1 != false) and ($finish1 < $finish2) ) ? $finish1 : $finish2;
        return substr($user_agent, $start, $finish - $start);
      }
    ?> 
  </body>
</html>
