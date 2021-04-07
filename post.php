<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>upld仮</title>
</head>
<body>
<p><?php
require_once('common.php');

$title=$_POST["title"];
$name=$_POST["name"];

setlocale(LC_CTYPE, 'C');

$logfile = fopen("log.txt", "a+");

$fileindex = fopen("fileindex.txt", "a+");
$records=loadFileRecords($fileindex);

$filenum=getBiggestFilenum($records)+1;


$date=date("Y-m-d H:i:s");
if (is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
  $ext = pathinfo($_FILES["upfile"]["name"], PATHINFO_EXTENSION);
  $filename = $filenum .".". $ext;

  if (move_uploaded_file($_FILES["upfile"]["tmp_name"], "files/$filename")) {
    echo $filename . "をアップロードしました。";
    
    fwrite($fileindex,"\n$filenum,$filename,\"$title\",\"$name\",$date,0");

    fwrite($logfile,"\nupload ".$date." ".$filename." ".$_SERVER['HTTP_USER_AGENT']." @ ".$_SERVER['REMOTE_ADDR']);
    
    $inputFile = getcwd().'\\files\\'.$filename;
    $outM4a = getcwd().'\\files\\'.$filenum.'.m4a';
    $outWf1 = getcwd().'\\files\\'.$filenum.'.wf1.png';
    $outWf2 = getcwd().'\\files\\'.$filenum.'.wf2.png';
    $outInfo = getcwd().'\\files\\'.$filenum.'.json';

    $fp = popen("ffmpeg -i $inputFile -filter_complex \"showwavespic=300x32:colors=gray:scale=log\" $outWf1", 'r');
    $fp = popen("ffmpeg -i $inputFile -filter_complex \"showwavespic=800x256:colors=gray:scale=log\" $outWf2", 'r');
    $fp = popen("ffmpeg -i $inputFile -vn -ac 2 -ar 44100 -ab 256k -acodec aac -strict experimental -f mp4 $outM4a", 'r');
    $fp = popen("C:\\apps\\ffprobe.exe -show_streams -of json -i $inputFile > $outInfo", 'r');

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
<a href="view.php">戻る</a>
</body>
</html>