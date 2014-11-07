create table users (
id int not null auto_increment,
username char(40) not null,
password char(40) not null,
constraint users_PK primary key (id)

);
