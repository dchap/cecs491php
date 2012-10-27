<?php
namespace Lib\File_Uploads
{
    use Config\Constants\File_Types as FileTypes;
    use Lib\Error\Exception_Handler as ExceptionHandler;
    /**
     * Implements abstract functions for the MysqlStorage() template method in base class
     */
    class DB_Storage_Sonde extends DB_Storage_Base
    {
        private $station = 'stations_name', $date = 'date', $temperature = 'temperature', $sp_cond = 'sp_cond', 
                $tds = 'tds', $salinity = 'salinity', $do_percent = 'do_percent', $do_conc = 'do_conc',
                $do_charge = 'do_charge', $depth = 'depth', $ph = 'ph', $ph_mv = 'ph_mv', $par1 = 'par1',
                $chlorophyl = 'chlorophyl', $bp = 'bp';
        private $stationVal, $bind = true;
        
        public function PDODelete()
        {
            parent::PDOStorage(FileTypes::Sonde, false);
        }
                
        protected final function PrepareInsert()
        {
            return "INSERT IGNORE INTO sonde 
                ( $this->station, $this->date, $this->temperature, $this->sp_cond, $this->tds, $this->salinity,
                    $this->do_percent, $this->do_conc, $this->do_charge, $this->depth, $this->ph, $this->ph_mv,
                    $this->par1, $this->chlorophyl, $this->bp ) 
                VALUES ( :$this->station, :$this->date, :$this->temperature, :$this->sp_cond, :$this->tds, :$this->salinity,
                    :$this->do_percent, :$this->do_conc, :$this->do_charge, :$this->depth, :$this->ph, :$this->ph_mv,
                    :$this->par1, :$this->chlorophyl, :$this->bp )";
        }
                
        protected final function PrepareDelete()
        {
            return "DELETE FROM `sonde` WHERE 
                $this->station = :$this->station AND $this->date = :$this->date AND $this->temperature = :$this->temperature 
                AND $this->sp_cond = :$this->sp_cond AND $this->tds = :$this->tds AND $this->salinity = :$this->salinity 
                AND $this->do_percent = :$this->do_percent AND $this->do_conc = :$this->do_conc 
                AND $this->do_charge = :$this->do_charge AND $this->depth = :$this->depth AND $this->ph = :$this->ph 
                AND $this->ph_mv = :$this->ph_mv AND $this->par1 = :$this->par1 AND $this->chlorophyl = :$this->chlorophyl 
                AND $this->bp = :$this->bp";
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
            if (!isset($data[4]) || !isset($data[5]) || strpos($data[0], "Date") === false 
                || strpos($data[1], "Temp") === false || strpos($data[2], "SpCond") === false 
                || strpos($data[3], "TDS") === false || strpose($data[4], "Salinity") === false 
                || strpose($data[5], "DO") === false)
                ExceptionHandler::Error404 ("<p>Incorrect sonde file format</p>");
        }
        
        /**
         * @param PDOStatement $pdoStatement value returned by PDO::prepare()
         * @param array $data single line of data from the csv file excluding headers
         * 
         * $data mapping:  0 => date, 1 => temperatre, 2 => sp_cond, 3 => tds, 4 => salinity,
         *                  5 => do_percent, 6 => do_conc, 7 => do_charge, skip 8, 9 => depth, 10 => ph,
         *                  11 => ph_mv, 12 => par1, 13 => chlorophyl, 14 => bp
         */
        protected final function Bind(&$pdoStatement, &$data)
        {
            $date = date("Y-m-d", strtotime($data[0]));
            $pdoStatement->bindValue(":$this->station", $this->stationVal, \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->date", $date, \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->temperature", $data[1], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->sp_cond", $data[2], \PDO::PARAM_INT);
            $pdoStatement->bindValue(":$this->tds", $data[3], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->salinity", $data[4], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->do_percent", $data[5], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->do_conc", $data[6], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->do_charge", $data[7], \PDO::PARAM_INT);
            $pdoStatement->bindValue(":$this->depth", $data[9], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->ph", $data[10], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->ph_mv", $data[11], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->par1", $data[12], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->chlorophyl", $data[13], \PDO::PARAM_STR);
            $pdoStatement->bindValue(":$this->bp", $data[14], \PDO::PARAM_STR);
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
