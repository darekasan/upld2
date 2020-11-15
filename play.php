<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>upld仮2 - MP3再生</title>
</head>
<body>
<p><?php
ini_set("display_errors",On);
error_reporting(E_ALL);
$filenum=$_GET["filenum"];

$fileindex = fopen("fileindex.txt", "r");
if( $fileindex ){
  while( !feof($fileindex) ){
     $records[]=fgetcsv($fileindex);
  }
}

$i=0;

for(;$i<sizeof($records);$i++){
    if($records[$i][0]==$filenum){
        break;
    }
}
fclose($fileindex);

$name=$records[$i][2];

echo "<h1>{$name}</h1>";
echo "<audio src=""files/{$filenum}.mp3"" controls>";
?></p>
<a href="view.php">戻る</a>
</body>
</html>