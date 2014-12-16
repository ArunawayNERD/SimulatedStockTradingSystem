use sstsdb;

replace into history
select symbol, str_to_date(last_trade_date, "%m/%d/%Y"), last_trade_price
from stocks 
where last_trade_date!="N/A";
