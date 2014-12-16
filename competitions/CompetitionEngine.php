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


/*
	Create a new competition and adds it to competetions table.
	This method also auto adds the creator to the players table

	$owner - the uid of the creator
	$ownerPort - the owners source portfolio
	$compName - the competitions name
	$strat - the timestamp for when the competition starts
	$end - the timestamp for when the competition ends
	$buyin - the amount it costs to enter the competition.

	returns -1 (from addUser) if the cid doesnt exist
		-2 (from addUser) if the user is already in the competition
		-3 (from addUser) if the source portfolio is in another active competiton
		-4 (from addUser) if the compettion has already started
		-5 (from addUser) if the uid/sourcePort combo doesnt esit in the database
		-6 (from addUser) if the sourcePort doesnt have enough cash
		-7 (from addUser) if the virtualPortfolio can be created (already exists in portfolio table)

		0 if the buyin is not numeric
		1 if the competition was created

		

*/
function createComp($owner, $ownerPort, $compName, $start, $end, $buyIn)
{
	$mysqli = connectDB();
	
	/*echo("Uid :" . $owner . getType($owner)."</br>");
	echo("port :" . $ownerPort . getType($ownerPort)."</br>");
	echo("compName :" . $compName . getType($compName) . "</br>");
	echo("start :" . $start . getType($start) . "</br>");
	echo("end :". $end . getType($end) . "</br>");
	echo("buyIn :" . $buyIn . getType($buyIn) . "</br>");
*/

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

/*
	Adds a user to a competition. IE addes them to the players table

	$cid - the id of the competiton that the user will be added to
	$uid - the user's id that will be added to the competiton
	$sourcePort - the name of the portfolio to take the buyin from.
	
	returns -1 if the cid does not exist in the table 
		-2 if the user is already in the competition
		-3 if the source portfolio is in another active competiton
		-4 if the compettion has already started
		-5 if the uid/sourcePort combo doesnt esit in the database
		-6 if the sourcePort doesnt have enough cash 
		-7 if the virtualPortfolio can be created (already exists in portfolio table)
*/

function addUser($cid, $uid, $sourcePort)
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
	$request = $mysqli->prepare('select cid from players where cid=? and uid=? and active=1');
	$request->bind_param("ii", $cid, $uid);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();
	$request->close();

	if(!is_null($result))
		return -2;
	


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

	//check that the comp hasnt already started 
	$now = time();
	$start = strtotime($settings["start_time"]);

	if($start < $now)
		return -4; //comp already started cant join

	//remove the cash from source portfolio
	$result = adjustPortfolioCash($uid, $sourcePort, ($settings["buyin"] * -1));
	if($result == -1) //if the uid, name combo does not exist
		return -5;
	
	if($result == 0) //if the portfolio doesnt have enough
		return -6;


	//make the virtual portfolio
	$result = makeCompPortfolio($uid, ($cid . $sourcePort), $settings["buyin"]);
	
	if($result === false)
	{
		adjustPortfolioCash($uid, $sourcePort, $settings["buyin"]);
		
		$mysqli->close();
		return -7;
	}


	//add the row into the player database
	$vName = $cid . $sourcePort;

	$request = $mysqli->prepare('replace into players(cid, uid, pname, compName, active) values (?,?,?,?,1);');
	$request->bind_param("iiss", $cid, $uid, $sourcePort, $vName);
	$request->execute();
	$request->close();
	
	return 1;
}

/*
	Checks if the competition associated with the entered $cid is ended or not

	$cid - the comeptition's cid

	return  true if the competition is over 
		false if the compeetition is not over
*/

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
		return true;
	else
		return false;

}

/*
	Handles a user leaving a compeittion

*/
function leaveComp($cid, $uid)
{
	if(ctype_digit($cid) || is_double($cid) || is_float($cid))
		$cid = (int)$cid;

	if(ctype_digit($uid) || is_double($uid) || is_float($uid))
		$uid = (int)$uid;

	if(!is_int($cid) || !is_int($uid))
		return -4;

	$settings = getCompSettings($cid);

	$mysqli = connectDB();

	$now = time();
	$start = strtotime($settings["start_time"]);
	$end = strtotime($settings["end_time"]);

	if($start < $now) //comp in progress you cant leave
		return -1;

	if($uid == $settings["uid"])
	{
		endComp($cid);

		$mysqli->query("delete from winners where cid=$cid");
		return 2;
	}

	$request = $mysqli->query("select pname, compName from players where cid=$cid and uid=$uid");

	if($request->num_rows > 0)
	{
		$results = $request->fetch_assoc();

		$result = removeUser($cid, $uid, $results['pname'], $results['compName']);
	
		if($result == -1)
			return -2;
	}
	else
	{
		return -3;
	}
	return 1;

}

function removeUser($cid, $uid, $pName, $vName)
{

	$settings = getCompSettings($cid);

	$mysqli = connectDB();

	$request = $mysqli->query('select cid, buyin from winners where cid='.$cid);

	if($request->num_rows == 0) //if the cid is not in winners handle removing the player row. if it is in winner then the entire comp is being removed and let the endComp method handle removing the player rows;
	{
		$request = $mysqli->prepare('delete from players where cid=? and uid=?');
		$request->bind_param("ii", $cid, $uid);
		$request->execute();
		$request->close();
	
		$mysqli->close();

		deletePortfolio($uid, $vName);

		adjustPortfolioCash($uid, $pName, $settings["buyin"]);
	}
	else
	{
		$mysqli->close();
			
		$result = $request->fetch_assoc();
		deletePortfolio($uid, $vName);

		adjustPortfolioCash($uid, $pName, $result['buyin']);
	}
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
		endComp($cid);

		$mysqli->query("delete from winnners where cid=$cid");
		$mysqli->close();

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
	
	$top3 = getTopThree($cid);
	
	//move the current comp status
	$mysqli->query('insert into winners (cid, name, start_time, end_time, buyin, uid, creator) select cid, name, start_time, end_time, buyin, uid, creator from competitions where cid='.$cid);
	
	if(!isset($top3[0]))
		$top3[0] = array(" ", 0);

	if(!isset($top3[1]))
		$top3[1] = array(" ", 0);
	
	if(!isset($top3[2]))
		$top3[2] = array(" ", 0);
	
	//move the top 3 player
	$mysqli->query('update winners set '. 
		'top1="'.$top3[0][0].'", top1value='.$top3[0][1].
		', top2="'.$top3[1][0].'", top2value='.$top3[1][1].
		', top3="'.$top3[2][0].'", top3value='.$top3[2][1].
		' where cid='.$cid);


	//get all the player info becuase players MUST be removed first
	$request = $mysqli->query('select * from players where cid='.$cid);
	
	while($row = $request->fetch_assoc())
	{
		$players[]=$row;
	}

	$mysqli->query("delete from players where cid=$cid");

	$mysqli->query('delete from competitions where cid='.$cid);

	foreach($players as $row)
		removeUser($row['cid'], $row['uid'], $row['pname'], $row['compName']);
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
/*
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
*/
/*function listUsersEndedComps($uid)
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
}*/

function listUsersCurrentComps($uid)
{
	$mysqli = connectDB();

	$comp = array();
	
	$counter = 0;

//	$results = $mysqli->query('select cid, name, start_time, end_time, buyin, creator from competitions where cid in (select cid from players where pname in (select name from portfolios where uid='.$uid.' and competition=0)) and (status=0 or status=1)');


	//$results = $mysqli->query('select * from competitions where cid in (select cid from players where pname in (select name from portfolios where uid='.$uid.' and competition=0) and active=0) and (status=0 or status=1)');


	$results= $mysqli->query("select * from competitions where cid in (select cid from players where uid=$uid and active=1)");

	while(!is_null($row = $results->fetch_assoc()))
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
		$ranks[$counter][0] = $player->getPName();
		$ranks[$counter][1] = $value; 
		$ranks[$counter][2] = $player->getUid();

		$counter = $counter  + 1;
	}

	foreach($ranks as $key => $row)
	{
		$values[$key] = $row[1];
		$names[$key] = $row[0];
	}

	array_multisort($values, SORT_NUMERIC, SORT_DESC, $names, SORT_STRING, $ranks);

	return ($ranks);
}

function getStanding($cid, $uid)
{
	$ranks = sortPlayersByRank($cid);

	if(!is_array($ranks))
		return 0;

	$rank = 1;
	foreach($ranks as $key=>$row)
	{
		if($row[2] == $uid)
			return $rank;
		else
			$rank++;
	}
/*	foreach($ranks as $key =>$row)
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
*/
	return $rank;
}

function getTopThree($cid)
{
	$ranks = sortPlayersByRank($cid);
	
	if(!is_array($ranks))
		return -1;

	$places = array();

	$lastRankValue = PHP_INT_MAX;
	$curRank = 0;

	$places = array_slice($ranks, 0, 3);
/*	foreach($ranks as $key => $row)
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
*/
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
    $count++;
  }
  return $results;
}
/* 
    input: user id and portfolio name

    output: an array containing opponent stocks 
*/

function getOpponentStocks ($uid, $pname) {
  $mysqli=connectDB();
  $compPort = getCompPortfolio($uid, $pname);

  $result = $mysqli->query("select symbol from portfolioStocks 
    where uid=$uid and name=\"$compPort\";"); 
 
  if($result->num_rows == 0)
    return 0;
  
  $count=0;
  while($row=$result->fetch_assoc()){
    $symbols[$count] = $row["symbol"];
    $count++;
  }
  return $symbols; 
}

/*
  input: user id and portfolio name

  output: name of the virtual competition portfolio

*/

function getCompPortfolio ($uid, $pname) {
  $mysqli=connectDB();

  $result=$mysqli->query("select compName from players where 
    uid=$uid and pname=\"$pname\" and active=1");
  $compPortArray = $result->fetch_assoc();

  $mysqli->close();
  return $compPortArray["compName"];

}

/*
    input: none
    output: an associative array of info pertaining to 
    past competitions
*/
function getPastComps () {
  $mysqli=connectDB();

  $result=$mysqli->query("select * from winners;");
  while ($row=$result->fetch_assoc()) {
    $pastComps[] = array(
      "name" => $row["name"],
      "start_time" => $row["start_time"],
      "end_time" => $row["end_time"],
      "buyin" => $row["buyin"],
      "creator" => $row["creator"],
      "top1" => $row["top1"],
      "top2" => $row["top2"],
      "top3" => $row["top3"],
      "top1value" => $row["top1value"],
      "top2value" => $row["top2value"],
      "top3value" => $row["top3value"],
    );
  }
  return $pastComps;
}
