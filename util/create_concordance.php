<?php

require_once('common.php');
require_once('porter.php');
$stemmer = new PorterStemmer;
#$f = file_get_contents("php://stdin");
#preg_match_all('/value=(".[^"]+)/', $f, $matches);
#for($i=0;$i<count($matches[0]);++$i) {
#echo $matches[0][$i] . "\n";
#	$vals = explode("/", $matches[0][$i]);
#	$books[$vals[2]]=$vals[2];
#}
$stmt=$conn->my_unbuffered_query("select version, title,verse_text from book_text");
while($row = $conn->my_fetch_assoc($stmt)) {
	$words = preg_replace(array("/--/","/(?:&#.+;)/")," ",$row['verse_text']);
	$words = explode(" ", $words);
	$version = str_replace("'","''", $row['version']);
	$title = str_replace("'","''",$row['title']);
	foreach($words as $word) {
			$word = strtolower(trim($word,".,:;'[](){}-\"?!"));
			$word = str_replace(array("'s", "'"),array("","''"),$word);
			$stemmed = $stemmer->Stem($word);
			$sql = "insert into book_words values('$word','$version','$title',1) on duplicate key update cnt=cnt+1;";
			echo $sql . "\n";
			$sql = "insert into book_stemmed values('$stemmed', '$version','$title',1) on duplicate key update cnt=cnt+1;";
			echo $sql . "\n";
	}
	
}


