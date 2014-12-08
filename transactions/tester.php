<html>
<body>
	<?php

		require "/home/ssts/simulatedstocktradingsystem/transactions/Transaction.php";
		require "/home/ssts/simulatedstocktradingsystem/transactions/TransactionEngine.php";
		
		echo(buyStock(22, "John", "GOOG", 1)."\n");
		sleep(1);
		echo(sellStock(22, "John", "GOOG", 1)."\n");

		$transactions = getAllUserTrans(29, "John");
		
		foreach($transactions as $trans)
		{
			echo($trans->toString()."\n");
		}

/*		if(realpath("~/simulatedstocktradingsystem/")===false)
		{
		
		echo("null");
		}
		else
			echo(realpath("~/"));
*/
	?>
</body>
</html>
