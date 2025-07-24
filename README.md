## Running the Project with Docker Compose

1. **Create the .env file**

In the project root, create a `.env` file (or copy from `.env.example`) and set the database values as follows:

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

2. **Build and start the containers**

```bash
docker-compose up --build -d
```

3. **Run the migrations (create database tables):**

```bash
docker-compose exec app php artisan migrate
```

4. **View the application:**

Open your browser and go to:

```
http://localhost:8080
```

---

### Notes:
- If this is your first time running the project, make sure to run the migrations.
- To view logs:
  ```bash
  docker-compose logs -f
  ```
- To stop the services:
  ```bash
  docker-compose down
  ```
