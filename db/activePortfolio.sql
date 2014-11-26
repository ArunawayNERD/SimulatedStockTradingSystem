create table activePortfolio (
  uid int auto_increment,
  name varchar(40) not null,
  constraint activePortfolio_PK primary key (uid),
  foreign key (uid) references users(id),
  foreign key (uid,name) references portfolios(uid,name)
);
