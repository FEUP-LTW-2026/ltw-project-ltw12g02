DROP TABLE IF EXISTS Users;

/*******************************************************************************
   Create Tables
********************************************************************************/

CREATE TABLE Users{
   UserId INTEGER NOT NULL,
   Name NVARCHAR(100) NOT NULL,
   Username NVARCHAR(50) NOT NULL,
   Email NVARCHAR(100) NOT NULL,
   PasswordHash NVARCHAR(255) NOT NULL,
   ProfilePhoto NVARCHAR(255),
   Role NVARCHAR(20) NOT NULL,
};

