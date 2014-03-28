<?php

  $key="";  //api key goes here
    // This file updates summoner info from the RIOT server and adds it to the SummonerInfo.xml file.
    // Send the summoner name as the query string and it will update that summoner.

    $summonerSpace=$_GET['summoner'];
    $summoner=str_replace(' ', '', $summonerSpace);
	$url = "http://prod.api.pvp.net/api/lol/na/v1.3/summoner/by-name/" . $summoner . "?api_key=" . $key;
	$JSON = file_get_contents($url);

	// echo the JSON (you can echo this to JavaScript to use it there)
	//echo $JSON;

	// You can decode it to process it in PHP
	$datanonarray = json_decode($JSON, true);
	$data=array_values($datanonarray);
	//since you can now send a comma-separated list of summoner names the data that comes back is in array form.
	//We are only sending one summoner name so the result will be the first element of the returned array or element [0]
  $summonerId=$data[0]["id"];
	//var_dump($data);
	$xml=simplexml_load_file("SummonerInfo.xml");
    //REMOVE SPACES BEFORE THIS WILL WORK
    $node=$xml->xpath('//summoner[name[.="'.$summonerSpace.'"]]');
    
	//$node[0]->id = $data["id"];
	//$node[0]->name = $data["name"];
	$node[0]->profileIconId = $data[0]["profileIconId"];
	$node[0]->summonerLevel = $data[0]["summonerLevel"];
	//now timestamp this entry
	date_default_timezone_set('America/Chicago');
	$timestamp = date('Y-m-d H:i:s');
	$node[0]->lastUpdate = $timestamp;
	
  
  $urlLeague = "http://prod.api.pvp.net/api/lol/na/v2.3/league/by-summoner/" . $summonerId . "?api_key=" . $key;
	$JSONLeague = file_get_contents($urlLeague);
  $ulData=json_decode($JSONLeague,true);
  if($ulData!=null)
  {
  $lData = array_values($ulData);  //array_values makes this array indexable.  Otherwise you have to know the leagueid in advance
  for($k=0;$k<count($lData);$k++)
  {
  $leagueData=$lData[$k];  //For some reason lData contains 4? divisions.  Most times the summoner is in [0] but sometimes it is in the other ones.  I don't understand why.
  //var_dump(count($lData));//['25277476']);
  //get player's node not other people in the league
  //foreach($leagueData["entries"] as $leaguePlayer)
  //$leagueEntries=$leagueData["entries"]
  for($i=0;$i<count($leagueData["entries"]);$i++)
  {
    $leaguePlayer=$leagueData["entries"][$i];
    if($leaguePlayer["playerOrTeamId"]==$summonerId)
    {
      //var_dump($leaguePlayer);
      echo "Ranked data found.";
      $playerNode=$leaguePlayer["entries"];
      $node[0]->league=$leaguePlayer["tier"];
      $node[0]->rank=$leaguePlayer["rank"];
      $node[0]->leaguePoints=$leaguePlayer["leaguePoints"];
    }
  }
  }
  }
  //under the summoner get their games and check to see if any games are new.
  $urlGames = "http://prod.api.pvp.net//api/lol/na/v1.3/game/by-summoner/" . $summonerId . "/recent?api_key=" . $key;
	$JSONGames = file_get_contents($urlGames);
  $gamesData = json_decode($JSONGames, true);
  $gameData=$gamesData["games"];
  $games = $node[0]->games;
  //var_dump($gameData);
  //loop through gameData until you reach a game that has already been saved to xml
  //currently each new game is being compared to every game in the games list which is accurate but slow.
  //Ideally both lists would be sorted chronologically so that only 10 checks would need to be made (instead of 10xthe number of games total)
  
  echo "<br />GameData Count: ".count($gameData)." Games Count: ".count($games->game)."<br />";
  for($i=0;$i<count($gameData);$i++)  //we will get the 10 most recent from the api but they aren't necessarily in chronological order (i want them to be eventually along with the saved games)
  {
    if(count($gameData[$i]["stats"])>0)
    {
    //first check to see if we already have this game
    $isNew=true;
    for($j=0;$j<count($games->game);$j++)
    {
      if(($gameData[$i]["gameId"] == $games->game[$j]->gameId) || ($gameData[$i]["createDate"]<1389895200000))
        {
          //echo "Game ".$i." matched.<br />";
          $isNew=false;
          $j=count($games->game);
        }
    }
    
    //if we don't already have this game
    if($isNew)
    {
    //do I insert or append
    //this saves all data from the game
      $thisGame = $games->addChild('game');
      $thisGame->addChild('gameId',$gameData[$i]["gameId"]);
      $thisGame->addChild('gameMode',$gameData[$i]["gameMode"]);
      $thisGame->addChild('gameType',$gameData[$i]["gameType"]);
      $thisGame->addChild('subType',$gameData[$i]["subType"]);
      $thisGame->addChild('mapId',$gameData[$i]["mapId"]);
      $thisGame->addChild('teamId',$gameData[$i]["teamId"]);
      $thisGame->addChild('championId',$gameData[$i]["championId"]);
      $thisGame->addChild('spell1',$gameData[$i]["spell1"]);
      $thisGame->addChild('spell2',$gameData[$i]["spell2"]);
      $thisGame->addChild('level',$gameData[$i]["level"]);
      $thisGame->addChild('createDate',$gameData[$i]["createDate"]);
      
      $thisGamePlayers = $thisGame->addChild('fellowPlayers');
      foreach($gameData[$i]["fellowPlayers"] as $player)
      {
        $thisPlayer = $thisGamePlayers->addChild('player');
        $thisPlayer->addAttribute('summonerId',$player["summonerId"]);
        $thisPlayer->addAttribute('teamId',$player["teamId"]);
        $thisPlayer->addAttribute('championId',$player["championId"]);
      }
      $thisGameStats = $thisGame->addChild('stats');
      $thisGameDataStats = $gameData[$i]["stats"];
      foreach($thisGameDataStats as $statKey => $stat)
      {
      	//echo $statKey." - ".$stat."<br>";
      	$thisStat = $thisGameStats->addChild($statKey,$stat);
        //$thisStat = $thisGameStats->addAttribute($statKey,$stat);
      }
    }
    }
    else
    {
    	echo "no statistics found...<br />";	    
    }
  }
	$xml->asXML("SummonerInfo.xml");
  
?>