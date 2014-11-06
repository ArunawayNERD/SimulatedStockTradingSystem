use sstsdb;

DROP TABLE IF EXISTS stocks;
DROP TABLE IF EXISTS history;


CREATE TABLE stocks
  (symbol            CHAR(5)       not null,
   name              VARCHAR(45)   not null,
   last_trade_price  FLOAT         not null,
   open_price        FLOAT         not null,
   price_change      FLOAT         not null,
   last_trade_date   VARCHAR(10)   not null,
   last_trade_time   VARCHAR(7)    not null,
   primary key(symbol)) ENGINE=InnoDB;

CREATE TABLE history
  (symbol            CHAR(5)       not null,
   name              VARCHAR(45)   not null,
   last_trade_price  FLOAT         not null,
   open_price        FLOAT         not null,
   price_change      FLOAT         not null,
   last_trade_date   VARCHAR(10)   not null,
   last_trade_time   VARCHAR(7)    not null,
   primary key(symbol)) ENGINE=InnoDB;

LOAD DATA LOCAL INFILE 'Dow.csv' INTO TABLE stocks
  FIELDS TERMINATED BY ',' ENCLOSED BY '"';
LOAD DATA LOCAL INFILE 'Nasdaq.csv' INTO TABLE stocks
  FIELDS TERMINATED BY ',' ENCLOSED BY '"';