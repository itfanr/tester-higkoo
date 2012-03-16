#!/usr/bin/php
<?php
$m = new Mongo("mongodb://192.168.223.111");
$db = $m->data;
$ph = $m->photos; //选择数据库
$grid = $ph->getGridFS(); //取得gridfs对象
$collection = new MongoCollection($db, 'Col');
$content="123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890HK";

require '/data/apps/cachetest/autoload.php';
$redis = new Predis\Client(array(
    'host'     => '192.168.223.113',
    'port'     => 6379,
    'database' => 0
));

switch($argv[1]){
case 1:// 写入100字节字符串
    for ($i = $argv[2]; $i < $argv[3]; $i++){
        $s = str_pad($i.'',8,'0',STR_PAD_LEFT);
        $info = array("id" => $s , "value" => $content.$s);
        $collection->insert($info, true);
    }
    break;
case 2:// 随机读取字符串
    $i=0;
    while($i<1000000) {
#    while($i<1) {
	$r = rand($argv[2],$argv[3]);
	$s = str_pad($r.'',8,'0',STR_PAD_LEFT);
        $key = array('id'=>$s);
        $cursor = $collection->findOne($key);
#	echo $s."\t";var_dump($cursor);
	$i++;
	$redis->incr('count');
    }
    break;
case 3:// 写入二进制数据
    for ($i = $argv[2]; $i < $argv[3]; $i++){
        $s = str_pad($i.'',8,'0',STR_PAD_LEFT);
# dd if=/dev/urandom of=/dev/shm/BinFile bs=1 count=100
	$p = '/dev/shm/BinFile';
        $info = array("id" => $s , "value" => new MongoBinData(file_get_contents($p)));
        $collection->save($info);
	$redis->incr('count');
    }
    break;
case 4:// 随机读取文件 
    $i=0;
    while($i<1000000) {
        $r = rand($argv[2],$argv[3]);
        $s = str_pad($r.'',8,'0',STR_PAD_LEFT);
        $key = array('filename'=>'/dev/shm/'.$s.'.jpg');
        $image = $grid->findOne($key);
	$image->write('/dev/null');
        $i++;
        $redis->incr('count');
    }
    break;
}
$m->close();
?>
