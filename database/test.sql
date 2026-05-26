CREATE TABLE IF NOT EXISTS PersonalClasses (
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

    FOREIGN KEY (UserId) REFERENCES Users(UserId)
        ON DELETE CASCADE,

    FOREIGN KEY (TrainerId) REFERENCES Trainers(TrainerId)
        ON DELETE CASCADE
);


CREATE INDEX IF NOT EXISTS IDX_PersonalClassesUser
ON PersonalClasses(UserId);

CREATE INDEX IF NOT EXISTS IDX_PersonalClassesTrainer
ON PersonalClasses(TrainerId);

CREATE INDEX IF NOT EXISTS IDX_PersonalClassesDate
ON PersonalClasses(StartDateTime);

CREATE INDEX IF NOT EXISTS IDX_PersonalClassesStatus
ON PersonalClasses(Status);