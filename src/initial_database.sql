/*
Only one admin account initial here.
You can add students,courses and other admins by this account.
Or operate the database directly.
*/
create database course_selection_system;
grant all privileges on course_selection_system.* to css@localhost Identified by "public";

use course_selection_system;

create table admin(id int  AUTO_INCREMENT primary key not null,username varchar(256) not null,password varchar(256) not null);
insert into admin (username,password) values ("root","111111");

create table course(id int  AUTO_INCREMENT primary key not null,classid varchar(128) not null,name varchar(256) not null,teacher varchar(256) not null,time varchar(256) not null,students varchar(32768) not null,num int not null,max int not null);
alter table course add unique index (classid);

create table student(id int  AUTO_INCREMENT primary key not null,username varchar(128) not null,password varchar(128) not null,name varchar(128) not null,courses varchar(8192) not null);
alter table student add unique index (username);
