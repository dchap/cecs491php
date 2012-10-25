--FISH sensor detections
SELECT `vue`.`frequency_codespace`, `vue`.`transmitter_id`, `fish`.`ascension`, `fish`.`genus`, `fish`.`species`, `vue`.`receivers_id`, `vue`.`date`, `vue`.`time`, `vue`.`sensor_value`, `vue`.`sensor_unit`, `stations_records`.`stations_name`, `stations_records`.`latitude`, `stations_records`.`longitude` FROM `projects`
    INNER JOIN `projects_fish` ON `projects`.`name` = `projects_fish`.`projects_name`
    INNER JOIN `fish` ON `fish`.`id` = `projects_fish`.`fish_id`
    INNER JOIN `fish_sensors` ON `fish`.`id` = `fish_sensors`.`fish_id`
    INNER JOIN `vue` ON `fish_sensors`.`sensor_codespace` = `vue`.`frequency_codespace`
        AND `fish_sensors`.`sensor_id` = `vue`.`transmitter_id`
    LEFT JOIN `stations_records` ON `vue`.`receivers_id` = `stations_records`.`receivers_id` 
WHERE `projects`.`name` = 'Los_Islotes_TT' 
    AND (`vue`.`date` BETWEEN `stations_records`.`date_in` AND `stations_records`.`date_out`) 
    AND `vue`.`date` > '2002-02-01' 
    AND `vue`.`time` > '0' 
    AND (`vue`.`sensor_unit` IS NOT NULL AND `vue`.`sensor_value` IS NOT NULL) 
    AND `fish`.`genus` = 'Lutjanus' AND `fish`.`species` = 'argentiventris' 

UNION 

SELECT `vue`.`frequency_codespace`, `vue`.`transmitter_id`, `fish`.`ascension`, `fish`.`genus`, `fish`.`species`, `vue`.`receivers_id`, `vue`.`date`, `vue`.`time`, `vue`.`sensor_value`, `vue`.`sensor_unit`, `stations_records`.`stations_name`, `stations_records`.`latitude`, `stations_records`.`longitude` 
FROM `projects`
    INNER JOIN `projects_fish` ON `projects`.`name` = `projects_fish`.`projects_name`
    INNER JOIN `fish` ON `fish`.`id` = `projects_fish`.`fish_id`
    INNER JOIN `vue` ON `fish`.`codespace` = `vue`.`frequency_codespace`
        AND `fish`.`transmitter_id` = `vue`.`transmitter_id`
    LEFT JOIN `stations_records` ON `vue`.`receivers_id` = `stations_records`.`receivers_id` 
WHERE `projects`.`name` = 'Los_Islotes_TT' 
    AND (`vue`.`date` BETWEEN `stations_records`.`date_in` AND `stations_records`.`date_out`) 
    AND `vue`.`date` > '2002-02-01' AND `vue`.`time` > '0' 
    AND (`vue`.`sensor_unit` IS NOT NULL AND `vue`.`sensor_value` IS NOT NULL) 
    AND `fish`.`genus` = 'Lutjanus' AND `fish`.`species` = 'argentiventris' 
ORDER BY `stations_name` asc

--FISH non sensor detections
SELECT `vue`.`frequency_codespace`, `vue`.`transmitter_id`, `fish`.`ascension`, `fish`.`genus`, `fish`.`species`, `vue`.`receivers_id`, `vue`.`date`, `vue`.`time`, `vue`.`sensor_value`, `vue`.`sensor_unit`, `stations_records`.`stations_name`, `stations_records`.`latitude`, `stations_records`.`longitude` 
FROM `projects`
    INNER JOIN `projects_fish` ON `projects`.`name` = `projects_fish`.`projects_name`
    INNER JOIN `fish` ON `fish`.`id` = `projects_fish`.`fish_id`
    INNER JOIN `vue` ON `fish`.`codespace` = `vue`.`frequency_codespace`
        AND `fish`.`transmitter_id` = `vue`.`transmitter_id`
    LEFT JOIN `stations_records` ON `vue`.`receivers_id` = `stations_records`.`receivers_id` 
WHERE `projects`.`name` = 'Los_Islotes_TT' 
    AND (`vue`.`date` BETWEEN `stations_records`.`date_in` AND `stations_records`.`date_out`) 
    AND `vue`.`date` > '2002-02-01' 
    AND `vue`.`time` > '0' 
    AND `vue`.`transmitter_id` >= 47412 
    AND `fish`.`genus` = 'Lutjanus' 
    AND `fish`.`species` = 'argentiventris' 
ORDER BY `stations_name` asc

--STATION fish in database
SELECT `vue`.`frequency_codespace`, `vue`.`transmitter_id`, `fish`.`ascension`, `fish`.`genus`, `fish`.`species`, `vue`.`receivers_id`, `vue`.`date`, `vue`.`time`, `vue`.`sensor_value`, `vue`.`sensor_unit`, `stations_records`.`stations_name`, `stations_records`.`latitude`, `stations_records`.`longitude` FROM `projects`
    INNER JOIN `projects_stations` ON `projects`.`name` = `projects_stations`.`projects_name`
    INNER JOIN `stations_records` ON `stations_records`.`stations_name` = `projects_stations`.`stations_name`
    INNER JOIN `vue` ON `vue`.`receivers_id` = `stations_records`.`receivers_id`
    INNER JOIN `fish` ON `fish`.`transmitter_id` = `vue`.`transmitter_id`
        AND `fish`.`codespace` = `vue`.`frequency_codespace` 
WHERE `projects`.`name` = 'Los_Islotes_TT' 
    AND (`vue`.`date` BETWEEN `stations_records`.`date_in` AND `stations_records`.`date_out`) 
    AND `vue`.`date` > '2002-02-01' AND `vue`.`time` > '0' 
    AND `fish`.`genus` = 'Lutjanus' AND `fish`.`species` = 'argentiventris' 

UNION 

SELECT `vue`.`frequency_codespace`, `vue`.`transmitter_id`, `fish`.`ascension`, `fish`.`genus`, `fish`.`species`, `vue`.`receivers_id`, `vue`.`date`, `vue`.`time`, `vue`.`sensor_value`, `vue`.`sensor_unit`, `stations_records`.`stations_name`, `stations_records`.`latitude`, `stations_records`.`longitude` 
FROM `projects`
    INNER JOIN `projects_stations` ON `projects`.`name` = `projects_stations`.`projects_name`
    INNER JOIN `stations_records` ON `stations_records`.`stations_name` = `projects_stations`.`stations_name`
    INNER JOIN `vue` ON `vue`.`receivers_id` = `stations_records`.`receivers_id`
    INNER JOIN `fish_sensors` ON `fish_sensors`.`sensor_codespace` = `vue`.`frequency_codespace`
        AND `fish_sensors`.`sensor_id` = `vue`.`transmitter_id`
    INNER JOIN `fish` ON `fish_sensors`.`fish_id` = `fish`.`id` 
WHERE `projects`.`name` = 'Los_Islotes_TT' 
    AND (`vue`.`date` BETWEEN `stations_records`.`date_in` AND `stations_records`.`date_out`) 
    AND `vue`.`date` > '2002-02-01' AND `vue`.`time` > '0' AND `fish`.`genus` = 'Lutjanus' 
    AND `fish`.`species` = 'argentiventris' ORDER BY `stations_name` asc

--STATION fish NOT in database
SELECT `vue`.`frequency_codespace`, `vue`.`transmitter_id`, `fish`.`ascension`, `fish`.`genus`, `fish`.`species`, `vue`.`receivers_id`, `vue`.`date`, `vue`.`time`, `vue`.`sensor_value`, `vue`.`sensor_unit`, `stations_records`.`stations_name`, `stations_records`.`latitude`, `stations_records`.`longitude` FROM `projects`
    INNER JOIN `projects_stations` ON `projects`.`name` = `projects_stations`.`projects_name`
    INNER JOIN `stations_records` ON `stations_records`.`stations_name` = `projects_stations`.`stations_name`
    INNER JOIN `vue` ON `vue`.`receivers_id` = `stations_records`.`receivers_id`
    LEFT JOIN `fish` ON `fish`.`transmitter_id` = `vue`.`transmitter_id`
        AND `fish`.`codespace` = `vue`.`frequency_codespace` 
WHERE `projects`.`name` = 'Los_Islotes_TT' 
    AND (`vue`.`date` BETWEEN `stations_records`.`date_in` AND `stations_records`.`date_out`) 
    AND `vue`.`date` > '2002-02-01' AND `vue`.`time` > '0' 
    AND `fish`.`transmitter_id` IS NULL
ORDER BY `stations_name` asc

