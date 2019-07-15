<?php
/* 主执行文件 */
include 'clineSQL.php';
include 'readfile.php';
include "scanDataFolder.php";

$dir = "/home/yetao/libraryPro/data"; // 所有的数据存放在dir/ 下 当前目录下的data文件夹
// 也可以写相对路径 但是建议在生产环境中写绝对路径


// 初始化一个连接
try{
    $db = new PDO('mysql:host=localhost;dbname=library', 'root', 'xiaoan@801yetao');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
}catch(PDOException $e){
    echo "连接失败: " .$e->getMessage();
}

// 先扫描文件夹
// 获取文件路径的关联数组
$pathArray_l = new DirFactory($dir);
echo var_dump($pathArray_l);
// $patharray = $pathArray_l->getpathArray(); 获取下一个需要遍历的列表
while (list($configpath, $LEDpath) = $pathArray_l->getpathArray()){
    // 通过路径获取数据
    $dataFactory = new DataFactory($configpath, $LEDpath);
    $config = $dataFactory->getconfig();
    $LEDdata = $dataFactory->getLED();
    // 存入工厂中 自动提交设备地区数据
    $device_dbobj = new OperaDevicesTable($db, $config, $LEDpath);
    $device_dbobj->autocommit();
    // 更新客流量数据
    $LED_dbobj = new OperaRiderTable($db, $LEDdata, $device_dbobj->getDeviceId());
    $LED_dbobj->autocommit();
    // echo "config:".$config;
    // echo "led data: ".$LEDdata->date;

    echo var_dump($device_dbobj->getDeviceId());
}
    echo var_dump($LED_dbobj);
// echo var_dump($patharray);





?>
