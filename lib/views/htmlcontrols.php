<?php

namespace Lib\Views
{
    use Lib\Error\Exception_Handler as ExceptionHandler;
    use Lib\Manual_Entries\Singular_Entries_Access as SingleEntries;
    use Lib\Manual_Entries\Members_Access as MembersAccess;
    use Config\Constants\Query as QueryConstants;
    /**
     * Description of drop_down
     */
    class HTMLControls
    {
        public static function DropDownList($table, $selectName, $defaultOption, $class)
        {
            echo "<select class='$class' name='$selectName'>";
            echo "<option selected='yes' value=''>$defaultOption</option>";
            foreach (SingleEntries::GetAllEntries($table) as $option)
            {
                $option = htmlspecialchars($option, ENT_QUOTES);
                echo "<option value='$option'>$option</option>";
            }
            echo "</select>";
        }
        
        public static function DropDownListFish($column, $selectName, $defaultOption, $class)
        {
            echo "<select class='$class' name='$selectName'>";
            echo "<option selected='yes' value=''>$defaultOption</option>";
            foreach (SingleEntries::GetAllEntriesFish($column) as $option)
            {
                $value = htmlspecialchars($option, ENT_QUOTES);
                echo "<option value='$value'>$value</option>";
            }
            echo "</select>";
        }
        
        /**
         * @param string $table table name to query
         * @param int $size size of list
         * @param string $selectName name attribute
         * @param string $class class attribute
         * @param array $entries optional list to generate html for if table has more than
         *                       one column
         */
        public static function SelectList($table, $size, $selectName, $class, $entries = null)
        {
            if ($entries == null)
                $entries = SingleEntries::GetAllEntries($table);
            echo "<select class='$class' name='$selectName' size='$size'>";
            foreach ($entries as $option)
            {
                $option = htmlspecialchars($option, ENT_QUOTES);
                echo "<option value='$option'>$option</option>";
            }
            echo "</select>";
        }
        
        public static function SelectListValueDisparity($size, $selectName, $class, $entries)
        {
            
            echo "<select class='$class' name='$selectName' size='$size'>";
            foreach ($entries as $id => $ascension)
            {
                $ascension = htmlspecialchars($ascension, ENT_QUOTES);
                echo "<option value='$id'>$ascension</option>";
            }
            echo "</select>";
        }
        
        public static function DateTimeRange()
        {
?>            
        <div class="inline-inputs">
            <div>
                <label>Date Range: <span class="help-inline">(YY-MM-DD)</span></label>
                <input class="small datepicker" type="text" name="<?php echo QueryConstants::DateStart; ?>" />
                <span class="date-separator">to</span>
                <input class="small datepicker" type="text" name="<?php echo QueryConstants::DateEnd; ?>" />
            </div>
            <div style="padding-left: 20px">
                <label>Time Range: <span class="help-inline">(HH:MM)</span></label>
                <input class="mini" type="text" name="<?php echo QueryConstants::TimeStart; ?>" />
                <span class="date-separator">to</span>
                <input class="mini" type="text" name="<?php echo QueryConstants::TimeEnd; ?>" />
            </div>
        </div>  
<?php
        }
    }
}
?>