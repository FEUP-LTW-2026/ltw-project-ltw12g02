DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Trainers;

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

   PRIMARY KEY (UserId),
   UNIQUE (Username),
   UNIQUE (Email)
};

CREATE TABLE Trainers{
   TrainerId INTEGER NOT NULL,
   UserId INTEGER NOT NULL,
   Bio NVARCHAR(500),
   Specializations NVARCHAR(255),
   Certifications NVARCHAR(255),

   PRIMARY KEY (TrainerId),
   UNIQUE (UserId),
   FOREIGN KEY (UserId) REFERENCES Users (UserId) ON DELETE NO ACTION ON UPDATE NO ACTION
};

/*******************************************************************************
   Create Foreign Keys
********************************************************************************/

CREATE INDEX IF NOT EXISTS IFK_TrainersUserId ON Trainers (UserId);