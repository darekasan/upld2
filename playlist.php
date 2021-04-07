<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>upld仮2 - プレイリスト再生</title>
</head>
<body>
<p><?php
setlocale(LC_CTYPE, 'C');
require_once('common.php');
$records = loadFileRecords();

$filenums=explode(",",$_GET["filenums"]);

$playlist=array();
foreach($filenums as $fnum){
    $idx=0;
    for(;$idx<sizeof($records);$idx++){
        if($records[$idx][0]==$fnum){
            array_push($playlist,$records[$idx]);
            break;
        }
    }
}


echo "<h1>プレイリスト再生</h1>";
echo "<p id='title'></p>";
echo "<audio id='play' controls></audio>";
echo "<div id='wave' style='width:800px;height:256px;border:solid 2px black'>";

?>
<div id='progress' style='height:256px;width:1px;background-color:white;opacity:0.5;'></div></div>

<a href="javascript:play.play()">再生</a>
<a href="javascript:play.pause()">一時停止</a>
<a href="javascript:var cue=play.currentTime+parseFloat(document.getElementById('offset').value)">CUEを打つ</a>
<a href="javascript:play.currentTime=cue">CUEに飛ぶ</a>
スピード<input type="range" min="0.5" max="1.5" step="0.125" value="1"
 onchange="play.playbackRate=this.value">
CUEのオフセット(遅延対策)<input id="offset" type="text" value="-0.25">
<div id="time"></div>
</p>
<script>
var play = document.getElementById("play");
var wave = document.getElementById("wave");
var title = document.getElementById("title");
var playlist = <?php echo json_encode($playlist); ?>;
var currentTrackNum;

play.addEventListener("timeupdate", update, false);
play.addEventListener("loadeddata", update, false);
play.addEventListener( "ended", function( event ) {
    if(currentTrackNum==playlist.length){
        select(0);
    }else{
        select(currentTrackNum+1);
    }
    
    play.play();
});

wave.addEventListener( "click", function( event ) {
	var clickX = event.pageX ;
	var clientRect = this.getBoundingClientRect() ;
	var positionX = clientRect.left + window.pageXOffset ;
	var x = clickX - positionX ;
	play.currentTime=play.duration*x/800;
} ) ;

select(0);

function secondsToMinutesAndSeconds(val){

  var seconds = parseInt(val % 60, 10)
  var minutes = parseInt((val / 60) % 60, 10)

  seconds = (seconds < 10) ? "0" + seconds : seconds

  return minutes + ":" + seconds;
}

function select(idx){
    wave.style.background="url('files/"+playlist[idx][0]+".wf2.png')";
    play.src="files/"+playlist[idx][0]+".m4a";
    play.currentTime=0;
    title.innerText=playlist[idx][3]+" - "+playlist[idx][2];
    currentTrackNum=idx;
    update();
}

function update(){
    var p =document.getElementById("progress");
    var time =document.getElementById("time");
	p.style.width = 800*play.currentTime/play.duration+"px";
    
    time.innerHTML = secondsToMinutesAndSeconds(play.currentTime) + " / " + secondsToMinutesAndSeconds(play.duration);
}

</script>
<table border="1">
    <tr>
        <th>番号</th>
        <th width="300">タイトル</th>
        <th width="200">投稿者</th>
    <tr>
<?php

foreach($playlist as $i => $row){
    echo "<tr>
    <td><a href='javascript:select($i)'>$i</a></td>
    <td background='files/{$row[0]}.wf1.png'><a href='files/$row[1]' download='$row[2] - $row[3] $row[0]'>$row[2]</a></td>
    <td>{$row[3]}</td>";
}
?>
<a href="view.php">戻る</a>
</body>
</html>