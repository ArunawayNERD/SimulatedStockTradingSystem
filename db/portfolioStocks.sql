
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
