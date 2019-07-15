<?php

// 初始化一个连接
try{
    $db = new PDO('mysql:host=localhost;dbname=library', 'root', 'xiaoan@801yetao');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
  }catch(PDOException $e){
    echo "连接失败: " .$e->getMessage();
  }
?>
