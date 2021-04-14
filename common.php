<?php
setlocale(LC_CTYPE, 'C');
ini_set("display_errors","On");
error_reporting(E_ALL);

// Incoming WebhookのURLを書く
// 空なら投稿しない
$SLACK_URL = "";

// おいてるとこのURL、外から見えるようにスラッシュで終わる
$DIR_URL = "";

function parseFileRecordsFromStream($fileindex)
{
    if ($fileindex) {
        while (!feof($fileindex)) {
            $line = fgetcsv($fileindex);
            if (empty($line[0])) continue;
            $records[] = $line;
        }
    }
    
    return $records;
}

function loadFileRecords(){
    $fileindex = fopen("fileindex.txt", "a+");
    $records = parseFileRecordsFromStream($fileindex);
    return $records;
}

function getBiggestFilenum($records)
{
    $maxidx = 0;
    foreach ($records as $val) {
        if (intval($val[0]) > $maxidx) {
            $maxidx = intval($val[0]);
        }
    }
    return $maxidx;
}

function postSlack($text){
    global $SLACK_URL, $DIR_URL;

    if($SLACK_URL === ""){
        return false;
    }

    $data = array(
        "text" => $text
    );

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/json; charset=UTF-8',
            'content' => json_encode($data),
            'ignore_errors' => true
        ]
    ]);
    $res = file_get_contents($SLACK_URL, false, $context);
    echo $res;

    return true;
}