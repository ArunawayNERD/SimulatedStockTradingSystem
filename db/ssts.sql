drop table if exists users;

create table users (
  id int not null auto_increment,
  username varchar(40) not null,
  password varchar(40) not null,
  constraint users_PK primary key (id)
);

CREATE TABLE stocks (
  symbol            VARCHAR(5)    not null,
  name              VARCHAR(50)   not null, 
  last_trade_price  VARCHAR(10)         not null,
  open_price        VARCHAR(10)         not null,
  price_change      VARCHAR(10)         not null,
  last_trade_date   VARCHAR(10)   not null,
  last_trade_time   VARCHAR(10)    not null,
  primary key (symbol)
);
drop table if exists history;

CREATE TABLE history (
  symbol            VARCHAR(5)    not null,
  name              VARCHAR(50)   not null, 
  trade_date        VARCHAR(10)   not null,
  closing_price     VARCHAR(10)	  not null,
  primary key (symbol, trade_date)
);
drop table if exists portfolios;

create table portfolios (
  uid int not null,
  name varchar(40) not null, 
  cash float not null,
  competition boolean not null,
  constraint portfolios_PK primary key (uid, name),
  foreign key (uid) references users(id)
);
drop table if exists activePortfolio;

create table activePortfolio (
  uid int auto_increment,
  name varchar(40),
  constraint activePortfolio_PK primary key (uid, name),
  constraint activePortfolio_FK1 foreign key (uid, name) 
    references portfolios(uid, name)
    on update cascade
);

drop table if exists portfolioStocks;

create table portfolioStocks (
  uid int not null,
  name varchar(40) not null,
  symbol varchar(5) not null,
  stocks int not null,
  constraint portfolioStocks_PK primary key (uid, name, symbol),
  constraint activePortfolios_FK foreign key (uid, name)
    references portfolios(uid,name)
    on update cascade
  
);

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
  on update cascade
);
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

drop trigger if exists default_portfolio;

create trigger default_portfolio 
after insert on users
for each row
    insert into portfolios 
      values (new.id, new.username, 100000, false);
    
