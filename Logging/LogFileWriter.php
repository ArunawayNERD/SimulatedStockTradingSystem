<?php
/**
 * Created by PhpStorm.
 *
 * Author: Johnny
 *
 * Created: 10/29/2014
 *
 *
 * Class to write log messages to a log file.
 *
 * It will create a new log file per day.
 */

class LogFileWriter
{
    /** holds the time values for the writer.
     *
     * index 0 = hours, 1 = minutes, 2 = seconds,
     *
     * 3 = is the index values in the format"0:1:2"
     */
    private $time;

    /** holds the time values for the writer.
     *
     * index 0 = year, 1 = month, 2 = day,
     *
     * 3 = is the index values in the format"0-1-2"
     */
    private $date;

    public function __construct()
    {
        $this->time = $this->getTime();
        $this->date = $this->getDate();

        date_default_timezone_set ("America/New_York");
    }

    /**
     * Writes a message to the log file.
     *
     * The timestamp will be added to the message by the function
     *
     * $msg - text to be written
     */
    public function logMessage($msg)
    {
        $logFile = $this->makeLogFile();

        //add time stamp to the msg.
        $msg = "[" . $this->time[3] . "]" . $msg ."\n";

        fwrite($logFile, $msg);

    }

    /**
     * Make a new log file using the date to make it unique
     */
    private function makeLogFile()
    {
        return fopen(".\\LogFiles\\LogFile_".$this->date[3].".txt", "a+");
    }

    /**
     * Gets the current date
     *
     * Returns the date in array format
     */
    private function getDate()
    {

        date_default_timezone_set('America/New_York');
        $currentDate = date("Y-m-d");

        echo("CurrentDate " . $currentDate);
        $date = explode('-', $currentDate);

        $date[3] = $currentDate;

        return $date;
    }

    /**
     * Get current time based on the GMT time zone.
     *
     * Returns the time in array format
     */
    private function getTime()
    {
        date_default_timezone_set('America/New_York');
        $currentTime = date("H:i:s");

        echo("CurrentTime " . $currentTime);
        $time = explode('-', $currentTime);

        $time[3] = $currentTime;
        return $time;
    }
} 