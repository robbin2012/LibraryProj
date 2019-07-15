<?php
    /* 读取环境配置文件和LED文件的接口文件 */
    // $folderpath = "江门市/";
    // $datapath = "";
    // $configpath = $datapath.$foladerpath.'';
    // $filepath = $folderpath.$datapath;
    // echo file_exists($filepath);
    // $file = fopen($filepath, "r");
    // $content = fgets($file);
    // // fcloes($file);
    // echo $content;
    include "loadfile.php";

    interface Readdatafrom_file{
        // 接口 打开文件并获取数据
        public function openfile($filepath);
        public function closefile();
        public function getdata();
    }

    abstract class Readdata implements Readdatafrom_file{
        // 通过继承可以根据不同需求读取文件数据
        protected $file;
        protected $filename;
        public function __construct($filepath)
        {   
            $this->filename = $filepath;
            $this->file = $this->openfile($filepath);
        }
        public function openfile($filepath){
            if (is_file($filepath)){
                $file = fopen($filepath, "r");
                return $file;
            }
            return null;
        }
        public function closefile(){
            if($this->file){
                fclose($this->file);
                return TRUE;
            }
            return NULL;
        }
        abstract public function getdata();
    }

    class Readconfig extends Readdata{
        protected $file;
        // 打开config文件获取需要 更新的数据文件
        public function getdata(){
            $data = fgets($this->file);
            $this->closefile();
            return $data;
        }
    }


    // $configpath = "江门市/config.txt";
    // $config = new Readconfig($configpath);
    // $contend = $config->getdata();
    // echo $contend;
    // echo iconv("gb2312", "utf-8",$contend);

    class ReadLED extends  Readdata{
        // 打开文件获取数据
        protected $file;
        public function getdata(){
            $this->closefile();
            // 通过一个对象来过滤数据
            $data_obj = new ReadLEDdata($this->filename);
            return $data_obj;
            echo var_dump($data_obj);
        }
    }
    // $LEDpath = "江门市/iCounter无线客流量统计系统/Log/LED.txt";
    // $file = is_file($LEDd);
    // // echo $is_file;
    // $LEDdata = (new ReadLED($LEDpath))->getdata();
    // echo $LEDdata->date;
    // class ReadLED extends Read {
    //     protected $file;
    //     public function getdata($file){
    //         $data = fgets($file);
    //         return $data;
    //     }
    // }

    interface sortdata {
        public function getconfig();
        public function getLED();
    }

    class DataFactory implements sortdata{
        // 通过文件路径生产数据的工厂 只负责数据的生产 不负责路径的形成
        private $complete_configpath;
        private $complete_LEDpath;
        public function __construct($configpath=null, $LEDpath=null){
            $this->complete_configpath = $configpath;
            $this->complete_LEDpath = $LEDpath;
        }
        public function getconfig(){
            if ($this->complete_configpath){
                $config = (new Readconfig($this->complete_configpath))->getdata();
                $config = iconv("gb2312", "utf-8", $config);
                return $config;
            }
            return null;
        }

        public function getLED(){
            if ($this->complete_LEDpath){
                $LEDdata = (new ReadLED($this->complete_LEDpath))->getdata();
                return $LEDdata;
            }
            return null;
        }
    }
?>