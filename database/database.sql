PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS Enrollments;
DROP TABLE IF EXISTS Classes;
DROP TABLE IF EXISTS Trainers;
DROP TABLE IF EXISTS ClassType;
DROP TABLE IF EXISTS Equipment;
DROP TABLE IF EXISTS Users;

/*******************************************************************************
   Create Tables
********************************************************************************/

CREATE TABLE Users(
   UserId INTEGER PRIMARY KEY AUTOINCREMENT,
   Name NVARCHAR(100) NOT NULL,
   Username NVARCHAR(50) NOT NULL,
   Email NVARCHAR(100) NOT NULL,
   PasswordHash NVARCHAR(255) NOT NULL,
   Role NVARCHAR(20) NOT NULL,
   ProfileImage NVARCHAR(255),

   UNIQUE (Username),
   UNIQUE (Email)
);

CREATE TABLE Trainers(
   TrainerId INTEGER PRIMARY KEY AUTOINCREMENT,
   UserId INTEGER NOT NULL,
   Bio NVARCHAR(500),
   Specializations NVARCHAR(255),
   Certifications NVARCHAR(255),

   UNIQUE (UserId),
   FOREIGN KEY (UserId) REFERENCES Users (UserId) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE ClassType(
   ClassTypeId INTEGER PRIMARY KEY AUTOINCREMENT,
   Name NVARCHAR(100) NOT NULL,
   Description NVARCHAR(500) NOT NULL,
   Duration INTEGER NOT NULL,

   UNIQUE (Name)
);

CREATE TABLE Classes(
   ClassId INTEGER PRIMARY KEY AUTOINCREMENT,
   TrainerId INTEGER NOT NULL,
   ClassTypeId INTEGER NOT NULL,
   ClassDateTime DATETIME NOT NULL,
   Capacity INTEGER NOT NULL,

   FOREIGN KEY (TrainerId) REFERENCES Trainers (TrainerId) ON DELETE NO ACTION ON UPDATE NO ACTION,
   FOREIGN KEY (ClassTypeId) REFERENCES ClassType (ClassTypeId) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE Enrollments(
   EnrollmentId INTEGER PRIMARY KEY AUTOINCREMENT,
   UserId INTEGER NOT NULL,
   ClassId INTEGER NOT NULL,
   EnrollmentDate DATETIME NOT NULL,
   Status NVARCHAR(20) NOT NULL,

   UNIQUE (UserId, ClassId),
   FOREIGN KEY (UserId) REFERENCES Users (UserId) ON DELETE NO ACTION ON UPDATE NO ACTION,
   FOREIGN KEY (ClassId) REFERENCES Classes (ClassId) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE Equipment(
   EquipmentId INTEGER PRIMARY KEY AUTOINCREMENT,
   Name NVARCHAR(100) NOT NULL,
   Type NVARCHAR(50) NOT NULL,
   Quantity INTEGER NOT NULL,
   AvailabilityStatus NVARCHAR(20) NOT NULL
);

/*******************************************************************************
   Create Foreign Key Indexes
********************************************************************************/

CREATE INDEX IF NOT EXISTS IFK_TrainersUserId ON Trainers (UserId);

CREATE INDEX IF NOT EXISTS IFK_ClassesTrainerId ON Classes (TrainerId);
CREATE INDEX IF NOT EXISTS IFK_ClassesClassTypeId ON Classes (ClassTypeId);

CREATE INDEX IF NOT EXISTS IFK_EnrollmentsUserId ON Enrollments (UserId);
CREATE INDEX IF NOT EXISTS IFK_EnrollmentsClassId ON Enrollments (ClassId);

/*******************************************************************************
   Populate Tables
********************************************************************************/


INSERT INTO Trainers (UserId, Bio, Specializations, Certifications) VALUES
(2, 'Experienced trainer focused on strength and conditioning.', 'HIIT, Strength Training, Functional Training', 'Level 3 PT'),
(3, 'Trainer specialized in mobility and indoor cycling.', 'Pilates, Spinning, Mobility', 'Pilates Certification');

INSERT INTO ClassType (Name, Description, Duration) VALUES
('HIIT', 'High intensity workout to improve endurance, strength and conditioning.', 60),
('Functional Training', 'Training session focused on balance, mobility, coordination and core strength.', 60),
('Spinning', 'Indoor cycling class designed for cardio and endurance improvement.', 45),
('Pilates', 'Class focused on posture, flexibility, controlled movement and core stability.', 60);
('Yoga', 'Class focused on flexibility, breathing, balance and body awareness.', 60),
('Body Pump', 'Strength training class using weights to improve muscular endurance.', 60),
('Zumba', 'Dance fitness class combining cardio, rhythm and full-body movement.', 45),
('Cross Training', 'High intensity functional workout combining strength, cardio and agility.', 60),
('Boxing', 'Combat-inspired class focused on cardio, coordination and explosive movement.', 45),
('Core Training', 'Workout focused on abdominal strength, stability and posture control.', 30),
('Stretching', 'Low intensity class focused on mobility, flexibility and muscle recovery.', 30),
('Legs and Glutes', 'Lower body workout focused on strength, endurance and muscle toning.', 45);

INSERT INTO Classes (TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES
(1, 1, '2026-04-27 09:00', 20),
(1, 1, '2026-04-29 18:00', 20),
(1, 1, '2026-05-01 07:30', 20),

(1, 2, '2026-04-28 18:00', 15),
(1, 2, '2026-04-30 09:30', 15),
(1, 2, '2026-05-02 11:00', 15),

(2, 3, '2026-04-27 17:30', 18),
(2, 3, '2026-04-29 17:30', 18),
(2, 3, '2026-05-03 10:30', 18),

(2, 4, '2026-04-28 08:30', 16),
(2, 4, '2026-05-01 08:30', 16),
(2, 4, '2026-05-02 10:00', 16);

INSERT INTO Enrollments (UserId, ClassId, EnrollmentDate, Status) VALUES
(4, 1, '2026-04-22 10:00', 'active'),
(5, 1, '2026-04-22 10:15', 'active'),
(6, 1, '2026-04-22 10:30', 'active'),
(7, 2, '2026-04-22 11:00', 'active'),
(8, 2, '2026-04-22 11:10', 'active'),
(9, 3, '2026-04-22 11:20', 'active'),
(10, 3, '2026-04-22 11:25', 'cancelled'),
(4, 4, '2026-04-22 11:40', 'active'),
(5, 4, '2026-04-22 11:50', 'active'),
(6, 5, '2026-04-22 12:00', 'active'),
(7, 5, '2026-04-22 12:10', 'active'),
(8, 6, '2026-04-22 12:20', 'active'),
(9, 6, '2026-04-22 12:30', 'active'),
(10, 7, '2026-04-22 12:40', 'active'),
(11, 7, '2026-04-22 12:50', 'active'),
(12, 8, '2026-04-22 13:00', 'active'),
(4, 8, '2026-04-22 13:10', 'active'),
(5, 9, '2026-04-22 13:20', 'active'),
(6, 9, '2026-04-22 13:30', 'cancelled'),
(7, 10, '2026-04-22 13:40', 'active'),
(8, 10, '2026-04-22 13:50', 'active'),
(9, 11, '2026-04-22 14:00', 'active'),
(10, 11, '2026-04-22 14:10', 'active'),
(11, 12, '2026-04-22 14:20', 'active'),
(12, 12, '2026-04-22 14:30', 'active');

INSERT INTO Equipment (Name, Type, Quantity, AvailabilityStatus) VALUES
('Treadmill', 'Cardio', 12, 'available'),
('Exercise Bike', 'Cardio', 10, 'available'),
('Weight Bench', 'Strength', 8, 'available'),
('Rowing Machine', 'Cardio', 4, 'maintenance'),
('Yoga Mat', 'Studio', 30, 'available'),
('Dumbbell Set', 'Strength', 20, 'available'),
('Kettlebell', 'Strength', 15, 'available'),
('Resistance Band', 'Functional', 25, 'available'),
('Foam Roller', 'Recovery', 10, 'available'),
('Medicine Ball', 'Functional', 12, 'available');