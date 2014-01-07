<?php

  $key="aa31ef83-5c5d-433a-ad05-1dea9c3736e5";  //api key goes here
    // This file updates summoner info from the RIOT server and adds it to the SummonerInfo.xml file.
    // Send the summoner name as the query string and it will update that summoner.

    $summonerSpace=$_GET['summoner'];
    $summoner=str_replace(' ', '', $summonerSpace);
	$url = "http://prod.api.pvp.net/api/lol/na/v1.1/summoner/by-name/" . $summoner . "?api_key=" . $key;
	$JSON = file_get_contents($url);

	// echo the JSON (you can echo this to JavaScript to use it there)
	//echo $JSON;

	// You can decode it to process it in PHP
	$data = json_decode($JSON, true);
  $summonerId=$data["id"];
	//var_dump($data);

	$xml=simplexml_load_file("SummonerInfo.xml");
    //REMOVE SPACES BEFORE THIS WILL WORK
    $node=$xml->xpath('//summoner[name[.="'.$summonerSpace.'"]]');
	$node[0]->id = $data["id"];
	$node[0]->name = $data["name"];
	$node[0]->profileIconId = $data["profileIconId"];
	$node[0]->summonerLevel = $data["summonerLevel"];
  
  $urlLeague = "http://prod.api.pvp.net/api/lol/na/v2.2/league/by-summoner/" . $summonerId . "?api_key=" . $key;
	$JSONLeague = file_get_contents($urlLeague);
  $leagueData=json_decode($JSONLeague,true);
  var_dump($leagueData[0]);
  //must get queue:  RANKED_SOLO_5x5
  //get player's node not other people in the league
  //foreach($leagueData["entries"] as $leaguePlayer)
  for($i=0;$i<count($leagueData["entries"]);$i++)
  {
    echo "Saving player ranked info";
    $leaguePlayer=$leagueData["entries"][$i];
    if($leaguePlayer["playerOrTeamId"]==$summonerId)
    {
      $playerNode=$leaguePlayer["entries"];
      $node[0]->league=$leagueData["tier"];
      $node[0]->rank=$leagueData["rank"];
      $node[0]->leaguePoints=$leagueData["leaguePoints"];
    }
  }
  
  //under the summoner get their games and check to see if any games are new.
  $urlGames = "http://prod.api.pvp.net//api/lol/na/v1.2/game/by-summoner/" . $summonerId . "/recent?api_key=" . $key;
	$JSONGames = file_get_contents($urlGames);
  $gamesData = json_decode($JSONGames, true);
  $gameData=$gamesData["games"];
  $games = $node[0]->games;
  //var_dump($node[0]);
  //loop through gameData until you reach a game that has already been saved to xml
  $i=0;
  while(($i<10) And ($gameData[$i]["createDate"] > $games->game[0]->createDate))
  {
    //echo "looping".$games;
    //var_dump($games);
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
      //var_dump($gameData[$i]);
      $thisGamePlayers = $thisGame->addChild('fellowPlayers');
      foreach($gameData[$i]["fellowPlayers"] as $player)
      {
        $thisPlayer = $thisGamePlayers->addChild('player');
        $thisPlayer->addAttribute('summonerId',$player["summonerId"]);
        $thisPlayer->addAttribute('teamId',$player["teamId"]);
        $thisPlayer->addAttribute('championId',$player["championId"]);
      }
      $thisGameStats = $thisGame->addChild('statistics');
      foreach($gameData[$i]["statistics"] as $stat)
      {
        $thisStat = $thisGameStats->addChild('stat');
        $thisStat->addAttribute('id',$stat["id"]);
        $thisStat->addAttribute('name',$stat["name"]);
        $thisStat->addAttribute('value',$stat["value"]);
      }
      $i=$i+1;
  }
	$xml->asXML("SummonerInfo.xml");
  
?>