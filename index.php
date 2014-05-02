<?php

//引入框架核心文件
require './Happy/Happy.php';

run_time('start');
$i = 2000000;
while ($i > 0) {
    $i--;
}

echo run_time('start', 'end');
