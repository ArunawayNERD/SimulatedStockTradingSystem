<?php

	require "/home/ssts/simulatedstocktradingsystem/Logging/LoggingEngine.php";
	require "/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php";
	require "/home/ssts/simulatedstocktradingsystem/competitions/Player.php";

/*function connectDB()
{
	require "/home/ssts/simulatedstocktradingsystem/public_html/creds.php";

	$mysqli = new mysqli($host, $user, $pass, $db);

	if($mysqli->connect_error)
		die($mysqli->connect_error);

	return $mysqli;
}*/

function createComp($owner, $ownerPort, $virtPort, $compName, $start, $end, $buyIn)
{
	$mysqli = connectDB();

	//create the comp
	$request = $mysqli->prepare('insert into competitions (name, start_time, end_time, buyin, uid, creator) values(?, ?, ?, ?, ?, ?)');
	$request->bind_param("sssdis", $compName, $start, $end, $buyIn, $owner, $ownerPort);
	$request->execute();
	
	//get the last auto increment value (aka the cid just made)
	$cid = $mysqli->insert_id;
	echo($cid."\n");
	
	$request->close();

	//add the owner
	$result = addUser($cid, $owner, $ownerPort, $virtPort);

	if($result != 1) //only 1 means everything worked
	{
		//if something failed in adding the user remove the added comp 
		echo($result . ":Result\n");
		$request = $mysqli->prepare('delete from competitions where cid=?');
		$request->bind_param("i", $cid);
		$request->execute();

		$request->close();
		$mysqli->close();

		return -1;
	}

	return 1;
}

function addUser($cid, $uid, $sourcePort, $vitPortName)
{
	$mysqli = connectDB();

	$settings = getCompSettings($cid);

	//check if cid is valid
	$request = $mysqli->prepare('select cid from competitions where cid=?');
	$request->bind_param("i", $cid);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();
	$request->close();

	if(is_null($result))
		return -1;

	//check to see if the user is already in this comp
	$request = $mysqli->prepare('select cid from players where cid=? and uid=?');
	$request->bind_param("ii", $cid, $uid);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();
	$request->close();

	if(!is_null($result))
		return -2;
	
	//check that the source portfolio isnt being used in any other active comps
	$request = $mysqli->prepare('select cid from players where uid=? and pname=?');
	$request->bind_param("is", $uid, $sourcePort);
	$request->execute();
	$request->bind_result($result);
	
	while(!is_null($request->fetch()))
	{
		$compOver = isCompEnded($cid);

		if(!$compOver)
			return -3;
	}

	//check that the comp hasnt already started 
	$now = time();
	$start = strtotime($settings["start_time"]);

	if($start < $now)
		return -4; //comp already started cant join

	//remove the cash from source portfolio
	$result = adjustPortfolioCash($uid, $sourcePort, ($settings["buyin"] * -1));
	if($result == -1) //if the uid, name combo does not exist
		return -4;
	
	if($result == 0) //if the portfolio doesnt have enough
		return -5;

	//make the virtual portfolio
	$result = makeCompPortfolio($uid, $vitPortName, $settings["buyin"]);
	
	if($result === false)
	{
		adjustPortfolioCash($uid, $sourcePort, $settings["buyin"]);
		
		$mysqli->close();
		return -6;
	}

	//add the row into the player database
	$request = $mysqli->prepare('insert into players (cid, uid, pname, compName) values (?,?,?,?)');
	$request->bind_param("iiss", $cid, $uid, $sourcePort, $vitPortName);
	$request->execute();
	$request->close();

	return 1;
}

function isCompEnded($cid)
{
	$mysqli = connectDB();

	$request = $mysqli->prepare('select end_time from competitions where cid=?');
	$request->bind_param("i", $cid);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();
	$request->close();
	$mysqli->close();


	if(is_null($result))
		return -1;

	$now = time();
	$endTime = strtotime($result);

	if($endTime < $now)
		return true;
	else
		return false;
}

function leaveComp($cid, $uid)
{
	$settings = getCompSettings($cid);

	$now = time();
	$start = strtotime($settings["start_time"]);
	$end = strtotime($settings["end_time"]);

	if($start < $now) //comp in progress you cant leave
		return -1;

	if($uid == $settings["uid"])
	{
		endComp($cid);
		return 2;
	}

	$result = removeUser($cid, $uid);
	
	if($result == -1)
		return -2;
	
	return 1;

}

function removeUser($cid, $uid)
{
	$setting = getCompSettings($cid);

	$mysqli = connectDB();

	$request = $mysqli->prepare('select pname, compName from players where cid=? and uid=?');
	$request->bind_param("ii", $cid, $uid);
	$request->execute();
	$request->bind_result($pName, $vName);
	$request->fetch();
	$request->close();

	if(is_null($pname)) //the cid/uid combo does not exist
	{
		$mysqli->close();
		return -1;
	}
	deletePortfolio($uid, $vName);

	adjustPortfolioCash($uid, $pName, $settings["buyin"]);

	$request = $mysqli->prepare('delete from players where cid=? and uid=?');
	$request->bind_param("ii", $cid, $uid);
	$request->execute();
	$request->close();
	$mysqli->close();

	return 1;
}

function startComp($cid)
{
	$mysqli = connectDB();

	$request = $mysqli->prepare('select uid from players where cid=?');
	$request->bind_param("i", $cid);
	$request->execute();

	$results = $request->get_result();

	if($results->num_rows < 2) //if the owner is the only player
	{
		$request->close();
		$mysqli->close();

		endComp();
		return -1;
	}

	$requst->close();
	$mysqli->close();

	return 1;
}

function endComp($cid)
{
	$mysqli = connectDB();
	
	$request = $mysqli->prepare('select uid from players where cid=?');
	$request->bind_param("i", $cid);
	$request->execute();

	$results = $request->get_result();

	while(!is_null($row = $results->fetch_assoc()))
		removeUser($cid, $row["uid"]);

	$request->close();
	$mysqli->close();
}

function listAllUsersComps($uid)
{
	$mysqli = connectDB();

	$request = $mysqli->prepare('select cid from players where uid=?');
	$request->bind_param("i", $uid);
	$request->execute();

	$results = $request->get_result();

	if($results === false)
	{
		$request->close();
		$mysqli->close();
		return -1;
	}
	$compsUserIsIn = array();
	$counter = 0;

	while(!is_null($row = $results->fatch_assoc()))
	{
		$compUserIsIn[$counter] = $row["cid"];
		$counter = $counter + 1;
	}
	$request->close();
	$mysqli->close();
	return $compsUserIsIn;
}

function listCreatedComps($uid)
{
	$mysqli = connectDB();

	$request = $mysqli->prepare('select cid from competitions where uid=?');
	$request->bind_param("i", $uid);
	$request->execute();

	$results = $request->get_result();

	if($results === false)
	{
		$request->close();
		$mysqli->close();
		return -1;
	}
	$compsUserCreated = array();
	$counter = 0;

	while(!is_null($row = $results->fatch_assoc()))
	{
		$compUserCreated[$counter] = $row["cid"];
		$counter = $counter + 1;
	}
	$request->close();
	$mysqli->close();
	return $compsUserCreated;
}

function listUsersEndedComps($uid)
{
	$AllComps = listAllUsersComps($uid);
	$endedComps = array();

	if($AllComps == -1)
		return -1;

	$counter = 0;
	foreach($AllComps as $cid)
	{
		if(isCompEnded($cid))
		{
			$endedComps[$counter] = $cid;
			$counter = $counter + 1;
		}
	}

	return $endedComps;
}

function listUsersCurrentComps($uid)
{
	$AllComps = listAllUsersComps($uid);
	$currentComps;

	if($AllComps == -1)
		return -1;

	$counter = 0;
	foreach($AllComps as $cid)
	{
		if(!isCompEnded($cid))
		{
			$currentComps[$counter] = $cid;
			$counter = $counter + 1;
		}
	}

	return $currentComps;
}

function listAvailableComps()
{
	$mysqli = connectDB();

	$availableComps = array();
	$counter = 0;


 	$result = $mysqli->query('select cid from competitions');

	while($row = $result->fetch_assoc())
	{
		$cid = $row["cid"];

		if(!isCompEnded($cid))
		{
			$availableComps[$counter] = $cid;
			$counter = $counter + 1;
		}

	}

	return $availableComps;
}

function getCompPlayers($cid)
{
	$players = array();
	$counter = 0;
	$mysqli = connectDB();

	$results = $mysqli->query(("select uid, pname, compName where cid=".$cid));

	while($row = $results->fetch_assoc())
	{
		$players[$counter] = new Player($row["uid"], $row["pname"], $row["compName"]);
		$counter = $counter + 1;
	}

	return $players;
}

function getCompSettings($cid)
{
	$mysqli = connectDB();

	$request = $mysqli->prepare('select * from competitions where cid=?');
	$request->bind_param("i", $cid);
	$request->execute();
	$results = $request->get_result();
	$request->close();
	$mysqli->close();

	if(is_null($results))
		return -1;
	
	return $results->fetch_assoc();
}

function getStanding($cid, $uid)
{

}

function getTopThree($cid)
{
	
}

