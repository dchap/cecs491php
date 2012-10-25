<?php
namespace Lib\Manual_Entries
{
    /**
     * Description of station_records
     */
    abstract class Data_Access
    {
        protected $data = array();
             
        public function __construct($data) 
        {
            foreach ($data as $key => $value) 
            {
                if (array_key_exists($key, $this->data)) 
                        $this->data[$key] = $value;
            }
        }

        public function getValue($field) 
        {
            if (array_key_exists($field, $this->data)) 
                return $this->data[$field];
            else 
                die("Field not found");
        }

        public function getValueEncoded($field) 
        {
            return htmlspecialchars($this->getValue($field));
        }
    }
}
?>
