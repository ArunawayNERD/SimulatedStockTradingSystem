<Html>
    <body>
    <?php
        require 'LoggingEngine.php';
	
	echo("Testing");
        $loggingEngine = new LoggingEngine();

        $loggingEngine->logMessage("Starting log testing");
        $loggingEngine->logMessage(" ");

        $loggingEngine->logStockDataUpdate();

        $loggingEngine->logWhatIfScenario("UserJohnny");

        $loggingEngine->logUserLogin("UserJohnny");
        
        $loggingEngine->logUserCreation("UserJohnny");
        

        $loggingEngine->logPortActivity("UserJohnny");
        
        $loggingEngine->logTransActivity("UserJohnny", true, true, "1 Google Share for 1 million dollars");
        $loggingEngine->logTransActivity("UserJohnny", true, false, "1 Google Share for 1 million dollars");
        $loggingEngine->logTransActivity("UserJohnny", false, true, "1 Google Share for 1 million dollars");
        $loggingEngine->logTransActivity("UserJohnny", false, false, "1 Google Share for 1 million dollars");

        $loggingEngine->logCompActivity("UserJohnny", true);
        $loggingEngine->logCompActivity("UserJohnny", false);

    ?>
    </body>
</Html>

