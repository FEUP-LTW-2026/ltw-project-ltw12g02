PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS Complaints;
DROP TABLE IF EXISTS EquipmentReservations;
DROP TABLE IF EXISTS Enrollments;
DROP TABLE IF EXISTS PersonalClasses;
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
   Plan NVARCHAR(20) NOT NULL DEFAULT 'basic',
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
   Rating INTEGER,
   Review NVARCHAR(500),
   Attendance BOOLEAN NOT NULL DEFAULT FALSE,

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

CREATE TABLE EquipmentReservations(
   EquipmentReservationId INTEGER PRIMARY KEY AUTOINCREMENT,
   UserId INTEGER NOT NULL,
   EquipmentId INTEGER NOT NULL,
   ReservationDateTime DATETIME NOT NULL,
   Duration INTEGER NOT NULL,
   Status NVARCHAR(20) NOT NULL DEFAULT 'active',

   CHECK (Status IN ('active', 'cancelled')),
   CHECK (Duration > 0),

   FOREIGN KEY (UserId) REFERENCES Users (UserId) ON DELETE NO ACTION ON UPDATE NO ACTION,
   FOREIGN KEY (EquipmentId) REFERENCES Equipment (EquipmentId) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE PersonalClasses (
    PersonalClassId INTEGER PRIMARY KEY AUTOINCREMENT,
    UserId INTEGER NOT NULL,
    TrainerId INTEGER NOT NULL,
    StartDateTime DATETIME NOT NULL,
    DurationMinutes INTEGER NOT NULL,
    Status NVARCHAR(20) NOT NULL DEFAULT 'pending',
    RequestMessage TEXT,
    TrainerResponse TEXT,
    CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CHECK (DurationMinutes > 0),
    CHECK (Status IN ('pending', 'accepted', 'rejected')),

    FOREIGN KEY (UserId) REFERENCES Users(UserId) ON DELETE CASCADE,
    FOREIGN KEY (TrainerId) REFERENCES Trainers(TrainerId) ON DELETE CASCADE
);

CREATE TABLE Complaints(
   ComplaintId INTEGER PRIMARY KEY AUTOINCREMENT,
   UserId INTEGER NOT NULL,
   Reason NVARCHAR(100) NOT NULL,
   Details NVARCHAR(500) NOT NULL,
   ComplaintDate DATETIME NOT NULL,
   Response NVARCHAR(500),

   FOREIGN KEY (UserId) REFERENCES Users (UserId) ON DELETE NO ACTION ON UPDATE NO ACTION
);

/*******************************************************************************
   Create Foreign Key Indexes
********************************************************************************/

CREATE INDEX IF NOT EXISTS IFK_TrainersUserId ON Trainers (UserId);

CREATE INDEX IF NOT EXISTS IFK_ClassesTrainerId ON Classes (TrainerId);
CREATE INDEX IF NOT EXISTS IFK_ClassesClassTypeId ON Classes (ClassTypeId);

CREATE INDEX IF NOT EXISTS IFK_EnrollmentsUserId ON Enrollments (UserId);
CREATE INDEX IF NOT EXISTS IFK_EnrollmentsClassId ON Enrollments (ClassId);

CREATE INDEX IF NOT EXISTS IFK_EquipmentReservationsUserId ON EquipmentReservations (UserId);
CREATE INDEX IF NOT EXISTS IFK_EquipmentReservationsEquipmentId ON EquipmentReservations (EquipmentId);
CREATE INDEX IF NOT EXISTS IDX_EquipmentReservationsTime ON EquipmentReservations (EquipmentId, ReservationDateTime);

CREATE INDEX IF NOT EXISTS IFK_ComplaintsUserId ON Complaints (UserId);

CREATE INDEX IF NOT EXISTS IFK_PersonalClassesUserId ON PersonalClasses (UserId);
CREATE INDEX IF NOT EXISTS IFK_PersonalClassesTrainerId ON PersonalClasses (TrainerId);

/*******************************************************************************
   Populate Tables
********************************************************************************/



-- Users
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (1, 'Luis Lima', 'luislima9970', 'pt.luis.lima2006@gmail.com', '$2y$10$QcQZ2OPsXsrfJq0yCm8e3uLeXSJA/.4VOfAYGnm/ZIF0SS2GHB6Rm', 'admin', '4533071924ef2384f2351c605ee78974.jpg', 'premium');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (2, 'Guilherme Coutinho', 'Gui-Coutinho', 'guilhermecoutinho.casa@gmail.com', '$2y$10$oZuzQO/fqxY2axdjolzvz.znsI6qc2fXC2jyK1nfoJ4c/yCWK4s.u', 'member', 'e982302cf0ca835c1c528b390034922e.png', 'premium');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (4, 'Guilherme Coutinho 2', 'Gui2', 'gui2@gmail.com', '$2y$12$eI4Q0DPALi2y3Ar9FTZK4et6/XtKUKCvXfgMApaHsd.tuYO1BauCa', 'trainer', NULL, 'premium');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (5, 'luis lima', 'luislima', 'pt.luis.lima@gmail.com', '$2y$10$xdtLdBggDCh9T615hWBWbOmQZB5XHl3urLgfugLH.B1Ft2L1GRajK', 'member', NULL, 'premium');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (6, 'luis abreu', 'luisabreu', 'luis@gmail.com', '$2y$10$/f4qYd7MEbEQcX79E8URz.EYpISqf9CITmbifrnprWgSXEFbddq7m', 'trainer', NULL, 'premium');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (7, 't', 't', 'test@gmail.com', '$2y$10$sdaA6.vbUIRsYb/GjHPfbewt39CxsbR4IPXPSqgnRdTSwmIBm0zvK', 'trainer', NULL, 'premium');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (8, 'luis abreu', 'abreu', 'abreu@gmail.com', '$2y$10$H1YGhx6dOvg9RtUg9pNeIeBl4S2A5RXvdg//eRxcZbiXN1H936IFC', 'trainer', NULL, 'premium');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (9, 'Membro', 'Membro', 'membro@gmail.com', '$2y$10$kMQy02u3RTCwk6NxWHS8RusbmJYozvxuxamw9Kaqiwg01c4V8K0hq', 'member', NULL, 'premium');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (10, 'gabriela', 'gabrielaaf', 'gabriela@gmail.com', '$2y$10$.Quag5OaWsa8YnZrU2pFm.U/CwpImHqWjYheni9INoKs009vc2tHe', 'member', NULL, 'premium');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (11, 'Guilherme Coutinho 3', 'Gui3', 'gui3@gmail.com', '$2y$12$8TZs79bZZHuWaGIvQCUzoOPTjOhrUeT4F345ftRYvlFoooTao.HE.', 'member', '93649d174bfc531ccd266308d1092fdc.png', 'basic');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (12, 'Joaquim da Silva', 'Xx_Sh4d0wL0rd_xX', 'gui4@gmail.com', '$2y$12$ts8DE0uJv.dqjflbzd8F4eYwmOncmTsb/CJpUnUGVbWCfTcGos.UG', 'member', NULL, 'basic');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (13, 'Member Name', 'm3mb3r', 'member@example.com', '$2y$12$vDdvHMlZyP2haGGglqrKguYY.s3ljt8FOy2xVsMihcn5rKYQhOspq', 'member', NULL, 'basic');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (14, 'Trainer Name', 'tr41n3r', 'trainer@example.com', '$2y$12$hDwf.s9psrzKPojP6TPgK.RHP8qreWf0bAO7YViwfP/RkF0b1qp0m', 'trainer', NULL, 'basic');
INSERT INTO Users (UserId, Name, Username, Email, PasswordHash, Role, ProfileImage, Plan) VALUES (15, 'Admin Name', '4dm1n', 'admin@example.com', '$2y$12$pxV1i4Qr6JA5fxuq4HrF8uzq4nvkzIPUnJ9ipq1bb.3SoGC2KBrYW', 'admin', NULL, 'basic');

-- Trainers
INSERT INTO Trainers (TrainerId, UserId, Bio, Specializations, Certifications) VALUES (4, 4, 'Hi! I am a trainer at PowerPit', '', '');
INSERT INTO Trainers (TrainerId, UserId, Bio, Specializations, Certifications) VALUES (6, 8, 'Hi! I am a trainer at PowerPit', NULL, 'ps');
INSERT INTO Trainers (TrainerId, UserId, Bio, Specializations, Certifications) VALUES (7, 7, 'Hi! I am a trainer at PowerPit', '', '');
INSERT INTO Trainers (TrainerId, UserId, Bio, Specializations, Certifications) VALUES (10, 14, 'Hi! I am a trainer at PowerPit', '', '');

-- ClassType
INSERT INTO ClassType (ClassTypeId, Name, Description, Duration) VALUES (1, 'HIIT', 'High intensity workout to improve endurance, strength and conditioning.', 60);
INSERT INTO ClassType (ClassTypeId, Name, Description, Duration) VALUES (2, 'Functional Training', 'Training session focused on balance, mobility, coordination and core strength.', 60);
INSERT INTO ClassType (ClassTypeId, Name, Description, Duration) VALUES (3, 'Spinning', 'Indoor cycling class designed for cardio and endurance improvement.', 45);
INSERT INTO ClassType (ClassTypeId, Name, Description, Duration) VALUES (4, 'Pilates', 'Class focused on posture, flexibility, controlled movement and core stability.', 60);

-- Classes
INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES (2, 4, 1, '2030-05-20 18:30:00', 20);
INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES (6, 7, 1, '2026-06-01 18:00:00', 20);
INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES (7, 4, 4, '2026-05-27 23:11:00', 1);
INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES (9, 7, 1, '2026-06-06 10:30:00', 15);
INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES (10, 7, 4, '2026-05-29 10:30:00', 15);
INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES (11, 7, 2, '2026-06-05 09:30:00', 10);
INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES (12, 7, 1, '2026-05-27 11:30:00', 10);
INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES (13, 7, 2, '2026-05-27 18:42:00', 10);
INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES (14, 4, 2, '2026-05-27 20:12:00', 1);
INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES (15, 4, 3, '2026-06-02 11:20:00', 10);
INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES (16, 6, 3, '2026-06-04 09:00:00', 20);
INSERT INTO Classes (ClassId, TrainerId, ClassTypeId, ClassDateTime, Capacity) VALUES (17, 4, 4, '2026-06-30 20:00:00', 15);

-- Enrollments
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (3, 1, 2, '2026-05-20 15:55:42', 'active', NULL, NULL, 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (6, 2, 2, '2026-05-21 15:48:37', 'active', 3, 'ok', 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (8, 4, 2, '2026-05-23 15:01:36', 'active', 4, 'lol', 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (9, 5, 2, '2026-05-24 07:29:07', 'active', 5, 'great class', 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (10, 6, 2, '2026-05-24 09:10:31', 'active', 4, 'this class was great! love it!', 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (18, 8, 7, '2026-05-26 12:06:43', 'active', NULL, NULL, 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (19, 1, 9, '2026-05-26 17:04:41', 'active', NULL, NULL, 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (46, 9, 6, '2026-05-27 08:22:04', 'active', NULL, NULL, 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (47, 9, 9, '2026-05-27 08:22:07', 'active', NULL, NULL, 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (48, 9, 11, '2026-05-27 08:22:18', 'active', NULL, NULL, 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (49, 1, 6, '2026-05-27 08:22:36', 'active', NULL, NULL, 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (50, 10, 13, '2026-05-27 10:28:54', 'active', 3, 'i liked but i think it was too much for me', 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (51, 10, 11, '2026-05-27 10:29:01', 'active', NULL, NULL, 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (52, 10, 6, '2026-05-27 10:29:11', 'active', NULL, NULL, 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (53, 10, 12, '2026-05-27 10:29:14', 'active', 5, '3', 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (98, 2, 6, '2026-05-27 18:32:54', 'active', NULL, NULL, 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (99, 2, 9, '2026-05-27 18:32:58', 'active', NULL, NULL, 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (101, 2, 13, '2026-05-27 18:40:08', 'active', 3, 'mieh', 1);
INSERT INTO Enrollments (EnrollmentId, UserId, ClassId, EnrollmentDate, Status, Rating, Review, Attendance) VALUES (102, 2, 14, '2026-05-27 19:11:19', 'active', 4, 'mto fixe', 1);

-- Equipment
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (1, 'Treadmill', 'Cardio', 12, 'unavailable');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (2, 'Exercise Bike', 'Cardio', 10, 'available');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (3, 'Weight Bench', 'Strength', 8, 'available');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (4, 'Rowing Machine', 'Cardio', 4, 'maintenance');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (5, 'Yoga Mat', 'Studio', 30, 'available');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (6, 'Dumbbell Set', 'Strength', 20, 'available');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (7, 'Kettlebell', 'Strength', 15, 'available');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (8, 'Resistance Band', 'Functional', 25, 'available');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (9, 'Foam Roller', 'Recovery', 10, 'available');
INSERT INTO Equipment (EquipmentId, Name, Type, Quantity, AvailabilityStatus) VALUES (10, 'Medicine Ball', 'Functional', 12, 'available');

-- EquipmentReservations
INSERT INTO EquipmentReservations (EquipmentReservationId, UserId, EquipmentId, ReservationDateTime, Duration, Status) VALUES (1, 8, 2, '2026-05-29 11:11:00', 60, 'cancelled');
INSERT INTO EquipmentReservations (EquipmentReservationId, UserId, EquipmentId, ReservationDateTime, Duration, Status) VALUES (2, 1, 2, '2026-05-29 11:11:00', 60, 'cancelled');
INSERT INTO EquipmentReservations (EquipmentReservationId, UserId, EquipmentId, ReservationDateTime, Duration, Status) VALUES (3, 1, 2, '2026-05-29 11:11:00', 60, 'cancelled');
INSERT INTO EquipmentReservations (EquipmentReservationId, UserId, EquipmentId, ReservationDateTime, Duration, Status) VALUES (4, 1, 2, '2026-05-29 11:11:00', 60, 'cancelled');
INSERT INTO EquipmentReservations (EquipmentReservationId, UserId, EquipmentId, ReservationDateTime, Duration, Status) VALUES (5, 2, 9, '2026-05-28 21:09:00', 30, 'cancelled');

-- PersonalClasses
INSERT INTO PersonalClasses (PersonalClassId, UserId, TrainerId, StartDateTime, DurationMinutes, Status, RequestMessage, TrainerResponse, CreatedAt) VALUES (1, 8, 4, '2026-05-29 09:40:00', 60, 'pending', 'fuhipwojp', NULL, '2026-05-26 15:32:21');
INSERT INTO PersonalClasses (PersonalClassId, UserId, TrainerId, StartDateTime, DurationMinutes, Status, RequestMessage, TrainerResponse, CreatedAt) VALUES (2, 8, 4, '2026-05-29 04:23:00', 60, 'pending', 'reiwohqp+', NULL, '2026-05-26 15:33:21');
INSERT INTO PersonalClasses (PersonalClassId, UserId, TrainerId, StartDateTime, DurationMinutes, Status, RequestMessage, TrainerResponse, CreatedAt) VALUES (3, 1, 7, '2026-06-29 10:30:00', 60, 'accepted', 'test - accept', 'of course !!!', '2026-05-27 08:17:28');
INSERT INTO PersonalClasses (PersonalClassId, UserId, TrainerId, StartDateTime, DurationMinutes, Status, RequestMessage, TrainerResponse, CreatedAt) VALUES (4, 1, 7, '2026-06-09 20:30:00', 90, 'rejected', 'test, reject', 'sorry im busy , try another time', '2026-05-27 08:18:03');
INSERT INTO PersonalClasses (PersonalClassId, UserId, TrainerId, StartDateTime, DurationMinutes, Status, RequestMessage, TrainerResponse, CreatedAt) VALUES (5, 9, 7, '2026-06-07 10:30:00', 90, 'accepted', 'another test', 'tes, i can ! see u there :)', '2026-05-27 08:20:07');
INSERT INTO PersonalClasses (PersonalClassId, UserId, TrainerId, StartDateTime, DurationMinutes, Status, RequestMessage, TrainerResponse, CreatedAt) VALUES (6, 2, 4, '2026-05-28 19:42:00', 30, 'accepted', 'blablabla', 'Hm, oq será q acontece se eu escrever uma resposta demasiado grande, será q tem isto em conta? Vamos lá ver... Hmm, isto já deve ser suficiente, vou enviar então', '2026-05-27 18:42:47');
INSERT INTO PersonalClasses (PersonalClassId, UserId, TrainerId, StartDateTime, DurationMinutes, Status, RequestMessage, TrainerResponse, CreatedAt) VALUES (7, 2, 4, '2026-05-29 19:43:00', 90, 'pending', '...', NULL, '2026-05-27 18:43:16');
INSERT INTO PersonalClasses (PersonalClassId, UserId, TrainerId, StartDateTime, DurationMinutes, Status, RequestMessage, TrainerResponse, CreatedAt) VALUES (8, 10, 7, '2026-06-06 10:30:00', 60, 'pending', 'a', NULL, '2026-05-28 13:51:55');

-- Complaints
INSERT INTO Complaints (ComplaintId, UserId, Reason, Details, ComplaintDate, Response) VALUES (1, 2, 'other', 'test', '2026-05-26 23:08:09', 'ok');
INSERT INTO Complaints (ComplaintId, UserId, Reason, Details, ComplaintDate, Response) VALUES (2, 2, 'class cancellation', 'this class was canceled, why?', '2026-05-26 23:41:20', 'ok');
INSERT INTO Complaints (ComplaintId, UserId, Reason, Details, ComplaintDate, Response) VALUES (3, 2, 'equipment malfunction', 'it doesnt work', '2026-05-26 23:49:39', 'ok');
INSERT INTO Complaints (ComplaintId, UserId, Reason, Details, ComplaintDate, Response) VALUES (4, 2, 'other', 'dgfjnfgbhfd', '2026-05-26 23:51:21', 'ok');
INSERT INTO Complaints (ComplaintId, UserId, Reason, Details, ComplaintDate, Response) VALUES (5, 2, 'equipment malfunction', 'erhyegr', '2026-05-26 23:51:34', 'ok');
INSERT INTO Complaints (ComplaintId, UserId, Reason, Details, ComplaintDate, Response) VALUES (6, 2, 'class cancellation', '7tij6trk', '2026-05-26 23:51:54', 'ok');
INSERT INTO Complaints (ComplaintId, UserId, Reason, Details, ComplaintDate, Response) VALUES (7, 2, 'other', '?', '2026-05-27 20:16:50', 'ok');
INSERT INTO Complaints (ComplaintId, UserId, Reason, Details, ComplaintDate, Response) VALUES (8, 7, 'equipment malfunction', 'testttt', '2026-05-28 17:14:54', 'ok');
INSERT INTO Complaints (ComplaintId, UserId, Reason, Details, ComplaintDate, Response) VALUES (9, 10, 'equipment malfunction', 'aaahhh test member', '2026-05-28 17:15:21', 'ok');
