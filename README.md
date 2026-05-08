# Meeting Room Booking API

## Setup
1. `composer install`
2. `cp .env.example .env`
3. `php artisan key:generate`
4. `touch database/database.sqlite`
5. `php artisan migrate --seed`
6. `php artisan serve`

## API Endpoints

### 1. List Rooms
`GET /api/rooms` — Returns all available meeting rooms with their IDs.

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
`GET /api/bookings?uid=john_doe`
`GET /api/bookings?room_id=1`

## Engineering Standards & Features
- **Race Condition Prevention:** Uses database transactions and `lockForUpdate()` to ensure atomicity and prevent double-booking during concurrent requests.
- **Overlap Protection:** Robust logic to prevent any time-slot overlaps for the same room.
- **Automated Testing:** Comprehensive Feature tests covering edge cases, boundary conditions, and validation.
- **CI/CD:** GitHub Actions workflow configured to run tests automatically on push.
- **Code Quality:** 
    - **Laravel Pint:** Code style strictly follows Laravel standards.
    - **Form Requests:** Validation logic encapsulated in dedicated Request classes for clean controllers.
    - **API Resources:** Standardized JSON responses using Laravel Resources.
- **Performance:** Optimized database indexes for frequent filtering and availability checks.
