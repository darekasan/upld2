<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>upld仮2</title>
<style>
table tr td{
    height:32px;
}

/* 投稿者隠しいらないなら消す */
.tbl-author {
    background-color: black;
}

.tbl-author:hover{
    background-color: white;
}
</style>
<script>
function setFilename(){
    var regxWithOutExt = /(.*)(?:\.([^.]+$))/;
    var name=document.getElementById('upfile').files[0].name;
    
    var newName = name.match(regxWithOutExt)[1];
    if(newName==null){
        newName = name;
    }

    document.getElementById('title').value=newName;
}
</script>
</head>
<body>

<?php
setlocale(LC_CTYPE, 'C');
ini_set("display_errors",On);
error_reporting(E_ALL);

require_once('common.php');
$records = loadFileRecords();
?>

<h1>upld仮<span style="color:blue;">2</span></h1>

<p>
    WAVやMP3、AIFFなど主要なフォーマットなら大体大丈夫っぽい。
</p>

<form action="post.php" method="post" enctype="multipart/form-data">
    ファイル:<input type="file" name="upfile" id="upfile" onchange="setFilename()"><br>
    タイトル:<input type="text" name="title" id="title" size="60"><br>
    投稿者:<input type="text" name="name" size="30"><br>
    <input type="submit" value="アップロード">
</form>

<table border="1">
    <tr>
        <th>番号</th>
        <th></th>
        <th width="300">タイトル</th>
        <th>長さ</th>
        <th width="200">投稿者</th>
        <th>日付</th>
    </tr>
<?php
foreach(array_reverse($records) as $val){
    if(intval($val[5])==0){
        $json=json_decode(file_get_contents("files\\$val[0].json"),true);
        $duration=intval($json["streams"][0]["duration"]);
        $duration_h=date('i:s', $duration);

        
        echo "<tr>
            <td>$val[0]</td>
            <td><a href='preview.php?filenum={$val[0]}'>再生</a></td>
            <td background='files/$val[0].wf1.png'><a href='files/$val[1]' download='[$val[0]]$val[2] $val[1]'>$val[2]</a></td>

            <td>$duration_h</td>
            <td class='tbl-author'>$val[3]</td>
            <td>$val[4]</td>
            </tr>";
    }
}
?>
</table>

<p>
    playlist.php?filenums=1,2,3 みたいなURLでプレイリスト再生ができます。
</p>
<p>
    <a href="player.html">みんなで聞ける！同期プレイヤー</a>
</p>
</body>
</html>