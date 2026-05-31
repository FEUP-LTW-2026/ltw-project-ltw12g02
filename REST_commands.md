# REST API Commands

## Start server

```bash
php -S localhost:9000
```

## Login

```bash
curl -i -c cookies.txt -H "Content-Type: application/json" -X POST http://localhost:9000/api/login.php -d '{"email":"admin@example.com","password":"password123"}'
```

## Logged user

```bash
curl -i -b cookies.txt http://localhost:9000/api/me.php
```

## Logout

```bash
curl -i -b cookies.txt -c cookies.txt -X POST http://localhost:9000/api/logout.php
```

## Class types

```bash
curl -i http://localhost:9000/api/workout_class_types.php
```

```bash
curl -i "http://localhost:9000/api/workout_class_types.php?id=1"
```

## Classes

```bash
curl -i http://localhost:9000/api/classes.php
```

```bash
curl -i "http://localhost:9000/api/classes.php?id=1"
```

```bash
curl -i -b cookies.txt -H "Content-Type: application/json" -X POST http://localhost:9000/api/classes.php -d '{"trainerId":1,"classTypeId":1,"classDateTime":"2026-06-01 18:30:00","capacity":20}'
```

```bash
curl -i -b cookies.txt -H "Content-Type: application/json" -X PATCH "http://localhost:9000/api/classes.php?id=1" -d '{"capacity":25}'
```

```bash
curl -i -b cookies.txt -X DELETE "http://localhost:9000/api/classes.php?id=1"
```

## Enrollments

```bash
curl -i -b cookies.txt http://localhost:9000/api/enrollments.php
```

```bash
curl -i -b cookies.txt -H "Content-Type: application/json" -X POST http://localhost:9000/api/enrollments.php -d '{"classId":1}'
```

```bash
curl -i -b cookies.txt -X DELETE "http://localhost:9000/api/enrollments.php?id=1"
```

## Equipment

```bash
curl -i http://localhost:9000/api/equipment.php
```

```bash
curl -i "http://localhost:9000/api/equipment.php?id=1"
```

```bash
curl -i -b cookies.txt -H "Content-Type: application/json" -X POST http://localhost:9000/api/equipment.php -d '{"name":"Treadmill","type":"Cardio","quantity":5,"status":"available"}'
```

```bash
curl -i -b cookies.txt -H "Content-Type: application/json" -X PATCH "http://localhost:9000/api/equipment.php?id=1" -d '{"status":"maintenance"}'
```

```bash
curl -i -b cookies.txt -X DELETE "http://localhost:9000/api/equipment.php?id=1"
```

## Equipment reservations

```bash
curl -i -b cookies.txt http://localhost:9000/api/equipment_reservations.php
```

```bash
curl -i -b cookies.txt -H "Content-Type: application/json" -X POST http://localhost:9000/api/equipment_reservations.php -d '{"equipmentId":1,"reservationDateTime":"2026-06-01 15:00:00","duration":60}'
```

```bash
curl -i -b cookies.txt -X DELETE "http://localhost:9000/api/equipment_reservations.php?id=1"
```

## Trainers

```bash
curl -i http://localhost:9000/api/trainers.php
```

```bash
curl -i "http://localhost:9000/api/trainers.php?id=1"
```

```bash
curl -i -b cookies.txt -H "Content-Type: application/json" -X POST http://localhost:9000/api/trainers.php -d '{"userId":3,"bio":"Personal trainer.","specializations":"Strength","certifications":"CPT"}'
```

```bash
curl -i -b cookies.txt -H "Content-Type: application/json" -X PATCH "http://localhost:9000/api/trainers.php?id=1" -d '{"bio":"Updated trainer bio."}'
```

```bash
curl -i -b cookies.txt -X DELETE "http://localhost:9000/api/trainers.php?id=1"
```

## Users

```bash
curl -i -b cookies.txt http://localhost:9000/api/users.php
```

```bash
curl -i -b cookies.txt "http://localhost:9000/api/users.php?id=1"
```

```bash
curl -i -b cookies.txt -H "Content-Type: application/json" -X POST http://localhost:9000/api/users.php -d '{"name":"John Doe","username":"johndoe","email":"john@example.com","password":"password123","role":"member","plan":"basic"}'
```

```bash
curl -i -b cookies.txt -H "Content-Type: application/json" -X PATCH "http://localhost:9000/api/users.php?id=2" -d '{"plan":"premium"}'
```

```bash
curl -i -b cookies.txt -X DELETE "http://localhost:9000/api/users.php?id=2"
```
