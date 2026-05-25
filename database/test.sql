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