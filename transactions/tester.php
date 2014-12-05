<html>
<body>
	<?php

//		require "../transaction.php";
//		require "../TransactionEngine.php";
		
		//$testTrans = new Transaction("2014/11/30 17:23", "portfolio", "Google", "GOOG", -5, 15);

		//echo($testTrans->toString()."\n");

/*		echo(buyStock(22, "John", "GOOG", 1)."\n");
		sleep(1.5);
		echo(sellStock(22, "John", "GOOG", 1)."\n");

		$transactions = getAllUserTrans(29, "John");
		
		foreach($transactions as $trans)
		{
			echo($trans->toString()."\n");
		}
*/
		if(realpath("~/simulatedstocktradingsystem/")===false)
		{
		
		echo("null");
		}
		else
			echo(realpath("~/"));
	?>
</body>
</html>
