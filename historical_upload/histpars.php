<?php 

/* Rafael Torres 
   11/19/2014 

   This php file will modify the raw stock data files and append the 
   stock ticker inforation to allow easy entry into the database as a
   .csv file. Files should be located in the files folder after having 
   executed stockpull.php.
*/

$dir = "../historical_upload/files/";
$files = scandir($dir);

foreach($files as $name){
  $handle = file_get_contents("./files/$name");
  $handle = str_replace("Date,Open,High,Low,Close,Volume,Adj Close\n" , "" , $handle);
  $handle = str_replace("\n" , ",$name\n" , $handle);
  $handle = str_replace(".csv" , "," , $handle);
  file_put_contents("../historical_upload/historical/$name.csv", $handle);

}
?>
