<?php

declare(strict_types=1);
date_default_timezone_set('Asia/Tokyo');

if ($argc <= 1) {
    error_log('WARNING: This script cannot be run in itself!', 0);
    exit(1);
}

$src_file = $argv[1];
$dest_file = $argv[2];

if (file_exists($src_file) === false) {
    $msg = "Failed to find the source file \"$src_file\". ";
    error_log($msg, 0);
    exit(1);
}

$flag = 0;

while (1) {
    $bool = file_exists($dest_file);

    if ($flag === 0 and $bool === true) {
        break;
    }

    if ($flag === 0 and $bool === false) {
        file_put_contents($dest_file, "", LOCK_EX);
        $flag = 1;
    } elseif ($flag === 1 and $bool === true) {
        $msg = "Created a new destination file: \"$dest_file\"." . PHP_EOL;
        echo $msg;
        break;
    } else {
        $msg = "Failed to create a new destination file: \"$dest_file\". ";
        error_log($msg, 0);
        exit(1);
    }
}

$modtdate = [];

do {
    // キャッシュをクリアしないと更新しても反映されない
    clearstatcache();

    $modtdate[] = filemtime($src_file);

    // array は0からカウントするので一個少ない
    $last = count($modtdate) - 1;

    if (count($modtdate) > 2) {
        array_push($modtdate_tmp, $modtdate[$last - 1], $modtdate[$last]);
        $diff = $modtdate_tmp[1] - $modtdate_tmp[0];

        // 更新があった時に実行
        if ($diff !== 0) {

            passthru("sass $src_file $dest_file");

            $msg = "$src_file was successfully compiled into $dest_file!";
            echo date(DATE_ATOM) . ": $msg" . PHP_EOL;
        }
    }

    // 古いデータを削除
    if (count($modtdate) > 10) {
        $modtdate = [];
    }
    $modtdate_tmp = [];

    sleep(1);
} while (true);
