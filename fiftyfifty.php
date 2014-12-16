<?php
	include_once("php/functions.php");
	$dbconn = pg_connect($_ENV["DATABASE_URL"]);
	if (isset($_GET["id"]) and ($_GET["id"] != "")) $result = pg_fetch_all(pg_query($dbconn, "SELECT * FROM data WHERE id='{$_GET["id"]}'"))[0];
	else $result = pg_fetch_all(pg_query($dbconn, "SELECT * FROM data ORDER BY random() LIMIT 1;"))[0];
	
	$stats_option_1 = max($result["stats_option_1"], 1);
	$stats_option_2 = max($result["stats_option_2"], 1);

	$total = $stats_option_1 + $stats_option_2;
	$url_width_1 = max(($stats_option_1 / $total * 100), 25);
	$url_width_2 = max(100 - $url_width_1, 25);
?>

<html>
	<head>
		<title>50/50 [<?=$result["id"]?>]</title>
		<link rel="stylesheet" type="text/css" href="/style/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="/style/style.css">
		<script type="text/javascript" src="/js/jquery.js"></script>
	</head>
	<body>
		<a id="main" class="title rand" href="/"><span>fifty</span><span>/</span><span>50</span></a>
		<div id="random" class="title">[<span><?=$result["title"]?></span>]</div>

		<div class="random">
			<a class="option" href="/5050/<?=$result["id"]?>/1" style="width: <?=$url_width_1?>%;">URL #1 <span class="perc">[<?=round($stats_option_1 / $total * 100)?>%]</span></a>
			<div style="border-left: 1px dashed white; margin: auto; height: 300px;"></div>
			<a class="option" href="/5050/<?=$result["id"]?>/2" style="width: <?=$url_width_2?>%;">URL #2 <span class="perc">[<?=round($stats_option_2 / $total * 100)?>%]</span></a>
		</div>
	
		<div class="tips">you can see the URLs <a href="/stats/<?=$result["id"]?>">here</a>, if you must</div>
		<div class="tips">seen by <?=($result["stats_option_1"] + 0 + $result["stats_option_2"])?> <?=(($result["stats_option_1"] + 0 + $result["stats_option_2"]) != 1) ? "people" : "person"?></div>
		

		<script type="text/javascript">
			$("div#random.title span").html($("#random.title span").text().replace(/( or |\||\\|\/)/, "<span class=\"hightlight\">$1</span>"));
		</script>
	</body>
</html>