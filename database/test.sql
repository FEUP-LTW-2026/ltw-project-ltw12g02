ALTER TABLE Users
ADD Plan NVARCHAR(20) NOT NULL DEFAULT 'basic';

UPDATE Users 
SET Plan = 'premium'