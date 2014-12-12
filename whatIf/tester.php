<?php

	require "/home/ssts/simulatedstocktradingsystem/whatIf/WhatIfEngine.php";

	$results = getWhatIf("IBM", 2014, "04", "04", 15);

	if(is_array($results))
		foreach($results as $result)
			echo($result . "\n");
	else
		echo($results);
