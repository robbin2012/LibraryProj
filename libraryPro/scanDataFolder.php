<?php

function getpath($datadir, $foldir){
    // 返回一个路径数组 第一项存放config文件的路径 第二项存放config文件的路径 
    $configfilename = "config.txt";
    $LEDfilename = "LED.txt";
    $config_completepath = $datadir."/".$foldir."/".$configfilename;
    $LED_conpletepath = $datadir."/".$foldir."/"."iCounter无线客流量统计系统/Log/".$LEDfilename;
    $pathArray = array($config_completepath, $LED_conpletepath);
    return $pathArray;
}
class DirFactory {
    // 工厂迭代器模式 可以通过这个工厂返回需要的路径
    public $pathArray_l;
    public $index; // 记录上次读取的位置
    // 处理所有文件夹列表的生成
    public function __construct($dir)
    {  
        // 获取文件夹的列表 
        $folddir_l = scandir($dir);
        $this->index = 0; // 初始化游标位置
        foreach($folddir_l as $foldir){
            if (! preg_match("/\.+/", $foldir)){
                // 排除带有. 和 .. 的隐藏目录
                $this->pathArray_l[] = getpath($dir, $foldir);
            }
        }
    }
    public function getpathArray(){
        // 每次读取一个路径数组 指针右移
        if ($this->index < count($this->pathArray_l)){
            $this->index++;
            return $this->pathArray_l[$this->index - 1];
        }
        return null;
    }
}
// $dir = "data"; // 所有的数据存放在dir/ 下 当前目录下的data文件夹

// $pathArray_l = new DirFactory($dir);
// echo var_dump($pathArray_l);
// $patharray = $pathArray_l->getpathArray();
// echo var_dump($patharray);
// $patharray = $pathArray_l->getpathArray();
// echo var_dump($patharray);

    // $datapath = "data/";

    // $result = scandir($datapayth);
    // echo var_dump($result);
    //获取当前文件所在的绝对目录
// $dir =  dirname(__FILE__);
// $dir = "./data";
// // echo $dir;
// //扫描文件夹
// $file = scandir($dir);
// //显示
// echo " <pre>";
// // $str = substr($file[0], 0, 1);
// $str = 'fssf';
// $bool = preg_match("/\.+/", $str);
// echo $str;
// echo var_dump($bool);
// $folddir = "江门市";
// $basepath = $dir.$folddir;
?>