# Task Tracker Web Application

## Purpose

This is a web application designed to help users manage and track their tasks efficiently. Whether you're working on personal projects or collaborating with a team, this task tracker helps you organize your work, set priorities, and keep track of your progress all in one place.

## Team Members

- CHOW KAI JIE BAI_A2009F-2509001
- CHEW SEE YUAN BIT_A2201F-2509002
- GUO RUNTING BDS_B2201F-2505001

## Team Members Role

| Role | Team Member | Responsibilities |
|------|-------------|------------------|
| Product Owner & Scrum Master | CHOW KAI JIE | Identify the purpose of application, document README file, ,build the application structure, decide the languages, database and server use, plan for the iteration and guide team member about GitHub |
| Developer 1 | CHEW SEE YUAN | Develop application function, create task, edit task, delete task |
| Developer 2 | GUO RUNTING | Develop application UIUX, task listing |

## Technologies Used

The project is built using the following technologies:

- **HTML** - Structure and markup
- **CSS** - Styling and layout
- **JavaScript** - Client-side interactivity
- **PHP** - Server-side logic and backend
- **MySQL** - Database management (via XAMPP)

## Quick Start Guide

### Prerequisites

1. Install [XAMPP](https://www.apachefriends.org/) on your machine
2. Have Git installed for cloning the repository

### Setup Steps

1. **Install XAMPP**
   - Download and install XAMPP from the official website
   - Start the Apache and MySQL services from the XAMPP Control Panel

2. **Clone the Repository**
   - Open your terminal/command prompt
   - Navigate to the XAMPP `htdocs` folder:
     ```
     cd C:\xampp\htdocs
     ```
   - Clone the project:
     ```
     git clone https://github.com/KJIE-430-XX/Task-Tracker-Development.git
     ```

3. **Setup Database Tables**
   - Open your browser and go to:
     ```
     http://localhost/Task-Tracker-Development/database/setup.php
     ```
   - This will create the database and all necessary tables automatically
   - You should see a success message confirming the database setup

4. **Access the Application**
   - Open your browser and go to:
     ```
     http://localhost/Task-Tracker-Development/
     ```
   - You're all set! Start creating and tracking your tasks

### Repository

- **GitHub**: [https://github.com/KJIE-430-XX/Task-Tracker-Development.git](https://github.com/KJIE-430-XX/Task-Tracker-Development.git)

## Development Setup

This project uses **XAMPP** to provide a local development environment with Apache, PHP, and MySQL support.

### Prerequisites

- XAMPP installed on your machine
- A code editor (VS Code, etc.)

### Getting Started

1. Clone or download this project into the `htdocs` folder of your XAMPP installation
2. Start XAMPP (Apache and MySQL modules)
3. Navigate to `http://localhost/Task-Tracker-Development/` in your browser
4. Start tracking your tasks!

## Project Structure

```
Task-Tracker-Development/
├── public/          # Publicly accessible files (login, register, dashboard, etc.)
├── src/             # Source code and utilities
├── database/        # Database schema
├── docs/            # Documentation
└── tests/           # Test files
```

## SCRUM Development Iterations

### Iteration 1: Week 1 (18/5 - 22/5)
**Setup Project**
- Create GitHub Repo
- Download and install XAMPP
- Document README file
- Setup Application Structure

### Iteration 2: Week 2 (25/5 - 29/5)
**CRUD Features**
- Add, Edit, Delete Task
- UI/UX Design
- Testing and Bugs Fixing

### Iteration 3: Week 3 (1/6 - 5/6)
**Deployment**
- Run Full Application to avoid bugs

## Features
**Core Feature 1**
Task Management
- Create Task
- Edit Task
- Delete Task
- View Task List

**Supporting Feature**
User Authentication
- Login
- Register

---
