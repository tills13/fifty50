<?php include_once("php/functions.php"); ?>

<html>
	<head>
		<title>fifty/50 - [all]</title>
		<link rel="stylesheet" type="text/css" href="/style/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="/style/style.css">
		<script type="text/javascript" src="/js/jquery.js"></script>
	</head>
	<body>
		<a class="title dark-bg" href="/"><span>FIFTY</span><span>/</span><span>50</span></a>

		<div class="list">
			<?php $items = pg_fetch_all(pg_query(pg_connect($_ENV["DATABASE_URL"]), "SELECT id,title,timestamp,views FROM data ORDER BY timestamp desc;")); ?>
			<?php //$items = pg_fetch_all(pg_query(pg_connect("dbname=5050 user=_www"), "SELECT id,title,timestamp FROM data ORDER BY timestamp desc;")); ?>
			<?php if (!$items) { ?> <div class="nada">nothing here yet...</div>  <?php } ?>
			<?php foreach ($items as $index => $item) { ?>
				<div class="fifty-fifty">
					<a class="title" href="/info/<?=$item["id"]?>"><?=$item["title"]?></a>
					<div class="ago"><?=ago($item["timestamp"])?> [<a class="stat_url" href="/stats/<?=$item["id"]?>">stats</a>] [<span><?=($item["views"])?> views</span>]</div>
				</div>
			<?php } ?>
		</div>
	</body>
</html>