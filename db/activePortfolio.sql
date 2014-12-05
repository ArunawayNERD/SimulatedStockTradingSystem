drop table if exists activePortfolio;

create table activePortfolio (
  uid int auto_increment,
  name varchar(40),
  constraint activePortfolio_PK primary key (uid, name),
  constraint activePortfolio_FK1 foreign key (uid, name) 
    references portfolios(uid, name)
    on update cascade
);
