
drop table if exists transactions;

create table transactions (
  ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  uid int not null,
  name varchar(40) not null,
  symbol varchar(5) not null,
  stocks int not null,
  sharePrice float not null,
  constraint transactions_PK primary key (ts, uid, name),
  foreign key (uid, name) references portfolios(uid, name)
);
