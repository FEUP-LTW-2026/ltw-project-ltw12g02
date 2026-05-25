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

CREATE INDEX IF NOT EXISTS IFK_EquipmentReservationsUserId ON EquipmentReservations (UserId);
CREATE INDEX IF NOT EXISTS IFK_EquipmentReservationsEquipmentId ON EquipmentReservations (EquipmentId);
CREATE INDEX IF NOT EXISTS IDX_EquipmentReservationsTime ON EquipmentReservations (EquipmentId, ReservationDateTime);

INSERT INTO EquipmentReservations (UserId, EquipmentId, ReservationDateTime, Duration, Status) VALUES
(4, 1, '2026-06-01 09:00', 60, 'active'),
(5, 2, '2026-06-01 10:00', 45, 'active'),
(6, 3, '2026-06-01 11:30', 60, 'active'),
(7, 5, '2026-06-01 14:00', 30, 'active'),
(8, 6, '2026-06-01 16:00', 90, 'active'),

(4, 7, '2026-06-02 09:30', 60, 'active'),
(5, 8, '2026-06-02 12:00', 30, 'active'),
(6, 9, '2026-06-02 15:00', 45, 'active'),
(7, 10, '2026-06-02 18:00', 60, 'active'),

(8, 1, '2026-06-03 08:00', 60, 'cancelled'),
(9, 2, '2026-06-03 09:00', 30, 'active'),
(10, 3, '2026-06-03 10:00', 120, 'active'),
(11, 5, '2026-06-03 17:00', 60, 'active'),
(12, 6, '2026-06-03 18:30', 45, 'active');