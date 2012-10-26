<?php
namespace Lib\File_Uploads
{
    use Config\Constants\File_Types as FileTypes;
    use Lib\Error\Exception_Handler as ExHandler;
    use Lib\File_Uploads\File_Storage as File;
    /**
     * Implements abstract functions for the MysqlStorage() template method in base class
     */
    class DB_Storage_Vue extends DB_Storage_Base
    {
        private $date = 'date', $time = 'time', $frequency = 'frequency_codespace',
                $transId = 'transmitter_id', $sensorVal = 'sensor_value', $sensorUn = 'sensor_unit',
                $receiversId = 'receivers_id';
        
        public function PDODelete()
        {
            parent::PDOStorage(FileTypes::Vue, false);
        }
        
        public static function MysqlStorage($filepath)
        {
            $db = \Config\Database\Mysqli_Connection::GetMysqliInstance();
            $insert = $insertInitial = "INSERT IGNORE INTO vue ( date, time, frequency_codespace, transmitter_id,
                                                                sensor_value, sensor_unit, receivers_id ) VALUES ";
            
            $data = array();
            $lineNum = 0;
            $affectedRows = 0;
            $timestart = time();
            if (($handle = fopen($filepath, 'r')) !== false) 
            {
                while (($data = fgetcsv($handle, 1000, ",")) !== false) 
                {
                    if ($lineNum == 0)
                    {
                        if (!isset($data[1]) || !isset($data[2]) || !isset($data[5]) || !isset($data[6]) || 
                            $data[1] != "Receiver" || $data[2] != "Transmitter" || 
                            $data[5] != "Sensor Value" || $data[6] != "Sensor Unit")
                        {
//                            File::Delete($filepath);
                            ExHandler::Error500("<p>Incorrect vue file format</p>");
                        }
                    }
                    elseif ($lineNum > 0)
                    {
                        $date = $db->real_escape_string(date("Y-m-d", strtotime($data[0])));
                        $time = $db->real_escape_string(date("H:i:s", strtotime($data[0])));
                        $receiver = $db->real_escape_string($data[1]);
                        $sensorValue = $db->real_escape_string($data[5]);
                        if (empty($sensorValue))
                            $sensorValue = "null";
                        $sensor = $db->real_escape_string($data[6]);
                        $sensorUnit = (!empty($sensor)) ? "'$sensor'" : "null";
                        $splitTransmitter = explode("-", $data[2]);
                        $codespace = $db->real_escape_string($splitTransmitter[0] . "-" . $splitTransmitter[1]);
                        $transmitterId = $db->real_escape_string($splitTransmitter[2]);

                        $insert .= "('$date', '$time', '$codespace', '$transmitterId', 
                        $sensorValue, $sensorUnit, '$receiver'),";

                        if ($lineNum % 5000 == 0)
                        {
                            $insert = substr($insert, 0, -1);
                            if ($db->query($insert))
                                $affectedRows += $db->affected_rows;
                            else
                                ExHandler::Error404("Upload failed: Receiver(s) may have to be manually
                                    entered before uploading this file.<br />" . $db->error . "<br />");
                            $insert = $insertInitial;
                        }
                    }
                    $lineNum++;
                }
                 // Inserts the rest of the data
                if (strlen($insert) > strlen($insertInitial))
                {
                    $insert = substr($insert, 0, -1);
                    if ($db->query($insert))
                        $affectedRows += $db->affected_rows;
                    else
                        ExHandler::Error404("Upload failed: Receiver(s) may have to be manually
                            entered before uploading this file.<br />" . $db->error . "<br />");
                }
            }
            $timeend = time();
            return $affectedRows;
            //echo "MYSQL<br />affected: $affectedRows, line: $lineNum, time:".date("i:s", $timeend - $timestart);
        }
        
        public static function MysqlDelete($filepath)
        {
            $db = \Config\Database\Mysqli_Connection::GetMysqliInstance();
            $delete = $deleteInitial = "DELETE FROM vue WHERE ";
            
            $data = array();
            $lineNum = 0;
            $affectedRows = 0;
            if (($handle = fopen($filepath, 'r')) !== false) 
            {
                while (($data = fgetcsv($handle, 1000, ",")) !== false) 
                {
                    if ($lineNum == 0)
                    {
                        if (!isset($data[1]) || !isset($data[2]) || !isset($data[5]) || !isset($data[6]) || 
                            $data[1] != "Receiver" || $data[2] != "Transmitter" || 
                            $data[5] != "Sensor Value" || $data[6] != "Sensor Unit")
                        {
//                            File::Delete($filepath);
                            ExHandler::Error500("<p>Incorrect vue file format</p>");
                        }
                    }
                    elseif ($lineNum > 0)
                    {
                        $date = $db->real_escape_string(date("Y-m-d", strtotime($data[0])));
                        $time = $db->real_escape_string(date("H:i:s", strtotime($data[0])));
                        $receiver = $db->real_escape_string($data[1]);
                        $sensorValue = $db->real_escape_string($data[5]);
                        if (empty($sensorValue))
                            $sensorValue = "sensor_value is null";
                        else
                            $sensorValue = "sensor_value = $sensorValue";
                        $sensor = $db->real_escape_string($data[6]);
                        $sensorUnit = (!empty($sensor)) ? "sensor_unit = '$sensor'" : "sensor_unit is null";
                        $splitTransmitter = explode("-", $data[2]);
                        $codespace = $db->real_escape_string($splitTransmitter[0] . "-" . $splitTransmitter[1]);
                        $transmitterId = $db->real_escape_string($splitTransmitter[2]);

                        $delete .= "(date = '$date' and time = '$time' and frequency_codespace = '$codespace' and transmitter_id = '$transmitterId' and 
                        $sensorValue and $sensorUnit and receivers_id = '$receiver') OR ";

                        if ($lineNum % 3000 == 0)
                        {
                            $delete = substr($delete, 0, -4);
                            if ($db->query($delete))
                                $affectedRows += $db->affected_rows;
                            else
                                ExHandler::Error404("Delete failed: " . $db->error . "<br />");
                            $delete = $deleteInitial;
                        }
                    }
                    $lineNum++;
                }
                 // Inserts the rest of the data
                if (strlen($delete) > strlen($deleteInitial))
                {
                    $delete = substr($delete, 0, -4);
                    if ($db->query($delete))
                        $affectedRows += $db->affected_rows;
                    else
                        ExHandler::Error404("Delete failed: " . $db->error . "<br />");
                }
            }
            return $affectedRows;
        }
        
        protected final function PrepareInsert()
        {
            return "INSERT IGNORE INTO vue 
                ( $this->date, $this->time, $this->frequency, $this->transId, $this->sensorVal,
                    $this->sensorUn, $this->receiversId ) 
                VALUES ( :$this->date, :$this->time, :$this->frequency, :$this->transId, :$this->sensorVal, 
                    :$this->sensorUn, :$this->receiversId)";
        }
        
        protected final function PrepareDelete()
        {
            return "DELETE FROM `vue` WHERE 
                $this->date = :$this->date AND $this->time = :$this->time AND $this->frequency = :$this->frequency AND 
                $this->transId = :$this->transId AND $this->sensorVal = :$this->sensorVal AND 
                $this->sensorUn = :$this->sensorUn AND $this->receiversId = :$this->receiversId";
        }
        
        /**
         * Checks the format of the first line of the csv file
         *
         * @param array $data first line of csv file containing table headers
         */
        protected final function CheckFormat(&$data)
        {
            if ($data[1] != "Receiver" && $data[2] != "Transmitter") 
                ExHandler::Error500("Vue files must contain 'Receiver' and 'Transmitter' in cells B:1 and C:1 respectively.");
        }
        
        protected function CheckSecondFormat(&$data)
        {
            return false;
        }
        
        /**
         * @param PDOStatement $pdoStatement value returned by PDO::prepare()
         * @param array $data single line of data from the csv file excluding headers
         * 
         * $data mapping:  0 => date, 1 => receiver, 2 => transmitter, 5 => sensor value, 6 => sensor units
         */
        protected final function Bind(&$pdoStatement, &$data)
        {
            $date = date("Y-m-d", strtotime($data[0]));
            $time = date("H:i:s", strtotime($data[0]));
            
            $splitTransmitter = explode("-", $data[2]);
            $codespace = $splitTransmitter[0] . "-" . $splitTransmitter[1];
            $transmitterId = $splitTransmitter[2];
            
            $pdoStatement->bindValue(":$this->date", $date, \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->time", $time, \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->frequency", $codespace, \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->transId", $transmitterId, \PDO::PARAM_INT);
            if (empty($data[5]))
                $pdoStatement->bindValue(":$this->sensorVal", null, \PDO::PARAM_NULL);
            else
                $pdoStatement->bindValue(":$this->sensorVal", $data[5], \PDO::PARAM_STR);
            if (empty($data[6]))
                $pdoStatement->bindValue(":$this->sensorUn", null, \PDO::PARAM_NULL);
            else
                $pdoStatement->bindValue(":$this->sensorUn", $data[6], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->receiversId", $data[1], \PDO::PARAM_STR);
        }
        
        /**
         * @param bool $bind
         * @return bool metadata will not start binding on third row, but on second 
         */
        protected final function BindOnThirdRow($bind = true)
        {
            return false;
        }
    }

}
?>
