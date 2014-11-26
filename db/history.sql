CREATE TABLE history (
  symbol            VARCHAR(5)    not null,
  name              VARCHAR(50)   not null, 
  trade_date        VARCHAR(10)   not null,
  closing_price     VARCHAR(10)	  not null,
  primary key (symbol, trade_date)
);
