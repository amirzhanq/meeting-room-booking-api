# Meeting Room Booking API

## Setup
1. `composer install`
2. `cp .env.example .env`
3. `php artisan key:generate`
4. `touch database/database.sqlite`
5. `php artisan migrate --seed`
6. `php artisan test` (to verify the installation)
7. `php artisan serve`

## API Endpoints

### 1. List Rooms
`GET /api/rooms`
**Features:** Returns all meeting rooms. Each room includes a nested `bookings` array with all its **future** scheduled meetings.

### 2. Create Booking
`POST /api/bookings`
Payload:
```json
{
    "room_id": 1,
    "uid": "john_doe",
    "start_time": "2026-05-10 10:00:00",
    "end_time": "2026-05-10 11:00:00"
}
```

### 3. List Bookings (with filters)
`GET /api/bookings?uid=john_doe` — View bookings for a specific user.
`GET /api/bookings?room_id=1` — View all bookings for a specific room.

## Engineering Standards & Features
- **Race Condition Prevention:** Uses database transactions and `lockForUpdate()` during booking creation to prevent double-booking at the exact same millisecond.
- **Overlap Protection:** Robust logic ensures no time-slot overlaps for any room.
- **Automated Testing:** 7 comprehensive Feature tests covering edge cases, boundary conditions, and validation.
- **CI/CD:** GitHub Actions workflow configured to run tests automatically on every push.
- **Code Quality:** 
    - **Laravel Pint:** Code style strictly adheres to Laravel standards.
    - **Form Requests:** Validation logic is encapsulated for cleaner controllers.
    - **API Resources:** Standardized JSON structure and data transformation.
- **Performance:** Database indexes on `room_id`, `uid`, and time slots for fast lookups.
