# Movie Reservation System

Exercise proposed by [https://roadmap.sh/projects/movie-reservation-system](https://roadmap.sh/projects/movie-reservation-system)

## Tech Stack

- PHP 8.2
- Symfony 6.4
- Angular 17.3
- PostgreSQL 14.3
- Valkey 7.2
- Centrifugo 6.0
- Docker

## API methods

### All users

```bash
POST /api/v1/auth/login
POST /api/v1/auth/register
POST /api/v1/auth/validate-token (Check if the JWT is valid)
```

### Users

```bash
GET /api/v1/movies/upcoming/[?page=1]
GET /api/v1/movies/upcoming/:id - Get a list of showtimes along with his theater
GET /api/v1/books/showtimes/:id - Get the available seats of the selected showtime
GET /api/v1/books/:showtimeid/get-centrifugo-token - Get the token and the channelId for the WebSocket connection.
PUT /api/v1/books/showtimes/:showtimeId/seats/:seatId - Change the seat status to occupied/available (temporarily saved in Valkey, expires in 6 minutes).
POST /api/v1/books/showtimes/:showtimeId/hold - Prepare seats to be bought (saved again in valkey, reset the timer to 6 min)
POST /api/v1/books/showtimes/:showtimeId/pay - Tickets bought, saved in Postgres.
GET /api/v1/books - Get all my reservations.
GET /api/v1/books/:bookId - Get the detail of my reservation.
POST /api/v1/books/:bookId/cancel - Cancel reservation (only future reservations).
```

### Admin

```bash
GET /api/v1/genres

GET /api/v1/movies
GET /api/v1/movies/{id}
POST /api/v1/movies
PUT /api/v1/movies/{id}
DELETE /api/v1/movies/{id}
GET /api/v1/movies/:movieId/showtimes - Get the showtimes of a movie
POST /api/v1/movies/:movieId/showtimes - Add a showtime to a movie
DELETE /api/v1/movies/showtimes/:showtimeId - Delete the showtime only if it doesnt have any tickets bought.
GET /api/v1/reports/revenue - Get the revenue grouped by month
GET /api/v1/reports/revenue-by-month[?date=2025-01-01] - Get the revenue by month, grouped by movie.
  
GET /api/v1/theaters
GET /api/v1/theaters/{id}
GET /api/v1/theaters/{id}/unavailable-dates - Get the dates that are already occupied by other showtimes.
POST /api/v1/theaters
DELETE /api/v1/theaters/{id}
  
GET /api/v1/users/users-and-roles - Get all current users and all the available roles.
PUT /api/v1/users/users/{userId}/role - Update the user role.
PUT /api/v1/users/users/{userId}/roles
```

## Run project

### Configure environment variables

In the backend-movie-reservation/ folder, create a .env file with the following content:

```bash
APP_ENV=dev
APP_SECRET=dba44c25faa9ad26821f3d434b824968

JWT_SECRET=00000000000000000000000000000000
JWT_EXPIRATION=3600

DATABASE_URL="postgresql://USER:PASSWORD@localhost:5432/DATABASE?serverVersion=16&charset=utf8"

CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'

REDIS_URL="redis://localhost"

CENTRIFUGO_CLIENT_TOKEN_KEY="bbe7d157-a253-4094-9759-06a8236543f9"
CENTRIFUGO_HTTPAPI_KEY="d7627bb6-2292-4911-82e1-615c0ed3eebb"
CENTRIFUGO_URL="localhost:8000"
```

### Run project (development mode)

```bash
docker compose up
backend-movie-reservation/bin/rr serve -c .rr.dev.yaml
cd ./backend-movie-reservation
ng serve
```

The API will be running on port 8080
The frontend will be running on port 4200
Centrifugo will be running on port 8000
Valkey will be running on port 6379
PostgreSQL will be running on port 5432

### Overview

![Theaters](docs/create-theater.png)
You should create a Theater before adding a Showtime to a Movie.

![Showtimes](docs/book-showtime.png)
When you are on the seat selection page, a WebSocket connection is established, so if someone is booking a seat, it will reduce the chance of booking the same seat.

![ERD](docs/erd.png)

![Services](docs/services.png)
