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
    $summoners=array("CPHLegolas","YeahhhBuddy","FauxRizzle","Scribnibs","Pyow","LeetDotCom","memeyoumeyouyoume","Tumbletron","CPHLego","CPHPulsar");
    $arrLength=count($summoners);
    $results=array();

	$xml=simplexml_load_file("SummonerInfo.xml");

    for($i=0;$i<$arrLength;$i++)
    {
        //look up summoner info
	    $node=$xml->xpath('//summoner[name[.="'.$summoners[$i].'"]]');
        $results[$i]['name']= $node[0]->name;
		$results[$i]['id']= $node[0]->id;
		$results[$i]['summonerLevel']= $node[0]->summonerLevel;
		$results[$i]['profileIconId']= $node[0]->profileIconId;
    }

    //sort
?>

<table>
<tr><td>
	
</tr></td>
<?php foreach ($results as $row): ?>
<tr>
<td></td>
<td><$?php echo $row['name']; ?></td>
<td><$?php echo $row['id']; ?></td>
<td><$?php echo $row['summonerLevel']; ?></td>
<td><$?php echo $row['profileIconId']; ?></td>
<td><input>Check for updates</input></td>
</tr>
<?php endforeach; ?>
</table>

   <header>
      <h1>Find Summoner Info</h1>
   </header>
   <section id="fetch">
      <input type="text" placeholder="Enter summoner ID" id="txtSearch" />
      <button id="bSearch">Look Up</button>
   </section>
   <section id="SummonerInfo">
   </section>
   <footer>
   </footer>
</div>
</body>
</html>