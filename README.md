# Library Management System

The **Library Management System** is a RESTful web service built with PHP and MySQL that allows users to borrow books and return it. As well as rating books by users.

## Table of Contents

-   [Library Management System](#library-management-system)
    -   [Table of Contents](#table-of-contents)
    -   [Features](#features)
    -   [Getting Started](#getting-started)
        -   [Prerequisites](#prerequisites)
        -   [Installation](#installation)
        -   [Postman Test](#postman-test)

## Features

1. Books

-   Add new books to the library
-   Retrieve details of a specific book or all books
-   Update book information
-   Delete books from the library
-   Search for books by author and available books (not borrowed)
-   Get ratings average to each book

2. Ratings

-   Add new ratings to the books
-   Retrieve details of a specific book or all books
-   Update book information
-   Delete books from the library

3. Borrow Records

-   Add new borrow records to the books
-   Retrieve details of a specific borrow record or all borrow records
-   Update borrow record information
-   Delete borrow records from the books
-   Return books after borrowed

4. Authorization

-   Registration for new user
-   Login user
-   Logout user
-   Update user profile
-   Delete user account

## Getting Started

These instructions will help you set up and run the Library Management System on your local machine for development and testing purposes.

### Prerequisites

-   **PHP** (version 7.4 or later)
-   **MySQL** (version 5.7 or later)
-   **Apache** or **Nginx** web server
-   **Composer** (PHP dependency manager, if you are using any PHP libraries)

### Installation

1. **Clone the repository**:

    ```
    git clone https://github.com/osama806/Library-Management-System.git
    cd Library-Management-System
    ```

2. **Set up the environment variables:**:

Create a .env file in the root directory and add your database configuration:

```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=library-management-system
DB_USERNAME=root
DB_PASSWORD=password
```

3. **Set up the MySQL database:**:

-   Create a new database in MySQL:
    ```
    CREATE DATABASE library-management-system;
    ```
-   Run the provided SQL script to create the necessary tables:
    ```
    mysql -u root -p library-management-system < database/schema.sql
    ```

4. **Configure the server**:

-   Ensure your web server (Apache or Nginx) is configured to serve PHP files.
-   Place the project in the appropriate directory (e.g., /var/www/html for Apache on Linux).

5. **Install dependencies (if using Composer)**:

```
composer install
```

6. **Start the server:**:

-   For Apache or Nginx, ensure the server is running.
-   The API will be accessible at http://localhost/library-management-system.

### Postman Test

-   Link:
    ```
    https://documenter.getpostman.com/view/32954091/2sAXjT1pMm
    ```
