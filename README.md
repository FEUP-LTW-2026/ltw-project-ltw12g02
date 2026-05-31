# ltw012g02

## Features

**All users:**
- [X] Register a new account.
- [X] Log in and out.
- [X] Edit their profile, including name, username, password, and profile photo.

**Members:**
- [X] Browse the schedule of available fitness classes, filtering by type, trainer, day, or time.
- [X] Enroll in and cancel enrollment from upcoming classes, subject to capacity limits.
- [X] View trainer profiles, including their specializations and the classes they teach.
- [X] Check the current availability of equipment in the main training area.
- [X] Leave ratings and reviews for classes they have attended.

**Trainers:**
- [X] Manage their public profile, including bio, specializations, and certifications.
- [X] View the roster of members enrolled in their classes.
- [X] Track and manage their assigned class schedule.

**Admins:**
- [X] Manage members and trainers (create, update, and deactivate accounts).
- [X] Manage the class catalog (create, edit, and remove classes) and assign trainers to them.
- [X] Manage equipment in the main training area (add, update availability status, and remove items).
- [X] Elevate a user to admin status.
- [X] Oversee and ensure the smooth operation of the entire system.

**Extra:**
- [x] Something extra (e.g., personal training bookings, membership plans, waitlist, ...).

## Running

    sqlite3 database/database.db < database/database.sql
    php -S localhost:9000

## Credentials

- admin@example.com/admin123
- member@example.com/member123
- trainer@example.com/trainer123
