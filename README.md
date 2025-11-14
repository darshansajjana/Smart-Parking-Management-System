# Smart Parking Management System  
A MySQL & PHP-Based Automated Parking Solution

## Overview  
The **Smart Parking Management System** automates parking operations within a campus or organization.  
It removes manual errors, ensures efficient parking space utilization, and provides real-time tracking of vehicles, tickets, payments, and revenue.  

This project uses **PHP (frontend + backend)** and **MySQL** with stored procedures, triggers, and functions for smart database automation.

---

##  Problem Statement  
Manual parking systems face issues like:  
- Incorrect fee calculation  
- Double-assignment of parking spaces  
- Inaccurate tracking of vehicles  
- No real-time occupancy visibility  
- Limited reporting  

---

## Features

### Ticket Management  
- Generate parking tickets  
- Record entry & exit times  
- Auto-calculate duration & fee  
- Release slot on ticket closure  

### Parking Space Management  
- Add/update/delete parking spaces  
- Real-time “Free / Occupied” status  
- Prevent double-allocation of slots  

### Billing & Payments  
- Hourly rate per vehicle type  
- EV discount support  
- Store payment history  
- Total revenue tracking  

### Smart Database Logic  
- MySQL Stored Procedures  
- MySQL Functions  
- MySQL Triggers  
- Auto validations & constraints  

### Admin Dashboard  
- Manage lots, spaces, staff  
- View revenue reports  
- View occupancy data  
- Create & close tickets  

### User Dashboard  
- My Vehicles  
- Reservations  
- Fee calculator  
- Payment history  

### 🔹 MySQL Logic Automation  
- **Stored Procedures:**  
  - Create_Ticket  
  - Close_Ticket  
- **Functions:**  
  - Calculate_Fee  
  - IsVehicleParked  
- **Triggers:**  
  - Auto-update space status  
  - Validate parking slot  
  - Log ticket operations

---

# Smart Parking System - Requirements

# PHP Version
php >= 7.4

# Web Server
Apache HTTP Server (recommended: via XAMPP)

# Database
MySQL Server 8.x

# Development Tools
MySQL Workbench
VS Code

# Optional Tools
Git
Draw.io

