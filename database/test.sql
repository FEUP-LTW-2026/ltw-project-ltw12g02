ALTER TABLE Enrollments
DROP COLUMN Attendance;


ALTER TABLE Enrollments
ADD COLUMN Attendance INTEGER NOT NULL DEFAULT 0;

UPDATE Enrollments
SET Attendance = 1