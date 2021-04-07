<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>upld仮</title>
</head>
<body>
<p><?php
setlocale(LC_CTYPE, 'C');
$filenum=$_GET["filenum"];
require_once('common.php');
$fileindex = fopen("fileindex.txt", "a+");
$logfile = fopen("log.txt", "a+");


$records=loadFileRecords($fileindex);

$i=0;
for($i=0;i<count($records);i++){
    if(intval($records[i][0])==$filenum){
        break;
    }
}
fclose($fileindex);
$date=date("Y-m-d H:i:s");

$filename=$records[i][1];
if(unlink("files/$filename")){
	echo "$filenameを削除しました。";
	fwrite($logfile,"\ndelete ".$date." ".$filename." ".$_SERVER['HTTP_USER_AGENT']." @ ".$_SERVER['REMOTE_ADDR']);
	$records[i][5]="1";
    unlink("fileindex.txt");
	foreach($records as $val){
        $fileindex = fopen("fileindex.txt", "a+");
        fwrite($fileindex,"$val[0],$val[1],$val[2],$val[3],$val[4],$val[5]");
	}
    fclose($fileindex);
}else{
	echo "削除に失敗しました。";
}


fclose($logfile);

echo "del";
?></p>
<a href="view.php">戻る</a>
</body>
</html>