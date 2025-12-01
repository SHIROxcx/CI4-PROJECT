# DFD Level 0 - Facilities Dynamic Booking System (Text-Based Explanation)

## Overview

The Facilities Dynamic Booking System is a comprehensive platform designed to manage facility reservations, equipment tracking, and post-event inspections. The system serves three primary user roles: Students (who book facilities), Administrators (who manage the system), and Facilitators (who conduct equipment inspections). At the highest level (Level 0), the entire system functions as a single unified process that receives inputs from external entities and produces outputs that feed back to those entities.

---

## System Purpose & Scope

The primary purpose of this system is to:

1. **Enable students to book facilities** by providing a user-friendly interface to browse available facilities and submit booking requests with specific dates, times, and equipment requirements
2. **Manage facility operations** by maintaining an up-to-date inventory of available facilities, their pricing structures, and scheduling information
3. **Track equipment throughout the booking lifecycle** from the moment equipment is assigned to a booking through pre-event verification and post-event inspection
4. **Verify facility conditions and equipment status** through structured inspection processes conducted by trained facilitators
5. **Generate comprehensive reports** that document facility usage, equipment conditions, and any damages or discrepancies found during inspections
6. **Provide administrative oversight** by giving administrators comprehensive views of all bookings, facilities, equipment status, and system analytics

---

## External Entities

### Students

- **Role**: Primary users who request facility bookings for academic or organizational events
- **Inputs to System**: Booking requests that include facility preferences, desired dates/times, expected number of attendees, and equipment needs
- **Receives from System**: Booking confirmations, payment invoices, receipts, schedule confirmations, and ability to track their booking status
- **Activities**: Browse available facilities, submit booking forms, view their booking history, check pricing, receive notifications

### Administrators

- **Role**: System managers and operators who oversee all booking operations
- **Inputs to System**: Management commands, facility updates, equipment inventory adjustments, booking approvals/rejections
- **Receives from System**: Comprehensive reports on all bookings, facility utilization analytics, equipment status summaries, inspection findings
- **Activities**: Approve or reject bookings, manage facility information, configure pricing plans, view system-wide analytics, manage user accounts

### Facilitators

- **Role**: Event supervisors or technical staff who inspect facilities and equipment after events conclude
- **Inputs to System**: Inspection data including equipment condition assessments, damage reports, quantity verifications
- **Receives from System**: List of completed events requiring inspection, equipment checklists, historical booking data, pre-inspection summaries
- **Activities**: View events awaiting inspection, conduct equipment inspections, document conditions and damages, submit inspection reports, generate equipment status reports

### Supporting External Systems

- **Email System**: Sends notifications and confirmations to users about bookings, payments, and inspection results
- **Payment Gateway**: Processes payment transactions and returns payment status confirmations
- **Database Systems**: Stores all persistent data including bookings, facilities, equipment, events, and users

---

## Core System Process (Level 0)

At the highest level of abstraction, the system can be viewed as a single comprehensive process with the following functions:

### Process D.0: Facilities Booking & Management System

This master process encompasses:

1. **Booking Reception & Validation**: The system receives booking requests from students, validates that all required information is present, checks that the requested dates and times are in a valid format, and verifies that the student is authenticated and authorized to make bookings

2. **Facility Availability Verification**: The system checks whether the requested facility is available for the requested date and time by querying the booking records and facility schedules. It also verifies that the facility is not in maintenance and is marked as active in the system

3. **Equipment Assignment & Tracking**: When a booking is confirmed, the system tracks which equipment items are assigned to that booking. It records expected quantities, retrieves equipment status from inventory, and links equipment to the event record

4. **Event Lifecycle Management**: The system tracks the progression of events from the initial booking request through the scheduled event date. It maintains status indicators (pending, confirmed, completed, inspected, closed) to show the current state of each booking

5. **Inspection & Verification Coordination**: After an event concludes, the system prepares inspection checklists for facilitators by compiling the expected equipment list and pre-event conditions, then receives inspection data documenting actual equipment conditions

6. **Report Generation & Documentation**: The system compiles inspection findings, equipment status changes, damage assessments, and financial impacts into structured reports that can be viewed by administrators and facilitators

7. **Data Persistence & Audit Trail**: The system stores all booking records, modifications, inspection data, and financial transactions in the database, maintaining a complete audit trail of all system activities

---

## Key Data Flows Explained

### Flow 1: Student Booking Request to Confirmation

**Initiator**: Student user accessing the system

**Steps**:

1. Student browses the facilities list to see available facilities with icons, names, descriptions, and pricing information
2. Student selects a facility and accesses the booking form
3. Student provides booking details: event date, start time, expected duration, number of attendees, organization name, contact information, and any special equipment requests
4. System validates all required fields are completed and dates are in the future
5. System checks if the facility is available for that date/time by querying existing bookings
6. If available, system calculates the total cost based on facility rate and requested equipment
7. System prompts student for payment information
8. Payment gateway processes the payment
9. Upon successful payment, system creates a new booking record in the database
10. System sends confirmation email to student with booking details, receipt, and facility information
11. System displays confirmation message in student portal with reference number

**Key Data Items**: Facility ID, event date, start time, duration, attendee count, equipment list, student ID, payment amount, booking status (confirmed)

**Data Stores Updated**: Bookings table, Equipment assignments table, Payment records table

---

### Flow 2: Equipment Assignment & Inventory Tracking

**Initiator**: Booking confirmation or admin action

**Steps**:

1. When a booking is confirmed, the system identifies all equipment to be provided (either included in facility plan or rented separately)
2. For each equipment item, the system records the expected quantity and current condition/availability status
3. System retrieves current inventory levels to verify sufficient quantities are available
4. System links equipment records to the specific booking event
5. Equipment quantities are reserved (not decremented yet, just marked as assigned)
6. System creates a preliminary inspection checklist showing what equipment should be present and its expected condition
7. As the event date approaches, this checklist becomes available to facilitators
8. After the event, facilitators inspect actual quantities and conditions against this checklist
9. System records discrepancies (missing items, damaged items, quantity mismatches)
10. System then updates the equipment inventory based on actual post-event condition

**Key Data Items**: Equipment ID, quantity, category, condition status, equipment name, booking reference

**Data Stores Updated**: Booking_Equipment table, Equipment table (quantities and condition), Facilitator_Checklists table

---

### Flow 3: Post-Event Inspection & Report Generation

**Initiator**: Event completion (automatic trigger based on event date passing)

**Steps**:

1. When an event date passes, system marks that event as "completed" and available for inspection
2. System retrieves the facilitator-assigned to that facility/event
3. System prepares an inspection package including: event details, booking information, expected equipment list, facility information
4. Facilitator views the "Events Awaiting Inspection" list in their portal
5. Facilitator selects an event and views the equipment checklist with expected quantities and pre-event conditions
6. Facilitator conducts physical inspection of the facility and equipment
7. Facilitator enters inspection data for each equipment item: actual quantity found, condition assessment (good/damaged/missing), notes about damages or issues
8. System calculates damage costs based on facility pricing rules
9. Facilitator reviews the summary and submits the inspection report
10. System generates a formal inspection report document in Excel format showing all findings
11. System updates equipment inventory to reflect actual post-inspection condition (deducting missing items, incrementing damaged count)
12. System marks the event as "inspected" and the booking as ready for financial settlement
13. System notifies administrator that inspection is complete and final charges are calculated
14. Administrator can view the inspection report and make any necessary adjustments before finalizing the booking

**Key Data Items**: Event ID, equipment condition status, damage descriptions, quantities, inspection date, facilitator ID, damage costs

**Data Stores Updated**: Facilitator_Checklists table, Facilitator_Checklist_Items table, Equipment table, Events table, Bookings table

---

### Flow 4: Administrative Oversight & Reporting

**Initiator**: Administrator accessing admin panel

**Steps**:

1. Administrator logs into the system with admin credentials
2. System verifies admin role and grants access to admin panel
3. Administrator can view dashboard showing: total active bookings, facilities status, equipment inventory summary
4. Administrator can filter and search all bookings by date range, facility, status, student, cost
5. For each booking, administrator can see: confirmation details, assigned equipment, inspection status, any issues or damages reported
6. Administrator can generate various reports: bookings by facility, equipment damage summary, revenue reports, facility utilization rates
7. Administrator can manage facilities: activate/deactivate facilities, edit pricing, update facility descriptions
8. Administrator can manage equipment inventory: add new equipment, adjust quantities after damage, set categories and pricing
9. Administrator can view and manage all user accounts, assign facilitators to facilities, reset passwords
10. System provides analytics dashboard showing: peak booking times, most-used facilities, common equipment damage patterns, revenue trends

**Key Data Items**: Booking status, facility information, equipment inventory levels, inspection reports, user information, financial data

**Data Stores Accessed**: All tables for read operations; selective write operations for administrative updates

---

## Data Stores (Databases & Tables)

### Bookings Table

Stores the core booking records. Each booking represents one facility reservation by one student/organization. Contains:

- Booking ID (unique identifier)
- Student/Client information (name, organization, contact details)
- Facility information (which facility was booked)
- Event date and time range (when the facility is reserved)
- Booking status (pending, confirmed, completed, inspected, closed)
- Equipment list with quantities for this booking
- Payment information (amount, transaction ID, status)
- Created timestamp and last updated timestamp
- Administrative notes

### Facilities Table

Stores information about each facility that can be booked. Contains:

- Facility ID (unique identifier)
- Facility name (e.g., "University Auditorium")
- Icon/emoji representation for UI display
- Facility description and amenities
- Pricing information (base hourly rate, extended hour surcharge)
- Capacity information (number of people it can accommodate)
- Status flags (is_active, is_maintenance)
- Opening and closing hours
- Contact information and location

### Equipment Table

Stores the inventory of all equipment available in the system. Contains:

- Equipment ID (unique identifier)
- Equipment name and category (e.g., "Projector", "Sound System")
- Unit of measurement (per item, per hour, etc.)
- Total quantity in system
- Current "good" (working) quantity
- Current "damaged" quantity (needs repair)
- Current "rented out" quantity (currently with events)
- Current "available" quantity (can be used)
- Unit cost and rental rates
- Status (active, inactive, disposal)
- Last inventory update timestamp

### Events Table

Stores details about actual facility usage events. Contains:

- Event ID (unique identifier)
- Booking ID (links to the original booking)
- Event title/name
- Event date and time
- Expected attendee count
- Facility used
- Event status (scheduled, completed, inspected, closed)
- Equipment assigned to this event
- Timestamps for key milestones

### Facilitator Checklists Table

Stores inspection records submitted by facilitators. Contains:

- Checklist ID (unique identifier)
- Event ID (which event was inspected)
- Booking ID (links to original booking)
- Facilitator ID (who performed the inspection)
- Facilitator name and timestamp of submission
- Overall inspection status

### Facilitator Checklist Items Table

Stores detailed line-item data from inspections. Each inspection can have multiple items. Contains:

- Item ID (unique identifier)
- Checklist ID (links to the master checklist)
- Equipment ID (which equipment was inspected)
- Equipment name
- Expected quantity (from booking)
- Actual quantity found (from inspection)
- Equipment condition (good, damaged, missing)
- Damage description and notes
- Is_available flag (true if equipment is still usable)
- Creation timestamp

### Users Table

Stores login credentials and profile information for all system users. Contains:

- User ID (unique identifier)
- Username and email
- Password hash (not plain text)
- Full name and contact information
- Role (student, admin, facilitator)
- Organization affiliation (if applicable)
- Account status (active, inactive, suspended)
- Created and last login timestamps

### Plans Table

Stores predefined facility rental packages/plans. Some facilities have tiered pricing. Contains:

- Plan ID (unique identifier)
- Facility ID (which facility this plan applies to)
- Plan name (e.g., "Basic Package", "Premium Package")
- Included equipment and quantities
- Additional charges if equipment is modified
- Price for this tier
- Description of what's included

---

## System Interfaces & Communication

### How Students Interact

- **Web Browser**: Student opens the system in a web browser and navigates to the student booking portal
- **Authentication**: Student logs in using credentials (email/ID and password)
- **Facility Browsing**: System displays all available facilities with photos, descriptions, and availability calendars
- **Booking Form**: Student fills in required fields: facility, date, time, duration, attendees, contact info
- **Equipment Selection**: Student can see and select additional equipment if desired
- **Payment**: System displays total cost and directs student to payment gateway
- **Confirmation**: System displays confirmation details and sends email receipt

### How Administrators Interact

- **Web Browser**: Administrator opens admin panel in web browser
- **Authentication**: Administrator logs in with admin credentials
- **Dashboard**: System shows real-time summary of system status
- **Search & Filter**: Administrator can search bookings, facilities, equipment by various criteria
- **Reports**: System can generate PDF or Excel reports on demand
- **Management Forms**: Administrator uses web forms to add/edit/delete facilities and equipment
- **User Management**: Administrator can manage user accounts and reset passwords

### How Facilitators Interact

- **Web Browser**: Facilitator opens the system in web browser
- **Authentication**: Facilitator logs in with facilitator credentials
- **Events List**: System displays events that are ready for inspection (past their event date)
- **Inspection Form**: Facilitator clicks on an event and sees the equipment checklist
- **Data Entry**: Facilitator enters inspection findings for each equipment item
- **Report Generation**: System generates Excel inspection report which facilitator can download
- **Submission**: Facilitator submits the inspection, marking event as inspected

### API Endpoints

The system exposes REST API endpoints that allow programmatic access:

- `GET /api/facilities/list` - Retrieve all active facilities
- `GET /api/facilities/{id}/data` - Get detailed data for one facility
- `POST /api/bookings/create` - Create a new booking
- `GET /api/bookings/list` - Get bookings for logged-in user
- `GET /api/equipment/inventory` - Get current equipment availability
- `POST /api/inspections/submit` - Submit an inspection report
- `GET /api/reports/{bookingId}` - Download booking report

---

## Authentication & Authorization

The system implements role-based access control:

### Authentication Process

1. User accesses the system and sees login page
2. User enters username/email and password
3. System queries the Users table to find matching record
4. System compares submitted password hash with stored hash
5. If match is found, system creates a session and sets cookies in user's browser
6. User is redirected to appropriate portal based on their role
7. For subsequent requests, system validates the session to ensure user is still logged in

### Authorization Rules

- **Students** can only access their own bookings; cannot see other students' bookings or admin functions
- **Administrators** can access all bookings, all facilities, all equipment, all users; can perform administrative operations
- **Facilitators** can only access events assigned to their facilities and inspection functions; cannot modify bookings or create facilities
- **Public** (non-authenticated) users can view facility listings and pricing but cannot make bookings

---

## Request Processing Flow (Technical)

When any user makes a request to the system:

1. **Request Reception**: The web server receives the HTTP request from the user's browser
2. **Routing**: The routing system determines which controller method should handle this request based on the URL path
3. **Authentication Check**: Before processing, system verifies user is logged in and has valid session
4. **Authorization Check**: System verifies user's role has permission to perform this action
5. **Input Validation**: System checks that all required parameters are present and in valid format
6. **Business Logic**: System applies business rules (e.g., facility availability, payment processing)
7. **Database Operations**: System performs necessary read/write operations to database
8. **Response Formatting**: System prepares response data (JSON for API requests, HTML for web pages)
9. **Response Transmission**: Web server sends response back to user's browser
10. **Browser Rendering**: Browser displays the response to user

---

## System Layers & Components

### Presentation Layer

This is what users see when they access the system. Includes:

- **Student Portal**: Facility browsing page, booking form, booking history page, invoice page
- **Admin Panel**: Dashboard, booking management page, facility management page, reports page, user management page
- **Facilitator Portal**: Events list page, inspection form page, inspection history page, reports page
- **Common Components**: Navigation menu, header/footer, login page, logout functionality

Technologies: HTML, CSS, JavaScript, Bootstrap for responsive design

### Application Layer (Controllers)

This layer receives requests from users and coordinates responses. Includes:

- **StudentController**: Handles student-related requests (browse facilities, submit bookings, view history)
- **AdminController**: Handles administrative operations (manage bookings, manage facilities, generate reports)
- **FacilitatorController**: Handles facilitator operations (view events, submit inspections)
- **AuthController**: Handles login, logout, session management
- **ApiController**: Exposes programmatic endpoints for data access

Each controller receives user input, validates it, calls appropriate business logic, and returns a response.

### Business Logic Layer (Models & Services)

This layer contains the rules and procedures for how the system operates. Includes:

- **BookingModel**: Rules for creating, updating, validating bookings; calculating costs; checking availability
- **FacilityModel**: Rules for managing facility data, status, pricing
- **EquipmentModel**: Rules for inventory management, equipment assignment, condition tracking
- **UserModel**: Rules for user management, authentication, authorization
- **InspectionService**: Rules for inspection workflows, report generation, equipment updates

### Data Access Layer

This layer communicates with the database. Includes:

- **Database Connection Management**: Establishes and maintains connections to MySQL/PostgreSQL database
- **Query Builders**: Constructs SQL queries based on business logic requirements
- **Transaction Management**: Ensures data consistency when multiple related operations occur
- **Error Handling**: Catches database errors and propagates them to application layer

### Database Layer

The persistent data store. Stores all system data in tables as described above. Uses MySQL or PostgreSQL database management system.

---

## Security Considerations

### Data Protection

- Passwords are never stored in plain text; instead, password hashes are stored
- Sensitive data like payment information is never stored in the system (processed through external payment gateway)
- Database connections use encryption when available
- Access logs are maintained for audit purposes

### Access Control

- All requests require authentication except the public facility listing
- Role-based access control ensures users can only access appropriate features
- Sensitive operations (delete, modify) require additional confirmation

### Input Validation

- All user input is validated on both client side (browser) and server side
- Special characters are escaped to prevent SQL injection attacks
- File uploads (if any) are restricted and scanned

---

## System Workflows Summary

### Complete Booking Workflow

1. Student logs in
2. Student browses facilities
3. Student selects facility and fills booking form
4. System validates availability and calculates cost
5. Student processes payment
6. System creates booking record
7. Student receives confirmation
8. Administrator can view and manage the booking
9. Event date arrives
10. Facilitator inspects facility and equipment post-event
11. Inspection report is generated
12. Administrator views report and can finalize charges
13. Student receives inspection summary if applicable

### Complete Inspection Workflow

1. Event date passes
2. System marks event as "completed"
3. Facilitator views event in their inspection list
4. Facilitator opens event and sees equipment checklist
5. Facilitator physically inspects facility and equipment
6. Facilitator enters findings for each item
7. System calculates damage costs
8. Facilitator submits inspection report
9. System generates Excel report document
10. System updates equipment inventory based on findings
11. System notifies administrator of completion
12. Administrator can download and review detailed report

---

## Summary

The Facilities Dynamic Booking System at Level 0 (system context) is a comprehensive platform that coordinates interactions between students (who need facilities), administrators (who manage operations), and facilitators (who verify conditions). The system maintains detailed information about facilities, equipment, bookings, and inspections in a central database. Through a multi-layered architecture with presentation, application, business logic, data access, and database layers, the system provides specialized portals for each user type while maintaining data integrity, security, and comprehensive audit trails of all operations. The core value proposition is enabling efficient facility reservation, precise equipment tracking, and systematic post-event verification through integrated workflows that connect all system stakeholders.
