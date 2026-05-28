# Task Tracker Web Application
# Project Management System

## Purpose

This project is an internal web-based project management system designed to help teams efficiently organize and manage projects within a company environment. Users first create a high-level project before creating, assigning, prioritizing, and tracking individual tasks within it. By monitoring task progress through stages such as “To Do,” “In Progress,” and “Done,” the system provides clear project visibility, enhances team collaboration, improves workflow organization, and increases overall productivity.

## Team Members

- CHOW KAI JIE BAI_A2009F-2509001
- CHEW SEE YUAN BIT_A2201F-2509002
- GUO RUNTING BDS_B2201F-2505001

## Team Members Role

| Role | Team Member | Responsibilities |
|------|-------------|------------------|
| Product Owner & Scrum Master | CHOW KAI JIE | Defining the application purpose, documenting the README, designing the system structure, selecting programming languages, database and server, planning development iterations, guiding the team on GitHub workflow, and contributing to ERD design and database structure decisions. |
| Developer 1 | CHEW SEE YUAN | Develop core features including project and task creation, covering both frontend and backend. Handle linking and page redirection between modules to ensure smooth navigation and user experience. |
| Developer 2 | GUO RUNTING | Design the application UI/UX, focusing on styling (CSS) and the task listing interface to ensure a clean, consistent, and user-friendly layout. |

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
   - Open your cmd and go to the folder:
     ```
     C:\xampp\php\php.exe database/setup.php
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
- Install and configure XAMPP
- Setup project folder structure
- Initialize database connection
- Prepare README documentation

### Iteration 2: Week 2 (23/5 - 5/6)
**Create and Read Features**
- Implement Add Project and Task functionality
- Implement Project and Task List display
- Connect frontend with backend logic
- UI design and layout improvement
- Basic testing and bug fixing

## Features
**Core Feature**
Project Management System
- Create and manage projects
- Add members to project

Task Management System
- Create tasks under project
- Set priority, status and deadline to the task
- Assign tasks to project's member

**Supporting Feature**
User Authentication
- Login
- Register

---
