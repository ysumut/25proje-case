# 25project Case Rest API
* [Deployment Link](http://ec2-52-91-46-0.compute-1.amazonaws.com)
* [Postman Collection](https://www.getpostman.com/collections/12f6bfaa02c1ced11076)

## Used in this project:
* Laravel 8
* Mysql

## Available Scripts To Run In Order
* Firstly, create a KargaKarga mysql database and create .env file from .env.example

```bash 
$ cp .env.example .env
```
```bash 
$ composer install
$ composer update # if you don't use php 8, run this command
```
```bash 
$ php artisan key:generate
```
```bash 
$ php artisan serve
```
