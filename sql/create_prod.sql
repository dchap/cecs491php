SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `csulbsha_sharktopus` ;
CREATE SCHEMA IF NOT EXISTS `csulbsha_sharktopus` DEFAULT CHARACTER SET latin1 ;
USE `csulbsha_sharktopus` ;

-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`fish`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`fish` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`fish` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `codespace` VARCHAR(20) NOT NULL ,
  `transmitter_id` INT(11) UNSIGNED NOT NULL ,
  `ascension` VARCHAR(45) NOT NULL ,
  `genus` VARCHAR(45) NOT NULL ,
  `species` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `codespace_xid_UNIQUE` (`codespace` ASC, `transmitter_id` ASC) ,
  UNIQUE INDEX `ascension_UNIQUE` (`ascension` ASC) ) 
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`fish_details`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`fish_details` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`fish_details` (
  `fish_id` INT(10) UNSIGNED NOT NULL ,
  `date_deployed` DATE NOT NULL ,
  `time_deployed` TIME NOT NULL ,
  `sex` ENUM('male','female','unknown') NOT NULL ,
  `total_length` INT(11) NOT NULL ,
  `fork_length` INT(11) NULL ,
  `standard_length` INT(11) NOT NULL ,
  `girth` INT(11) NULL ,
  `weight` INT(11) NULL ,
  `dart_tag` VARCHAR(45) NOT NULL ,
  `dart_color` VARCHAR(45) NOT NULL ,
  `landed_latitude` DECIMAL(10,6) NOT NULL ,
  `landed_longitude` DECIMAL(10,6) NOT NULL ,
  `released_latitude` DECIMAL(10,6) NOT NULL ,
  `released_longitude` DECIMAL(10,6) NOT NULL ,
  `time_out_of_water` DECIMAL(10,6) NULL ,
  `time_in_tricane` DECIMAL(10,6) NULL ,
  `time_in_surgery` DECIMAL(10,6) NULL ,
  `recovery_time` DECIMAL(10,6) NULL ,
  `landing_depth` DECIMAL(10,6) NULL ,
  `release_depth` DECIMAL(10,6) NULL ,
  `landing_temperature` DECIMAL(10,6) NULL ,
  `release_temperature` DECIMAL(10,6) NULL ,
  `fish_condition` VARCHAR(45) NULL ,
  `release_method` VARCHAR(45) NOT NULL ,
  `photo_reference` VARCHAR(50) NULL ,
  `comment` VARCHAR(100) NULL ,
  PRIMARY KEY (`fish_id`) ,
  CONSTRAINT `fish_details_fish_id`
    FOREIGN KEY (`fish_id` )
    REFERENCES `csulbsha_sharktopus`.`fish` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`fish_sensors`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`fish_sensors` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`fish_sensors` (
  `fish_id` INT(10) UNSIGNED NOT NULL ,
  `sensor_codespace` VARCHAR(20) NOT NULL ,
  `sensor_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`fish_id`, `sensor_codespace`, `sensor_id`) ,
  INDEX `sensor_INDEX` (`sensor_codespace` ASC, `sensor_id` ASC) ,
  CONSTRAINT `fish_sensors_fish_id`
    FOREIGN KEY (`fish_id` )
    REFERENCES `csulbsha_sharktopus`.`fish` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;



-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`members`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`members` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`members` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(45) NOT NULL ,
  `password` VARCHAR(45) NOT NULL ,
  `fname` VARCHAR(45) NOT NULL ,
  `lname` VARCHAR(45) NOT NULL ,
  `account_type` ENUM('admin','superadmin','user','superuser') NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`projects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`projects` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`projects` (
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`name`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`projects_members`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`projects_members` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`projects_members` (
  `members_id` INT(11) NOT NULL ,
  `projects_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`members_id`, `projects_name`) ,
  INDEX `members_projects_projects_name` (`projects_name` ASC) ,
  CONSTRAINT `members_projects_projects_name`
    FOREIGN KEY (`projects_name` )
    REFERENCES `csulbsha_sharktopus`.`projects` (`name` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `members_projects_members_id`
    FOREIGN KEY (`members_id` )
    REFERENCES `csulbsha_sharktopus`.`members` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`receivers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`receivers` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`receivers` (
  `id` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`metadata`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`metadata` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`metadata` (
  `time` TIME NOT NULL ,
  `date` DATE NOT NULL ,
  `receivers_id` VARCHAR(45) NOT NULL ,
  `description` VARCHAR(45) NOT NULL ,
  `data` VARCHAR(100) NULL ,
  `units` VARCHAR(5) NULL ,
  INDEX `metadata_receivers_id` (`receivers_id` ASC) ,
  PRIMARY KEY (`time`, `date`, `receivers_id`, `description`) ,
  CONSTRAINT `metadata_receivers_id`
    FOREIGN KEY (`receivers_id` )
    REFERENCES `csulbsha_sharktopus`.`receivers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`projects_fish`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`projects_fish` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`projects_fish` (
  `projects_name` VARCHAR(45) NOT NULL ,
  `fish_id` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`projects_name`, `fish_id`) ,
  INDEX `projects_fish_fish_id` (`fish_id` ASC) ,
  CONSTRAINT `projects_fish_fish_id`
    FOREIGN KEY (`fish_id` )
    REFERENCES `csulbsha_sharktopus`.`fish` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `projects_fish_project_name`
    FOREIGN KEY (`projects_name` )
    REFERENCES `csulbsha_sharktopus`.`projects` (`name` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`stations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`stations` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`stations` (
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`name`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`projects_stations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`projects_stations` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`projects_stations` (
  `projects_name` VARCHAR(45) NOT NULL ,
  `stations_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`stations_name`, `projects_name`) ,
  INDEX `projects_stations_projects_name` (`projects_name` ASC) ,
  CONSTRAINT `projects_stations_projects_name`
    FOREIGN KEY (`projects_name` )
    REFERENCES `csulbsha_sharktopus`.`projects` (`name` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `projects_stations_stations_name`
    FOREIGN KEY (`stations_name` )
    REFERENCES `csulbsha_sharktopus`.`stations` (`name` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`sonde`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`sonde` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`sonde` (
  `stations_name` VARCHAR(45) NOT NULL ,
  `date` DATE NOT NULL ,
  `temperature` DECIMAL(10,6) NOT NULL ,
  `sp_cond` INT(11) NOT NULL ,
  `tds` DECIMAL(10,6) NOT NULL ,
  `salinity` DECIMAL(10,6) NOT NULL ,
  `do_percent` DECIMAL(10,6) NOT NULL ,
  `do_conc` DECIMAL(10,6) NOT NULL ,
  `do_charge` INT(11) NOT NULL ,
  `depth` DECIMAL(10,6) NOT NULL ,
  `ph` DECIMAL(10,6) NOT NULL ,
  `ph_mv` DECIMAL(10,6) NOT NULL ,
  `par1` DECIMAL(10,6) NOT NULL ,
  `chlorophyl` DECIMAL(10,6) NOT NULL ,
  `bp` DECIMAL(10,6) NOT NULL ,
  PRIMARY KEY (`stations_name`, `date`, `depth`, `par1`) ,
  CONSTRAINT `sonde_stations_name`
    FOREIGN KEY (`stations_name` )
    REFERENCES `csulbsha_sharktopus`.`stations` (`name` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`stations_records`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`stations_records` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`stations_records` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `stations_name` VARCHAR(45) NOT NULL ,
  `receivers_id` VARCHAR(45) NOT NULL ,
  `release_value` INT(11) NULL ,
  `hobo` INT(11) NULL ,
  `frequency_codespace` VARCHAR(20) NULL ,
  `sync_tag` INT(11) UNSIGNED NULL ,
  `latitude` DECIMAL(10,6) NOT NULL ,
  `longitude` DECIMAL(10,6) NOT NULL ,
  `secondary_latitude` DECIMAL(10,6) NULL ,
  `secondary_longitude` DECIMAL(10,6) NULL ,
  `secondary_waypoint` INT(11) NULL ,
  `depth` DECIMAL(10,6) NULL ,
  `receiver_height` DECIMAL(10,6) NULL ,
  `date_in` DATE NOT NULL ,
  `time_in` TIME NOT NULL ,
  `date_out` DATE NULL ,
  `time_out` TIME NULL ,
  `date_downloaded` DATE NULL ,
  `comment` VARCHAR(100) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `StationsRecords_stationName_UNIQUE` (`stations_name` ASC, `date_in` ASC, `time_in` ASC) ,
  INDEX `station_records_receivers_receiver_id` (`receivers_id` ASC) ,
  CONSTRAINT `station_records_receivers_receiver_id`
    FOREIGN KEY (`receivers_id` )
    REFERENCES `csulbsha_sharktopus`.`receivers` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `station_records_stations_name`
    FOREIGN KEY (`stations_name` )
    REFERENCES `csulbsha_sharktopus`.`stations` (`name` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`temperatures`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`temperatures` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`temperatures` (
  `stations_name` VARCHAR(45) NOT NULL ,
  `date` DATE NOT NULL ,
  `time` TIME NOT NULL ,
  `temperature` DECIMAL(10,6) NOT NULL ,
  `intensity` DECIMAL(10,6) NOT NULL ,
  `battery_volt` DECIMAL(10,6) NOT NULL ,
  PRIMARY KEY (`stations_name`, `date`, `time`) ,
  CONSTRAINT `temperatures_stations_name`
    FOREIGN KEY (`stations_name` )
    REFERENCES `csulbsha_sharktopus`.`stations` (`name` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`vue`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`vue` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`vue` (
  `date` DATE NOT NULL ,
  `time` TIME NOT NULL ,
  `frequency_codespace` VARCHAR(20) NOT NULL ,
  `transmitter_id` INT(11) NOT NULL ,
  `sensor_value` DECIMAL(10,6) NULL ,
  `sensor_unit` VARCHAR(5) NULL ,
  `receivers_id` VARCHAR(45) NOT NULL ,
  INDEX `vue_receivers_id` (`receivers_id` ASC) ,
  PRIMARY KEY (`date`, `time`, `frequency_codespace`, `transmitter_id`, `receivers_id`) ,
  INDEX `full_transmitter_INDEX` (`frequency_codespace` ASC, `transmitter_id` ASC) ,
  INDEX `transmitter_INDEX` (`transmitter_id` ASC) ,
  CONSTRAINT `vue_receivers_id`
    FOREIGN KEY (`receivers_id` )
    REFERENCES `csulbsha_sharktopus`.`receivers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `csulbsha_sharktopus`.`uploads`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `csulbsha_sharktopus`.`uploads` ;

CREATE  TABLE IF NOT EXISTS `csulbsha_sharktopus`.`uploads` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `uploader` VARCHAR(45) NOT NULL ,
  `filename` VARCHAR(45) NOT NULL ,
  `entries` INT UNSIGNED NOT NULL ,
  `file_type` ENUM('metadata', 'temperature', 'vue', 'sonde') NOT NULL ,
  `date` DATE NOT NULL ,
  `time` TIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `filename_UNIQUE` (`filename` ASC) )
ENGINE = InnoDB;

INSERT INTO `csulbsha_sharktopus`.`members` 
VALUES ( NULL, 'admin', PASSWORD('admin'), 'admin', 'admin', 'superadmin' );

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
