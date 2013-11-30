<?php

class m131130_153816_ver2 extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `soap_service` CHANGE COLUMN `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT  ;
        ");

        $this->execute("
            ALTER TABLE `soap_service` RENAME TO `service` ;
        ");

        $this->execute("
            ALTER TABLE `group_functions`
            CHANGE COLUMN `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT  ,
            CHANGE COLUMN `service_id` `service_id` INT(11) UNSIGNED NOT NULL  ;
        ");

        $this->execute("
            ALTER TABLE `group_functions`
            ADD INDEX `service` (`service_id` ASC) ;
        ");

        $this->execute("
            ALTER TABLE `group_functions`
            ADD CONSTRAINT `fk_group_functions_1`
            FOREIGN KEY (`service_id` )
            REFERENCES `twt_soap_test`.`service` (`id` )
            ON DELETE NO ACTION
            ON UPDATE NO ACTION;
        ");

        $this->execute("
            ALTER TABLE `soap_function` CHANGE COLUMN `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT  ,
            CHANGE COLUMN `group_id` `group_id` INT(11) UNSIGNED NOT NULL  ,
            ADD COLUMN `service_id` INT(11) UNSIGNED NOT NULL  AFTER `id` ;
        ");

        $this->execute("
            ALTER TABLE `soap_function` RENAME TO  `twt_soap_test`.`function` ;
        ");

        $this->execute("
            ALTER TABLE `function`
            ADD INDEX `service` (`service_id` ASC)
            , ADD INDEX `group` (`group_id` ASC) ;

        ");

        $this->execute("
            ALTER TABLE `function` CHANGE COLUMN `description` `description` VARCHAR(100) NULL DEFAULT NULL  ;
        ");

        $this->execute("
            ALTER TABLE `function`
              ADD CONSTRAINT `fk_function_1`
              FOREIGN KEY (`group_id` )
              REFERENCES `twt_soap_test`.`group_functions` (`id` )
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;
        ");

        $this->execute("
            UPDATE `function` f,
                group_functions gf
            SET
                f.service_id = gf.service_id
            WHERE
                gf.id = f.group_id;
        ");

        $this->execute("
            ALTER TABLE `function`
              ADD CONSTRAINT `fk_function_2`
              FOREIGN KEY (`service_id` )
              REFERENCES `twt_soap_test`.`service` (`id` )
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;
        ");
	}

	public function down()
	{
		echo "m131130_153816_ver2 does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}