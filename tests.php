<?php
require 'nzpMedia.php';

$much_code = "Hej min https://www.youtube.com/watch?v=zO5u8ifbxkU ven https://soundcloud.com/mr-shield/shield-art-gallery-clip hvad så https://soundcloud.com/leagueoflegends/riot-games-freljord-theme";
$everything = nzpMedia::getAllMedia($much_code);
echo "<pre>";var_dump($everything);echo "</pre>";
foreach($everything as $provider=>$objs){
	echo "<h1>$provider</h1>";
	foreach($objs as $mediaObj){
		echo $mediaObj->embed();
	}
}