<?php

	require_once "/home/ssts/simulatedstocktradingsystem/Logging/LoggingEngine.php";
	require_once "/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php";
	require_once "/home/ssts/simulatedstocktradingsystem/competitions/Player.php";

/*function connectDB()
{
	require "/home/ssts/simulatedstocktradingsystem/public_html/creds.php";

	$mysqli = new mysqli($host, $user, $pass, $db);

	if($mysqli->connect_error)
		die($mysqli->connect_error);

	return $mysqli;
}*/

function createComp($owner, $ownerPort, $compName, $start, $end, $buyIn)
{
	$mysqli = connectDB();
	
	echo("Uid :" . $owner . getType($owner)."</br>");
	echo("port :" . $ownerPort . getType($ownerPort)."</br>");
	echo("compName :" . $compName . getType($compName) . "</br>");
	echo("start :" . $start . getType($start) . "</br>");
	echo("end :". $end . getType($end) . "</br>");
	echo("buyIn :" . $buyIn . getType($buyIn) . "</br>");


	if(ctype_digit($buyIn)||is_int($buyIn)|| is_float($buyIn))
		$buyIn = (double)$buyIn;

	if(!is_double($buyIn))
		return 0;

	//create the comp
	$request = $mysqli->prepare('insert into competitions (name, start_time, end_time, buyin, uid, creator, status) values(?, ?, ?, ?, ?, ?, 0)');
	$request->bind_param("sssdis", $compName, $start, $end, $buyIn, $owner, $ownerPort);
	$request->execute();
	
	//get the last auto increment value (aka the cid just made)
	$cid = $mysqli->insert_id;
	
	$request->close();

	//add the owner
	$result = addUser($cid, $owner, $ownerPort);

	if($result != 1) //only 1 means everything worked
	{
		//if something failed in adding the user remove the added comp 
		$request = $mysqli->prepare('delete from competitions where cid=?');
		$request->bind_param("i", $cid);
		$request->execute();

		$request->close();
		$mysqli->close();
		return $result;
	}

	return 1;
}

function addUser($cid, $uid, $sourcePort)
{
	$mysqli = connectDB();

//	echo("start add user</br>");
	$settings = getCompSettings($cid);
//	echo("got comp setting</br>");
	//check if cid is valid
	$request = $mysqli->prepare('select cid from competitions where cid=?');
	$request->bind_param("i", $cid);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();
	$request->close();

	if(is_null($result))
		return -1;

//	echo("after cid valid check</br>");

	//check to see if the user is already in this comp
	$request = $mysqli->prepare('select cid from players where cid=? and uid=? and active=1');
	$request->bind_param("ii", $cid, $uid);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();
	$request->close();

	if(!is_null($result))
		return -2;
	
//	echo("after user already in check</br>");


	//check that the source portfolio isnt being used in any other active comps
	$request = $mysqli->prepare('select cid, active from players where uid=? and pname=?');
	$request->bind_param("is", $uid, $sourcePort);
	$request->execute();
	$request->bind_result($result, $result2);
	
	while(!is_null($request->fetch()))
	{
		$compOver = isCompEnded($result);

		if(!$compOver)
		{	
			if($result2 == 1)
				return -3;
		}
	}

//	echo("after check for source use");


	//check that the comp hasnt already started 
	$now = time();
	$start = strtotime($settings["start_time"]);

	if($start < $now)
		return -4; //comp already started cant join

//	echo("after time check</br>");
	//remove the cash from source portfolio
	$result = adjustPortfolioCash($uid, $sourcePort, ($settings["buyin"] * -1));
	if($result == -1) //if the uid, name combo does not exist
		return -5;
	
	if($result == 0) //if the portfolio doesnt have enough
		return -6;

//	echo("adjusted source portfolio</br>");

	//make the virtual portfolio
	$result = makeCompPortfolio($uid, ($cid . $sourcePort), $settings["buyin"]);
	
	if($result === false)
	{
		adjustPortfolioCash($uid, $sourcePort, $settings["buyin"]);
		
		$mysqli->close();
		return -7;
	}

//	echo("portfolio made </br>");

	//add the row into the player database

	$vName = $cid . $sourcePort;

	$request = $mysqli->prepare('replace into players(cid, uid, pname, compName, active) values (?,?,?,?,1);');
	$request->bind_param("iiss", $cid, $uid, $sourcePort, $vName);
	$request->execute();
	$request->close();
	
//	echo("done</br>");
	return 1;
}

function isCompEnded($cid)
{
	$mysqli = connectDB();

	$request = $mysqli->prepare('select status from competitions where cid=?');
	$request->bind_param("i", $cid);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();

	$request->close();
	$mysqli->close();


	if(is_null($result))
		return -1;

/*	$now = time();
	$endTime = strtotime($result);

	if($endTime < $now)
		return true;
	else
		return false;

*/

	if($result == -1)
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
	$settings = getCompSettings($cid);

	$mysqli = connectDB();

	$request = $mysqli->prepare('select pname, compName from players where cid=? and uid=?');
	$request->bind_param("ii", $cid, $uid);
	$request->execute();
	$request->bind_result($pName, $vName);
	$request->fetch();
	$request->close();

	if(is_null($pName)) //the cid/uid combo does not exist
	{
		$mysqli->close();
		return -1;
	}

	deletePortfolio($uid, $vName);

	adjustPortfolioCash($uid, $pName, $settings["buyin"]);

	$request = $mysqli->prepare('update players set active=0 where cid=? and uid=?');
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

		endComp($cid);
		return -1;
	}
	$request->close();

	$request = $mysqli->prepare('update competitions set status=1 where cid=?');
	$request->bind_param("i", $cid);
	$request->execute();
	$request->close();
	$mysqli->close();

	return 1;
}

function endComp($cid)
{
	$mysqli = connectDB();
	
	//get all the players and remove them
	$request = $mysqli->prepare('select uid from players where cid=?');
	$request->bind_param("i", $cid);
	$request->execute();

	$results = $request->get_result();

	while(!is_null($row = $results->fetch_assoc()))
		removeUser($cid, $row["uid"]);

	$request->close();
	
	//set the competiton status to finished and handled
	$request = $mysqli->prepare('update competitions set status=-1 where cid=?');
	$request->bind_param("i", $cid);
	$request->execute();
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
	$mysqli = connectDB();

	$comp = array();
	$counter = 0;

//	$results = $mysqli->query('select cid, name, start_time, end_time, buyin, creator from competitions where cid in (select cid from players where pname in (select name from portfolios where uid='.$uid.' and competition=0)) and (status=0 or status=1)');


	$results = $mysqli->query('select * from competitions where cid in (select cid from players where pname in (select name from portfolios where uid='.$uid.' and competition=0) and active=0) and (status=0 or status=1)');


	while($row = $results->fetch_assoc())
	{
		$comp[$counter] = array("name" => $row["name"], "start_time" => $row["start_time"], "end_time" => $row["end_time"], "buyin" => $row["buyin"], "creator" => $row["creator"], "cid" => $row["cid"]);

		$counter = $counter + 1;
	}
/*
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
	}*/

	return $comp;
}

function listAvailableComps($uid, $pname)
{
	$mysqli = connectDB();

	$comp = array();
	$counter = 0;

//$result = $mysqli->query("select cid, name, start_time, end_time, buyin, creator from competitions where cid in (select cid from players where pname not in (select name from portfolios where uid=$uid and competition=0)) and status=0");


	$result = $mysqli->query("select * from competitions where status=0 and buyin < 
	(select cash from portfolios where uid=$uid and name=\"$pname\") and cid 
	not in (select cid from players where uid=$uid and active=1);");

	while($row = $result->fetch_assoc())
	{
		$comp[$counter] = array("name" => $row["name"], "start_time" => $row["start_time"], "end_time" => $row["end_time"], "buyin" => $row["buyin"], "creator" => $row["creator"], "cid" => $row["cid"]);

	/*	$comp[$counter]["name"] = $row["name"];
		$comp[$counter]["start_time"] = $row["start_time"];
		$comp[$counter]["end_time"] = $row["end_time"];
		$comp[$counter]["buyin"] = $row["buyin"];
		$comp[$counter]["creator"] = $row["creator"];

	*/
		$counter = $counter + 1;
	}
// 	$result = $mysqli->query('select cid from competitions where status=0');

/*	while($row = $result->fetch_assoc())
	{
		$cid = $row["cid"];
		
		$players = getCompPlayers($cid)
	
		$inComp = 0;

		foreach($players as $player)
		{
			$tempUid = $player->getUid();

			if($tempUid == $uid)
			{
				$inComp = 1;
				break;
			}
		}

		if(!$inComp)
		{
		//	$availableComps[$counter] = $cid;
		//	$counter = $counter + 1;

			$availableComps[] = $cid;
		}
	}
*/
	return $comp;
}

function getCompPlayers($cid)
{
	$players = array();
	$counter = 0;
	$mysqli = connectDB();

	$results = $mysqli->query(("select uid, pname, compName from players where cid=".$cid));

	if($results->num_rows == 0)
		return -1;

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

function sortPlayersByRank($cid)
{
	$players = getCompPlayers($cid);
	if(!is_array($players))
		return -1;
		
	$ranks = array();
	
	$counter = 0;
	foreach($players as $player)
	{
		$value = getValue($player->getUid(), $player->getCompName());
		$ranks[$counter][0] = $player;
		$ranks[$counter][1] = $value; 

		$counter = $counter  + 1;
	}

	foreach($ranks as $key => $row)
	{
		$values[$key] = $row[1];
	}

	array_multisort($values, SORT_NUMERIC, SORT_DESC, $ranks);

	return ($ranks);
}

function getStanding($cid, $uid)
{
	$ranks = sortPlayersByRank($cid);

	if(!is_array($ranks))
		return 0;

	$lastRankValue = PHP_INT_MAX;
	$curRank = 0;

	$isUserIn = 0;
	foreach($ranks as $key =>$row)
	{
		if($isUserIn == 1)
			break;
		
		if($row[0]->getUid() == $uid)
			$isUserIn = 1;
	}

	if($isUserIn == 0)
		return -1;

	foreach($ranks as $key => $row)
	{

		$userValue = $row[1];

		if($userValue < $lastRankValue)
		{
			$curRank = $curRank + 1;
			$lastRankValue = $userValue;
		}

		if($row[0]->getUid() == $uid)
			break;
	}

	return $curRank;
}

function getTopThree($cid)
{
	$ranks = sortPlayersByRank($cid);
	
	if(!is_array($ranks))
		return -1;

	$places = array();

	$lastRankValue = PHP_INT_MAX;
	$curRank = 0;

	foreach($ranks as $key => $row)
	{

		$userValue = $row[1];

		if($userValue < $lastRankValue)
		{
			$curRank = $curRank + 1;
			$lastRankValue = $userValue;
		}
		if($curRank > 3)
			break;

		$places[$curRank][] = $row[0]->getUid();
	}

	return $places;
}

/*
     input: user id

     output: an array of portfolios that are not currently
     involed in a competition

     WARNING: DO NOT BLINDLY ENTER USER INPUT. NOT SAFE.

*/

function getNonCompetingPortfolios($uid) {

  if(!isset($uid))
  	return null;

  $mysqli=connectDB();

  $result = $mysqli->query("select name from portfolios where
    uid=$uid and name not in (select pname from
    players where uid=$uid);");

  $names = $result->fetch_all(MYSQLI_NUM);
  
  return $names;
}

/*
   input: user id, portfolio name

   output: boolean
     true if in an active competition
     false otherwise
*/

function isCompeting ($uid, $pname) {
  $mysqli = connectDB();
  $result = $mysqli->query("select cid from players
    where uid=$uid and pname=\"$pname\" and
    active=1;");

  if($result->num_rows == 0 ) {
    return false;
  } else {
    $row=$result->fetch_assoc();
    return $row["cid"];
  }

  $mysqli->close();
}

function getCompPortfolios($cid, $uid)
{
	$mysqli = connectDB();
	
	$result = $mysqli->query("select pname, compName from players where cid=$cid and uid=$uid");

	$mysqli->close();

	if($result->num_rows == 0)
		return 0;
	
	return $result->fetch_assoc();

}
/* 
    input: user id and portfolio name

    output: an array containing opponent names
*/

function getOpponentNames ($uid, $pname) {
  $mysqli=connectDB();
  $result = $mysqli->query("select uid, pname from players 
    where cid =(select cid from players where uid=$uid 
    and pname=\"$pname\") and uid!=$uid;");
  
  if($result->num_rows == 0)
    return 0;
  
  $count=0;
  while($row=$result->fetch_assoc()) {
    $results[$count] = array (
      "uid" => $row["uid"],
      "pname" => $row["pname"]
    );
  }
  return $results;
}
/* 
    input: user id and portfolio name

    output: an array containing opponent stocks 
*/

function getOpponentStocks ($uid, $pname) {
  $mysqli=connectDB();
  $result = $mysqli->query("select pname from players 
    where cid =(select cid from players where uid=$uid 
    and pname=\"$pname\") and uid!=$uid;");
  
  if($result->num_rows == 0)
    return 0;
    
  
}

/*
  input: user id and portfolio name

  output: name of the virtual competition portfolio

*/

function getCompPortfolio ($uid, $pname) {
  $mysqli=connectDB();

  $result=$mysqli->query("
}
