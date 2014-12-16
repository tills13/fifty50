<?php
	function ago($time) {
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60", "60", "24", "7", "4.35", "12", "10");

		$now = time();
		$difference = $now - $time;
		$tense = "ago";

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) $difference /= $lengths[$j];
		$difference = round($difference);

		if ($difference != 1) $periods[$j] .= "s";
		return "$difference $periods[$j] ago";
	} 

	function short_num($number) {
		if ($number < 1000) return $number;
		return round($number/1000) . "." . round(round($number - 1000 * round($number/1000)) / 100) . "k";
	}
?>