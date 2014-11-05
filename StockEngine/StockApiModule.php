<?php
/**
 * Created by PhpStorm.
 * User: Johnny
 * Date: 11/4/2014
 * Time: 1:57 PM
 */

function pullStockData($market)
{
    $dLinkStart = "http://download.finance.yahoo.com/d/CurrentQuotes?s=";
    $dLinkEnd = "&f=snl1oc1d1t1";

    $yahooUrl = curl_init();
    curl_setopt($yahooUrl, CURLOPT_RETURNTRANSFER, 1);

    cleanOldData();

    if($market == 0) //nasdaq
    {
        fopen(".\\Tickers\\NasdaqTickers.txt", "r"); //open the ticker file for reading
        fopen(".\\StockData\\Nasdaq.csv", "a"); //create file for all stock data
        fopen(".\\StockData\\temp.csv", "w+"); //temp file to hold stock data before its appended

        //load the ticker lines into array elements
        $tickerLines = file(".\\Tickers\\NasdaqTickers.txt", FILE_IGNORE_NEW_LINES |FILE_SKIP_EMPTY_LINES);

        for($i = 0; $i < count($tickerLines); $i++)
        {
            $dLinkTickers = $tickerLines[$i]; // load a line of tickers from the file

            $dLinkFull = $dLinkStart . $dLinkTickers . $dLinkEnd; // build full url

            curl_setopt($yahooUrl, CURLOPT_URL,$dLinkFull); //set the url to the curl resource

            $returnData = curl_exec($yahooUrl); //send request to yahoo

            file_put_contents(".\\StockData\\Nasdaq.csv", $returnData,FILE_APPEND ); //put returned data in to csv file
        }
    }
    else if($market = 1) //dow jones
    {
        fopen(".\\Tickers\\DowTickers.txt", "r"); //open the ticker file for reading
        fopen(".\\StockData\\Dow.csv", "w"); //create file for all stock data

        //pull the tickers from the file
        $dLinkTickers = file_get_contents(".\\Tickers\\DowTickers.txt");

        $dLinkFull = $dLinkStart . $dLinkTickers . $dLinkEnd; // build full link

        curl_setopt($yahooUrl, CURLOPT_URL,$dLinkFull); //set the url to the curl resource

        $returnData = curl_exec($yahooUrl); //send request to yahoo
        echo ($returnData);


        file_put_contents(".\\StockData\\Dow.csv", $returnData); //put returned data in to a csv file
    }
}

/**
 * If old stock data remains from last stock update delete it.
 */
function cleanOldData()
{
    if(file_exists(".\\StockData\\Nasdaq.csv"))
        unlink(".\\StockData\\Nasdaq.csv");

    if(file_exists(".\\StockData\\Dow.csv"))
        unlink(".\\StockData\\Dow.csv");
}