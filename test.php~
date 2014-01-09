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
		background-color: #7ADAC6;
		color: #000099;
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
	//consider getting this array straight from Summoners.xml
	//currently these are the summoners that are pulled for this page from the xml although the xml might contain more than this.
    $summoners=array("CPH Legolas","YeahhhBuddy","FauxRizzle","Scribnibs","Pyow","Leetdotcom","Memeyoumeyouyou","Tumbletron","CPH Lego","CPH Pulsar");
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
    
    usort($results,"cmp");
    
?>
<center>
<table class='ranktable' border='1'>
<tr class='ranktableheader'>
	<td>Icon</td><td>Name</td><!--<td></td>--><td>Level</td><td colspan='2'>League</td><td>Points</td><!--<td></td>-->
</tr>
<?php foreach ($results as $row): ?>
<tr>
<td><div class='iconcell' style="background-image:url(http://lkimg.zamimg.com/shared/riot/images/profile_icons/profileIcon<?php echo $row['profileIconId']; ?>.jpg);"> </td>
<td><?php echo $row['name']; ?></td>
<!--<td>Id: <?php echo $row['id']; ?></td>-->
<td><?php echo $row['summonerLevel']; ?></td>
<td><?php echo $row['league']; ?></td>
<td><?php echo $row['rank']; ?></td>
<td><?php echo $row['leaguePoints']; ?></td>
<!--<td><button id="bUpdate">Check for updates</button></td>-->
</tr>
<?php endforeach; ?>
</table>

   </center>
</div>
</body>
</html>