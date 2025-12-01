# DFD Level 0 - Facilities Dynamic Booking System

## System Context Diagram (Level 0 DFD)

```
                    ┌─────────────────────────────────────────────────────────┐
                    │                                                         │
                    │   ┌─────────────────────────────────────────────────┐  │
                    │   │                                                 │  │
                    │   │        FACILITIES BOOKING SYSTEM                │  │
                    │   │                                                 │  │
                    │   │  ┌──────────────────────────────────────────┐  │  │
                    │   │  │                                          │  │  │
                    │   │  │    • Booking Management                  │  │  │
                    │   │  │    • Equipment Tracking                  │  │  │
                    │   │  │    • Facility Inspection                 │  │  │
                    │   │  │    • Report Generation                   │  │  │
                    │   │  │                                          │  │  │
                    │   │  └──────────────────────────────────────────┘  │  │
                    │   │                                                 │  │
                    │   └─────────────────────────────────────────────────┘  │
                    │                                                         │
                    └─────────────────────────────────────────────────────────┘
                                            ▲
                    ┌───────────────────────┼───────────────────────┐
                    │                       │                       │
                    │                       │                       │
         ┌──────────▼─────────┐  ┌─────────▼──────────┐  ┌────────▼─────────┐
         │                    │  │                    │  │                  │
         │    STUDENT USERS   │  │  ADMIN USERS       │  │ FACILITATOR      │
         │                    │  │                    │  │ USERS            │
         │ • Browse Facilities│  │ • Manage Bookings  │  │                  │
         │ • Submit Bookings  │  │ • Manage Students  │  │ • Inspect Events │
         │ • View Schedule    │  │ • Generate Reports │  │ • Check Equipment│
         │ • Track Events     │  │ • Manage Facilities│  │ • Submit Reports │
         │                    │  │ • View Analytics   │  │                  │
         └────────────────────┘  └────────────────────┘  └──────────────────┘
```

---

## Detailed Process Flow

```
                        ┌─────────────────────────────────────────────────────────┐
                        │         EXTERNAL SYSTEMS / DATA SOURCES                  │
                        │                                                         │
                        │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
                        │  │ Facilities   │  │  Equipment   │  │   Events     │  │
                        │  │ Database     │  │  Database    │  │  Database    │  │
                        │  └──────────────┘  └──────────────┘  └──────────────┘  │
                        │                                                         │
                        └────────────────┬────────────────────────────────────────┘
                                         │ (Data Records)
                                         │
        ┌────────────────────────────────▼────────────────────────────────────┐
        │                                                                      │
        │                   MAIN BOOKING SYSTEM PROCESS                       │
        │                                                                      │
        │  ┌──────────────┐                                  ┌──────────────┐ │
        │  │  Booking     │                                  │   Report     │ │
        │  │  Management  │◄────────────────────────────────►│ Generation   │ │
        │  └──────────────┘                                  └──────────────┘ │
        │       ▲   │                                              ▲   │      │
        │       │   │                                              │   │      │
        │  ┌────┴───▼────────────────┐  ┌─────────────────────────┴───▼────┐ │
        │  │                         │  │                                  │ │
        │  │  Equipment Tracking &   │  │  Facilities Inspection &         │ │
        │  │  Management             │  │  Facilitation Verification      │ │
        │  │                         │  │                                  │ │
        │  └─────────────────────────┘  └──────────────────────────────────┘ │
        │                                                                      │
        └────────────────────────────────┬─────────────────────────────────────┘
                                         │
        ┌────────────────────────────────▼─────────────────────────────────────┐
        │                       DATA STORE LAYER                               │
        │                                                                      │
        │  ┌─────────────┐  ┌─────────────┐  ┌──────────────┐  ┌──────────┐ │
        │  │  Bookings   │  │  Equipment  │  │    Events    │  │ Facility │ │
        │  │   Table     │  │    Table    │  │    Table     │  │  Plans   │ │
        │  └─────────────┘  └─────────────┘  └──────────────┘  └──────────┘ │
        │  ┌────────────────────────────────────────────────────────────────┐ │
        │  │              Database (MySQL / PostgreSQL)                      │ │
        │  └────────────────────────────────────────────────────────────────┘ │
        │                                                                      │
        └──────────────────────────────────────────────────────────────────────┘
                                         │
        ┌────────────────────────────────▼─────────────────────────────────────┐
        │              USER INTERFACE & OUTPUT LAYER                           │
        │                                                                      │
        │  ┌──────────────────┐  ┌──────────────────┐  ┌─────────────────┐   │
        │  │  Student Portal  │  │   Admin Panel    │  │  Facilitator    │   │
        │  │                  │  │                  │  │     Portal      │   │
        │  │ • Browse         │  │ • Manage         │  │                 │   │
        │  │ • Book           │  │ • Report         │  │ • Inspect       │   │
        │  │ • Track          │  │ • Analytics      │  │ • Submit Report │   │
        │  │                  │  │                  │  │                 │   │
        │  └──────────────────┘  └──────────────────┘  └─────────────────┘   │
        │         ▲                     ▲                     ▲                │
        │         │                     │                     │                │
        │         └─────────────────────┼─────────────────────┘                │
        │                               │                                      │
        │                  ┌────────────▼────────────┐                        │
        │                  │  API Layer / Controllers │                        │
        │                  │  & Web Services          │                        │
        │                  └──────────────────────────┘                        │
        │                                                                      │
        └──────────────────────────────────────────────────────────────────────┘
```

---

## Level 0 DFD - Simplified Flow

```
                                    STUDENTS
                                       │
                                       │ Booking Request
                                       │ (Dates, Facility)
                                       ▼
         ┌─────────────────────────────────────────────────────────┐
         │                                                         │
         │         FACILITIES BOOKING SYSTEM (Level 0)             │
         │                                                         │
         │  ┌───────────────────────────────────────────────────┐  │
         │  │  D.0: Booking & Facility Management               │  │
         │  │                                                   │  │
         │  │  • Receive and validate booking requests          │  │
         │  │  • Check facility availability                    │  │
         │  │  • Manage equipment assignments                   │  │
         │  │  • Track event status                             │  │
         │  │  • Generate inspection reports                    │  │
         │  │  • Store booking records                          │  │
         │  └───────────────────────────────────────────────────┘  │
         │                                                         │
         └──────┬──────────────────────┬──────────────────────┬────┘
                │                      │                      │
                │                      │                      │
    ┌───────────▼────────┐  ┌──────────▼─────────┐  ┌────────▼──────────┐
    │   BOOKING INFO     │  │   FACILITY INFO    │  │  INSPECTION      │
    │                    │  │                    │  │  REPORTS         │
    │ • Confirmation     │  │ • Availability     │  │                  │
    │ • Receipt          │  │ • Assignment       │  │ • Equipment      │
    │ • Schedule         │  │ • Plans            │  │   Status         │
    │                    │  │ • Pricing          │  │ • Event Details  │
    │                    │  │                    │  │                  │
    └────────────────────┘  └────────────────────┘  └──────────────────┘
           │                         │                      │
           │ To Students             │ To Admins           │ To Facilitators
           ▼                         ▼                      ▼
    ┌─────────────────┐    ┌──────────────────┐   ┌──────────────────┐
    │  STUDENT        │    │    ADMIN         │   │   FACILITATOR    │
    │  PORTAL         │    │    PANEL         │   │   PORTAL         │
    │                 │    │                  │   │                  │
    │ • My Bookings   │    │ • All Bookings   │   │ • Events List    │
    │ • Invoices      │    │ • Reports        │   │ • Inspection     │
    │ • Receipts      │    │ • Analytics      │   │ • Checklist      │
    │                 │    │ • Settings       │   │                  │
    └─────────────────┘    └──────────────────┘   └──────────────────┘
```

---

## Key Data Flows

### 1. **Booking Request Flow**

```
Student Input → Request Validation → Facility Check →
Availability Check → Equipment Assignment → Payment Processing →
Confirmation → Email Notification → Booking Record
```

### 2. **Equipment Tracking Flow**

```
Booking Created → Equipment Assigned → Item Quantity Recorded →
Pre-Event Check → Post-Event Inspection → Condition Assessment →
Damage Documentation → Equipment Update
```

### 3. **Inspection & Report Flow**

```
Event Completed → Facilitator Retrieves Checklist →
Equipment Inspection → Status Recording → Report Generation →
Report Upload → Admin Notification → Archive
```

### 4. **Administrative Flow**

```
Admin Access System → View All Bookings → Generate Reports →
View Analytics → Manage Facilities → Monitor Equipment →
View Inspection Results
```

---

## External Entities (Level 0)

| Entity              | Role                           | Input/Output                                             |
| ------------------- | ------------------------------ | -------------------------------------------------------- |
| **Students**        | Users making facility bookings | INPUT: Booking requests; OUTPUT: Confirmations, receipts |
| **Admins**          | System managers                | INPUT: Management commands; OUTPUT: Reports, analytics   |
| **Facilitators**    | Event inspectors               | INPUT: Event data; OUTPUT: Inspection reports            |
| **Email System**    | Notification service           | OUTPUT: Emails to users                                  |
| **Payment Gateway** | Payment processor              | INPUT: Payment data; OUTPUT: Transaction status          |

---

## Main Processes

| Process                    | Description                          | Key Activities                                  |
| -------------------------- | ------------------------------------ | ----------------------------------------------- |
| **Booking Management**     | Handle all booking operations        | Request → Validation → Approval → Confirmation  |
| **Equipment Management**   | Track equipment throughout lifecycle | Assignment → Monitoring → Inspection → Update   |
| **Facility Management**    | Manage facility availability         | Scheduling → Maintenance → Pricing → Status     |
| **Inspection & Reporting** | Post-event quality assurance         | Verification → Documentation → Report → Archive |
| **User Management**        | Handle user accounts & roles         | Registration → Authentication → Authorization   |

---

## Data Stores (Level 0)

| Store             | Purpose                     | Key Data                                   |
| ----------------- | --------------------------- | ------------------------------------------ |
| **Bookings DB**   | Store all booking records   | Dates, times, facilities, costs, status    |
| **Facilities DB** | Manage facility information | Names, icons, descriptions, rates, status  |
| **Equipment DB**  | Track equipment inventory   | Names, categories, quantities, conditions  |
| **Events DB**     | Record event information    | Event details, attendance, facilities used |
| **Users DB**      | Manage system users         | Names, roles, authentication, contact info |

---

## System Interfaces

### API Endpoints (Level 0)

- `/api/facilities/list` - Get all facilities
- `/api/bookings/create` - Create new booking
- `/api/bookings/list` - Get user bookings
- `/api/equipment/track` - Get equipment status
- `/api/reports/generate` - Generate inspection report

### Authentication

- Login system with role-based access (Student, Admin, Facilitator)
- Session management
- Permission validation

---

## Data Flow Summary

```
┌─────────────────┐
│  Users Input    │
└────────┬────────┘
         │
         ▼
┌──────────────────────────┐
│  Request Validation      │
│  & Processing            │
└────────┬─────────────────┘
         │
         ▼
┌──────────────────────────┐
│  Business Logic          │
│  & Rules Engine          │
└────────┬─────────────────┘
         │
         ▼
┌──────────────────────────┐
│  Database Operations     │
│  (Create/Read/Update)    │
└────────┬─────────────────┘
         │
         ▼
┌──────────────────────────┐
│  Generate Response       │
│  & Notifications         │
└────────┬─────────────────┘
         │
         ▼
┌──────────────────────────┐
│  Return to User          │
│  Display in Portal       │
└──────────────────────────┘
```

---

## System Components Architecture

```
                         ┌──────────────────┐
                         │  Web Browser     │
                         │  (Student/Admin/ │
                         │   Facilitator)   │
                         └────────┬─────────┘
                                  │
                    ┌─────────────▼──────────────┐
                    │   Presentation Layer       │
                    │  (HTML/CSS/JavaScript)     │
                    └─────────────┬──────────────┘
                                  │
                    ┌─────────────▼──────────────┐
                    │   Application Layer        │
                    │   (Controllers/API)        │
                    │                            │
                    │  • StudentController       │
                    │  • AdminController         │
                    │  • FacilitatorController   │
                    │  • BookingApiController    │
                    │                            │
                    └─────────────┬──────────────┘
                                  │
                    ┌─────────────▼──────────────┐
                    │   Business Logic Layer     │
                    │   (Models/Services)        │
                    │                            │
                    │  • BookingModel            │
                    │  • FacilityModel           │
                    │  • EquipmentModel          │
                    │  • UserModel               │
                    │                            │
                    └─────────────┬──────────────┘
                                  │
                    ┌─────────────▼──────────────┐
                    │   Data Access Layer        │
                    │   (Database Queries)       │
                    └─────────────┬──────────────┘
                                  │
                    ┌─────────────▼──────────────┐
                    │   Database Layer           │
                    │   (MySQL/PostgreSQL)       │
                    │                            │
                    │  • facilities              │
                    │  • bookings                │
                    │  • equipment               │
                    │  • events                  │
                    │  • users                   │
                    │  • plans                   │
                    │                            │
                    └────────────────────────────┘
```

---

## Summary

The **Facilities Booking System** at Level 0 is a comprehensive platform that manages:

1. **Booking Operations** - Students request facilities, admins manage requests
2. **Equipment Management** - Track equipment from assignment to post-event inspection
3. **Facility Management** - Maintain facility information, availability, and pricing
4. **Event Management** - Record and track events with full audit trails
5. **Inspection & Quality** - Facilitators inspect equipment and generate reports

The system integrates multiple user roles (Student, Admin, Facilitator) and provides specialized interfaces for each, all backed by a central database and API layer.
