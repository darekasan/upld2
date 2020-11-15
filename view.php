<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>upld仮2</title>
<style>
table tr td{
    height:32px;
}
</style>
<script>
function setFilename(){
    var name=document.getElementById('upfile').files[0].name;
    document.getElementById('title').value=name;
}
</script>
</head>
<body>

<?php
setlocale(LC_CTYPE, 'C');
$fileindex = fopen("fileindex.txt", "a+");
if( $fileindex ){
  while( !feof($fileindex) ){
     $records[]=fgetcsv($fileindex);
  }
}
fclose($fileindex);
?>

<h1>upld仮<span style="color:blue;">2</span></h1>

<form action="test.php" method="post" enctype="multipart/form-data">
    ファイル:<input type="file" name="upfile" id="upfile" onchange="setFilename()"><br>
    タイトル:<input type="text" name="title" id="title" size="60"><br>
    投稿者:<input type="text" name="name" size="30"><br>
    <input type="submit" value="アップロード">
</form>

<table border="1">
    <tr>
        <th>番号</th>
        <th>MP3</th>
        <th width="300">タイトル</th>
        <th>長さ</th>
        <th width="200">投稿者</th>
        <th>日付</th>
    </tr>
<?php
foreach(array_reverse($records) as $val){
    if(intval($val[5])==0){
        $json=json_decode(file_get_contents("files\\$val[1].json"),true);
        $duration=intval($json[streams][0][duration]);
        $duration_h=date('i:s', $duration);

        
        echo "<tr>
            <td>$val[0]</td>
            <td><a href='preview.php?filenum={$val[0]}'>再生</a></td>
            <td background='files/mini$val[1].png'><a href='files/$val[1]' download='[$val[0]]$val[2].wav'>$val[2]</a></td>
            <td>$duration_h</td>
            <td>$val[3]</td>
            <td>$val[4]</td>
            </tr>";
    }
}
?>
</table>
</body>
</html>