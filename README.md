# Handball League Management System

A web application for managing a local handball league, implemented as a single-page application (SPA) using PHP, JavaScript, and MySQL.

## Project Overview

The Handball League Management System ("Rukometni Centar") provides functionality to manage all aspects of a local handball league, including teams, players, matches, venues, and standings. The system supports two user roles (admin and regular user) with appropriate access control.

### Technology Stack

- *Frontend*: HTML, CSS, JavaScript, Bootstrap
- *Backend*: PHP with FlightPHP framework
- *Database*: MySQL with PHP PDO
- *Libraries*: jQuery, jQuery SPA framework

## Features

- User authentication and authorization with JWT tokens
- Team management with logos and details
- Player registration and management
- Match scheduling and results tracking
- Venue management
- Player statistics tracking
- League standings and team rankings
- Mobile-friendly responsive design

## Project Structure


handball-league-management/
├── backend/
│   ├── routes/       # API route definitions
│   ├── services/     # Business logic layer
│   ├── dao/          # Data access objects
│   ├── config.php    # Configuration
│   └── index.php     # Entry point
├── frontend/
│   ├── css/          # Stylesheets
│   ├── js/           # JavaScript files
│   ├── templates/    # HTML templates
│   ├── assets/       # Images and other assets
│   └── index.html    # SPA main page
└── README.md


## Database Schema

The application uses a MySQL database with the following entities:

1. *Users* - System users with roles (admin/regular)
2. *Teams* - Handball teams participating in the league
3. *Players* - Players registered with teams
4. *Venues* - Locations where matches are played
5. *Matches* - Scheduled and completed games
6. *Statistics* - Player performance statistics

## Current Status (Milestone 1)

- Project structure set up
- Static frontend implemented as a single-page application
- Database schema designed
- Pages implemented:
  - Home
  - Teams
  - Players
  - Matches
  - Venues
  - Standings
  - Login/Register

## Upcoming Milestones

- *Milestone 2*: Database creation and DAO layer implementation
- *Milestone 3*: Business logic implementation and API documentation
- *Milestone 4*: Authentication, middleware, and role-based access
- *Milestone 5*: Final deployment and frontend MVC implementation

## Installation and Setup

Coming in future milestones

## Usage

Coming in future milestones

## Author
Isa Selimović
