
use sstsdb;

DROP TABLE IF EXISTS stocks;
DROP TABLE IF EXISTS history;
DROP TABLE IF EXISTS users;

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

CREATE TABLE history (
  symbol            VARCHAR(5)    not null,
  name              VARCHAR(50)   not null, 
  trade_date        VARCHAR(10)   not null,
  closing_price     VARCHAR(10)	  not null,
  primary key (symbol, trade_date)
);
