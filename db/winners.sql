drop table if exists winners;

create table winners (
  cid int not null,
  name varchar(40) not null,
  start_time timestamp not null default '0000-00-00 00:00:00',
  end_time timestamp not null,
  buyin float not null,
  uid int not null,
  creator varchar(40) not null,
  top1 varchar(40),
  top1value float,
  top2 varchar(40),
  top2value float,
  top3 varchar(40),
  top3value float,
  constraint competitions_PK primary key (cid)

);

