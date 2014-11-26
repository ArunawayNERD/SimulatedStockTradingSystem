<?php

$dir = "../RafsPlayground/files/";
$files = scandir($dir);

foreach($files as $name){
  $handle = file_get_contents("./files/$name");
  $handle = str_replace("Date,Open,High,Low,Close,Volume,Adj Close\n" , "" , $handle);
  $handle = str_replace("\n" , ",$name\n" , $handle);
  $handle = str_replace(".csv" , "," , $handle);
  file_put_contents("../RafsPlayground/historical/$name", $handle);

}
?>
