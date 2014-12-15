drop table if exists competitions;

create table competitions (
  cid int not null auto_increment,
  name varchar(40) not null,
  start_time timestamp not null default '0000-00-00 00:00:00',
  end_time timestamp not null,
  buyin float not null,
  uid int not null,
  creator varchar(40) not null,
  status int not null check (status=-1 or status=0 or status=1),
  constraint competitions_PK primary key (cid),
  foreign key (uid, creator) references portfolios (uid, name)
    on update cascade
);

