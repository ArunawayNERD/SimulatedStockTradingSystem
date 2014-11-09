<?php

$date = new DateTime('tomorrow');

$filename = "/home/ssts/simulatedstocktradingsystem/Logging/LogFiles/LogFile_".$date->format("Y-m-d").".txt";

fopen($filename, "a+");

chmod($filename, 0666);
