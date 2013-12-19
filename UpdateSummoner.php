<?php
    // This file updates summoner info from the RIOT server and adds it to the SummonerInfo.xml file.
    // Send the summoner name as the query string and it will update that summoner.

    $summoner=$_GET['summoner'];
	$url = "http://prod.api.pvp.net/api/lol/na/v1.1/summoner/by-name/" . $summoner . "?api_key=aa31ef83-5c5d-433a-ad05-1dea9c3736e5";
	$JSON = file_get_contents($url);

	// echo the JSON (you can echo this to JavaScript to use it there)
	//echo $JSON;

	// You can decode it to process it in PHP
	$data = json_decode($JSON, true);
	var_dump($data);

	$xml=simplexml_load_file("SummonerInfo.xml");
    $node=$xml->xpath('//summoner[name[.="'.$summoner.'"]]');
    print_r($node[0]);
	$node[0]->id = $data["id"];
	$node[0]->name = $data["name"];
	$node[0]->profileIconId = $data["profileIconId"];
	$node[0]->summonerLevel = $data["summonerLevel"];
	$xml->asXML("SummonerInfo.xml");
?>