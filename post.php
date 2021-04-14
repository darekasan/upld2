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

$date=date("Y-m-d H:i:s");
if (is_uploaded_file($_FILES["upfile"]["tmp_name"])) {

  // クライアントを信用してはだめ、環境次第じゃ拡張子が付かない
  //$ext = pathinfo($_FILES["upfile"]["name"], PATHINFO_EXTENSION);

  // ffprobeに拡張子をつけてもらう movとかになったりするけど問題ない
  $path = $_FILES["upfile"]["tmp_name"];
  exec("ffprobe.exe -show_format -of json -i $path",$out);
  $ffp = json_decode(implode("\n",$out),true);
  $ext = explode(",",$ffp["format"]["format_name"])[0];

  $fileindex = fopen("fileindex.txt", "a+");
  $records=loadFileRecords($fileindex);
  $filenum=getBiggestFilenum($records)+1;

  // 元ファイルのファイル名
  $filename = $filenum .".raw.". $ext;

  if (move_uploaded_file($_FILES["upfile"]["tmp_name"], "files/$filename")) {
    echo $filename . "をアップロードしました。";

    $logfile = fopen("log.txt", "a+");
    
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

    postSlack("新しい投稿\n<{$DIR_URL}preview.php?filenum={$filenum}|{$title}>");

    fclose($logfile);
    fclose($fileindex);

  } else {
    echo "ファイルをアップロードできません。";
  }
} else {
  echo "ファイルが選択されていません。";
}



?></p>
<a href="view.php">戻る</a>
</body>
</html>