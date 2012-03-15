#!/usr/bin/php
# createimg.php 1 2000000 3000000
<?php
$m = new Mongo('192.168.223.111:27017'); //连接MongoDB 
$db = $m->photos; //选择数据库
$grid = $db->getGridFS(); //取得gridfs对象

require '/data/apps/cachetest/autoload.php';
$redis = new Predis\Client(array(
    'host'     => '192.168.223.113',
    'port'     => 6379,
    'database' => 0
));

$file_count = $argv[3];
switch($argv[1]){
case 1:
    for ($i = $argv[2]; $i < $file_count; $i++){
	$im = imagecreatefromjpeg('/dev/shm/demo.jpg'); or die("Cannot Initialize new GD image stream");
	$text_color = imagecolorallocate($im, 0, 0, 0);
	$s = str_pad($i.'',8,'0',STR_PAD_LEFT);
	imagestring($im, 5, 30, 25, $s ,$text_color);
	$p = substr($s,0,2).'/'.substr($s,2,2).'/'.substr($s,4,2).'/';
	imagejpeg($im,'/data/appdata/www/img/'.$p.$s.'.jpg');
	$redis->incr('count');
	imagedestroy($im);
    }
break;
case 2:
    for ($i = $argv[2]; $i < $file_count; $i++){
        $im = @imagecreatefromjpeg('/dev/shm/demo.jpg'); or die("Cannot Initialize new GD image stream");
        $background_color = imagecolorallocate($im, 255, 255, 255);
        $text_color = imagecolorallocate($im, 0, 0, 0);
        $s = str_pad($i.'',8,'0',STR_PAD_LEFT);
        imagestring($im, 5, 30, 25, $s ,$text_color);
        $p = '/dev/shm/'.$s.'.jpg'; 
	imagejpeg($im,$p);
	$grid->storeFile($p);
    $redis->incr('count');
	unlink($p);
    imagedestroy($im);
    }
break;
}
?>
