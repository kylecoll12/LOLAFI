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
		font-family: Courier;	
	}
	
	.ranktable {
		border-collapse: collapse;
		border: solid 1px #000099;
		background-color: #FFFFFF;
		color: #000000;
	}
	
	.ranktableheader {
		font-weight: bold;
	}
	
	.ranktable td {
		padding: 4px;	
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
	$summonerInfo=array();
	$games=array();
	$xml=simplexml_load_file("SummonerInfo.xml");
	$summonerSpace=$_GET['summoner'];
	$summoner=str_replace(' ', '', $summonerSpace);
        //look up summoner info
	$node=$xml->xpath('//summoner[name[.="'.$summonerSpace.'"]]');;  //search by name
	//var_dump($node[0]);
	$summonerInfo['name']= $node[0]->name;
	$summonerInfo['id']= $node[0]->id;
	$summonerInfo['summonerLevel']= $node[0]->summonerLevel;
	$summonerInfo['profileIconId']= $node[0]->profileIconId;
	$summonerInfo['league']=$node[0]->league;
	$summonerInfo['rank']=$node[0]->rank;
	$summonerInfo['leaguePoints']=$node[0]->leaguePoints;
	
	$games=$node[0]->games->game;
	//var_dump($games);
    
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
		foreach($games->game as $thisGame)
		{
			$gamesTot++;
				//each game contains the attribute WIN (23) if the game was won and LOSS (25) if the game was lost.  We are just counting the wins here.
				$result = $thisGame->xpath('statistics/stat[@name="WIN"]');
				if(count($result)>0)                    
					$wins++;
				else
					$losses++;
					
				$attribute = $thisGame->xpath('statistics/stat[@name="CHAMPIONS_KILLED"]');
				if(count($attribute)>0)
					$kills = $kills + $attribute[0]["value"];
				
				$attribute = $thisGame->xpath('statistics/stat[@name="NUM_DEATHS"]');
				if(count($attribute)>0)
					$deaths = $deaths + $attribute[0]["value"];
				
				$attribute = $thisGame->xpath('statistics/stat[@name="ASSISTS"]');
				if(count($attribute)>0)
					$assists = $assists + $attribute[0]["value"];
				
				$gameType = $thisGame->subType;
				//var_dump($attribute);
				if($gameType=="RANKED_SOLO_5x5" || $gameType=="NORMAL")
				{
				$fiveGames++;
				$attribute = $thisGame->xpath('statistics/stat[@name="TIME_PLAYED"]');
				if(count($attribute)>0)
					$time = $time + $attribute[0]["value"];
				
				$attribute = $thisGame->xpath('statistics/stat[@name="GOLD_EARNED"]');
				if(count($attribute)>0)
					$gold = $gold + $attribute[0]["value"];
				
				$attribute = $thisGame->xpath('statistics/stat[@name="MINIONS_KILLED"]');
				if(count($attribute)>0)
					$cs = $cs + $attribute[0]["value"];
				}
				
		}
		$killsPerGame=number_format($kills/$gamesTot,2);
		$deathsPerGame=number_format($deaths/$gamesTot,2);
		$assistsPerGame=number_format($assists/$gamesTot,2);
		$gpm=number_format($gold/($time/60));
		$record = $wins."-".$losses;
		$kda = $killsPerGame."/".$deathsPerGame."/".$assistsPerGame;
		$seeleyscore = number_format(((($kills + $assists - $deaths)*($cs*13/$time)+$gpm)/3.14)/$fiveGames,2);
		$gameStats = array($record,$kda,$gpm,$seeleyscore);
		return $gameStats;
	}
	//var_dump(count($results));
    //sort
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
    
    //usort($results,"cmp");
    
?>
<center>
<table class='ranktable' border='1'>
<tr class='ranktableheader'>
	<td>Stats</td>
</tr>
<?php foreach ($games as $game): ?>
<tr>
<td><table>
<tr><td>Mode</td><td><?php echo $game->gameMode; ?></td></tr>
<tr><td>Champion</td><td><?php echo $game->championId; ?></td></tr>
<tr><td>Date</td><td><?php echo date("d-m-Y", ($game->createDate/1000)); ?></td></tr>
<?php foreach ($game->stats->children() as $statName => $stat): ?>
<tr><td><?php echo $statName; ?></td><td><?php echo $stat; ?></td></tr>
<?php endforeach; ?>
</table></td>
</tr>
<?php endforeach; ?>
</table>

   </center>
</div>
</body>
</html>
