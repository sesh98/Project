<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="Favi.ico" />
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
	<title>	
		<?php echo $_GET["searchquery"]."-Flash search";?>
	</title>
</head>
<style type="text/css">
	#cit{
		font-family: Arial;
		color:red;
	}
	#link{
		font-size: 150%;
		color:black;
		font-family:sans-serif;
	}
</style>
<body>
</body>
</html>
<?php
$servername="localhost";
$username="root";
$password="";
$db="project";
$conn=mysqli_connect($servername,$username,$password,$db);
if(!$conn){
	echo "Connection failed";
}
$search=$_GET["searchquery"];
$searchstr=explode(" ",$search);//breaks
$x=0;$construct="";
foreach ($searchstr as $search_each ) {
	if($x==0)
		$temp="'%$search_each%'";//search for words containing  search_each
	else 
		$temp =" AND '%$search_each%'";//to insert algorithm to give weightage to recurring words.
	$x++;
	$construct= implode(array($construct, $temp));
}//WHERE MATCH(productline) AGAINST('Classic');
echo $construct."<br>";

$sql=mysqli_query($conn,"SELECT url FROM searchengine WHERE MATCH (keywords) AGAINST ('$search' IN NATURAL LANGUAGE MODE);");
echo $sql;
$numResults=mysqli_num_rows($sql);

if($numResults==0){
	echo "Sorry :( ive failed you"."<br>";
}
else
{
	echo $numResults."Found"."<br>";

	while($row=mysqli_fetch_assoc($sql)){
		countocc($row['url'],$search);//return var
		echo "<a href='".$row['url']."' id='link'>".page_title($row['url'])."</a>"."<br>";
		echo "<cite id='cit'>".$row['url']."</cite><br>";
		if((array_key_exists('description', get_meta_tags($row['url']))))
			echo (get_meta_tags($row['url'])['description'])."<br>";
		else
			echo "No description"."<br>";
		echo "<br><br>";
		/*$title=$row['title'];
		$desc=$row['description'];
		$url=$row['url'];
		echo "<a href='$url'>"."<br>".$title."<br>".$desc."<br>";
		echo "<br>";*/
	}
}
function page_title($url) {
        $fp = file_get_contents($url);
        if (!$fp) 
            return null;

        $res = preg_match("/<title>(.*)<\/title>/siU", $fp, $title_matches);
        if (!$res) 
            return null; 

        // Clean up title: remove EOL's and excessive whitespace.
        $title = preg_replace('/\s+/', ' ', $title_matches[1]);
        $title = trim($title);
        return $title;
}
function countocc($url,$find){
       $str=file_get_contents($url);
       $no=(array_count_values(str_word_count(strip_tags(strtolower($str)), 1)));
       echo "No of occurrences=".$no[$find]."<br>";

	}
?>
