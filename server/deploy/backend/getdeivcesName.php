<?php

include "pdoConfig.php";

// $sql = "SELECT devices.deviceName, ridership.date, ridership.Fin, ridership.Fout, ridership.updateTime FROM ridership, devices WHERE ridership.deviceID = devices.device_id";
$sql = "SELECT devices.deviceName, devices.device_id FROM devices";
$pdostate = $db->query($sql);


// foreach ($pdostate as $row){
//     echo $row["deviceName"];
// } // 使用这种之后 这条查询语句pdostate 里面指向对象的指针就会向后移动 
// echo var_dump($pdostate->fetchall(PDO::FETCH_ASSOC)); foreach之后 输出为一个空数字 是因为查询索引移动了 需要构造新的pdostate才能继续使用

// echo var_dump($pdostate->fetchall(PDO::FETCH_ASSOC));

header('Content-Type:application/json; charset=utf-8');

$data = $pdostate->fetchall(PDO::FETCH_ASSOC);

exit(json_encode($data));  // 输出这条语句并退出脚本
?>