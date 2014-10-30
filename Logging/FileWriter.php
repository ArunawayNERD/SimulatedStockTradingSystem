<?php
/**
 * Created by PhpStorm.
 * User: Johnny
 * Date: 10/29/2014
 * Time: 11:09 PM
 */

class FileWriter
{
    private $time;
    private $date;

    public function __construct()
    {
        $time = $this->getTime();
        $date = $this->getDate();
        makeNewLogFile();
    }

    /**
     * Writes a message to the log file
     *
     * $msg - text to be written
     */
    public function logMessage($msg)
    {

    }

    /**
     * Make a new log file using the date to make it unique
     */
    private function makeNewLogFile()
    {

    }

    /**
     * Gets the current date.
     *
     * Returns the date as a int
     */
    private function getDate()
    {
        $timeArray = getdate();
    }

    /**
     * Get current time.
     *
     * Returns the time as a string (so it can be pasted onto the message
     */
    private function getTime()
    {
        $timeArray = getdate();
    }
} 