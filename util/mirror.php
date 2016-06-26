<?php

require_once('common.php');
#$f = file_get_contents("php://stdin");
#preg_match_all('/value=(".[^"]+)/', $f, $matches);
#for($i=0;$i<count($matches[0]);++$i) {
#echo $matches[0][$i] . "\n";
#	$vals = explode("/", $matches[0][$i]);
#	$books[$vals[2]]=$vals[2];
#}
$stmt=$conn->my_query("select title from books where title not in('1_chronicles','1_corinthians')");
while($row = $conn->my_fetch_assoc($stmt)) {

	for($c = 1;$c<200;++$c) {
		$f = @file_get_contents('http://biblehub.com/'. $row['title'] . "/$c.htm");
		if(!$f) break;
		for($v = 1;$v<200;++$v) {
			$url = 'http://biblehub.com/' . $row['title'] . "/$c-$v.htm";
#	echo "$url\n";
			$f = @file_get_contents('http://biblehub.com/' . $row['title'] . "/$c-$v.htm");
if(!$f) break ;
			#Parallel Verses</div><div id="par"><span class="versiontext"><a href="/niv/1_chronicles/1.htm">New International Version</a></span><br />Adam, Seth, Enosh,
			$p=strpos($f,'Parallel Verses');
			$p2 = strpos($f,'Commentary');
			$f = substr($f, $p, $p2 - $p);
			$m = preg_split("+<span+", $f);
			foreach($m as $m2) {
#1.htm">New International Version</a></span><br />Adam, Seth, Enosh,<span
				$x = explode(">", $m2);
if(count($x) != 6) continue;
$version = trim(substr($x[2],0,-3));
$text = trim($x[5]);
#echo "version:{$version}\ntext:{$text}\n";
$text = str_replace('"','\\"', $text);
$sql = "insert into book_text values(\"$version\", \"{$row['title']}\",$c, $v, \"{$text}\")";
echo $sql . ";\n";
#$conn->my_query($sql);
			}
		}
	}

}

