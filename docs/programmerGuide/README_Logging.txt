This provides documention for the methods in the Logging engine php file

The logging engine is set up in object oriented fassion,so a LoggingEngine
object will have to be created before the methods can be used.

In order for logging to accessiable to a php file; the php file must include the LoggingEngine.php file




Method Name:		logStockDataUpdate
Method Discription: 	Writes a message to the loog file that stock data
			was updated.
Method Paramaters: 	None


Method Name:		logWhatIfScenario
Method Discription:	Writes a message to the log file at a user created
			a What-If.
Method Paramaters: 	1) user - The user who create the What-If senerio 


Method Name:		logUserLogin
Method Discription: 	Writes a message to the log file that a 
			user logged on.
Method Paramaters: 	1) user - The user who logged on


Method Name: 		logUserRegistration
Method Discription: 	Writes a message to the log file that a new 
			account was created.
Method Paramaters:	1) user - The name/id of the new account created


Method Name: 		logPortCreation
Method Discription: 	Write a message to the log file that portfolio
			was created.
Method Paramaters: 	1) user - the user who created a portfolio


Method Name:		logTransaction
Method Discription:	Writes a message to the log file that a transaction
			took place or failed.
Method Paramaters:	1) user - The user who did the transaction
			2) boolSuccessOrFail (boolean) - set to true if the
				transaction was successful. false otherwise.

			3) boolBuyOrSell (boolean) - set to true if the 
				transation bought stocks. False if the
				transtion sold stock.

			4) strItems (string) - string containg the items 
				bought or sold.


Method Name:		logCompActivity
Method Discription: 	Writes a message to the log file that a competition
			was created or deleated.
Method Paramaters: 	1) user - The  user who is createding / deleting a 
				competition.


Method Name 		logMessage
Method Discription: 	Write a message to the log file
Method Paramaters: 	1) msg (String) - the message to be written
