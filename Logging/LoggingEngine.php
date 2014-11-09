<?php
/**
 * Created by PhpStorm.
 * User: Johnny
 * Date: 10/29/2014
 * Time: 11:10 PM
 */

include "/home/ssts/simulatedstocktradingsystem/Logging/LogFileWriter.php";

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
    public function logWhatIfScenario($user)
    {
        $msg = (String)$user . " created a What-If Scenario.";

        $this->fileWriter->logMessage($msg);
    }

    /*
     * Writes to the daily log file that a user logged in
     *
     * param $strUser - the user to log in
     */
    public function logUserLogin($user)
    {
        $msg = (String)$user;

        $msg = $msg . " logged into the system. ";
       
        $this->fileWriter->logMessage($msg);
    }

    /**
    * Writes a message to the log fle when a user logs off the system
    *
    * param $user - the user who logged out.
    *
    */
    public function logUserLogout($user)
    {
	$msg = (String)$user;

	$msg = $msg . " logged out.";

	$this->logMessage($msg);
    }


    /*
     * Writes a message to the daily log file about an account creation
     *
     * param $strUser - the user account to be created
     */
    public function logUserRegistration($user)
    {
    	$msg = "";
        $msg = $msg . "New account created: " . (String)$user;
        
        $this->fileWriter->logMessage($msg);
    }

    /*
     * Writes a message to the daily log file about portfolio activity
     *
     * param $strUser - the user who created portfolio
     *
     */
    public function logPortCreation($user)
    {
        $msg = (String)$strUser;

        $msg = $msg . " created a new portfolio.";
       
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
    public function logTransaction($user, $boolSuccessful, $boolBuyOrSell, $strItems)
    {
        $msg = (String)$user;

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
    public function logCompActivity($user, $boolCreateOrDelete)
    {
        $msg = (String)$user;

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
