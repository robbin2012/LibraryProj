<?php
/* 过滤LED文件 取出其中重要的数据 */
class ReadLEDdata{
    // 获取文件中的数据 过滤数据
    public $date;
    public $updateTime;
    public $Fin;
    public $Fout;
    protected $data;

    protected function getdata($filename){
        if(is_file($filename)){
            // 打开文件获取数据
            $file = fopen($filename, 'r');
            $content  = '';
            while(!feof($file))
            {

                $content = $content  . fgets($file);  
            }
            fclose($file);
            return $this->filter_data($content);
        }else{
            throw new Exception('filename Error.');
        }

    }

    protected function filter_data($content){
        // 过滤文件内容得到可用数据
        $pattern = '/([^\d])+/s';
        $data = preg_split($pattern, $content, -1, PREG_SPLIT_NO_EMPTY);
        return $data;
    }

    function __construct($filename)
    {
        $this->data = $this->getdata($filename);
        $this->date = $this->data[0].'.'.$this->data[1].'.'.$this->data[2];
        $this->updateTime = $this->date.' '.$this->data[3].':'.$this->data[4].':'.$this->data[5];
        echo var_dump($this->date);
        $this->Fin = $this->data[6];
        $this->Fout = $this->data[7];
    }
};
// $data = new readdata($config['filename']);

// echo $filepath . PHP_EOL;
// echo $config['filename'];
// 创建文件操作实例
// echo var_dump(is_file($filepath));
// echo var_dump(file_exists("./data/江门市/Log/LED.txt"));
// $file = fopen("du.txt", 'r');
// $file_content = new readdata($filepath);
// echo $file->date;
// echo $file_content->date;
?>