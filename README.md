# Anagram Finder

A full-stack anagram finder application built as part of a developer technical assignment.

- **Backend:** PHP 8.2 (Symfony 7)
- **Frontend:** React 19 (Vite)
- **Database:** MySQL 8.4 (dockerized)
- **Architecture:** Fully Dockerized — no host-side PHP, Node or MySQL installation required.

### Features

- Imports and saves external wordbase (https://www.opus.ee/lemmad2013.txt)
- Word letters are sorted and stored on import for efficient database-based anagram search
- REST API endpoints for import and search functionality
- Frontend UI built with React and Axios
- Backend covered with functional tests and unit tests

## Anagram Algorithm Logic

- During import, all words are read line by line
- Each word is normalized by:
  - Converting character encoding (`ISO-8859-1` → `UTF-8`)
  - Sorting the letters alphabetically
- Both original word and its sorted letter representation are stored in database

- During search:
  - The input word is normalized using the same logic as earlier
  - A database lookup is performed using the sorted value to find all anagrams
  - MySQL collation `utf8mb4_bin` is used to ensure strict character sensitivity (i.e. `u` and `ü` are treated as distinct)

**Performance Notes:**

- Sorting letters provides an efficient one-key lookup rather than performing string permutations
- This design allows very fast searches even for large wordbases (~178,000 words)

---

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/) (tested with Docker Desktop on Windows using WSL2)
- [Docker Compose](https://docs.docker.com/compose/install/) (typically bundled with Docker Desktop)


## Quick Start Guide for local setup

The following commands are intended for a Unix-style shell environment such as WSL2 (Windows Subsystem for Linux), native Linux, or MacOS. Docker Desktop for Windows users are recommended to use WSL2 integration.


### Clone the repository

```bash
git clone https://github.com/kjlellep/symfony-anagram.git
cd symfony-anagram
```

### Build and start containers

In project root, run
```bash
docker compose up --build
```

* This builds and starts:

    * Symfony PHP backend (localhost:8000)
    * React frontend (localhost:5173)
    * MySQL database (localhost:3306 inside Docker)

After the very first build on local machine run
```bash
docker compose exec php composer install
```

### Initialize database

Run symfony migrations for default environment and test environment
```bash
docker compose exec php php bin/console doctrine:migrations:migrate
docker compose exec php php bin/console doctrine:migrations:migrate --env=test
```

#### Import wordbase
Navigate to http://localhost:5173/ and click `Import`. Note that the import will take some time since there are nearly 180,000 rows of data.

Alternatively the API import endpoint can be accessed directly via http://localhost:8000/api/import-wordbase, accessing this page will trigger the import as well.

### Run tests
Once the database is initialized, tests can be run using the command
```bash
docker compose exec php php bin/phpunit
```


### API Documentation

---

The API is fully documented using Swagger UI powered by NelmioApiDocBundle.

After starting the Docker containers, you can access the auto-generated interactive API documentation at:

```bash
http://localhost:8000/api/doc
```

    This documentation describes all available API endpoints, input parameters, response formats, and possible error codes.

    The OpenAPI schema is defined directly inside Symfony configuration using NelmioApiDocBundle.

    No external OpenAPI files or PHP annotations are used — documentation is fully maintained inside the Symfony configuration files.


### Useful Docker Commands
---

Stop containers
```bash
docker compose down
```

Run Symfony commands inside container
```bash
docker compose exec php php bin/console <command>
```

Access MySQL CLI inside database container
```bash
docker compose exec db mysql --default-character-set=utf8mb4 -u symfony -p
# Password: symfony
```

### Author
Built by Karl-Jontahan Lellep
