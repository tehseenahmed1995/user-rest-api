Prerequisite: PHP version 8.1 & MySQl version 5.7

Project Setup:

```
1\. clone the repo to a local directory
            2\. create database locally and import the database structure
            3\. update database credentials in config/Db.php file
            4\. check PHP is installed and accussiable globbaly in terminal by "php -v" command
            5\. move the control inside project directory and run "php -S localhost:8888 -t public"
            6\. now application is accessable on "localhost:8888"
```

How to insert data in DB?

```
1\. import POSTMAN collection in POSTMAN
            2\. hit "add 500 users fake records" end-point it will create 500 users records
            2\. hit "add 100000 locations and transactions fake records" end-point it will create 100K records in    transactions and location table ( make sure to set max_execution_time = 500 in php.ini to avoid fatel error as we need to insert 100K records in DB, normally these type of bulky operations are executed through command line to avoid maximum execution time out error)
```
