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
    $dLinkTickers = "";
    $dLinkEnd = "&f=snl1oc1d1t1";

    $dLinkFull = "";

    $cuResource = curl_init();
    curl_setopt($cuResource, CURLOPT_RETURNTRANSFER, 1);

    cleanOldData();

    if($market == 0) //nasdaq
    {
        $tickerFile = fopen(".\\Tickers\\DowTickers.txt", "r"); //open the ticker file for reading

        fopen(".\\StockData\\Nasdaq.csv", "a");

//        for($i = 0; $i < $fileLength; $i++)
//        {
//            curl_setopt($cuResource, CURLOPT_URL,$dLinkFull);
//        }
    }
    else if($market = 1) //dow jones
    {
        fopen(".\\Tickers\\DowTickers.txt", "r"); //open the ticker file for reading
        fopen(".\\StockData\\Dow.csv", "w");

        $dLinkTickers = file_get_contents(".\\Tickers\\DowTickers.txt");

        $dLinkFull = $dLinkStart . $dLinkTickers . $dLinkEnd;

        curl_setopt($cuResource, CURLOPT_URL,$dLinkFull);

        $returnData = curl_exec($cuResource);

        file_put_contents(".\\StockData\\Dow.csv", $returnData);
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