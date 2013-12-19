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
    $summoners=array("CPHLegolas","YeahhhBuddy","FauxRizzle","Scribnibs","Pyow","LeetDotCom","memeyoumeyouyoume","Tumbletron","CPHLego","CPHPulsar");
    $arrLength=count($summoners);
    $results=array();
    for($i=0;$i<$arrLength;$i++)
    {
        //look up summoner info
        $results[$i]= $summoners[$i]; //SUMMONERINFO
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
<td><$?php echo $row['league']; ?></td>
<td><$?php echo $row['division']; ?></td>
<td><$?php echo $row['points']; ?></td>-->
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