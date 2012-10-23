<?php
namespace Lib\File_Uploads
{
    use Lib\Error\Exception_Handler as ExceptionHandler;
    use Config\Constants\File_Types as Type;
    use Config\Database\Pdo_Connection as PdoConnect;
    
    /**
     * Base class for database storage of data contained in different types of csv files
     * 
     * todo: add real count of affected rows
     */
    abstract class DB_Storage_Base
    {
        private $_uploadFilePath, $_filename;
                
        /**
         *
         * @param string $uploadPath absolute path to the file on the server
         * @param string $fileType original filename
         */
        public function __construct($uploadPath, $fileType)
        {
            $this->_uploadFilePath = $uploadPath;
            $this->_filename = $fileType;
        }
        
        /**
         * Template method for all types of csv uploads containing interchangeable 
         * sub-methods for specific file s.
         * 
         * @return array if inserting > [filename, entries added]
         */
        public function PDOStorage($fileType, $insert = true)
        {
            try
            {
                $db = PdoConnect::GetPDOInstance();
            }
            catch (\PDOException $e)
            {
                ExceptionHandler::Error500("<p>Connection failed: " . $e->getMessage() . "</p>");
            }
            if (!file_exists($this->_uploadFilePath))
                ExceptionHandler::Error500 ("<p>Could not open file for processing.</p>");

            $data = array();
            $isTableHeader = true;
            $lineNum = 0;
            $timestart = time();
            if (($handle = fopen($this->_uploadFilePath, 'r')) !== false) 
            {
            	if ($insert)
                	$sql = $this->PrepareInsert();
            	else
            		$sql = $this->PrepareDelete();
                try
                {
                    $statement = $db->prepare($sql);
                    $db->beginTransaction();
                    while (($data = fgetcsv($handle, 1000, ",")) !== false)
                    {
                        if ($isTableHeader)
                        {
                            $this->CheckFormat($data);
                            $isTableHeader = false;
                        }
                        elseif ($this->BindOnThirdRow())
                        {
                            $this->BindOnThirdRow(false);
                            $this->CheckSecondFormat($data);
                        }
                        else 
                        {
                            $this->Bind($statement, $data);
                            $statement->execute();
                            $lineNum++;
                        }
                    }
                    $db->commit();
                }
                catch (\PDOException $e)
                {
                    $db->rollBack();
                    PdoConnect::Disconnect();
                    if ($insert)
                    {
                        if ($fileType == Type::Metadata || $fileType == Type::Vue)
                            ExceptionHandler::Error404("<p>Upload failed: Receiver(s) may have to be 
                                entered before uploading this file.</p>" . $e->getMessage() . "<br />");
                        elseif ($fileType == Type::Sonde || $fileType == Type::Temperature)
                            ExceptionHandler::Error404("<p>Upload failed: Check if the station name has been 
                                entered before uploading this file.</p>" . $e->getMessage() . "<br />");
                    }
                    else
                        ExceptionHandler::Error404($e->getMessage());
                }
            }

            fclose($handle);
            PdoConnect::Disconnect();
            $timeend = time();
            if ($insert)
                return $lineNum;
        }
        
        /**
         * Checks if data begins on third row
         */
        abstract protected function BindOnThirdRow($bool = true);
        
        /**
         * @return insert statement with parameter markers to be used in Bind()
         */
        abstract protected function PrepareInsert();
        
        /**
         * @return delete statement with parameter markers to be used in Bind()
         */
        abstract protected function PrepareDelete();
        
        /**
         * Checks cell content to make sure the file is the correct type
         * 
         * @param array $data first line of the csv file
         */
        abstract protected function CheckFormat(&$data);
        
        abstract protected function CheckSecondFormat(&$data);

        /**
         * @param PDOStatement $pdoStatement value returned by PDO::prepare()
         * @param array $data single line of data from the csv file excluding headers
         */
        abstract protected function Bind(&$pdoStatement, &$data);
    }
}
?>
