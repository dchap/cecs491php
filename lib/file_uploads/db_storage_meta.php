<?php
namespace Lib\File_Uploads
{
    use Config\Constants\File_Types as FileTypes;
    /**
     * Implements abstract functions for the MysqlStorage() template method in base class
     */
    class DB_Storage_Meta extends DB_Storage_Base
    {
        private $date = 'date', $time = 'time', $receiversId = 'receivers_id', 
                $description = 'description', $data = 'data', $units = 'units';
        
        public function PDODelete()
        {
            parent::PDOStorage(FileTypes::Metadata, false);
        }
        
        protected final function PrepareInsert()
        {
            return "INSERT IGNORE INTO metadata 
                ( $this->date, $this->time, $this->receiversId, $this->description, $this->data, $this->units ) 
                VALUES ( :$this->date, :$this->time, :$this->receiversId, :$this->description, :$this->data, :$this->units )";
        }
        
        protected final function PrepareDelete()
        {
            return "DELETE FROM metadata WHERE
                $this->date = :$this->date AND $this->time = :$this->time AND $this->receiversId = :$this->receiversId 
                AND $this->description = :$this->description AND $this->data = :$this->data AND $this->units = :$this->units";
        }
        
        /**
         * Checks the format of the first line of the csv file
         *
         * @param array $data first line of csv file containing table headers
         */
        protected final function CheckFormat(&$data)
        {
            if (!isset($data[0]) || !isset($data[1]) || !isset($data[2]) || !isset($data[3]) || !isset($data[4]) 
                || $data[0] != "Date/Time" || $data[1] != "Receiver" || $data[2] != "Description"
                || $data[3] != "Data" || $data[4] != "Units") 
                \Lib\Error\Exception_Handler::Error500("<p>Incorrect metadata format</p>");
        }
        
        protected function CheckSecondFormat(&$data)
        {
            return false;
        }
        
        /**
         * @param PDOStatement $pdoStatement value returned by PDO::prepare()
         * @param array $data single line of data from the csv file excluding headers
         * 
         * $data mapping:  0 => date, 1 => receivers_id, 2 => description, 3 => data, 4 => units
         */
        protected final function Bind(&$pdoStatement, &$data)
        {
            $date = date("Y-m-d", strtotime($data[0]));
            $time = date("H:i:s", strtotime($data[0]));
            $pdoStatement->bindValue(":$this->date", $date, \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->time", $time, \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->receiversId", $data[1], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->description", $data[2], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->data", $data[3], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->units", $data[4], \PDO::PARAM_STR);
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
