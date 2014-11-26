
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
