drop trigger if exists default_portfolio;
drop trigger if exists default_active

create trigger default_portfolio 
after insert on users
for each row
  begin
    insert into portfolios 
      values (new.id, new.username, 100000, false);
    
create trigger default_active    
after insert on users
for each row
  insert into activePortfolio
      values (new.id, new.username);
  end;
