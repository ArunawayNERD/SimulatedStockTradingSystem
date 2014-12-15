drop table if exists players;

create table players (
  cid int not null auto_increment,
  uid int not null,
  pname varchar(40) not null, 
  compName varchar(80) not null,
  active boolean not null,
  constraint competitors_PK primary key (cid, uid, pname),
  foreign key (cid) references competitions (cid),
  foreign key (uid, pname) references portfolios (uid, name)
    on update cascade,
  foreign key(uid, compName) references portfolios (uid, name)
    on update cascade
);

