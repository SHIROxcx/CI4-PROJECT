# Equipment Availability Per Date - Database Storage Implementation

## Overview

Equipment availability is now **SAVED to the database** per date instead of being calculated on-the-fly. This provides persistent, historical tracking and better performance.

## Database Tables

### 1. **equipment_schedule** (NEW)

```sql
CREATE TABLE equipment_schedule (
  id INT PRIMARY KEY AUTO_INCREMENT,
  equipment_id INT NOT NULL,
  event_date DATE NOT NULL,
  total_quantity INT DEFAULT 0,       -- Total available on this date
  booked_quantity INT DEFAULT 0,      -- Quantity already booked
  available_quantity INT DEFAULT 0,   -- total - booked
  created_at DATETIME,
  updated_at DATETIME,
  UNIQUE KEY unique_equipment_date (equipment_id, event_date),
  FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
);
```

### 2. **booking_equipment** (EXISTING - No Change)

```sql
CREATE TABLE booking_equipment (
  id INT PRIMARY KEY AUTO_INCREMENT,
  booking_id INT NOT NULL,
  equipment_id INT NOT NULL,
  quantity INT NOT NULL,
  rate DECIMAL(10,2) NOT NULL,
  total_cost DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  FOREIGN KEY (equipment_id) REFERENCES equipment(id)
);
```

## Data Flow

### When User Books Equipment:

```
Step 1: User selects date Nov 30
    ↓
Step 2: Frontend calls checkEquipmentAvailabilityOnDate()
    ↓
Step 3: API calls equipmentAvailability endpoint
    ↓
Step 4: Check equipment_schedule table for Nov 30
    ├─ If exists: Return available_quantity
    └─ If not exists: Create new schedule with full inventory
    ↓
Step 5: Display available quantities to user
    ↓
Step 6: User confirms booking
    ↓
Step 7: createBooking() is called
    ├─ Insert into booking_equipment (quantity booked)
    └─ Update equipment_schedule (booked_qty + available_qty)
    ↓
Step 8: Equipment_schedule table is now PERSISTED with new availability
```

## Models

### EquipmentScheduleModel (NEW)

```php
// Get or create schedule for a date
$schedule = $equipmentScheduleModel->getOrCreateSchedule($equipmentId, $eventDate);

// Update when equipment is booked
$equipmentScheduleModel->updateBookedQuantity($equipmentId, $eventDate, $quantity);

// Get available quantity
$available = $equipmentScheduleModel->getAvailableQuantity($equipmentId, $eventDate);
```

## Example Scenario

**Scenario:** Gymnasium has 10 chairs total

### Nov 29:

```
equipment_schedule (Nov 29):
- equipment_id: 5 (Chair)
- total_quantity: 10
- booked_quantity: 0
- available_quantity: 10
```

### User A books 3 chairs for Nov 29:

```
booking_equipment:
- booking_id: 100
- equipment_id: 5
- quantity: 3

equipment_schedule (Nov 29) UPDATED:
- equipment_id: 5
- total_quantity: 10
- booked_quantity: 3        ← Updated
- available_quantity: 7     ← Updated
```

### User B selects Nov 29:

```
API returns: available_quantity = 7 (from equipment_schedule)
User sees: "Available: 7"
```

### Nov 30 (Different Date):

```
equipment_schedule (Nov 30):
- equipment_id: 5
- total_quantity: 10        ← Same (different date)
- booked_quantity: 0        ← Reset
- available_quantity: 10    ← Reset
```

## Key Differences

| Aspect            | OLD (Calculated)          | NEW (Database)              |
| ----------------- | ------------------------- | --------------------------- |
| Storage           | Calculated on-the-fly     | Saved in equipment_schedule |
| Persistence       | Temporary                 | Permanent ✅                |
| Speed             | Recalculate every request | Direct lookup               |
| History           | No history                | Full audit trail            |
| Per-Date Tracking | Yes                       | Yes + Database Backup       |
| Scalability       | Slower with many bookings | Faster with indexing        |

## Migration Steps

1. Run migration:

```bash
php spark migrate
```

This creates the `equipment_schedule` table automatically.

2. The system will auto-create schedules when:
   - User selects a date (if not exists)
   - Booking is saved (updates booked quantity)

## Benefits

✅ **Persistent Data** - Equipment availability is saved per date  
✅ **Audit Trail** - Track what was available when  
✅ **Better Performance** - No complex calculations on every request  
✅ **Reporting** - Easy to query historical availability  
✅ **Reliable** - Database handles consistency  
✅ **Scalable** - Indexed lookups instead of aggregations

## API Endpoint

```
POST /api/bookings/equipment-availability

Request:
{
  "event_date": "2025-11-30",
  "facility_id": 1
}

Response:
{
  "success": true,
  "event_date": "2025-11-30",
  "equipment_availability": {
    "1": 7,    // Chair: 7 available
    "2": 5,    // Table: 5 available
    "3": 10    // Speaker: 10 available
  }
}
```

## Database Query Examples

```sql
-- Get availability for a specific equipment on a date
SELECT available_quantity
FROM equipment_schedule
WHERE equipment_id = 5 AND event_date = '2025-11-30';

-- Get all bookings for a date
SELECT e.name, es.total_quantity, es.booked_quantity, es.available_quantity
FROM equipment_schedule es
JOIN equipment e ON e.id = es.equipment_id
WHERE es.event_date = '2025-11-30'
ORDER BY e.name;

-- Track availability changes over time
SELECT es.event_date, es.booked_quantity, es.available_quantity, es.updated_at
FROM equipment_schedule es
WHERE es.equipment_id = 5
ORDER BY es.event_date DESC;
```
