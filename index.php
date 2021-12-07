<?php
require_once("scrapper.php");
// Lets test the crawler
$scrapper = new WebScrapper;
// echo $scrapper->crawler("https://orbcoins.com");
$page = $scrapper->scrap("https://orbcoins.com", ["container" => "section"]);
echo $page['title'];
 ?>