# Meeting Room Booking API

## Setup
1. `composer install`
2. `cp .env.example .env`
3. `touch database/database.sqlite`
4. `php artisan migrate --seed`
5. `php artisan serve`

## API Endpoints

### 1. List Rooms
`GET /api/rooms`

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

## Correctness Features
- **Overlap Protection:** The system prevents booking a room that is already occupied during the requested time slot.
- **Validation:** Ensures `end_time` is after `start_time` and `start_time` is in the future.
- **Database Indexes:** Optimized for filtering by `uid` and checking room availability.
- **Resources:** Structured JSON responses using Laravel API Resources.
- **Tests:** Includes Feature tests for core business logic.
