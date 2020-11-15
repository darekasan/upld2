<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>upld仮</title>
</head>
<body>
<p><?php
$title=$_POST["title"];
$name=$_POST["name"];
$ext="wav";
setlocale(LC_CTYPE, 'C');
$fileindex = fopen("fileindex.txt", "a+");
$logfile = fopen("log.txt", "a+");
if( $fileindex ){
  while( !feof($fileindex) ){
     $records[]=fgetcsv($fileindex);
     
  }
}

$maxidx=0;
foreach($records as $val){
  if(intval($val[0])>$maxidx){
    $maxidx=intval($val[0]);
  }
}
$filenum=$maxidx+1;
$filename=$filenum .".". $ext;
$date=date("Y-m-d H:i:s");
if (is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
  if (move_uploaded_file($_FILES["upfile"]["tmp_name"], "files/$filename")) {
    echo $filename . "をアップロードしました。";
    
    fwrite($fileindex,"\n$filenum,$filename,\"$title\",\"$name\",$date,0");

    fwrite($logfile,"\nupload ".$date." ".$filename." ".$_SERVER['HTTP_USER_AGENT']." @ ".$_SERVER['REMOTE_ADDR']);
    
    $fp = popen('start C:\\apps\\lame.exe -V2 "'.getcwd().'\\files\\'.$filenum.'.wav"', 'r');
    $fp = popen('C:\\apps\\ffprobe.exe -show_streams -of json -i "'.getcwd().'\\files\\'.$filenum.'.wav" > "'.getcwd().'\\files\\'.$filenum.'.wav.json"', 'r');
    //$fp = popen('start C:\\apps\\ffprobe.exe > test.txt','r');
    $fp = popen('start C:\\apps\\wav2png.exe -w 800 -h 256 -b ffffffff -f 0000bbff "'.getcwd().'\\files\\'.$filenum.'.wav"', 'r');
    $fp = popen('start C:\\apps\\wav2png.exe -w 300 -h 32 -b ffffffff -f ccccccff -o "'.getcwd().'\\files\\mini'.$filenum.'.wav.png"'.' "'.getcwd().'\\files\\'.$filenum.'.wav"', 'r');
    pclose($fp);
  } else {
    echo "ファイルをアップロードできません。";
  }
} else {
  echo "ファイルが選択されていません。";
}

fclose($logfile);
fclose($fileindex);

?></p>
<a href="index.html">戻る</a>
</body>
</html>