<?php
setlocale(LC_CTYPE, 'C');
require_once('common.php');
$records = loadFileRecords();

foreach(array_reverse($records) as $val){
    if(intval($val[5])==0){
        $json=json_decode(file_get_contents("files\\$val[1].json"),true);
        $duration=intval($json[streams][0][duration]);
        $duration_h=date('i:s', $duration);
        $result[$val[0]]=array('filenum'=>$val[0], 'filename'=>$val[1], 'title'=>$val[2], 'author'=>$val[3], 'duration'=>$duration_h);
        //$result[]=array('filenum'=>$val[0], 'title'=>$val[2], 'author'=>$val[3], 'duration'=>$duration_h);
    }
}
print(json_encode($result,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
?>