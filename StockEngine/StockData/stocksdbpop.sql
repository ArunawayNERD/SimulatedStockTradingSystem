use sstsdb;

DROP TABLE IF EXISTS stocks;

CREATE TABLE stocks
  (symbol            CHAR(5)       not null,
   name              VARCHAR(50)   not null,
   last_trade_price  VARCHAR(10)         not null,
   open_price        varchar (10)         not null,
   price_change      VARCHAR(10)         not null,
   last_trade_date   VARCHAR(10)   not null,
   last_trade_time   VARCHAR(10)    not null,
   primary key(symbol)
);
LOAD DATA LOCAL INFILE 'Dow.csv' INTO TABLE stocks
  fields terminated by ',' optionally enclosed by '"'
  lines terminated by '\r\n';

LOAD DATA LOCAL INFILE 'Nasdaq.csv' INTO TABLE stocks
  fields terminated by ',' optionally enclosed by '"'
  lines terminated by '\r\n';

