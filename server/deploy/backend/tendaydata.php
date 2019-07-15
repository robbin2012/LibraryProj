<?php
// 获取前十天的进入人数和出去人数数据

include "pdoConfig.php";

$device_id = $_GET["device_id"];
// $device_id = "3";
$datetime10ago = date("Y-m-d", strtotime("-10 day"));
$nowdatetime = date("Y-m-d");

$sql = "SELECT devices.device_id, devices.deviceName, ridership.date, ridership.Fin, ridership.Fout, ridership.updateTime FROM ridership, devices WHERE ridership.deviceID = devices.device_id AND devices.device_id = (:device_id) AND ridership.date >= (:datetime10ago) AND ridership.date <= (:nowdatetime) ORDER BY ridership.date DESC";
$selectpdo = $db->prepare($sql);
$selectpdo->bindParam(":device_id", $device_id);
$selectpdo->bindParam(":datetime10ago", $datetime10ago);
$selectpdo->bindParam(":nowdatetime", $nowdatetime);
$selectpdo->execute();



$data = $selectpdo->fetchall(PDO::FETCH_ASSOC);
foreach($data as $item){
    $Fin[] = $item["Fin"];
    $Fout[] =  $item["Fout"];
}
$Fin_out = array("Fin"=>$Fin, "Fout"=>$Fout);

header('Content-Type:application/json; charset=utf-8');
exit(json_encode($Fin_out));  // 输出这条语句并退出脚本

?>