# توثيق Controllers والـ Models - System Documentation

## نظرة عامة / Overview

هذا الملف يوثق جميع الـ Controllers والـ Models في النظام وعلاقاتها ببعضها البعض.
This document describes all Controllers and Models in the system and their relationships.

---

## 📋 جدول المحتويات / Table of Contents

1. [Models (Database Entities)](#models-database-entities)
2. [Controllers by Role](#controllers-by-role)
3. [Relationships Map](#relationships-map)
4. [Usage Instructions](#usage-instructions)

---

## 🗄️ Models (Database Entities)

### User Models

#### Admin
- **Table**: `admins`
- **Relationships**:
  - `hasMany` JobApplication (as reviewer)
- **Fields**: id, name, email, password, remember_token

#### Client
- **Table**: `clients`
- **Relationships**:
  - `hasMany` Consultation
  - `hasMany` Appointment
  - `hasMany` ConsultationReview
- **Fields**: id, name, email, password, phone, address, photo, status, email_verified_at
- **Soft Deletes**: ✅

#### Lawyer
- **Table**: `lawyers`
- **Relationships**:
  - `belongsToMany` Specialization (through lawyer_specialization)
  - `hasMany` Consultation
  - `hasMany` Appointment
  - `hasMany` LawyerAvailability
  - `hasMany` AvailabilityTemplate
- **Fields**: id, name, email, age, password, phone, address, photo, certificate, specialization_id
- **Soft Deletes**: ✅

#### Employee
- **Table**: `employees`
- **Relationships**: None (standalone)
- **Fields**: id, name, email, password, age, phone, address, photo
- **Soft Deletes**: ✅

---

### Core Business Models

#### Specialization
- **Table**: `specializations`
- **Relationships**:
  - `belongsToMany` Lawyer (through lawyer_specialization)
  - `hasMany` Consultation
  - `hasMany` JobApplication
- **Fields**: id, name, description
- **Soft Deletes**: ✅

#### Consultation
- **Table**: `consultations`
- **Relationships**:
  - `belongsTo` Client
  - `belongsTo` Lawyer
  - `belongsTo` Specialization
  - `hasMany` ConsultationAttachment
  - `hasMany` ConsultationMessage
  - `hasMany` Appointment
  - `hasOne` ConsultationReview
- **Fields**: id, client_id, lawyer_id, specialization_id, subject, description, priority, preferred_channel, meeting_link, status, rejection_reason, legal_summary
- **Soft Deletes**: ✅

#### Appointment
- **Table**: `appointments`
- **Relationships**:
  - `belongsTo` Consultation (nullable)
  - `belongsTo` Lawyer
  - `belongsTo` Client
  - `belongsTo` LawyerAvailability (nullable)
- **Fields**: id, consultation_id, availability_id, lawyer_id, client_id, subject, description, datetime, type, notes, status, cancellation_reason, cancelled_by
- **Soft Deletes**: ✅
- **Special Methods**: checkAndMarkAsDone(), markCompletedAppointments()

#### LawyerAvailability
- **Table**: `lawyer_availability`
- **Relationships**:
  - `belongsTo` Lawyer
  - `hasMany` Appointment
- **Fields**: id, lawyer_id, date, start_time, end_time, status, notes, is_vacation, vacation_reason

#### AvailabilityTemplate
- **Table**: `availability_templates`
- **Relationships**:
  - `belongsTo` Lawyer
- **Fields**: id, lawyer_id, name, start_time, end_time, days_of_week, start_date, end_date, is_active

---

### Consultation Related Models

#### ConsultationMessage
- **Table**: `consultation_messages`
- **Relationships**:
  - `belongsTo` Consultation
  - `morphTo` sender (Client or Lawyer)
- **Fields**: id, consultation_id, sender_type, sender_id, message, attachment_path, is_read, read_at

#### ConsultationAttachment
- **Table**: `consultation_attachments`
- **Relationships**:
  - `belongsTo` Consultation
- **Fields**: id, consultation_id, file_path, file_name, file_type, file_size

#### ConsultationReview
- **Table**: `consultation_reviews`
- **Relationships**:
  - `belongsTo` Consultation
  - `belongsTo` Client
- **Fields**: id, consultation_id, client_id, rating, comment

---

### Other Models

#### Law
- **Table**: `laws`
- **Relationships**: None
- **Fields**: id, title, category, summary, full_content, status
- **Soft Deletes**: ✅

#### FixedPrice
- **Table**: `fixed_prices`
- **Relationships**: None
- **Fields**: id, name, name_ar, type, price, unit, description, is_active
- **Soft Deletes**: ✅
- **Scopes**: active(), ofType()

#### JobApplication
- **Table**: `job_applications`
- **Relationships**:
  - `belongsTo` Admin (as reviewer)
  - `belongsTo` Specialization (nullable, for lawyer applications)
- **Fields**: id, type, status, name, email, phone, age, address, photo, specialization_id, experience_years, bio, certificate, admin_notes, reviewed_at, reviewed_by
- **Soft Deletes**: ✅

---

## 🎮 Controllers by Role

### Admin Controllers

#### AdminAuthController
- **Path**: `app/Http/Controllers/API/Admin/AdminAuthController.php`
- **Routes**: `/api/admin/login`, `/api/admin/logout`
- **Models Used**: Admin

#### AdminProfileController
- **Path**: `app/Http/Controllers/API/Admin/AdminProfileController.php`
- **Routes**: `/api/admin/profile`
- **Models Used**: Admin

#### LawyerController
- **Path**: `app/Http/Controllers/API/Admin/LawyerController.php`
- **Routes**: `/api/admin/lawyers/*`
- **Models Used**: Lawyer, Specialization
- **Operations**: CRUD, Archive, Restore, Force Delete

#### EmployeeController
- **Path**: `app/Http/Controllers/API/Admin/EmployeeController.php`
- **Routes**: `/api/admin/employees/*`
- **Models Used**: Employee
- **Operations**: CRUD, Archive, Restore, Force Delete

#### LawController
- **Path**: `app/Http/Controllers/API/Admin/LawController.php`
- **Routes**: `/api/admin/laws/*`
- **Models Used**: Law
- **Operations**: CRUD, Toggle Status, Archive, Restore

#### SpecializationController
- **Path**: `app/Http/Controllers/API/Admin/SpecializationController.php`
- **Routes**: `/api/admin/specializations/*`
- **Models Used**: Specialization
- **Operations**: CRUD, Archive, Restore

#### AdminConsultationController
- **Path**: `app/Http/Controllers/API/Admin/AdminConsultationController.php`
- **Routes**: `/api/admin/consultations/*`
- **Models Used**: Consultation, Client, Lawyer
- **Operations**: Read-only (View, Statistics)

#### JobApplicationController
- **Path**: `app/Http/Controllers/API/Admin/JobApplicationController.php`
- **Routes**: `/api/admin/job-applications/*`
- **Models Used**: JobApplication, Lawyer, Employee, Specialization
- **Operations**: View, Approve, Reject, Delete

---

### Client Controllers

#### ClientAuthController
- **Path**: `app/Http/Controllers/API/Client/ClientAuthController.php`
- **Routes**: `/api/client/register`, `/api/client/login`, `/api/client/logout`
- **Models Used**: Client

#### ClientProfileController
- **Path**: `app/Http/Controllers/API/Client/ClientProfileController.php`
- **Routes**: `/api/client/profile`
- **Models Used**: Client

#### ConsultationController
- **Path**: `app/Http/Controllers/API/Client/ConsultationController.php`
- **Routes**: `/api/client/consultations/*`
- **Models Used**: Consultation, ConsultationMessage, ConsultationAttachment, ConsultationReview
- **Operations**: CRUD, Cancel, Complete, Send Message, Create Review

#### AppointmentController
- **Path**: `app/Http/Controllers/API/Client/AppointmentController.php`
- **Routes**: `/api/client/appointments/*`
- **Models Used**: Appointment, LawyerAvailability, Lawyer
- **Operations**: View, Book, Cancel, Reschedule, Calendar Views

#### ClientLawController
- **Path**: `app/Http/Controllers/API/Client/ClientLawController.php`
- **Routes**: `/api/client/laws/*`
- **Models Used**: Law
- **Operations**: View, Search, Categories

#### ClientFixedPriceController
- **Path**: `app/Http/Controllers/API/Client/ClientFixedPriceController.php`
- **Routes**: `/api/client/fixed-prices`
- **Models Used**: FixedPrice
- **Operations**: View Active Prices Only

#### NotificationController
- **Path**: `app/Http/Controllers/API/Client/NotificationController.php`
- **Routes**: `/api/client/notifications/*`
- **Models Used**: Notification (Laravel)
- **Operations**: View, Mark as Read, Delete

---

### Employee Controllers

#### EmployeeAuthController
- **Path**: `app/Http/Controllers/API/Employee/EmployeeAuthController.php`
- **Routes**: `/api/employee/login`, `/api/employee/logout`
- **Models Used**: Employee

#### EmployeeProfileController
- **Path**: `app/Http/Controllers/API/Employee/EmployeeProfileController.php`
- **Routes**: `/api/employee/profile`
- **Models Used**: Employee

#### ClientManagementController
- **Path**: `app/Http/Controllers/API/Employee/ClientManagementController.php`
- **Routes**: `/api/employee/clients/*`
- **Models Used**: Client
- **Operations**: View, Update, Activate, Reject, Suspend, Archive, Restore

#### EmployeeAvailabilityController
- **Path**: `app/Http/Controllers/API/Employee/EmployeeAvailabilityController.php`
- **Routes**: `/api/employee/availability/*`
- **Models Used**: LawyerAvailability, Lawyer
- **Operations**: CRUD, Batch Create, Create Schedule

#### EmployeeAvailabilityTemplateController
- **Path**: `app/Http/Controllers/API/Employee/EmployeeAvailabilityTemplateController.php`
- **Routes**: `/api/employee/availability-templates/*`
- **Models Used**: AvailabilityTemplate, Lawyer
- **Operations**: CRUD, Apply Template

#### EmployeeAppointmentController
- **Path**: `app/Http/Controllers/API/Employee/EmployeeAppointmentController.php`
- **Routes**: `/api/employee/appointments/*`
- **Models Used**: Appointment, Client, Lawyer, Consultation
- **Operations**: View, Accept, Reject, Calendar Views, Custom Time Requests

#### EmployeeConsultationController
- **Path**: `app/Http/Controllers/API/Employee/EmployeeConsultationController.php`
- **Routes**: `/api/employee/consultations/*`
- **Models Used**: Consultation, Client, Lawyer, Specialization
- **Operations**: View, Assign, Auto-Assign, Statistics

#### EmployeeFixedPriceController
- **Path**: `app/Http/Controllers/API/Employee/EmployeeFixedPriceController.php`
- **Routes**: `/api/employee/fixed-prices/*`
- **Models Used**: FixedPrice
- **Operations**: Full CRUD, Archive, Restore, Force Delete

#### NotificationController
- **Path**: `app/Http/Controllers/API/Employee/NotificationController.php`
- **Routes**: `/api/employee/notifications/*`
- **Models Used**: Notification (Laravel)
- **Operations**: View, Mark as Read, Delete

---

### Lawyer Controllers

#### LawyerAuthController
- **Path**: `app/Http/Controllers/API/Lawyer/LawyerAuthController.php`
- **Routes**: `/api/lawyer/login`, `/api/lawyer/logout`
- **Models Used**: Lawyer

#### LawyerProfileController
- **Path**: `app/Http/Controllers/API/Lawyer/LawyerProfileController.php`
- **Routes**: `/api/lawyer/profile`
- **Models Used**: Lawyer

#### LawyerConsultationController
- **Path**: `app/Http/Controllers/API/Lawyer/LawyerConsultationController.php`
- **Routes**: `/api/lawyer/consultations/*`
- **Models Used**: Consultation, Appointment, ConsultationMessage
- **Operations**: View, Accept, Reject, Create Appointment, Send Message, Add Legal Summary

#### LawyerAppointmentController
- **Path**: `app/Http/Controllers/API/Lawyer/LawyerAppointmentController.php`
- **Routes**: `/api/lawyer/appointments/*`
- **Models Used**: Appointment, Client, Consultation
- **Operations**: View, Cancel, Calendar Views

#### LawyerAvailabilityController
- **Path**: `app/Http/Controllers/API/Lawyer/LawyerAvailabilityController.php`
- **Routes**: `/api/lawyer/availability/*`
- **Models Used**: LawyerAvailability
- **Operations**: CRUD, Set Vacation

#### LawyerLawController
- **Path**: `app/Http/Controllers/API/Lawyer/LawyerLawController.php`
- **Routes**: `/api/lawyer/laws/*`
- **Models Used**: Law
- **Operations**: View, Search, Categories

#### NotificationController
- **Path**: `app/Http/Controllers/API/Lawyer/NotificationController.php`
- **Routes**: `/api/lawyer/notifications/*`
- **Models Used**: Notification (Laravel)
- **Operations**: View, Mark as Read, Delete

---

### Guest Controllers

#### GuestAuthController
- **Path**: `app/Http/Controllers/API/Guest/GuestAuthController.php`
- **Routes**: `/api/guest/register`, `/api/guest/login`, `/api/guest/verify-email`
- **Models Used**: Client

#### LawyerController
- **Path**: `app/Http/Controllers/API/Guest/LawyerController.php`
- **Routes**: `/api/guest/lawyers/*`
- **Models Used**: Lawyer, Specialization
- **Operations**: View Public Information

#### SpecializationController
- **Path**: `app/Http/Controllers/API/Guest/SpecializationController.php`
- **Routes**: `/api/guest/specializations/*`
- **Models Used**: Specialization
- **Operations**: View Public Information

#### LawController
- **Path**: `app/Http/Controllers/API/Guest/LawController.php`
- **Routes**: `/api/guest/laws/*`
- **Models Used**: Law
- **Operations**: View Published Laws Only

#### JobApplicationController
- **Path**: `app/Http/Controllers/API/Guest/JobApplicationController.php`
- **Routes**: `/api/guest/job-applications`
- **Models Used**: JobApplication, Specialization
- **Operations**: Submit Application

---

## 🔗 Relationships Map

### Model Relationships

```
Admin
  └─ hasMany → JobApplication (as reviewer)

Client
  ├─ hasMany → Consultation
  ├─ hasMany → Appointment
  └─ hasMany → ConsultationReview

Lawyer
  ├─ belongsToMany → Specialization (many-to-many)
  ├─ hasMany → Consultation
  ├─ hasMany → Appointment
  ├─ hasMany → LawyerAvailability
  └─ hasMany → AvailabilityTemplate

Employee
  └─ (standalone, no relationships)

Specialization
  ├─ belongsToMany → Lawyer (many-to-many)
  ├─ hasMany → Consultation
  └─ hasMany → JobApplication

Consultation
  ├─ belongsTo → Client
  ├─ belongsTo → Lawyer
  ├─ belongsTo → Specialization
  ├─ hasMany → ConsultationAttachment
  ├─ hasMany → ConsultationMessage
  ├─ hasMany → Appointment
  └─ hasOne → ConsultationReview

Appointment
  ├─ belongsTo → Consultation (nullable)
  ├─ belongsTo → Lawyer
  ├─ belongsTo → Client
  └─ belongsTo → LawyerAvailability (nullable)

LawyerAvailability
  ├─ belongsTo → Lawyer
  └─ hasMany → Appointment

AvailabilityTemplate
  └─ belongsTo → Lawyer

ConsultationMessage
  ├─ belongsTo → Consultation
  └─ morphTo → sender (Client or Lawyer)

ConsultationAttachment
  └─ belongsTo → Consultation

ConsultationReview
  ├─ belongsTo → Consultation
  └─ belongsTo → Client

JobApplication
  ├─ belongsTo → Admin (as reviewer)
  └─ belongsTo → Specialization (nullable)
```

### Controller to Model Usage

```
Admin Controllers:
  AdminAuthController → Admin
  AdminProfileController → Admin
  LawyerController → Lawyer, Specialization
  EmployeeController → Employee
  LawController → Law
  SpecializationController → Specialization
  AdminConsultationController → Consultation, Client, Lawyer
  JobApplicationController → JobApplication, Lawyer, Employee, Specialization

Client Controllers:
  ClientAuthController → Client
  ClientProfileController → Client
  ConsultationController → Consultation, ConsultationMessage, ConsultationAttachment, ConsultationReview
  AppointmentController → Appointment, LawyerAvailability, Lawyer
  ClientLawController → Law
  ClientFixedPriceController → FixedPrice
  NotificationController → Notification

Employee Controllers:
  EmployeeAuthController → Employee
  EmployeeProfileController → Employee
  ClientManagementController → Client
  EmployeeAvailabilityController → LawyerAvailability, Lawyer
  EmployeeAvailabilityTemplateController → AvailabilityTemplate, Lawyer
  EmployeeAppointmentController → Appointment, Client, Lawyer, Consultation
  EmployeeConsultationController → Consultation, Client, Lawyer, Specialization
  EmployeeFixedPriceController → FixedPrice
  NotificationController → Notification

Lawyer Controllers:
  LawyerAuthController → Lawyer
  LawyerProfileController → Lawyer
  LawyerConsultationController → Consultation, Appointment, ConsultationMessage
  LawyerAppointmentController → Appointment, Client, Consultation
  LawyerAvailabilityController → LawyerAvailability
  LawyerLawController → Law
  NotificationController → Notification

Guest Controllers:
  GuestAuthController → Client
  LawyerController → Lawyer, Specialization
  SpecializationController → Specialization
  LawController → Law
  JobApplicationController → JobApplication, Specialization
```

---

## 📖 Usage Instructions

### استخدام PlantUML / Using PlantUML

1. **تثبيت PlantUML**:
   - قم بتثبيت PlantUML extension في VS Code أو استخدم PlantUML online server
   - Install PlantUML extension in VS Code or use PlantUML online server

2. **فتح الملف**:
   - افتح ملف `SYSTEM_DIAGRAMS.puml`
   - Open `SYSTEM_DIAGRAMS.puml` file

3. **عرض المخططات**:
   - اضغط `Alt+D` في VS Code لعرض المخطط
   - أو استخدم PlantUML online: http://www.plantuml.com/plantuml/uml/
   - Press `Alt+D` in VS Code to view diagram
   - Or use PlantUML online: http://www.plantuml.com/plantuml/uml/

4. **تصدير المخططات**:
   - يمكنك تصدير المخططات كـ PNG, SVG, PDF
   - You can export diagrams as PNG, SVG, PDF

### المخططات المتوفرة / Available Diagrams

1. **ERD (Entity Relationship Diagram)**:
   - يوضح جميع الـ Models والعلاقات بينها
   - Shows all Models and their relationships

2. **Class Diagram للـ Controllers**:
   - يوضح جميع الـ Controllers منظمة حسب الدور
   - Shows all Controllers organized by role

3. **Controller-Model Relationships**:
   - يوضح أي Controller يستخدم أي Model
   - Shows which Controller uses which Model

---

## 📝 Notes

- جميع الـ Models التي تستخدم Soft Deletes موضحة بـ ✅
- All Models using Soft Deletes are marked with ✅
- العلاقات Many-to-Many تستخدم pivot tables
- Many-to-Many relationships use pivot tables
- بعض الـ Controllers تستخدم نفس الاسم ولكن في namespaces مختلفة (مثل NotificationController)
- Some Controllers have the same name but in different namespaces (e.g., NotificationController)

---

**Last Updated**: 2025-01-20
**Version**: 1.0.0



