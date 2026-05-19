PRAGMA foreign_keys = ON;

BEGIN TRANSACTION;

/* Make Luis a trainer */
UPDATE Users
SET Role = 'trainer'
WHERE Email = 'pt.luis.lima2006@gmail.com';

/* Create trainer profile for Luis if it does not exist */
INSERT OR IGNORE INTO Trainers (UserId, Bio, Specializations, Certifications)
SELECT UserId,
       'Trainer at PowerPIT.',
       'Strength Training, HIIT, Functional Training',
       'Certified Personal Trainer'
FROM Users
WHERE Email = 'pt.luis.lima2006@gmail.com';

/* Create class using the existing ClassTypeId = 1 */
INSERT INTO Classes (TrainerId, ClassTypeId, ClassDateTime, Capacity)
SELECT t.TrainerId,
       1,
       '2030-05-20 18:30:00',
       20
FROM Trainers t
JOIN Users u ON u.UserId = t.UserId
WHERE u.Email = 'pt.luis.lima2006@gmail.com';

COMMIT;