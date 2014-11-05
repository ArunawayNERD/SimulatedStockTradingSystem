<?php
/**
 * Author: John Pigott
 * Created: 11/4/2014
 *
 * This php file contains the code needed by the system to pull current
 * stock files from the yahoo api.
 */

/**
 * Function to pull stock data from the yahoo api.
 *
 * The data is pulled from both the Nasdaq and Dow Jones markets.
 *
 * The data is stored in 2 csv files found in the folder .\StockData
 */
function pullStockData()
{
    $dLinkStart = "http://download.finance.yahoo.com/d/CurrentQuotes?s=";
    $dLinkEnd = "&f=snl1oc1d1t1";

    $yahooUrl = curl_init();
    curl_setopt($yahooUrl, CURLOPT_RETURNTRANSFER, 1);

    //if file from the last pull still exists
    //delete it so the new data isn't appended to it
    cleanOldData();


    ////////////
    //Code to pull nasdaq stock data
    ///////////
    fopen(".\\Tickers\\NasdaqTickers.txt", "r"); //open the ticker file for reading
    fopen(".\\StockData\\Nasdaq.csv", "a"); //create file for all the stock data

    //load the ticker lines into array elements ignoring both newline chars and empty lines
    $tickerLines = file(".\\Tickers\\NasdaqTickers.txt", FILE_IGNORE_NEW_LINES |FILE_SKIP_EMPTY_LINES);

    for($i = 0; $i < count($tickerLines); $i++)
    {
        $dLinkTickers = $tickerLines[$i]; // load a line of tickers from the array

        $dLinkFull = $dLinkStart . $dLinkTickers . $dLinkEnd; // build full url

        curl_setopt($yahooUrl, CURLOPT_URL,$dLinkFull); //set the url to the curl resource

        $returnData = curl_exec($yahooUrl); //send request to yahoo

        file_put_contents(".\\StockData\\Nasdaq.csv", $returnData,FILE_APPEND ); //append returned data into the csv file
    }

    ////////////
    //Code to pull Dow Jones stock data
    ///////////
    fopen(".\\Tickers\\DowTickers.txt", "r"); //open the ticker file for reading
    fopen(".\\StockData\\Dow.csv", "w"); //create file for all stock data

    //pull the tickers from the file
    $dLinkTickers = file_get_contents(".\\Tickers\\DowTickers.txt");

    $dLinkFull = $dLinkStart . $dLinkTickers . $dLinkEnd; // build full link

    curl_setopt($yahooUrl, CURLOPT_URL,$dLinkFull); //set the url to the curl resource

    $returnData = curl_exec($yahooUrl); //send request to yahoo

    file_put_contents(".\\StockData\\Dow.csv", $returnData); //put returned data in to a csv file
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