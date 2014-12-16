<?php include_once("php/functions.php"); ?>

<html>
	<head>
		<title>all</title>
		<link rel="stylesheet" type="text/css" href="/style/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="/style/style.css">
		<script type="text/javascript" src="/js/jquery.js"></script>
	</head>
	<body>
		<a id="main" class="title rand" href="/"><span>fifty</span><span>/</span><span>50</span></a>

		<div class="list">
			<?php $recents = pg_fetch_all(pg_query(pg_connect($_ENV["DATABASE_URL"]), "SELECT id,title,timestamp FROM data ORDER BY timestamp desc;")); ?>
			<?php //$recents = pg_fetch_all(pg_query(pg_connect("dbname=5050 user=_www"), "SELECT id,title,timestamp FROM data ORDER BY timestamp desc;")); ?>
			<?php if (!$recents) { ?> <div class="nada">nothing here yet...</div>  <?php } ?>
			<?php foreach ($recents as $index => $recent) { ?>
				<div class="recent">
					<div class="title"><a href="/info/<?=$recent["id"]?>"><?=$recent["title"]?></a></div>
					<div class="ago"><?=ago($recent["timestamp"])?> [<a class="stat_url" href="/stats/<?=$recent["id"]?>">stats</a>]</div>
				</div>
			<?php } ?>
		</div>
	</body>
</html>