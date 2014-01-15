LOLAFI
======

Testing the Riot API

I set up this Repo to allow the other guys to be able to access the code and make tweaks of their own if they are so inclined.


SummonerInfo.xml
================
This file contains all information that has been saved from summoner updates.  It contains all games and summoner information and works similar to a small database.
(That being said I didn't think there would be enough statistical information to justify using a real database but I guess we'll see as time goes on.)


UpdateSummoner.xml
==================
This file takes in a summoner name as a query string and updates their record in the SummonerInfo.xml file with new information.  All new games and league information will be saved.


index.php
=========
Contains the basic ladder which displays combined information on all summoners included in the SummonerInfo.xml file.
