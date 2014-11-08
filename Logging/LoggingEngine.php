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


    /*
     *  Constructs a new logging engine
     */
    public function __construct()
    {
        $this->fileWriter = new LogFileWriter();
    }

    /*
     * Writes that the daily stock data was updated to the daily log file
     */
    public function logStockDataUpdate()
    {
        $msg = "Stock Data updated";

        $this->fileWriter->logMessage($msg);
    }

    /*
     * Writes that a user created a what if scenario to the daily log file
     *
     */
    public function logWhatIfScenario($User)
    {
        $msg = (String)$strUser . " created a What-If Scenario.";

        $this->fileWriter->logMessage($msg);
    }

    /*
     * Writes to the daily log file that a user logged in
     *
     * param $strUser - the user to log in
     * param $boolSuccessful - true if the log in was successful
     */
    public function logUserLogin($strUser, $boolSuccessful)
    {
        $msg = (String)$strUser;

        if($boolSuccessful === true)
               $msg = $msg . " successfully logged into the system. ";
        else
                $msg = $msg . " failed to log into the system. ";

        $this->fileWriter->logMessage($msg);
    }

    /*
     * Writes a message to the daily log file about an account creation
     *
     * param $strIP - the ip of the person making the account
     * param $strUser - the user account to be created
     * param $boolSuccessOrFail - true if the creation was successful
     */
    public function logUserCreation($strIP, $strUser, $boolSuccessful)
    {
        $msg = (String)$strIP;

        if($boolSuccessful === true)
            $msg = $msg . " successfully created a new account " . (String)$strUser;
        else
            $msg = $msg . " failed to create a new account.";

        $this->fileWriter->logMessage($msg);
    }

    /*
     * Writes a message to the daily log file about portfolio activity
     *
     * param $strUser - the user who created portfolio
     * param $boolSuccessOrFail - true if the creation was successful
     *
     */
    public function logPortActivity($strUser, $boolSuccessful)
    {
        $msg = (String)$strUser;

        if($boolSuccessful === true)
            $msg = $msg . " successfully created a new portfolio.";
        else
            $msg = $msg . " failed to create a new portfolio.";

        $this->fileWriter->logMessage($msg);
    }

    /*
     * writes a message to the daily log file about a transaction
     *
     * param $strUser - the user who did the transaction
     * param $boolSuccessOrFail - true if the transaction was successful false otherwise
     * param $boolBuyOrSell - true is the user was buying stocks. false if they are selling
     * param $strItems - the list of items they are/tried to sell
     */
    public function logTransActivity($strUser, $boolSuccessful, $boolBuyOrSell, $strItems)
    {
        $msg = (String)$strUser;

        if($boolSuccessful === true)
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

    /*
     * Log a message to the log file about a competition
     *
     * param $strUser - the user who created/deleted a comp
     * param $boolCreateOrDelete true if a competition was created. false if it was deleted
     */
    public function logCompActivity($strUser, $boolCreateOrDelete)
    {
        $msg = (String)$strUser;

        if($boolCreateOrDelete === true)
            $msg = $msg . " created a competition.";
        else
            $msg = $msg . " deleted a competition.";

        $this->fileWriter->logMessage($msg);
    }

    /*
     * Log a string to the current log file.
     */
    public function logMessage($msg)
    {
        $this->fileWriter->logMessage($msg);
    }
} 
