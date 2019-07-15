<?php

include "pdoConfig.php";
// $device_id = 1;
$nowdatetime = date("Y-m-d");

$sql = "SELECT devices.device_id, devices.deviceName, ridership.date, ridership.Fin, ridership.Fout, ridership.updateTime FROM ridership, devices WHERE ridership.deviceID = devices.device_id AND ridership.date = (:nowdatetime)";

$selectpdo = $db->prepare($sql);
$selectpdo->bindParam(":nowdatetime", $nowdatetime);
$selectpdo->execute();
// echo var_dump($selectpdo->fetchall(PDO::FETCH_ASSOC));

header('Content-Type:application/json; charset=utf-8');

$data = $selectpdo->fetchall(PDO::FETCH_ASSOC);

exit(json_encode($data));  // 输出这条语句并退出脚本
?>