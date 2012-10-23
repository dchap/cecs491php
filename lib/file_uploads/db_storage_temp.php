<?php
namespace Lib\File_Uploads
{
    use Config\Constants\File_Types as FileTypes;
    use Lib\Error\Exception_Handler as ExceptionHandler;
    /**
     * Implements abstract functions for the MysqlStorage() template method in base class
     */
    class DB_Storage_Temp extends DB_Storage_Base
    {
        private $station = 'stations_name', $date = 'date', $time = 'time', $temperature = 'temperature', 
                $intensity = 'intensity', $battery = 'battery_volt';
        private $stationVal, $bind = true;
        
        public function PDODelete()
        {
            parent::PDOStorage(FileTypes::Temperature, false);
        }
                
        protected final function PrepareInsert()
        {
            return "INSERT IGNORE INTO temperatures 
                ( $this->station, $this->date, $this->time, $this->temperature, 
                    $this->intensity, $this->battery )
                VALUES ( :$this->station, :$this->date, :$this->time, :$this->temperature, 
                    :$this->intensity, :$this->battery )";
        }
        
        protected final function PrepareDelete()
        {
	        return "DELETE FROM `temperatures` WHERE
                    $this->station = :$this->station AND $this->date = :$this->date AND $this->time = :$this->time 
                    AND $this->temperature = :$this->temperature AND $this->intensity = :$this->intensity 
                    AND $this->battery = :$this->battery";
        }
        
        /**
         * @param array $data first line of csv containing table headers
         */
        protected final function CheckFormat(&$data)
        {
            if (!isset($data[0])) 
                ExceptionHandler::Error500("Station name must be in cell A:1 and be the last word");
            $this->stationVal = array_pop(explode(" ", $data[0]));
        }
        
        protected function CheckSecondFormat(&$data)
        {
            if (!isset($data[1]) || !isset($data[2]) || !isset($data[3]) || !isset($data[4]) 
                || strpos($data[1], "Date Time") === false || strpos($data[2], "Temp") === false
                || strpos($data[3], "Intensity") === false || strpos($data[4], "Batt") === false)
                ExceptionHandler::Error404 ("<p>Incorrect temperature file format</p>");
        }
        
        /**
         * @param PDOStatement $pdoStatement value returned by PDO::prepare()
         * @param array $data single line of data from the csv file excluding headers
         * 
         * $data mapping:  1 => date, 2 => temperature, 3 => intensity, 4 => battery_volt
         */
        protected final function Bind(&$pdoStatement, &$data)
        {
            $date = date("Y-m-d", strtotime($data[1]));
            $time = date("H:i:s", strtotime($data[1]));
            $celciusTemp = (5/9) * ((float)$data[2] - 32);
            $pdoStatement->bindValue(":$this->station", $this->stationVal, \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->date", $date, \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->time", $time, \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->temperature", $celciusTemp, \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->intensity", $data[3], \PDO::PARAM_INT);
            $pdoStatement->bindValue(":$this->battery", $data[4], \PDO::PARAM_STR);
        }
        
        /**
         * @param bool $bind
         * @return bool will skip the second row
         */
        protected final function BindOnThirdRow($bind = true)
        {
            if (!$bind)
                $this->bind = false;
            return $this->bind;
        }
    }
}
?>
