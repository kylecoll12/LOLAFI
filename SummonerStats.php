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
	date_default_timezone_set('America/Chicago');
	$summonerInfo=array();
	$games=array();
	$xml=simplexml_load_file("SummonerInfo.xml");
	$summonerSpace=$_GET['summoner'];
	$summoner=str_replace(' ', '', $summonerSpace);
        //look up summoner info
	$node=$xml->xpath('//summoner[name[.="'.$summonerSpace.'"]]');  //search by name
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
    
	
	//var_dump(count($results));
    //sort
	function formatStat($sName,$s)
	{
		if(substr($sName,0,4)=="item")
			return lookupItem($s);
		else
			return $s;
	}
	function lookupItem($itemId)
	{
		$xmlItems=simplexml_load_file("ItemData.xml");
		$node=$xmlItems->xpath('//item[@id="'.$itemId.'"]');
		return $node[0]["name"];
	}
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
<tr><td>Champion</td><td><?php echo lookupChampion($game->championId); ?></td></tr>
<tr><td>Date</td><td><?php echo date('Y-m-d H:i:s', (int)substr($game->createDate,0,-3)); ?></td></tr>
<?php foreach ($game->stats->children() as $statName => $stat): ?>
<tr><td><?php echo $statName; ?></td><td><?php echo formatStat($statName,$stat); ?></td></tr>
<?php endforeach; ?>
</table></td>
</tr>
<?php endforeach; ?>
</table>

   </center>
</div>
</body>
</html>
