<?php 
	include_once("php/functions.php");
	if (!empty($_POST)) {
		$id = uniqid();
		$title = $_POST["title"];
		$url1 = $_POST["url1"];
		$url2 = $_POST["url2"];

		if (strlen($title) < 5) $error["title"] = "error";
		if (strlen($url1) == 0) $error["url1"] = "error";
		if (strlen($url2) == 0) $error["url2"] = "error";
		if ($url1 == $url2) $error["url1"] = $error["url2"] = "error";

		if (!$error) {
			$dbconn = pg_connect($_ENV["DATABASE_URL"]);
			pg_query($dbconn,"INSERT INTO data (id,timestamp,title,option_1,option_2,stats_option_1,stats_option_2) VALUES ('{$id}',extract(epoch from now()), '{$title}', '{$url1}', '{$url2}',0,0);");
			$short_url = $id;
		} else {
			$cache["title"] = $title;
			$cache["url1"] = $url1;
			$cache["url2"] = $url2;
		}
	}

	if (!empty($_GET) and !isset($_GET["stats"])) {
		$dbconn = pg_connect($_ENV["DATABASE_URL"]);
		//$dbconn = pg_connect("dbname=5050 user=_www");
		$result = pg_fetch_all(pg_query($dbconn, "SELECT * FROM data WHERE id='{$_GET["id"]}';"))[0];

		if (!$result) exit("what");
		else {
			pg_query($dbconn, "DELETE FROM connections WHERE (extract(epoch from now()) - timestamp) > 90;");
			$check = pg_fetch_all(pg_query($dbconn, "SELECT * FROM connections WHERE ip='{$_SERVER["REMOTE_ADDR"]}' AND ff_id='{$_GET["id"]}';"))[0];

			if (!$check) {
				$which = (isset($_GET["choice"]) and ($_GET["choice"] != "")) ? $_GET["choice"] : (((rand() % 10) > 5) ? 1 : 2);
				pg_query($dbconn, "INSERT INTO connections (ff_id,ip,timestamp,image_served) VALUES ('{$_GET["id"]}', '{$_SERVER["REMOTE_ADDR"]}', extract(epoch from now()), {$which});");
			} else $which = $check["image_served"];
		
			$url = $result["option_" . $which];
			pg_query($dbconn,"UPDATE data SET stats_option_{$which}=stats_option_{$which} + 1 WHERE id='{$_GET["id"]}';");
			print($url);
			//var_dump(preg_match_all("^http(s)?:\/\/.*", $url, $matches));
			//strstr("http(s)?://", needle)
			//header("Location: " . $url, true, 302);
			die();
		}
	}

	$stats = isset($_GET["stats"]);
?>


<html>
	<head>
		<title>50/50</title>
		<!--<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>-->
		<link rel="stylesheet" type="text/css" href="/style/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="/style/style.css">
		<script type="text/javascript" src="/js/jquery.js"></script>
	</head>
	<body>
		<div class="content">
			<form method="POST">
				<div align="center"><a id="main" class="title" href="/"><span>fifty</span><span>/</span><span>50</span></a></div>
				<div class="fl-container">
					<div class="fl-label">
						<span class="title">title</span>
						<span class="extra"></span>
					</div>
					<input class="fl-input min-length" type="text" name="title" placeholder="title" autocomplete="off">	
				</div>
				<div class="fl-container">
					<div class="fl-label">
						<span class="title">url #1</span>
						<span class="extra"></span>
					</div>
					<input class="fl-input is-url" type="text" name="url1" placeholder="url #1" autocomplete="off">	
				</div>
				<div class="fl-container">
					<div class="fl-label">
						<span class="title">url #2</span>
						<span class="extra"></span>
					</div>
					<input class="fl-input is-url" type="text" name="url2" placeholder="url #2" autocomplete="off">	
				</div>

				<div><a href="/random"><i class="fa fa-random random"></i><a href="/all"><i class="fa fa-list random"></i></a><input type="submit"></div>
				<script type="text/javascript">
					$(".fl-input").keyup(function() {
						var val = $(this).val();
						var container = $(this).parents(".fl-container");
						var fl = container.children(".fl-label");

						if (val === "") { container.animate({ padding: 9 }); fl.animate({ opacity: 0, top: 18 }); $(this).animate({ top: 0 }); }
						else { container.animate({ padding: "18 9" }); fl.animate({ opacity: 1, top: 9 }); $(this).animate({ top: 9 }); }

						if ($(this).hasClass("is-url")) { if (check_url($(this).val())) { fl.children(".extra").removeClass("bad").addClass("good"); } else { fl.children(".extra").removeClass("good").addClass("bad"); } }
						//if ($(this).hasClass("min-length"))
					});


					var check_url = function(val) {
						return val.match(/((https?:\/\/)[\w-]+(\.[\w-]+)+\.?(\/\S*)?)/);
					}
				</script>
			</form>

			<?php if (!empty($short_url)) { ?> 
				<div class="url" href="/5050/<?=$short_url?>">
					<div id="url" class="title">here's your 50/50</div>

					<div class="recent active" href="5050/<?=$short_url?>">
						<div class="title"><?=$title?></div>
						<div class="ago">now</div>
					</div> 

					<div id="url" class="title">share via</div>

					<a href="http://www.reddit.com/r/fiftyfifty/submit?title=<?=$title?>&url=<?=$_SERVER["HTTP_HOST"]?>/5050/<?=$short_url?>">
						<i class="fa fa-reddit"></i>
						<span id="url">reddit</span>
					</a>

					<a href="https://twitter.com/share?url=<?=$_SERVER["HTTP_HOST"]?>/5050/<?=$short_url?>&text=<?=title?>">
						<i class="fa fa-twitter"></i>
						<span id="url">twitter</span>
					</a>

					<a href="5050/<?=$short_url?>">
						<i class="fa fa-chain"></i>
						<span id="url"><?=$short_url?></span>
					</a>
				</div>
				
			<?php } else if ($stats) { 
				$dbconn = pg_connect($_ENV["DATABASE_URL"]);
				//$dbconn = pg_connect("dbname=5050 user=_www");
				$result = pg_fetch_all(pg_query($dbconn,"SELECT * FROM data WHERE id='{$_GET["id"]}';"))[0];
				$stats_option_1 = max($result["stats_option_1"], 1);
				$stats_option_2 = max($result["stats_option_2"], 1);

				$total = $stats_option_1 + $stats_option_2;
				$url_width_1 = max(($stats_option_1 / $total * 100), 25);
				$url_width_2 = max(100 - $url_width_1, 25); ?>
			<div class="stats">
				<div id="stats" class="title">stats</div> 
					<div class="stat_options"><?=$result["id"]?> [<a href="#">toggle urls</a>]</div>
			<?php if ($total > 10) { ?>
					<div class="stat">
						<div id="option_1" style="width: <?=$url_width_1?>%;">
							<div class="label hidden">URL #1</div>
							<div class="label"><a href="<?=$result["option_1"]?>" target="_blank"><?=$result["option_1"]?></a></div>
							<span class="count"><?=short_num($result["stats_option_1"])?></span>
						</div>
						<div id="option_2" style="width: <?=$url_width_2?>%;">
							<div class="label hidden">URL #2</div>
							<div class="label"><a href="<?=$result["option_2"]?>" target="_blank"><?=$result["option_2"]?></a></div>
							<span class="count"><?=short_num($result["stats_option_2"])?></span>
						</div>
					</div> 
					<div class="tips">stats are fuzzed below a certain threshold</div>
					<script type="text/javascript">
						$(".stat_options a").click(function() {
							var showing = $(".label:visible");
							var hidden = $(".label:not(:visible)");

							showing.toggleClass("hidden");
							hidden.toggleClass("hidden");
						});
					</script>
			<?php } else { ?>
					<div class="stat disabled">
						<div id="option_1">not enough data</div>
					</div> 
			<?php } ?>
					<input type="button" id="delete" value="mark as broken">
				</div>
			<?php } else { ?>
				<div class="recents">
					<div id="recents" class="title">recent <span>50</span><span>/</span><span>50</span>s</div> 
					<?php $recents = pg_fetch_all(pg_query(pg_connect($_ENV["DATABASE_URL"]), "SELECT id,title,timestamp FROM data ORDER BY timestamp desc LIMIT 4;")); ?>
					<?php //$recents = pg_fetch_all(pg_query(pg_connect("dbname=5050 user=_www"), "SELECT id,title,timestamp FROM data ORDER BY timestamp desc LIMIT 4;")); ?>
					<?php if (!$recents) { ?> <div class="nada">nothing here yet...</div>  <?php } ?>
					<?php foreach ($recents as $index => $recent) { ?>
						<div class="recent">
							<div class="title"><a href="/info/<?=$recent["id"]?>"><?=$recent["title"]?></a></div>
							<div class="ago"><?=ago($recent["timestamp"])?> [<a class="stat_url" href="/stats/<?=$recent["id"]?>">stats</a>]</div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</body>
</html>