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
    
	var_dump(count($results));
    //sort
?>

<table>
<tr><td>
	
</tr></td>
<?php foreach ($results as $row): ?>
<tr>
<td></td>
<td>Name: <?php echo $row['name']; ?></td>
<td>Id: <?php echo $row['id']; ?></td>
<td>Level:  <?php echo $row['summonerLevel']; ?></td>
<td>Icon:  <?php echo $row['profileIconId']; ?></td>
<td>League:  <?php echo $row['league']; ?></td>
<td>Rank:  <?php echo $row['rank']; ?></td>
<td>Points:  <?php echo $row['leaguePoints']; ?></td>
<td><button id="bUpdate">Check for updates</button></td>
</tr>
<?php endforeach; ?>
</table>

   
</div>
</body>
</html>