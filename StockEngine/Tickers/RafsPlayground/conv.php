<?php
/*
$file = "../RafsPlayground/allstocks.txt";
$handle = file_get_contents($file);
$handle = str_replace("," , "\n", $handle);
file_put_contents($file, $handle);
*/

$file = "../RafsPlayground/historical/histdbload.csv";
$handle = file_get_contents($file);
$handle = str_replace("\n" , ",\n", $handle);
file_put_contents($file, $handle);

?>
