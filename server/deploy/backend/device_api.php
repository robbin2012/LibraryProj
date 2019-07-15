<?php
// 更新当天数据api

include "pdoConfig.php";
$device_id = $_GET["device_id"];
// $device_id = "3";
// $device_id = 1;
$nowdatetime = date("Y-m-d");
$sql = "SELECT devices.device_id, devices.deviceName, ridership.date, ridership.Fin, ridership.Fout, ridership.updateTime FROM ridership, devices WHERE ridership.deviceID = devices.device_id AND devices.device_id = (:device_id) AND ridership.date = (:nowdatetime)";
$selectpdo = $db->prepare($sql);
$selectpdo->bindParam(":device_id", $device_id);
$selectpdo->bindParam(":nowdatetime", $nowdatetime);
$selectpdo->execute();
// echo var_dump($selectpdo->fetchall(PDO::FETCH_ASSOC));

header('Content-Type:application/json; charset=utf-8');

$data = $selectpdo->fetch(PDO::FETCH_ASSOC);

exit(json_encode($data));  // 输出这条语句并退出脚本
?>