<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>upld仮2 - プレビュー</title>
</head>
<body>
<p><?php
setlocale(LC_CTYPE, 'C');
ini_set("display_errors",On);
error_reporting(E_ALL);

$filenum=$_GET["filenum"];

require_once('common.php');
$records = loadFileRecords();

$i=0;

for(;$i<sizeof($records);$i++){
    if($records[$i][0]==$filenum){
        break;
    }
}

$name=$records[$i][2];

$json=json_decode(file_get_contents("files\\{$records[$i][0]}.json"),true);

echo "<h1>{$name}</h1>";
echo "<audio id='play' src='files/{$filenum}.m4a' controls></audio>";
echo "<div id='wave' style='background:url(\"files/{$filenum}.wf2.png\");width:800px;height:256px;border:solid 2px black'>";

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
play.addEventListener("timeupdate", function(){
    var p =document.getElementById("progress");
    var time =document.getElementById("time");
	p.style.width = 800*play.currentTime/play.duration+"px";

    time.innerHTML = secondsToMinutesAndSeconds(play.currentTime) + " / " + secondsToMinutesAndSeconds(play.duration);
	}, false);
    

document.getElementById("wave").addEventListener( "click", function( event ) {
	var clickX = event.pageX ;
	var clientRect = this.getBoundingClientRect() ;
	var positionX = clientRect.left + window.pageXOffset ;
	var x = clickX - positionX ;
	play.currentTime=play.duration*x/800;
} ) ;
function secondsToMinutesAndSeconds(val){

  var seconds = parseInt(val % 60, 10)
  var minutes = parseInt((val / 60) % 60, 10)

  seconds = (seconds < 10) ? "0" + seconds : seconds

  return minutes + ":" + seconds;
}

</script>

<p>
<?php
echo "{$json['streams'][0]['channels']}ch {$json['streams'][0]['codec_long_name']} @ {$json['streams'][0]['sample_rate']}Hz";

?>
</p>

<a href="view.php">戻る</a>
</body>
</html>