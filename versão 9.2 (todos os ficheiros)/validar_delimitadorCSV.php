  <?php

       //The delimiters array to look through
       $delimiters = array(
        'semicolon' => ";",
        'tab'       => "\t",
        'comma'     => ",",
    );
    
    $file =  $_FILES["file"]["tmp_name"];
    
    //Load the csv file into a string
    $csv = file_get_contents($file);
    
    
    foreach ($delimiters as $key => $delim) {
        $res[$key] = substr_count($csv, $delim);
    }
    
    //reverse sort the values, so the [0] element has the most occured delimiter
    arsort($res);
    
    reset($res);
    $first_key = key($res);
    
    $d=$delimiters[$first_key];
    //echo($d); 

 ?>