# Geofencing System Test Guide

## Overview
This guide shows how to test the enhanced geofencing system that calculates proximity between buses and students, and sends notifications to parents when buses enter their children's geofencing areas.

## System Components

### 1. Enhanced Geofencing Service (`app/Services/EnhancedGeofencingService.php`)
- Calculates distance between bus and student pickup locations
- Checks if bus is within student's geofence radius
- Sends notifications to parents using NotificationController logic
- Supports FCM push notifications

### 2. Updated Geofencing Controller (`app/Http/Controllers/GeofencingController.php`)
- `checkProximity()` - Check proximity with provided coordinates
- `checkBusLocation()` - Update bus location and check proximity
- Integrated with enhanced geofencing service

### 3. Notification Controller (`app/Http/Controllers/NotificationController.php`)
- `addGeofencingNotification()` - Manually add geofencing notifications
- `getStudentNotifications()` - Get notifications for a specific student
- `history()` - Get all notifications for a student

## Test Data Available

Based on the seeders, you have these test students:
- **Student ID 1**: Mohamed Essam (Grade: six) - Pickup: 24.7136, 46.6753
- **Student ID 2**: Ahmed Yussef (Grade: seven) - Pickup: 24.7137, 46.6754

## API Endpoints for Testing

### 1. Check Bus Proximity (Real-time)
**URL:** `POST /api/bus/{busId}/check-proximity`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**
```json
{
    "latitude": 24.7135,
    "longitude": 46.6752
}
```

**Expected Response:**
```json
{
    "status": "success",
    "message": "Proximity check completed",
    "notifications_sent": 1,
    "total_students": 2,
    "results": [
        {
            "student_id": 1,
            "student_name": "Mohamed Essam",
            "distance": 45.23,
            "geofence_radius": 100,
            "is_within_geofence": true,
            "notification_sent": true,
            "pickup_location": {
                "latitude": 24.7136,
                "longitude": 46.6753,
                "address": "Qanat Elswes"
            }
        },
        {
            "student_id": 2,
            "student_name": "Ahmed Yussef",
            "distance": 120.45,
            "geofence_radius": 100,
            "is_within_geofence": false,
            "notification_sent": false,
            "pickup_location": {
                "latitude": 24.7137,
                "longitude": 46.6754,
                "address": "Qanat Elswes"
            }
        }
    ]
}
```

### 2. Update Bus Location and Check Proximity
**URL:** `POST /api/geofencing/check-bus-location`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**
```json
{
    "bus_id": 1,
    "latitude": 24.7135,
    "longitude": 46.6752
}
```

### 3. Manually Add Geofencing Notification
**URL:** `POST /api/notifications/geofencing`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**
```json
{
    "student_id": 1,
    "title": "Bus Approaching Your Location",
    "body": "Your bus is approaching your child's pickup location"
}
```

### 4. Get Student Notifications
**URL:** `GET /api/notifications/student?student_id=1`

**Headers:**
```
Accept: application/json
```

**Expected Response:**
```json
{
    "status": "success",
    "student": {
        "student_id": 1,
        "student_name": "Mohamed Essam"
    },
    "notifications": [
        {
            "id": 1,
            "title": "Bus Approaching Your Location",
            "body": "Your bus is approaching your child's pickup location",
            "model_type": "Geofencing",
            "read_at": null,
            "date": "2024-01-15",
            "time": "08:30:00",
            "student_id": 1,
            "student_name": "Mohamed Essam"
        }
    ],
    "model_types": {
        "Geofencing": "Geofencing"
    },
    "messages": {
        "Geofencing": {
            "title": "Geofencing",
            "body": "Geofencing notification"
        }
    }
}
```

### 5. Get Notification History
**URL:** `GET /api/notifications/history?student_id=1`

## Testing Scenarios

### Scenario 1: Bus Within Geofence (Should Send Notification)
**Test Coordinates:** 24.7135, 46.6752 (45 meters from Student 1)
**Expected Result:** Notification sent to parent of Student 1

### Scenario 2: Bus Outside Geofence (No Notification)
**Test Coordinates:** 24.7145, 46.6765 (150 meters from Student 1)
**Expected Result:** No notification sent

### Scenario 3: Multiple Students on Same Bus
**Test Coordinates:** 24.7136, 46.6753 (exactly at Student 1's location)
**Expected Result:** Notification sent to Student 1's parent, Student 2 might also get notification if within radius

### Scenario 4: Invalid Bus ID
**Test:** Use non-existent bus ID
**Expected Result:** Error response with "Bus not found"

### Scenario 5: Missing Coordinates
**Test:** Send request without latitude/longitude
**Expected Result:** Validation error

## Postman Collection Setup

### 1. Environment Variables
Set these variables in your Postman environment:
- `base_url`: `http://your-domain.com/api`
- `bus_id`: `1`
- `student_id`: `1`

### 2. Test Requests

#### Request 1: Check Proximity (Within Geofence)
```
Method: POST
URL: {{base_url}}/bus/{{bus_id}}/check-proximity
Body: {
    "latitude": 24.7135,
    "longitude": 46.6752
}
```

#### Request 2: Check Proximity (Outside Geofence)
```
Method: POST
URL: {{base_url}}/bus/{{bus_id}}/check-proximity
Body: {
    "latitude": 24.7145,
    "longitude": 46.6765
}
```

#### Request 3: Update Bus Location
```
Method: POST
URL: {{base_url}}/geofencing/check-bus-location
Body: {
    "bus_id": {{bus_id}},
    "latitude": 24.7135,
    "longitude": 46.6752
}
```

#### Request 4: Get Student Notifications
```
Method: GET
URL: {{base_url}}/notifications/student?student_id={{student_id}}
```

## Database Verification

After running tests, verify notifications are stored in the `notifications` table:

```sql
SELECT * FROM notifications WHERE student_id = 1 ORDER BY created_at DESC;
```

Expected fields:
- `title`: "Bus Approaching Your Location"
- `body`: Contains bus and student information
- `model_type`: "Geofencing"
- `student_id`: Student ID
- `read_at`: null (unread)

## FCM Push Notifications

If parents have FCM tokens stored, the system will also send push notifications. Check the logs for FCM responses:

```bash
tail -f storage/logs/laravel.log | grep "FCM notification"
```

## Troubleshooting

### Common Issues:

1. **"Bus not found" Error**
   - Verify bus ID exists in database
   - Check if bus has students assigned

2. **"Student not found" Error**
   - Verify student ID exists
   - Check if student has a parent assigned

3. **No Notifications Sent**
   - Check if bus coordinates are within geofence radius
   - Verify parent exists for student
   - Check notification logs

4. **Distance Calculation Issues**
   - Verify coordinate format (decimal degrees)
   - Check if coordinates are valid

### Debug Logs:
Check Laravel logs for detailed information:
```bash
tail -f storage/logs/laravel.log
```

## Performance Considerations

- The system calculates distance for all students assigned to a bus
- Consider implementing caching for frequently accessed data
- Monitor database performance with large numbers of students
- Consider implementing rate limiting for API endpoints 