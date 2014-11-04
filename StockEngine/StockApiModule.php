<?php
/**
 * Created by PhpStorm.
 * User: Johnny
 * Date: 11/4/2014
 * Time: 1:57 PM
 */

class StockApiModule
{
    public function pullStockData($market)
    {
        $dataFileLinks = array();

        $dLinkStart = "http://download.finance.yahoo.com/d/";
        $dLinkFileName = "";
        $dlinkMid = "?s=";
        $dlinkTickers = "";
        $dLinkEnd = "&f=snl1oc1d1t1";

        $dLinkFull = "";

        if($market == 0) //nasdaq
        {

        }
        else if($market = 1) //dow jones
        {

        }



        return $dataFileLinks;
    }

} 