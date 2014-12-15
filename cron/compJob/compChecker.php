<?php

	require_once "/home/ssts/simulatedstocktradingsystem/competitions/CompetitionEngine.php";

	$mysqli = connectDB();

	//end any competitons that have ended
	$result = $mysqli->query('select cid from competitions where end_time < CURRENT_TIMESTAMP and status =1');
	
	while($row = $result->fetch_assoc())
		endComp($row["cid"]);
	
	//start any compitition that need to be active
	$result = $mysqli->query('select cid from competitions where start_time < CURRENT_TIMESTAMP and status=0');
	
	while($row = $result->fetch_assoc())
		startComp($row["cid"]);

	$mysqli->close();
