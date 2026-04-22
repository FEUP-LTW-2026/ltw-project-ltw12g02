DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Trainers;
DROP TABLE IF EXISTS Classes;
DROP TABLE IF EXISTS Enrollments;
DROP TABLE IF EXISTS Equipment;

/*******************************************************************************
   Create Tables
********************************************************************************/

CREATE TABLE Users(
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
);

CREATE TABLE Trainers(
   TrainerId INTEGER NOT NULL,
   UserId INTEGER NOT NULL,
   Bio NVARCHAR(500),
   Specializations NVARCHAR(255),
   Certifications NVARCHAR(255),

   PRIMARY KEY (TrainerId),
   UNIQUE (UserId),
   FOREIGN KEY (UserId) REFERENCES Users (UserId) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE Classes(
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
);

CREATE TABLE Enrollments(
   EnrollmentId INTEGER NOT NULL,
   UserId INTEGER NOT NULL,
   ClassId INTEGER NOT NULL,
   EnrollmentDate NVARCHAR(30) NOT NULL,
   Status NVARCHAR(20) NOT NULL,

   PRIMARY KEY (EnrollmentId),
   UNIQUE (UserId, ClassId),
   FOREIGN KEY (UserId) REFERENCES Users (UserId) ON DELETE NO ACTION ON UPDATE NO ACTION,
   FOREIGN KEY (ClassId) REFERENCES Classes (ClassId) ON DELETE NO ACTION ON UPDATE NO ACTION
);


CREATE TABLE Equipment(
   EquipmentId INTEGER NOT NULL,
   Name NVARCHAR(100) NOT NULL,
   Type NVARCHAR(50) NOT NULL,
   Quantity INTEGER NOT NULL,
   AvailabilityStatus NVARCHAR(20) NOT NULL,

   PRIMARY KEY (EquipmentId)
);

/*******************************************************************************
   Create Foreign Keys
********************************************************************************/

CREATE INDEX IF NOT EXISTS IFK_TrainersUserId ON Trainers (UserId);

CREATE INDEX IF NOT EXISTS IFK_ClassesTrainerId ON Classes (TrainerId);

CREATE INDEX IF NOT EXISTS IFK_EnrollmentsUserId ON Enrollments (UserId);
CREATE INDEX IF NOT EXISTS IFK_EnrollmentsClassId ON Enrollments (ClassId);

/*******************************************************************************
   Populate Tables
********************************************************************************/

INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, ProfilePhoto, Role) VALUES (1, 'Ana Martins', 'anamartins', 'ana@powerpit.pt', 'hash_ana_123', 'images/ana.jpg', 'admin');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, ProfilePhoto, Role) VALUES (2, 'Bruno Costa', 'brunocosta', 'bruno@powerpit.pt', 'hash_bruno_123', 'images/bruno.jpg', 'trainer');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, ProfilePhoto, Role) VALUES (3, 'Carla Sousa', 'carlasousa', 'carla@powerpit.pt', 'hash_carla_123', 'images/carla.jpg', 'trainer');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, ProfilePhoto, Role) VALUES (4, 'Diana Silva', 'dianasilva', 'diana@powerpit.pt', 'hash_diana_123', 'images/diana.jpg', 'member');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, ProfilePhoto, Role) VALUES (5, 'Eduardo Ferreira', 'eduardoferreira', 'eduardo@powerpit.pt', 'hash_eduardo_123', 'images/eduardo.jpg', 'member');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, ProfilePhoto, Role) VALUES (6, 'Filipa Gomes', 'filipagomes', 'filipa@powerpit.pt', 'hash_filipa_123', 'images/filipa.jpg', 'member');

INSERT INTO Trainers (TrainerId, UserId, Bio, Specializations, Certifications) VALUES (1, 2, 'Experienced trainer focused on strength and conditioning.', 'HIIT, Strength Training, Functional Training', 'Level 3 PT');
INSERT INTO Trainers (TrainerId, UserId, Bio, Specializations, Certifications) VALUES (2, 3, 'Trainer specialized in mobility and indoor cycling.', 'Pilates, Spinning, Mobility', 'Pilates Certification');

INSERT INTO Classes (ClassId, TrainerId, Name, Type, Description, Day, StartTime, EndTime, Capacity) VALUES (1, 1, 'Morning HIIT', 'HIIT', 'High intensity workout to start the day.', 'Monday', '09:00', '10:00', 20);
INSERT INTO Classes (ClassId, TrainerId, Name, Type, Description, Day, StartTime, EndTime, Capacity) VALUES (2, 1, 'Functional Core', 'Functional Training', 'Core and balance focused training session.', 'Tuesday', '18:00', '19:00', 15);
INSERT INTO Classes (ClassId, TrainerId, Name, Type, Description, Day, StartTime, EndTime, Capacity) VALUES (3, 2, 'Power Cycling', 'Spinning', 'Indoor cycling class for cardio and endurance.', 'Wednesday', '17:30', '18:30', 18);
INSERT INTO Classes (ClassId, TrainerId, Name, Type, Description, Day, StartTime, EndTime, Capacity) VALUES (4, 2, 'Pilates Flow', 'Pilates', 'Pilates class focused on posture and flexibility.', 'Friday', '08:30', '09:30', 16);

INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status) VALUES (1, 4, 1, '2026-04-22 10:00', 'active');
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status) VALUES (2, 5, 1, '2026-04-22 10:15', 'active');
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status) VALUES (3, 4, 2, '2026-04-22 11:00', 'active');
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status) VALUES (4, 6, 3, '2026-04-22 11:20', 'active');
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status) VALUES (5, 5, 4, '2026-04-22 12:00', 'cancelled');

INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (1, 'Treadmill', 'Cardio', 12, 'available');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (2, 'Exercise Bike', 'Cardio', 10, 'available');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (3, 'Weight Bench', 'Strength', 8, 'available');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (4, 'Rowing Machine', 'Cardio', 4, 'maintenance');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (5, 'Yoga Mat', 'Studio', 30, 'available');