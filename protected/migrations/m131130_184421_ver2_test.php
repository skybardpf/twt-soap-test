<?php

class m131130_184421_ver2_test extends CDbMigration
{
	public function up()
	{
        $this->execute("
            ALTER TABLE `soap_test`
            CHANGE COLUMN `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT  ,
            CHANGE COLUMN `function_id` `function_id` INT(11) UNSIGNED NOT NULL  ,
            CHANGE COLUMN `status` `status` TINYINT(4) UNSIGNED NOT NULL DEFAULT '1'  ,
            CHANGE COLUMN `test_result` `test_result` TINYINT(4) UNSIGNED NOT NULL DEFAULT '2'  ;
        ");

        $this->execute("
            ALTER TABLE `soap_test`
            ADD INDEX `function` (`function_id` ASC) ;
        ");

        $this->execute("
            ALTER TABLE `soap_test` RENAME TO  `function_test` ;
        ");

        $this->execute("
            DELETE FROM function_test WHERE id IN (
                SELECT * FROM (
                    SELECT t.id FROM function_test t
                    LEFT JOIN `function` f ON  f.id=t.function_id WHERE f.id IS NULL
                ) AS p
            );
        ");

        $this->execute("
            ALTER TABLE `twt_soap_test`.`function_test`
              ADD CONSTRAINT `fk_function_test_1`
              FOREIGN KEY (`function_id` )
              REFERENCES `twt_soap_test`.`function` (`id` )
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;
        ");

        $this->execute("
            ALTER TABLE `soap_function_param` CHANGE COLUMN `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT  ,
            CHANGE COLUMN `function_id` `function_id` INT(11) UNSIGNED NOT NULL  ,
            RENAME TO  `function_param` ;
        ");

        $this->execute("
            ALTER TABLE `function_param`
            ADD INDEX `function` (`function_id` ASC) ;
        ");

        $this->execute("
            delete from function_param where id IN (
                select * from (
                    select p.id from function_param p
                    left join `function` f on f.id=p.function_id
                    where f.id is null
                ) as p
            );
        ");

        $this->execute("
            ALTER TABLE `function_param`
              ADD CONSTRAINT `fk_function_param_1`
              FOREIGN KEY (`function_id` )
              REFERENCES `twt_soap_test`.`function` (`id` )
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;
        ");
	}

	public function down()
	{
		echo "m131130_184421_ver2_test does not support migration down.\n";
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