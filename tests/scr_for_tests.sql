drop table if exists t1;
create table t1 (id int primary key AUTO_INCREMENT, f1 varchar(10), f2 varchar(10));

drop table if exists t1_jrnl;
create table t1_jrnl (id_jrnl int primary key AUTO_INCREMENT, id int, f1 varchar(10), f2 varchar(10));

drop trigger if exists t1_after_ins_trig;

create trigger t1_after_ins_trig after insert on t1
for each row insert into t1_jrnl (id, f1, f2) values (new.id, new.f1, new.f2);



drop trigger if exists t1_after_upd_trig;
create trigger t1_after_upd_trig after update on t1 for each row insert into t1_jrnl (id, f1, f2) values (new.id, new.f1, new.f2);



insert into t1 values(1,'1','101');
insert into t1 values(2,'2','202');
insert into t1 values(3,'3','303');

update t1 set f2='122' where f1='1';
update t1 set f2='233' where f1='2';
update t1 set f2='344' where f1='3';

--SHOW TRIGGERS;
show triggers where `Trigger` like '%ns%' and `table`='T1'

$sql = "CREATE TRIGGER `tr3` BEFORE DELETE ON `t1` FOR EACH ROW BEGIN\n"
    . "set @valf2 = concat(old.id, \'-\' , old.f2)insert into t1_jrnl (id, f1, f2) values (old.id, \'del\', @valf2)END";

$sql = "CREATE TRIGGER `trig_insert_name` BEFORE INSERT ON `t1` FOR EACH ROW insert into t1_jrnl (id, f1, f2) values (new.id, new.f1, new.f2)";

$sql = "CREATE TRIGGER `t1_after_upd_trig` AFTER UPDATE ON `t1` FOR EACH ROW BEGIN\n"
    . "set @valf2 = concat(new.id, \'-\' , new.f2)insert into t1_jrnl (id, f1, f2) values (new.id, new.f1, @valf2)END";
    
    
-- start clin_test_lab DDL - with errors for testing    
    CREATE TABLE `clin_test_lab` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `visit_id` INTEGER(11) DEFAULT NULL COMMENT 'Визит',
  `patient_id` INTEGER(11) DEFAULT NULL COMMENT 'Пациент',
  `erythrocytes_yes_no_id` INTEGER(11) DEFAULT NULL COMMENT 'Эритроциты, проведение (да/нет)',
  `erythrocytes` FLOAT(11,2) DEFAULT NULL COMMENT 'Эритроциты',
  `field11` INTEGER(11) DEFAULT NULL,
  `erythrocytes_date` INTEGER(11) DEFAULT NULL COMMENT 'Эритроциты, дата проведения',
  `instr_mrt_descr` TEXT COLLATE utf8_general_ci COMMENT 'Заключение',
  `checked` INTEGER(11) NOT NULL DEFAULT '0' COMMENT 'Проверено монитором',
  `row_stat` INTEGER(11) NOT NULL DEFAULT '1' COMMENT 'Статус строки (акт 1, удал 0)',
  `user_insert` VARCHAR(50) COLLATE utf8_general_ci NOT NULL DEFAULT 'no_user' COMMENT 'Пользователь, создавший',
  `user_update` VARCHAR(50) COLLATE utf8_general_ci NOT NULL DEFAULT 'no_user' COMMENT 'Пользователь, обновивший',
  `insert_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата добавления записи',
  `update_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата обновления записи',
  PRIMARY KEY (`id`),
  UNIQUE KEY `patient_visit_uniq` (`patient_id`, `visit_id`),
  KEY `patient_id` (`patient_id`),
  KEY `visit_id` (`visit_id`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE DEFINER = 'root'@'%' TRIGGER `clin_test_labafter_insert` AFTER INSERT ON `clin_test_lab`
  FOR EACH ROW
BEGIN
 insert into clin_test_lab_jrnl ( id, patient_id, visit_id, erythrocytes_yes_no_id, erythrocytes, erythrocytes_date, instr_mrt_descr, sex_id, checked, row_stat, user_insert, user_update, insert_date, update_date, insert_ind) VALUES ( new.id, new.patient_id, new.visit_id, new.erythrocytes_yes_no_id, new.erythrocytes, new.erythrocytes_date, new.instr_mrt_descr, new.sex_id, new.checked, new.row_stat, new.user_insert, new.user_update, new.insert_date, new.update_date, 1);
END;

CREATE DEFINER = 'root'@'%' TRIGGER `clin_test_labafter_update` AFTER UPDATE ON `clin_test_lab`
  FOR EACH ROW
BEGIN
 insert into clin_test_lab_jrnl ( id, patient_id, visit_id, erythrocytes_yes_no_id, erythrocytes, erythrocytes_date, instr_mrt_descr, sex_id, checked, row_stat, user_insert, user_update, insert_date, update_date, insert_ind) VALUES ( new.id, new.patient_id, new.visit_id, new.erythrocytes_yes_no_id, new.erythrocytes, new.erythrocytes_date, new.instr_mrt_descr, new.sex_id, new.checked, new.row_stat, new.user_insert, new.user_update, new.insert_date, new.update_date, 0);
END;
-- finish clin_test_lab DDL - with errors for testing  