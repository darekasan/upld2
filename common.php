<?php
setlocale(LC_CTYPE, 'C');

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

