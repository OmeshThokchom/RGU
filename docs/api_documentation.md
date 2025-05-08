# RGU Portal API Documentation

## Overview
This document outlines the internal API endpoints used in the RGU Portal system. These endpoints facilitate communication between the frontend and backend components.

## Authentication Endpoints

### Login
```http
POST /admin/ajax_handlers.php
Content-Type: application/json

{
    "action": "login",
    "username": "string",
    "password": "string"
}
```

**Response**
```json
{
    "status": "success|error",
    "message": "string",
    "token": "string"    // Only on success
}
```

## Department Management

### List Departments
```http
GET /admin/ajax_handlers.php?action=list&type=departments
```

**Response**
```json
{
    "status": "success",
    "data": [
        {
            "dept_id": "integer",
            "dept_name": "string",
            "dept_code": "string",
            "hod_name": "string",
            "student_count": "integer",
            "faculty_count": "integer"
        }
    ]
}
```

### Create Department
```http
POST /admin/ajax_handlers.php
Content-Type: application/json

{
    "action": "create",
    "type": "department",
    "data": {
        "dept_name": "string",
        "dept_code": "string",
        "description": "string",
        "hod_name": "string"
    }
}
```

## Student Management

### Search Students
```http
GET /admin/ajax_handlers.php?action=search&type=students&query=string
```

**Response**
```json
{
    "status": "success",
    "data": [
        {
            "student_id": "integer",
            "roll_number": "string",
            "first_name": "string",
            "last_name": "string",
            "dept_name": "string",
            "semester": "integer"
        }
    ]
}
```

### Update Student
```http
POST /admin/ajax_handlers.php
Content-Type: application/json

{
    "action": "update",
    "type": "student",
    "id": "integer",
    "data": {
        "first_name": "string",
        "last_name": "string",
        "dept_id": "integer",
        "semester": "integer",
        "email": "string",
        "phone": "string"
    }
}
```

## Faculty Management

### List Faculty
```http
GET /admin/ajax_handlers.php?action=list&type=faculty&dept=string
```

**Response**
```json
{
    "status": "success",
    "data": [
        {
            "faculty_id": "integer",
            "first_name": "string",
            "last_name": "string",
            "designation": "string",
            "dept_name": "string",
            "qualification": "string"
        }
    ]
}
```

## Notice Board

### Create Notice
```http
POST /admin/ajax_handlers.php
Content-Type: application/json

{
    "action": "create",
    "type": "notice",
    "data": {
        "title": "string",
        "content": "string",
        "posted_date": "date",
        "expiry_date": "date",
        "important": "boolean"
    }
}
```

## Events Management

### List Events
```http
GET /admin/ajax_handlers.php?action=list&type=events&month=integer&year=integer
```

**Response**
```json
{
    "status": "success",
    "data": [
        {
            "event_id": "integer",
            "title": "string",
            "description": "string",
            "event_date": "date",
            "venue": "string",
            "organizer": "string"
        }
    ]
}
```

## Error Handling

All endpoints follow a consistent error response format:

```json
{
    "status": "error",
    "message": "Error description",
    "code": "error_code"
}
```

Common error codes:
- `auth_failed`: Authentication failed
- `invalid_input`: Invalid input parameters
- `not_found`: Resource not found
- `db_error`: Database error
- `validation_error`: Data validation failed

## Rate Limiting

API endpoints are rate-limited to prevent abuse:
- 60 requests per minute for authenticated users
- 30 requests per minute for unauthenticated users

## CORS Policy

The API follows a strict CORS policy:
- Only allows requests from trusted domains
- Requires proper headers for pre-flight requests
- Supports secure cross-origin resource sharing