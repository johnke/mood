<?php
try {
    $db = new PDO('sqlite:db/mood.db');
} catch (Exception $e) {
    die($e);
}

if (isset($_POST['mood'])) {
	$mood = $_POST['mood'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$day = $_POST['day'];
	$check_query = 'select mood from mood where year="'.$year.'" and month="'.$month.'" and day="'.$day.'"';
	$mood_check = $db->query($check_query);
	$num_rows = $mood_check->fetchColumn();
	if ($num_rows > 0) {
		$query = 'update mood set mood="'.$mood.'" where year = "'.$year.'" and month = "'.$month.'" and day = "'.$day.'"';
	} else {
		$query = 'insert into mood (mood, year, month, day) values ("'.$mood.'", "'.$year.'", "'.$month.'", "'.$day.'");';
	}
	$db->exec($query);
}
?><!doctype html>
<html lang="en" class="no-js">
<head>
  <meta charset="utf-8">
  <title>How was your day?</title>
  <meta name="description" content="">
  <meta name="author" content="John Kelly">

  <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
  <link rel="stylesheet" href="/mood/css/style.css?v=1">
</head>

<body>
  <div id="container">
    <header>
		<h1>How was your day?</h1>
    </header>
    <div id="main">
	<?php
	$days = "365";
	$curyear = date("Y");
	$is_leapyear = date('%L');
	if ($is_leapyear == "1")
		$days = "366";
	$day = date("d");				
	$month = date("m");				
	$year = date("Y");					
	if ($cur_year != "")
		$year = $cur_year;
	if ($cur_month != "")
		$month = $cur_month;
	if ($cur_day != "")
		$day = $cur_day;

	$longmonth = date("F", mktime(0, 0, 0, $month, $day, $year));
	$last_year = date("Y", strtotime("$day $longmonth $year -1 day"));
	$last_month = date("m", strtotime("$day $longmonth $year -1 day"));
	$last_day = date("d", strtotime("$day $longmonth $year -1 day"));
	
	if (isset($_GET['update'])) {
	?>
	<div class="date"><?php echo date("j M Y", mktime(0, 0, 0, $month, $day, $year)); ?></div>
	<div class="previous"><a href="/mood/?year=<?=$last_year?>&month=<?=$last_month?>&day=<?=$last_day?>">&laquo;</a></div>
	<div class="choose">
		<ul>
			<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
			<input type="hidden" name="year" value="<?php echo $year ?>">
			<input type="hidden" name="month" value="<?php echo $month ?>">
			<input type="hidden" name="day" value="<?php echo $day ?>">
			<li><input type="image" src="/mood/smileys/1.png" name="mood" value="1" alt="great!"></li>
			<li><input type="image" src="/mood/smileys/2.png" name="mood" value="2" alt="good"></li>
			<li><input type="image" src="/mood/smileys/3.png" name="mood" value="3" alt="meh."></li>
			<li><input type="image" src="/mood/smileys/4.png" name="mood" value="4" alt="bad"></li>
			<li><input type="image" src="/mood/smileys/5.png" name="mood" value="5" alt="terrible"></li>
			</form>
		</ul>
	</div>
	<div class="next"><a href="#">&raquo;</a></div>
	<?php
	} else {
		for ($i=1; $i<=12; $i++) {
			$month_num = date("M", mktime(0,0,0,$i,1,$curyear));
			$days_in_month = date("t", mktime(0,0,0,$i,1,$curyear));
			for ($j=1; $j<=$days_in_month; $j++) {
				$mon = $i;
				$day = $j;
				if ($i < 10)
					$mon = "0".$i;
				if ($j < 10)
					$day = "0".$j;
				$query = 'select mood from mood where year="'.$curyear.'" and month="'.$mon.'" and day="'.$day.'" limit 1';
		 		$moods = $db->prepare($query);
				$moods->execute();
				$fulldate = $curyear.$mon.$day;
				$png_number = "0";
				$png_class = " opaque";
				echo "\n<div class=\"smiley\"><img src=\"/mood/smileys/";
				while ($mood = $moods->fetchObject()):
					$mood_int = $mood->mood;
					if ($mood_int != "") {
						$png_class = "";
						$png_number = $mood_int;
					}
				endwhile;
				echo $png_number;
				if ($j == "1")
					echo "-01";
				echo ".png\" class=\"$fulldate $png_class";
				echo "\"><span class=\"date\">$j</span>";
				echo "</div>";
		
			}
		}
	}
	?>
    </div>
	<div style="clear: both"></div>
    <footer>
		All code &copy; 2010 <a href="mailto:johnke@gmail.com">johnke@gmail.com</a> - based on an idea by <a href="http://brigadacreativa.bigcartel.com/product/life-calendar-how-was-your-day">Brigada Creativa</a>
    </footer>
</div> <!--! end of #container -->
</body>
</html>
