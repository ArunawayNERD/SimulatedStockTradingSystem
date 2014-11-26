drop table if exists users;

create table users (
  id int not null auto_increment,
  username varchar(40) not null,
  password varchar(40) not null,
  constraint users_PK primary key (id)
);
