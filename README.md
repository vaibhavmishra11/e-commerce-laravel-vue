
# e-commerce-laravel-vue

# E-Commerce Platform

This project is an e-commerce platform with features for sellers and buyers. It includes functionalities for managing products, purchases, and user profiles with all proper functionalities and use Razorpay for online payment.

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [API Documentation](#api-documentation)
- [Demo](#demo)
- [Contributing](#contributing)
- [License](#license)

## Introduction

The E-Commerce Platform is designed to facilitate the interaction between sellers and buyers. Sellers can manage products and view sales analytics, while buyers can browse products, make purchases, and view order history.

## Features

### Seller Features:

1. Seller Login and Signup
2. Product Management (Create, Edit, Deactivate, Activate, Mark Out-of-Stock)
3. Upload CSV file to upload the bulk data 
4. View Sold Products by Date Filter and sorting with all parameters(name , date ,quantity)
5. View Top-Selling Products and sorting with all parameters(name , date ,quantity)
6. View Purchase History and sorting with all parameters(buyer , date ,quantity,amount)
7. Manage Payment from Razorpay
### Buyer Features:

1. Buyer Login and Signup
2. Browse Products with Pagination, Sorting, and Searching
3. Add item to the cart
4. Purchase Products with RazorPay Integration
5. Cancel Orders within 3 Days
6. View Purchase History and Wallet Balance

## Prerequisites

- PHP
- Laravel
- Composer
- Node.js
- npm
- Vue.js
- MySQL
- Razorpay Account (RAZORPAY_API_KEY , RAZORPAY_API_SECRET)
## Installation

```bash
git clone https://github.com/vaibhavmishra11/e-commerce-laravel-vue.git
cd <project-directory>
npm install
composer install
php artisan key:generate
php artisan migrate
```

## Configuration
Create a .env file based on the provided .env.example.
Update database configurations in the .env file.
Usage

### Start the Laravel server: 
```bash
php artisan serve
```
Start the Vue.js development server:
```bash
npm run dev
```
Access the application at
```bash
http://localhost:8000
http://localhost:3000
```

## API Reference

[Click here for Postman API Documentatioin](https://documenter.getpostman.com/view/32201798/2s9YsNeVrJ)
