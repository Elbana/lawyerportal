# Lawyer Portal

Lawyer portal is a simple example for appointment booking system
## Installation

Update database credentials inside LawyerPortal/.env

Run this command to create the database
```bash
php bin/console doctrine:database:create
```
Create tables
```bash
php bin/console doctrine:schema:update --force
```

Start server
```bash
 php bin/console server:start
```

## Routes

```bash
login       : localhost
registration: localhost/registration
booking     : localhost/appointment/book (Require citizen role)
booking list: localhost/appointment/list (require lawyer role)
```

## Directory structure

```bash
Appointment : LawyerPortal/src/Appointment
user        : LawyerPortal/src/User
templates   : LawyerPortal/templates
```