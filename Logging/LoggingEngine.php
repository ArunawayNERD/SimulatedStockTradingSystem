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
        $msg = "Stock Data updated";

        $this->fileWriter->logMessage($msg);
    }

    public function logWhatIfScenario($strUser)
    {
        $msg = (String)$strUser . " created a What-If Scenario.";

        $this->fileWriter->logMessage($msg);
    }

    public function logUserLogin($strUser, $boolSuccessOrFail)
    {
        $msg = (String)$strUser;

        if($boolSuccessOrFail === true)
               $msg = $msg . " successfully logged into the system. ";
        else
                $msg = $msg . " failed to log into the system. ";

        $this->fileWriter->logMessage($msg);
    }

    public function logUserCreation($strIP, $strUser, $boolSuccessOrFail)
    {
        $msg = (String)$strIP;

        if($boolSuccessOrFail === true)
            $msg = $msg . " successfully created a new account " . (String)$strUser;
        else
            $msg = $msg . " failed to create a new account.";

        $this->fileWriter->logMessage($msg);
    }


    public function logPortActivity($strUser, $boolSuccessOrFail)
    {
        $msg = (String)$strUser;

        if($boolSuccessOrFail === true)
            $msg = $msg . " successfully created a new portfolio.";
        else
            $msg = $msg . " failed to create a new portfolio.";

        $this->fileWriter->logMessage($msg);
    }

    public function logTransActivity($strUser, $boolSuccessOrFail, $boolBuyOrSell, $strItems)
    {
        $msg = (String)$strUser;

        if($boolSuccessOrFail === true)
        {
            $msg = $msg . " successfully ";

            if($boolBuyOrSell === true)
                $msg = $msg . "bought ";
            else
                $msg = $msg . "sold ";
        }
        else
        {
            $msg = $msg . " failed to ";

            if ($boolBuyOrSell === true)
                $msg = $msg . "buy ";
            else
                $msg = $msg . "sell ";
        }

        $msg = $msg . (String)$strItems;

        $this->fileWriter->logMessage($msg);
    }

    public function logCompActivity($strUser, $boolCreateOrDelete)
    {
        $msg = (String)$strUser;

        if($boolCreateOrDelete === true)
            $msg = $msg . " created a competition.";
        else
            $msg = $msg . " deleted a competition.";

        $this->fileWriter->logMessage($msg);
    }

    public function logMessage($msg)
    {
        $this->fileWriter->logMessage($msg);
    }
} 