DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Trainers;
DROP TABLE IF EXISTS Classes;
DROP TABLE IF EXISTS ClassType;
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
   --ProfilePhoto NVARCHAR(255),
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

CREATE TABLE ClassType(
   ClassTypeId INTEGER NOT NULL,
   Name NVARCHAR(100) NOT NULL,
   Description NVARCHAR(500) NOT NULL,
   Duration INTEGER NOT NULL,

   PRIMARY KEY  (ClassTypeId),
   UNIQUE (Name)
);

CREATE TABLE Classes(
   ClassId INTEGER NOT NULL,
   TrainerId INTEGER NOT NULL,
   ClassTypeId INTEGER NOT NULL,
   Day NVARCHAR(20) NOT NULL,
   StartTime NVARCHAR(10) NOT NULL,
   Capacity INTEGER NOT NULL,

   PRIMARY KEY (ClassId),
   FOREIGN KEY (TrainerId) REFERENCES Trainers (TrainerId) ON DELETE NO ACTION ON UPDATE NO ACTION,
   FOREIGN KEY (ClassTypeId) REFERENCES ClassType (ClassTypeId) ON DELETE NO ACTION ON UPDATE NO ACTION
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


INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, ProfilePhoto, Role) VALUES
(1, 'Ana Martins', 'anamartins', 'ana@powerpit.pt', 'hash_ana_123', 'images/ana.jpg', 'admin'),
(2, 'Bruno Costa', 'brunocosta', 'bruno@powerpit.pt', 'hash_bruno_123', 'images/bruno.jpg', 'trainer'),
(3, 'Carla Sousa', 'carlasousa', 'carla@powerpit.pt', 'hash_carla_123', 'images/carla.jpg', 'trainer'),
(4, 'Diana Silva', 'dianasilva', 'diana@powerpit.pt', 'hash_diana_123', 'images/diana.jpg', 'member'),
(5, 'Eduardo Ferreira', 'eduardoferreira', 'eduardo@powerpit.pt', 'hash_eduardo_123', 'images/eduardo.jpg', 'member'),
(6, 'Filipa Gomes', 'filipagomes', 'filipa@powerpit.pt', 'hash_filipa_123', 'images/filipa.jpg', 'member'),
(7, 'Goncalo Ribeiro', 'goncaloribeiro', 'goncalo@powerpit.pt', 'hash_goncalo_123', 'images/goncalo.jpg', 'member'),
(8, 'Helena Moreira', 'helenamoreira', 'helena@powerpit.pt', 'hash_helena_123', 'images/helena.jpg', 'member'),
(9, 'Ines Rocha', 'inesrocha', 'ines@powerpit.pt', 'hash_ines_123', 'images/ines.jpg', 'member'),
(10, 'Joao Mendes', 'joaomendes', 'joao@powerpit.pt', 'hash_joao_123', 'images/joao.jpg', 'member'),
(11, 'Marta Cunha', 'martacunha', 'marta@powerpit.pt', 'hash_marta_123', 'images/marta.jpg', 'member'),
(12, 'Nuno Alves', 'nunoalves', 'nuno@powerpit.pt', 'hash_nuno_123', 'images/nuno.jpg', 'member');

INSERT INTO Trainers (TrainerId, UserId, Bio, Specializations, Certifications) VALUES
(1, 2, 'Experienced trainer focused on strength and conditioning.', 'HIIT, Strength Training, Functional Training', 'Level 3 PT'),
(2, 3, 'Trainer specialized in mobility and indoor cycling.', 'Pilates, Spinning, Mobility', 'Pilates Certification');

INSERT INTO ClassType (ClassTypeId, Name, Description, Duration) VALUES
(1, 'HIIT', 'High intensity workout to improve endurance, strength and conditioning.', 60),
(2, 'Functional Training', 'Training session focused on balance, mobility, coordination and core strength.', 60),
(3, 'Spinning', 'Indoor cycling class designed for cardio and endurance improvement.', 45),
(4, 'Pilates', 'Class focused on posture, flexibility, controlled movement and core stability.', 60);

INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, Day, StartTime, Capacity) VALUES
(1, 1, 1, 'Monday', '09:00', 20),
(2, 1, 1, 'Wednesday', '18:00', 20),
(3, 1, 1, 'Friday', '07:30', 20),
(4, 1, 2, 'Tuesday', '18:00', 15),
(5, 1, 2, 'Thursday', '09:30', 15),
(6, 1, 2, 'Saturday', '11:00', 15),
(7, 2, 3, 'Monday', '17:30', 18),
(8, 2, 3, 'Wednesday', '17:30', 18),
(9, 2, 3, 'Sunday', '10:30', 18),
(10, 2, 4, 'Tuesday', '08:30', 16),
(11, 2, 4, 'Friday', '08:30', 16),
(12, 2, 4, 'Saturday', '10:00', 16);

INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status) VALUES
(1, 4, 1, '2026-04-22 10:00', 'active'),
(2, 5, 1, '2026-04-22 10:15', 'active'),
(3, 6, 1, '2026-04-22 10:30', 'active'),
(4, 7, 2, '2026-04-22 11:00', 'active'),
(5, 8, 2, '2026-04-22 11:10', 'active'),
(6, 9, 3, '2026-04-22 11:20', 'active'),
(7, 10, 3, '2026-04-22 11:25', 'cancelled'),
(8, 4, 4, '2026-04-22 11:40', 'active'),
(9, 5, 4, '2026-04-22 11:50', 'active'),
(10, 6, 5, '2026-04-22 12:00', 'active'),
(11, 7, 5, '2026-04-22 12:10', 'active'),
(12, 8, 6, '2026-04-22 12:20', 'active'),
(13, 9, 6, '2026-04-22 12:30', 'active'),
(14, 10, 7, '2026-04-22 12:40', 'active'),
(15, 11, 7, '2026-04-22 12:50', 'active'),
(16, 12, 8, '2026-04-22 13:00', 'active'),
(17, 4, 8, '2026-04-22 13:10', 'active'),
(18, 5, 9, '2026-04-22 13:20', 'active'),
(19, 6, 9, '2026-04-22 13:30', 'cancelled'),
(20, 7, 10, '2026-04-22 13:40', 'active'),
(21, 8, 10, '2026-04-22 13:50', 'active'),
(22, 9, 11, '2026-04-22 14:00', 'active'),
(23, 10, 11, '2026-04-22 14:10', 'active'),
(24, 11, 12, '2026-04-22 14:20', 'active'),
(25, 12, 12, '2026-04-22 14:30', 'active');

INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES
(1, 'Treadmill', 'Cardio', 12, 'available'),
(2, 'Exercise Bike', 'Cardio', 10, 'available'),
(3, 'Weight Bench', 'Strength', 8, 'available'),
(4, 'Rowing Machine', 'Cardio', 4, 'maintenance'),
(5, 'Yoga Mat', 'Studio', 30, 'available'),
(6, 'Dumbbell Set', 'Strength', 20, 'available'),
(7, 'Kettlebell', 'Strength', 15, 'available'),
(8, 'Resistance Band', 'Functional', 25, 'available'),
(9, 'Foam Roller', 'Recovery', 10, 'available'),
(10, 'Medicine Ball', 'Functional', 12, 'available');