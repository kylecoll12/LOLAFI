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
		border-left: 0;
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

<script type="text/javascript">
		function showDiv(id) {
			var jqueryname="#" + id;
			//if you use "block" on a <tr> then you will only occupy one column so we use "table-row" instead
			if($(jqueryname).css("display")=="table-row")
				$(jqueryname).css("display", "none");
			else
				$(jqueryname).css("display", "table-row");
		};
</script>

<?php
	//Page load functionality
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	date_default_timezone_set('America/Chicago');
	$summonerInfo=array();
	$games=array();
	$gamesparent=array();
	$xml=simplexml_load_file("SummonerInfo.xml");
	$summonerSpace=$_GET['summoner'];
	$summoner=str_replace(' ', '', $summonerSpace);
        //look up summoner info
	$node=$xml->xpath('//summoner[name[.="'.$summonerSpace.'"]]');  //search by name
	$summonerInfo['name']= $node[0]->name;
	$summonerInfo['id']= $node[0]->id;
	$summonerInfo['summonerLevel']= $node[0]->summonerLevel;
	$summonerInfo['profileIconId']= $node[0]->profileIconId;
	$summonerInfo['league']=$node[0]->league;
	$summonerInfo['rank']=$node[0]->rank;
	$summonerInfo['leaguePoints']=$node[0]->leaguePoints;
	
	$gamesparent=$node[0]->games->game;
	//Store all gamesparent into game array to make sort work correctly
	//(if you don't do this it always gets the [0] index game for some reason.
	foreach($gamesparent as $game) {$games[] = $game;}
	//now sort them before displaying them
	uasort($games,"timecmp");
	
	$gstats=getGameStats($node[0]->games);
	$gstatsArray = array();
	foreach($gstats[0] as $gs) {$gstatsArray[] = $gs;}
	uasort($gstatsArray,"champcmp");
	
	//formats item stats
	function formatStat($sName,$s)
	{
		if(substr($sName,0,4)=="item")
			return lookupItem($s);
		else
			return $s;
	}
	//Looks up item based on item id
	function lookupItem($itemId)
	{
		$xmlItems=simplexml_load_file("ItemData.xml");
		$node=$xmlItems->xpath('//item[@id="'.$itemId.'"]');
		return $node[0]["name"];
	}
	//Looks up champion based on champ id
	function lookupChampion($champId)
	{
		$xmlChamps=simplexml_load_file("ChampionData.xml");
		$node=$xmlChamps->xpath('//champion[@id="'.$champId.'"]');
		return $node[0]["name"];
	}
	//Gets Red for a loss and green for a win
        function getWLColor($a)
        {
        	    if($a=="1")
        	    	    return "#99FF99";
        	    else
        	    	    return "#FF9999";
        }
    
        //Sorts $games by createDate
        function timecmp($a,$b)
        { 
        	    return strcmp($b->createDate,$a->createDate);
        }
        //sorts $champGames by games played
        function champcmp($a,$b)
        {
        	//games played is index 5
        	return $a[5]<$b[5];	
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
				$gameType = $thisGame->subType;
				if(($gameType=="RANKED_SOLO_5x5") || ($gameType=="NORMAL") || ($gameType=="CAP_5x5"))
				{
					$gamesTot++;
					$dataSaved=false;
					$gameChamp = (int)$thisGame->championId;
					$gameKills = $thisGame->stats->championsKilled;
					$gameDeaths = $thisGame->stats->numDeaths;
					$gameAssists = $thisGame->stats->assists;
					$gameWin = $thisGame->stats->win>0;
					//$champsPlayed[] = array($gameChamp,$gameKills);
					for($i=0;$i<count($champsPlayed);$i++)
					{
						if($champsPlayed[$i][0]==$gameChamp)
						{
							$champsPlayed[$i][1] += $gameKills;
							$champsPlayed[$i][2] += $gameDeaths;
							$champsPlayed[$i][3] += $gameAssists;
							if($gameWin)
								$champsPlayed[$i][4] += 1;
							$champsPlayed[$i][5] += 1;
							$dataSaved=true;
						}
					}
					if(!$dataSaved)
					{
						$champsPlayed[] = array($gameChamp,$gameKills,$gameDeaths,$gameAssists,$gameWin,1);
					}
					
				}
				
		}
		
		$gameStats = array($champsPlayed);
		
		return $gameStats;
	}
	
    //date('Y-m-d H:i:s', (int)substr($game->createDate,0,-3));  gets date and time
?>
<center>
<table cellspacing='0' cellpadding='40'>
<tr><td style="vertical-align:top;">

<table class='ranktable' border='1'>
<tr class='ranktableheader'><td>Champ</td><td>Record</td><td>K/D/A</td></tr>
<?php foreach ($gstatsArray as $gsa): ?>
<tr><td><?php echo lookupChampion($gsa[0]) ?></td><td><?php echo (int)$gsa[4]."-".((int)$gsa[5]-(int)$gsa[4]); ?></td><td><?php echo number_format((int)$gsa[1]/(int)$gsa[5],2)."/".number_format((int)$gsa[2]/(int)$gsa[5],2)."/".number_format((int)$gsa[3]/(int)$gsa[5],2) ?></td></tr>
<?php endforeach; ?>
</table>

</td>
<td>

<table class='ranktable' border='1'>
<tr class='ranktableheader'>
	<td colspan='5'>Stats</td>
</tr>
<tr class='ranktableheader'><td>Mode</td><td>Champion</td><td>K/D/A</td><td>Date</td><td></td></tr>
<?php foreach ($games as $game): ?>
<?php echo "<tr style='background-color: ".getWLColor($game->stats->win).";'>" ?>
<td><?php echo $game->subType ?></td>
<td><?php echo lookupChampion($game->championId); ?></td>
<td><?php echo (int)$game->stats->championsKilled."/".(int)$game->stats->numDeaths."/".(int)$game->stats->assists ?></td>
<td><?php echo date('m/d/Y', (int)substr($game->createDate,0,-3)); ?></td>
<?php echo "<td style='background-color: gray; color: white; cursor: pointer; font-size: small;' onclick=\"showDiv('g".$game->gameId."')\">+</td>" ?>
</tr>
<?php echo "<tr style='display: none;' id='g".$game->gameId."'>" ?><td colspan='5'><table width='100%'>
<?php foreach ($game->stats->children() as $statName => $stat): ?>
<tr><td><?php echo $statName; ?></td><td><?php echo formatStat($statName,$stat); ?></td></tr>
<?php endforeach; ?>
<?php echo "</table>" ?>
</td></tr>
<?php endforeach; ?>
</table>

</td></tr></table

   </center>
</div>
</body>
</html>
