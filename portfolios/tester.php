<html>
<body>
	<?php

	include "/home/ssts/simulatedstocktradingsystem/portfolios/Portfolio.php";

	$names =getUserPortfolios(7);
	foreach($names as $name)
		echo($name);
	?>
</body>
</html>
