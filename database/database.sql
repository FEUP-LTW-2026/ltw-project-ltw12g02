DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Trainers;
DROP TABLE IF EXISTS Classes;
DROP TABLE IF EXISTS Enrollments;
DROP TABLE IF EXISTS Equipment;

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

CREATE TABLE Classes{
   ClassId INTEGER NOT NULL,
   TrainerId INTEGER NOT NULL,
   Name NVARCHAR(100) NOT NULL,
   Type NVARCHAR(50) NOT NULL,
   Description NVARCHAR(500),
   Day NVARCHAR(20) NOT NULL,
   StartTime NVARCHAR(10) NOT NULL,
   EndTime NVARCHAR(10) NOT NULL,
   Capacity INTEGER NOT NULL,

   PRIMARY KEY (ClassId),
   FOREIGN KEY (TrainerId) REFERENCES Trainers (TrainerId) ON DELETE NO ACTION ON UPDATE NO ACTION
};

CREATE TABLE Enrollments{
   EnrollmentId INTEGER NOT NULL,
   UserId INTEGER NOT NULL,
   ClassId INTEGER NOT NULL,
   EnrollmentDate NVARCHAR(30) NOT NULL,
   Status NVARCHAR(20) NOT NULL,

   PRIMARY KEY (EnrollmentId),
   UNIQUE (UserId, ClassId),
   FOREIGN KEY (UserId) REFERENCES Users (UserId) ON DELETE NO ACTION ON UPDATE NO ACTION,
   FOREIGN KEY (ClassId) REFERENCES Classes (ClassId) ON DELETE NO ACTION ON UPDATE NO ACTION
};


CREATE TABLE Equipment
(
   EquipmentId INTEGER NOT NULL,
   Name NVARCHAR(100) NOT NULL,
   Type NVARCHAR(50) NOT NULL,
   Quantity INTEGER NOT NULL,
   AvailabilityStatus NVARCHAR(20) NOT NULL,

   PRIMARY KEY (EquipmentId)
)

/*******************************************************************************
   Create Foreign Keys
********************************************************************************/

CREATE INDEX IF NOT EXISTS IFK_TrainersUserId ON Trainers (UserId);
CREATE INDEX IF NOT EXISTS IFK_ClassesTrainerId ON Classes (TrainerId);
CREATE INDEX IF NOT EXISTS IFK_EnrollmentsUserId ON Enrollments (UserId);
CREATE INDEX IF NOT EXISTS IFK_EnrollmentsClassId ON Enrollments (ClassId);