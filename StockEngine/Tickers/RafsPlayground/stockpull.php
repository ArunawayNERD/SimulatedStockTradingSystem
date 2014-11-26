<?php
/* Rafael Torres
   11/19/2014

   This php file will gather historical data from the yahoo api and
   store each file under the stock ticker later populate the history db.
*/

  $linkstart = "http://real-chart.finance.yahoo.com/table.csv?s=";
  $linkend   = "&a=10&b=19&c=2004&d=10&e=26&f=2014&g=d&ignore=.csv";

  $dlink = curl_init();
  curl_setopt($dlink, CURLOPT_RETURNTRANSFER, 1);

  // receives file contents as a string
  $tickers = file_get_contents("../RafsPlayground/dbstocks.txt"); 

  // parses large string into an array, separating elements by '\n'
  $ticks = explode("\n" , $tickers);
  
  // iterates through the array, puts url together, fetches data
  // and appends it to a file
  foreach($ticks as $company){
    $linkfull = $linkstart . $company . $linkend;
    curl_setopt($dlink, CURLOPT_URL, $linkfull);
    $data = curl_exec($dlink);
    file_put_contents("../RafsPlayground/files/$company.csv", $data);
  }

  curl_close($dlink);
?>
