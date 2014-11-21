<html>
<body>
	<?php

	include "/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php";

	$names =getUserPortfolios(7);
	foreach($names as $name)
	{
		echo($name. " ");
		echo(getCash(7, $name). "\n");
	}

	?>
</body>
</html>
