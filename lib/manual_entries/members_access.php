<?php
namespace Lib\Manual_Entries
{
    use Lib\Error\Exception_Handler as ExceptionHandler;
    use Config\Database\Pdo_Connection as PdoConnect;
    
    /**
     * Description of Members_Access
     */
    class Members_Access extends Data_Access
    {
        public function __construct(array $data)
        {
            parent::__construct($data);
            foreach (array_keys($data) as $field)
                $this->$field = $field;
        }
        
        protected $data = array(
            "id" => "",
            "username" => "",
            "password" => "",
            "confirm_password" => "",
            "fname" => "",
            "lname" => "",
            "account_type" => ""
        );
        
        public  $id, $username, $password, $confirm_password, $fname, $lname, $account_type;

        /**
         *
         * @param Members_Access $record to be inserted
         * @return Members_Access the record inserted
         */
        public static function Insert(Members_Access $record)
        {
            self::Validate($record);
            if (!self::UsernameNotTaken($record->data[$record->username]))
                ExceptionHandler::Error404 ("Username already exists");
            
            $sql = "INSERT INTO members 
                ( $record->username, $record->password, $record->fname, $record->lname, $record->account_type ) 
                VALUES 
                ( :$record->username, PASSWORD(:$record->password), :$record->fname, :$record->lname, :$record->account_type )";
            
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare($sql);
                self::Bind($st, $record);
                $st->bindValue(":$record->password", $record->data[$record->password], \PDO::PARAM_STR);
                $st->execute();
                $lastInsertId = $conn->lastInsertId();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Insert failed: " . $e->getMessage());
            }
            
            return self::GetMemberById($lastInsertId);
        }
        
        /**
         *
         * @param Members_Access $record to be updated
         * @return Members_Access the record updated
         */
        public static function Update(Members_Access $record)
        {
            $resetPassword = false;
            
            self::Validate($record);
            $sql = "UPDATE members 
                SET $record->username = :$record->username, $record->fname = :$record->fname,
                $record->lname = :$record->lname, $record->account_type = :$record->account_type
                WHERE id = :id";
            if (trim($record->data[$record->password]) != '')
                $resetPassword = true;
            
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                if ($resetPassword)
                {
                    $changePassword = "UPDATE members SET $record->password = PASSWORD(:$record->password)
                            WHERE id = :id";
                    $st = $conn->prepare($changePassword);
                    $st->bindValue(":id", $record->data[$record->id], \PDO::PARAM_INT);
                    $st->bindValue(":password", $record->data[$record->password], \PDO::PARAM_STR);
                    $st->execute();
                }
                
                $st = $conn->prepare($sql);
                self::Bind($st, $record);
                $st->bindValue(":id", $record->data['id'], \PDO::PARAM_INT);
                $st->execute();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Update failed: " . $e->getMessage());
            }    
            
            return self::GetMemberById($record->data['id']);
        }
        
        /**
         *
         * @param int $id id (primary key) of the row to be removed
         */
        public static function Delete($id)
        {
            $sql = "DELETE FROM members WHERE id = :id LIMIT 1";
            
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare($sql);
                $st->bindValue(":id", $id, \PDO::PARAM_INT);
                $st->execute();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Delete failed: " . $e->getMessage());
            }
        }
        
        /**
         *
         * @return array members
         */
        public static function GetAllMembers()// $startRow, $numRows, $order ) 
        {
            $sql = "SELECT * FROM members ORDER BY username";
            try 
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->query( $sql );
                $records = array();
                // every row is a dictionary with table field names as keys
                foreach ($st->fetchAll() as $row)
                    $records[] = new Members_Access($row);
                PdoConnect::Disconnect();
                
                return $records;
            }
            catch(\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Query failed: " . $e->getMessage());
            }
        }
        
        public static function GetMember($user, $pass)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                
                /* original query they had... 
                $sql = "SELECT `account_type`, CONCAT(`fname`, ' ', `lname`) AS 'name'
                        FROM members WHERE username = :username
                        AND password = PASSWORD(:password)";
                 */
                
                // took out the password from the query, bc. it was causing null output..
                $sql = "SELECT `account_type`, CONCAT(`fname`, ' ', `lname`) AS 'name'
                        FROM members WHERE username = :username";
                
                $st = $conn->prepare($sql);
                
                $st->bindValue(":username", $user, \PDO::PARAM_STR);
                //$st->bindValue(":password", $pass, \PDO::PARAM_STR);
                $st->execute();
                $member = $st->fetch(\PDO::FETCH_ASSOC);
                PdoConnect::Disconnect();
                return $member;
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error404("There was an error processing the request");
            }
        }
        
        public static function UsernameNotTaken($user)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $sql = "SELECT * FROM members WHERE username = :username";
                $st = $conn->prepare($sql);
                $st->bindValue(":username", $user, \PDO::PARAM_STR);
                $st->execute();
                $member = $st->fetch(\PDO::FETCH_ASSOC);
                PdoConnect::Disconnect();
                return $member === false;
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error404("There was an error processing the request");
            }
        }
        
        /**
         * @param int $id
         * @return Members_Access 
         */
        private static function GetMemberById($id)
        {
            try
            {
                $conn = PdoConnect::GetPDOInstance();
                $st = $conn->prepare("SELECT * FROM members WHERE id = :id");
                $st->bindValue(":id", $id, \PDO::PARAM_INT);
                $st->execute();
                $lastEntry = $st->fetch();
                PdoConnect::Disconnect();
            }
            catch (\PDOException $e)
            {
                PdoConnect::Disconnect();
                ExceptionHandler::Error500("Could not retrieve last entry:" . $e->getMessage());
            }
            
            return new Members_Access($lastEntry);
        }
        
        private static function Bind(&$st, Members_Access &$record)
        {
            $st->bindValue(":$record->username", $record->data[$record->username], \PDO::PARAM_STR);
            $st->bindValue(":$record->fname", $record->data[$record->fname], \PDO::PARAM_STR);
            $st->bindValue(":$record->lname", $record->data[$record->lname], \PDO::PARAM_STR);
            $st->bindValue(":$record->account_type", $record->data[$record->account_type], \PDO::PARAM_STR);
        }
        
        private static function Validate(Members_Access $record)
        {
            if (trim($record->data[$record->password]) != trim($record->data[$record->confirm_password]))
                ExceptionHandler::Error404("Passwords do not match");
            
            foreach ($record->data as $field => $value)
            {
                if ($field != $record->id && $field != $record->password 
                        && $field != $record->confirm_password && (!isset($value) || trim($value) === ''))
                    ExceptionHandler::Error404 ("One or more inputs must have a value");
            }
        }
    }
}
?>
