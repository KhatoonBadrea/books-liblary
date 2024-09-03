This project is a Book Library built with Laravel 10 that provides a RESTful APIf for managing books ,It allows users to perform CRUD operations (Create, Read, Update, Delete) on books with the ability to filter books by title or auth or description or published at  or category,or show the books that borrowed or not borrowed  , or apply both filtering and show available books.and allows user to perform CRUD on rating ,and the user can borrowed the book or return it by CRUD opeation on Borrow_Record. The project follows repository design patterns and incorporates clean code and refactoring principles.


Key Features:
CRUD Operations: Create, read, update, and delete book & borrow record & rating in the library.
Filtering : Filter books by all attribuit and show the books that not borrowed
Repository Design Pattern: Implements repositories and services for clean separation of concerns.
Form Requests: Validation is handled by custom form request classes.
API Response Service: Unified responses for API endpoints.

Resources: API responses are formatted using Laravel resources for a consistent structure.



Technologies Used:
Laravel 10
PHP
MySQL
XAMPP (for local development environment)
Composer (PHP dependency manager)
Postman Collection: Contains all API requests for easy testing and interaction with the API.
Installation
Prerequisites
Ensure you have the following installed on your machine:

XAMPP: For running MySQL and Apache servers locally.
Composer: For PHP dependency management.
PHP: Required for running Laravel.
MySQL: Database for the project
Postman: Required for testing the requestes.
Steps to Run the Project
Clone the Repository
git clone https:[//github.com/TukaHeba/Movie_Library.git](https://github.com/KhatoonBadrea/books-liblary)
Navigate to the Project Directory
cd book-library
Install Dependencies
composer install
Create Environment File
cp .env.example .env
Update the .env file with your database configuration (MySQL credentials, database name, etc.).
Generate Application Key
php artisan key:generate
Run Migrations
php artisan migrate
Seed the Database
php artisan db:seed
Run the Application
php artisan serve
Interact with the API and test the various endpoints via Postman collection Get the collection from here: 
