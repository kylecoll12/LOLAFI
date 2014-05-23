<!DOCTYPE html>
<html>
<head>
   <meta name="author" content="Kyle Collins">
   <meta name="description" content="Grabs Selected Summoner Information">
   <title>League of Legends Info</title>
   <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
   <script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.6.2.min.js"></script>
        <!--jQuery, linked from a CDN-->
   <!--<script src="scripts.js"></script>-->
	
   <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="container">
<style>
	body {
		font-family: Helvetica;	
	}
	
	.ranktable {
		border-collapse: collapse;
		background-color: #FFFFFF;
		color: #000000;
		height: 100%;
	}
	
	.ranktableheader {
		font-weight: bold;
		color: white;
		background-color: gray;
	}
	
	.ranktable tr {
		border: solid 1px #000000
	}
	
	.ranktable td {
		padding: 7px;	
		border-left: 1;
		border-right: 0;
	}
	
	.iconcell
	{
	background-size: 42px;
background-position: center;
width: 32px;
height: 32px;
display: inline-block;
vertical-align: middle;
border-radius: 3px;
box-shadow: 0 0 2px #000;
-webkit-box-shadow: 0 0 2px #000;
	}
</style>
<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	//consider getting this array straight from Summoners.xml
	//currently these are the summoners that are pulled for this page from the xml although the xml might contain more than this.  1/9 No longer used.  Now summoners pulled from the XML file
    //$summoners=array("CPH Legolas","YeahhhBuddy","FauxRizzle","Scribnibs","Pyow","Leetdotcom","Memeyoumeyouyou","Tumbletron","CPH Lego","CPH Pulsar");  
    //$arrLength=count($summoners);
    $results=array();

	$xml=simplexml_load_file("SummonerInfo.xml");
	//var_dump($xml);
        //look up summoner info
		//$node=$xml->xpath("//summoners/summoner[name='".$summoners[$i]."']");  //search by name
		//var_dump($node[0]);
		$xSummoners=$xml->xpath("//summoners/summoner/name");
		$arrLength=count($xSummoners);
		for($i=0;$i<$arrLength;$i++)
		{
			//var_dump($summoners[$i]);
			//look up summoner info
			$node=$xml->xpath("//summoners/summoner[name='".$xSummoners[$i]."']");
			//var_dump($node[0]);
			$results[$i]['name']= $node[0]->name;
			$results[$i]['id']= $node[0]->id;
			$results[$i]['summonerLevel']= $node[0]->summonerLevel;
			$results[$i]['profileIconId']= $node[0]->profileIconId;
			$results[$i]['league']=$node[0]->league;
			$results[$i]['rank']=$node[0]->rank;
			$results[$i]['leaguePoints']=$node[0]->leaguePoints;
			$gStats = getGameStats($node[0]->games);
			$results[$i]['record']=$gStats[0];
			$results[$i]['kda']=$gStats[1];
			$results[$i]['gpm']=$gStats[2];
			$results[$i]['ss']=$gStats[3];
			$results[$i]['quadrakills']=$gStats[4];
			$results[$i]['pentakills']=$gStats[5];
			$results[$i]['mostPlayedChamp']=$gStats[6];
			$results[$i]['cs']=$gStats[7];
		}
    
	function getGameStats($games)
	{
		$wins=0;
		$losses=0;
		$kills=0;
		$deaths=0;
		$assists=0;
		$gamesTot=0;
		$time=0;
		$gold=0;
		$cs=0;
		$fiveGames=0;
		$pentakills=0;
		$quadrakills=0;
		$champsPlayed = array();
		foreach($games->game as $thisGame)
		{
			//This is the date range for the lan
			if($thisGame->createDate > 1400880904000 && $thisGame->createDate < 1401175969000)
			{
				$gamesTot++;
				$gameType = $thisGame->subType;
				$champsPlayed[]= (int)$thisGame->championId;
				$quadrakills= $quadrakills + $thisGame->stats->quadraKills;
				$pentakills=$pentakills + $thisGame->stats->pentaKills;
				if($gameType=="RANKED_SOLO_5x5" || $gameType=="NORMAL" )
				{
					if($thisGame->stats->win>0)                    
						$wins++;
					else	
						$losses++;
					
					$attribute = $thisGame->xpath('statistics/stat[@name="CHAMPIONS_KILLED"]');
					if($thisGame->stats->championsKilled>0)
						$kills = $kills + $thisGame->stats->championsKilled;
					
					$deaths = $deaths + $thisGame->stats->numDeaths;
					$assists = $assists + $thisGame->stats->assists;
					$fiveGames++;
					$time = $time + $thisGame->stats->timePlayed;
					$gold = $gold + $thisGame->stats->goldEarned;
					$cs = $cs + $thisGame->stats->minionsKilled;
				}
			}
				
		}
		$champCount=array_count_values($champsPlayed);//Counts the values in the array, returns associatve array
		arsort($champCount);//Sort it from highest to lowest
		$keys=array_keys($champCount);//Split the array so we can find the most occuring key
		if($fiveGames>0)
			$mostPlayedChamp = lookupChampion($keys[0]); //champCount keys are the ids values are the amount of times played 
		else
			$mostPlayedChamp = "N/A";
		if($fiveGames>0)
		{
			$killsPerGame=number_format($kills/$fiveGames,2);
			$deathsPerGame=number_format($deaths/$fiveGames,2);
			$assistsPerGame=number_format($assists/$fiveGames,2);
			$csPerGame=number_format($cs/$fiveGames,2);
			if($time>0)
				$gpm=number_format($gold/($time/60));
			else
				$gpm=0;
			$record = $wins."-".$losses;
			$kda = $killsPerGame."/".$deathsPerGame."/".$assistsPerGame;
			$seeleyscore = 0;//number_format(((($kills + $assists - $deaths)*($cs*13/$time)+$gpm)/3.14)/$fiveGames,2);
			$gameStats = array($record,$kda,$gpm,$seeleyscore,$quadrakills,$pentakills,$mostPlayedChamp,$csPerGame);
		}
		else
		{ $gameStats = array(0,0,0,0,$quadrakills,$pentakills,$mostPlayedChamp,0); }
		return $gameStats;
	}
	//var_dump(count($results));
    //sort
    
    	function lookupChampion($champId)
	{
		$xmlChamps=simplexml_load_file("ChampionData.xml");
		$node=$xmlChamps->xpath('//champion[@id="'.$champId.'"]');
		return $node[0]["name"];
	}
    function getLeagueValue($a)
    {
    	if($a=="BRONZE")
    		return 5;
    	else if($a=="SILVER")
    		return 4;
    	else if($a=="GOLD")
    		return 3;
    	else if($a=="PLATINUM")
    		return 2;
    	else if($a=="DIAMOND")
    		return 1;
    	else if($a=="CHALLENGER")
    		return 0;
    	else
    		return 6;
    }
    function getRank($a)
    {
    	if($a=="V")
    		return 5;
    	else if($a=="IV")
    		return 4;
    	else if($a=="III")
    		return 3;
    	else if($a=="II")
    		return 2;
    	else if($a=="I")
    		return 1;
    	else
    		return 6;
    }
    function cmp($a,$b)  //compares summoner a and b to find the higher ranked.  if a<b returns <0 if a>b returns >0 0 if they are equal
    {
    	if(getLeagueValue($a["league"])==getLeagueValue($b["league"])) {
    			if(getRank($a["rank"])==getRank($b["rank"])) {
    					return ($a["leaguePoints"] < $b["leaguePoints"]) ? -1 : 1;
    			}
    			else
    				return (getRank($a["rank"]) < getRank($b["rank"])) ? -1 : 1;
    	}
    	else
    		return (getLeagueValue($a["league"]) < getLeagueValue($b["league"])) ? -1 : 1;
    	//return strcmp($a["league"],$b["league"]);
    }
    
    usort($results,"cmp");
    
?>
<center>
<table class='ranktable' border='1'>
<tr class='ranktableheader'>
	<td>Icon</td><td>Name</td><!--<td></td><td>Level</td><td>League</td><td>Points</td>--><td>Record</td><td>K/D/A</td><td>GPM</td><td>Avg CS</td><td>Quadra</td><td>Penta</td><td>Most Played</td><!--<td>S Score</td><td></td>-->
</tr>
<?php foreach ($results as $row): ?>
<tr>
<td><div class='iconcell' style="background-image:url(http://lkimg.zamimg.com/shared/riot/images/profile_icons/profileIcon<?php echo $row['profileIconId']; ?>.jpg);"> </td>
<td><?php echo '<a href="SummonerStats.php?summoner='.$row['name'].'">'.$row['name'].'</a>'; ?></td>
<!--<td>Id: <?php echo $row['id']; ?></td>-->
<td><?php echo $row['record']; ?></td>
<td><?php echo $row['kda']; ?></td>
<td><?php echo $row['gpm']; ?></td>
<td><?php echo $row['cs']; ?></td>
<td><?php echo $row['quadrakills']; ?></td>
<td><?php echo $row['pentakills']; ?></td>
<td><?php echo $row['mostPlayedChamp']; ?></td>
<!--<td><?php echo $row['ss']; ?></td>-->
<!--<td><button id="bUpdate">Check for updates</button></td>-->
</tr>
<?php endforeach; ?>
</table>

   </center>
</div>
</body>
</html>
