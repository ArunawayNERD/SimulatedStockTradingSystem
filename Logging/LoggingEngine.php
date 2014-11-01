<?php
/**
 * Created by PhpStorm.
 * User: Johnny
 * Date: 10/29/2014
 * Time: 11:10 PM
 */

require "LogFileWriter.php";

class LoggingEngine
{
    private $fileWriter;

    public function __construct()
    {
        $this->fileWriter = new LogFileWriter();
    }

    public function logStockDateUpdate()
    {

    }

    public function logWhatIfScenario()
    {

    }

    public function logUserLogin($strUser, $boolSuccessOrFail)
    {

    }

    public function logUserCreation($strUser, $boolSuccessOrFail)
    {

    }


    public function logPortActivity($strUser, $boolSuccessOrFail)
    {

    }

    public function logTransActivity($strUser, $booBuyOrSell, $strItems, $boolSuccessOrFail)
    {

    }

    public function logCompActivity($strUser, $boolCreateOrDelete)
    {

    }

} 