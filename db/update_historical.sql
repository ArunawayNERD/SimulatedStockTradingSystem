insert into history
select symbol, name, last_trade_date, last_trade_price
from stocks;
