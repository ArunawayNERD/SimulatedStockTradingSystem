use sstsdb;

replace into history
select symbol, last_trade_date, last_trade_price
from stocks;
