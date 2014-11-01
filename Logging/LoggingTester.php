<Html>
    <body>
    <?php
        require 'LoggingEngine.php';

        $loggingEngine = new LoggingEngine();

        $loggingEngine->logMessage("Starting log testing");
        $loggingEngine->logMessage(" ");

        $loggingEngine->logStockDateUpdate();

        $loggingEngine->logWhatIfScenario("UserJohnny");

        $loggingEngine->logUserLogin("UserJohnny", true);
        $loggingEngine->logUserLogin("UserJohnny", false);

        $loggingEngine->logUserCreation("127.0.0.1", "UserJohnny", true);
        $loggingEngine->logUserCreation("127.0.0.1", "UserJohnny", false);

        $loggingEngine->logPortActivity("UserJohnny", true);
        $loggingEngine->logPortActivity("UserJohnny", false);

        $loggingEngine->logTransActivity("UserJohnny", true, true, "1 Google Share for 1 million dollars");
        $loggingEngine->logTransActivity("UserJohnny", true, false, "1 Google Share for 1 million dollars");
        $loggingEngine->logTransActivity("UserJohnny", false, true, "1 Google Share for 1 million dollars");
        $loggingEngine->logTransActivity("UserJohnny", false, false, "1 Google Share for 1 million dollars");

        $loggingEngine->logCompActivity("UserJohnny", true);
        $loggingEngine->logCompActivity("UserJohnny", false);

    ?>
    </body>
</Html>

