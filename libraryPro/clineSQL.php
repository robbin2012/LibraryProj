<?php
/* 数据库相关操作文件 */
$logfilename = 'log.txt';
/* 
数据表
create table ridership 
(
    id INT(5) unsigned AUTO_INCREMENT, #id
    date DATE NOT NULL, # 日期 唯一 非空
    Fin INT NOT NULL, # 上个更新时间到这次更新的进入人数 非空
    Fout INT NOT NULL, # 上个更新时间到这次更新的出去人数 非空
    updateTime TIMESTAMP DEFAULT NOW(), # 此条数据更新的时间 默认当前
    deviceID INT(5) unsigned NOT NULL,
    PRIMARY KEY(id), # 主键约束
    CONSTRAINT data_device FOREIGN KEY(deviceID) REFERENCES devices(device_id) #外键约束

);
create table devices
(
    device_id INT(5) unsigned AUTO_INCREMENT, # 设备ID
    deviceName VARCHAR(30) UNIQUE, # 名称 可以是地区说明
    folderID  CHAR(30), # 远程目录文件夹ID
    folderName CHAR(100), # 文件夹名称 建议每项不要有重名 最好和设备名称相同
    PRIMARY KEY(device_id) # 主键约束
);
*/
function recordlog($msg){
    global $logfilename;
    $logfile = fopen($logfilename, 'a+');
    fwrite($logfile, $msg."\n");
    fclose($logfile);
} 
    // 操作数据库抽象类
    abstract class Operadb{
        protected $select;
        protected $insert;
        protected $update;
        public $id;
        abstract protected function select_state($db);
        abstract protected function insert_state($db);
        abstract protected function update_state($db);
        public function __construct($db)
        {
            $this->select = $this->select_state($db);
            $this->insert = $this->insert_state($db);
            $this->update = $this->update_state($db);
        }

        public function select_if_data()
        {
            // 判断是否需要更新数据 并构造查询集  
            $this->select->execute();
            $this->query = $this->select->fetchall(PDO::FETCH_NUM);
            $result = $this->query;
            if ($result){
                // 更新操作
                return TRUE;
            }else{
                // 插入操作
                return FALSE;
            }
        }
        public function updateOpera()
        {
            // 更新操作
            $last = end($this->query);
            // 最后一条数据 第一个字段 id 主键
            $this->id = $last[0];
            echo $this->id;
            $msg = $this->update->execute();
            echo '更新：'.var_dump($msg);
        }
        public function insertOpera()
        {
            // 插入操作
            $msg = $this->insert->execute();
            echo '插入:'.var_dump($msg);
        }
        public function autocommit()
        {
            // 自动化执行方法 已经有就更新数据 没有就插入新数据
            if ($this->select_if_data()){
                try{
                    $this->updateOpera();
                    echo 'up'.__CLASS__.PHP_EOL;
                }catch(Exception $e){
                    $msg = $e->getMessage();
                    recordlog($msg);
                }
            }else{
                try{
                    $this->insertOpera();
                    echo 'insert'.__CLASS__.PHP_EOL;
                }catch(Exception $e){
                    $msg = $e->getMessage();
                    recordlog($msg);
                }
            }
        }
    }

    // Rider表操作接口
    interface RiderTable{
        // 查询今天该设备是否存在数据 存在的话就覆写 不存在的话就插入
        public function select_if_data();
        // 覆写旧数据操作
        public function updateOpera();
        // 插入新数据操作
        public function insertOpera();
    }


    // ridership数据表操作类 行为类
    class OperaRiderTable extends Operadb implements RiderTable
    {
        /*
        自动化方法 autocommit()
        多个行为方法
        */
        public $date;
        public $Fin;
        public $Fout;
        public $deviceID;
        public $updateTime;
        public $id;
        public $query; # 查询结果集

        public function __construct($db, $file_content, $deviceID)
        {
            $this->date = $file_content->date;
            $this->Fin = $file_content->Fin;
            $this->Fout = $file_content->Fout;
            $this->updateTime = $file_content->updateTime;
            $this->deviceID = $deviceID;
            // echo var_dump($this->deviceID);
            // 三个主要操作语句被构造
            parent::__construct($db);
            // $this->select = $this->select_state($db);
            // $this->insert = $this->insert_state($db);
            // $this->update = $this->update_state($db);
        }

        protected function select_state($db)
        {
            // 构造查询预处理语句 查询数据id
            $select = $db->prepare("SELECT id FROM ridership WHERE date = ? AND deviceID= ?");
            $select->bindParam(1, $this->date);
            $select->bindParam(2, $this->deviceID);
            return $select;
        }

        protected function insert_state($db)
        {
            // 构造插入预处理语句
            $insert = $db->prepare("INSERT INTO ridership(date, Fin, Fout, updateTime, deviceID) VALUES(:date, :Fin, :Fout, :updateTime, :deviceID)");
            $insert->bindParam(":date", $this->date);
            $insert->bindParam(':Fin', $this->Fin);
            $insert->bindParam(":Fout", $this->Fout);
            $insert->bindParam(':updateTime', $this->updateTime);
            $insert->bindParam(':deviceID', $this->deviceID);
            return $insert;
        }

        protected function update_state($db)
        {
            // 构造更新预处理语句
            $update = $db->prepare("UPDATE ridership SET Fin = ? , Fout = ?, updateTime = ? WHERE id = ? AND deviceID = ?");
            $update->bindParam(1, $this->Fin);
            $update->bindParam(2, $this->Fout);
            $update->bindParam(3, $this->updateTime);
            $update->bindParam(4, $this->id);
            $update->bindParam(5, $this->deviceID);
            return $update;
        }

    }

    // devices表接口 抽象化 
    interface DevicesTable{
        public function Register(); // 没有的话进行外键注册
        public function getDeviceId(); // 传给下一个表作为外键根据
        public function has_Register(); // 判断数据表是否有该地区该设备
    }

    class OperaDevicesTable implements DevicesTable {
        private $db;
        private $deviceID;
        private $deviceName;
        private $folderName;
        public function __construct($db, $deviceName, $folderName)
        {
            $this->db = $db;
            $this->deviceName = $deviceName;
            $this->folderName = $folderName;
        }
        protected function select(){
            // 查询操作
            $select = $this->db->prepare("SELECT device_id FROM devices WHERE deviceName = ? ");
            $select->bindParam(1, $this->deviceName);
            $select->execute();
            $query = $select->fetchall(PDO::FETCH_NUM);
            if ($query){
                $this->deviceID = end($query)[0];
                return TRUE;
            }
            return FALSE;
        }
        protected function insert(){
            // 插入操作
            $insert = $this->db->prepare("INSERT INTO devices(deviceName, folderName) VALUES(:deviceName, :folderName)");
            $insert->bindParam(':deviceName', $this->deviceName);
            $insert->bindParam(':folderName', $this->folderName);
            $insert->execute();
        }
        public function has_Register()
        {
            // 通过查询判断是否注册
            return $this->select();
        }
        public function Register(){
            try{
                $this->insert();
            }catch(Exception $e){
                $msg = $e->getMessage();
                recordlog($msg);
            }
        }

        public function autocommit(){
            if ($this->has_Register()){
                return TRUE;
            }else{
                $this->Register();
                return TRUE;
            }
        }
        public function getDeviceId(){
            return $this->deviceID;
        }
    }

    // class Autoupdate {
    //     public
    // }
    // // Rider表操作接口
    // interface DevicesTable{
    //     // 查询今天该设备是否存在数据 存在的话就覆写 不存在的话就插入
    //     public function select_if_data();
    //     // 覆写旧数据操作
    //     public function updateOpera();
    //     // 插入新数据操作
    //     public function insertOpera();
    // }
    // // devices 数据表实例
    // class OperaDevicesTable extends Operadb implements DevicesTable
    // {
    //     public $id;
    //     public $deviceName;
    //     public $folderID;
    //     public $folderName;

    //     public function __construct($db, $deviceName=null,$device_id=null,  $folderID=null, $folderName=null)
    //     {
    //         $this->id = $device_id;
    //         $this->deviceName = $deviceName;
    //         $this->folderID = $folderID;
    //         $this->folderName = $folderName;
    //         parent::__construct($db);
    //     }

    //     protected function select_state($db)
    //     {
    //         // 构造查询预处理语句 查询该机器文件夹是否已经注册
    //         $select = $db->prepare("SELECT device_id FROM devices WHERE ffolderName = ? ");
    //         $select->bindParam(1, $this->folderName);
    //         return $select;
    //     }

    //     protected function insert_state($db)
    //     {
    //         // 构造插入预处理语句
    //         $insert = $db->prepare("INSERT INTO devices(device_id, deviceName, folderID, folderName) VALUES(:device_id, :deviceName, :folderID, :folderName)");
    //         $insert->bindParam(":device_id", $this->id);
    //         $insert->bindParam(':deviceName', $this->deviceName);
    //         $insert->bindParam(':folderID', $this->folderID);
    //         $insert->bindParam(':folderName', $this->folderName);
    //         return $insert;
    //     }

    //     protected function update_state($db)
    //     {
    //         // 构造更新预处理语句
    //         $update = $db->prepare("UPDATE devices SET deviceName = ? , folderID = ?, folderName = ? WHERE device_id = ?");
    //         $update->bindParam(1, $this->deviceName);
    //         $update->bindParam(2, $this->folderID);
    //         $update->bindParam(3, $this->folderName);
    //         $update->bindParam(4, $this->id);
    //         return $update;
    //     }
        // public function updateOpera()
        // {
        //     // 更新操作
        //     $last = end($this->query);
        //     // 最后一条数据 第一个字段 主键
        //     $this->device_id = $last[0];
        //     // $msg = $this->update->execute();
        //     echo var_dump(self::$);
        //     // echo var_dump($this->device_id);
        //     // echo '更新：'.var_dump($msg);
        // }
    
?>
