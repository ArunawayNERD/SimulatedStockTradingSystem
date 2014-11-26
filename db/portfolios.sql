drop table if exists portfolios;

create table portfolios (
  uid int not null,
  name varchar(40) not null, 
  cash float not null,
  competition boolean not null,
  constraint portfolios_PK primary key (uid, name),
  foreign key (uid) references users(id)
);
