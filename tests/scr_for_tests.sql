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

$sql = "CREATE TRIGGER `tr3` BEFORE DELETE ON `t1` FOR EACH ROW BEGIN\n"
    . "set @valf2 = concat(old.id, \'-\' , old.f2)insert into t1_jrnl (id, f1, f2) values (old.id, \'del\', @valf2)END";

$sql = "CREATE TRIGGER `trig_insert_name` BEFORE INSERT ON `t1` FOR EACH ROW insert into t1_jrnl (id, f1, f2) values (new.id, new.f1, new.f2)";

$sql = "CREATE TRIGGER `t1_after_upd_trig` AFTER UPDATE ON `t1` FOR EACH ROW BEGIN\n"
    . "set @valf2 = concat(new.id, \'-\' , new.f2)insert into t1_jrnl (id, f1, f2) values (new.id, new.f1, @valf2)END";