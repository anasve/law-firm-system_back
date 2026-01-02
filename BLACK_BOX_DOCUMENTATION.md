# Black Box Documentation - نظام الصندوق الأسود

## نظرة عامة / Overview

هذا الملف يوثق النظام من منظور الصندوق الأسود (Black Box) - أي كيف يبدو النظام من الخارج دون الدخول في التفاصيل الداخلية.
This document describes the system from a Black Box perspective - how the system appears from the outside without internal details.

---

## 📦 ما هو الصندوق الأسود؟ / What is a Black Box?

الصندوق الأسود هو طريقة لتصميم النظام حيث نركز على:
- **المدخلات (Inputs)**: ما الذي يدخل إلى النظام
- **المخرجات (Outputs)**: ما الذي يخرج من النظام
- **الوظائف (Functions)**: ما الذي يفعله النظام
- **الجهات الخارجية (External Actors)**: من يتفاعل مع النظام

A Black Box is a system design approach where we focus on:
- **Inputs**: What goes into the system
- **Outputs**: What comes out of the system
- **Functions**: What the system does
- **External Actors**: Who interacts with the system

---

## 🎭 الجهات الخارجية / External Actors

### 1. Client (العميل)
**Role**: العميل الذي يحتاج إلى خدمات قانونية
**Role**: Client who needs legal services

**Inputs (المدخلات)**:
- Registration data (name, email, password, phone, address, photo)
- Login credentials (email, password)
- Consultation requests (subject, description, priority, preferred channel)
- Appointment booking requests
- Profile update data
- Messages in consultations
- Reviews and ratings

**Outputs (المخرجات)**:
- Authentication tokens
- Consultation confirmations
- Appointment confirmations
- Available time slots
- Lawyer information
- Laws and regulations
- Fixed prices
- Notifications

**Main Functions (الوظائف الرئيسية)**:
- Register and login
- Create and manage consultations
- Book and manage appointments
- View laws and regulations
- View fixed prices
- Communicate with lawyers
- Review consultations

---

### 2. Lawyer (المحامي)
**Role**: المحامي الذي يقدم الخدمات القانونية
**Role**: Lawyer who provides legal services

**Inputs (المدخلات)**:
- Login credentials
- Availability schedules
- Consultation responses (accept/reject)
- Legal summaries
- Appointment confirmations/cancellations
- Messages in consultations
- Profile update data

**Outputs (المخرجات)**:
- Authentication tokens
- Consultation requests
- Appointment requests
- Client information
- Consultation statistics
- Notifications

**Main Functions (الوظائف الرئيسية)**:
- Login and manage profile
- Accept/reject consultations
- Set availability schedules
- Manage appointments
- Communicate with clients
- Add legal summaries
- View laws and regulations

---

### 3. Employee (الموظف)
**Role**: الموظف الذي يدير العمليات اليومية
**Role**: Employee who manages daily operations

**Inputs (المدخلات)**:
- Login credentials
- Client management actions (activate, reject, suspend)
- Consultation assignment decisions
- Appointment management actions
- Availability management
- Fixed price management (CRUD operations)
- Profile update data

**Outputs (المخرجات)**:
- Authentication tokens
- Client lists and details
- Consultation lists and statistics
- Appointment lists
- Availability schedules
- Fixed price lists
- Notifications

**Main Functions (الوظائف الرئيسية)**:
- Login and manage profile
- Manage client accounts
- Assign consultations to lawyers
- Manage appointments
- Manage lawyer availability
- Manage fixed prices
- View statistics

---

### 4. Admin (المدير)
**Role**: المدير الذي يدير النظام بالكامل
**Role**: Admin who manages the entire system

**Inputs (المدخلات)**:
- Login credentials
- Lawyer management actions (create, update, delete)
- Employee management actions (create, update, delete)
- Law management actions (create, update, publish)
- Specialization management actions
- Job application decisions (approve, reject)
- Profile update data

**Outputs (المخرجات)**:
- Authentication tokens
- Lawyer lists and statistics
- Employee lists and statistics
- Law lists
- Specialization lists
- Job application lists
- Consultation statistics
- System statistics

**Main Functions (الوظائف الرئيسية)**:
- Login and manage profile
- Manage lawyers (CRUD, archive, restore)
- Manage employees (CRUD, archive, restore)
- Manage laws (CRUD, publish, archive)
- Manage specializations (CRUD, archive)
- Review and approve/reject job applications
- View system-wide statistics

---

### 5. Guest (الزائر)
**Role**: زائر غير مسجل يريد الاطلاع على المعلومات العامة
**Role**: Unregistered visitor who wants to view public information

**Inputs (المدخلات)**:
- Registration data (to become a client)
- Job application data (for lawyer or employee positions)
- Search queries (for lawyers, laws, specializations)

**Outputs (المخرجات)**:
- Public lawyer information
- Public specialization information
- Published laws
- Registration confirmation
- Job application confirmation

**Main Functions (الوظائف الرئيسية)**:
- Register as a new client
- View public lawyer profiles
- View specializations
- View published laws
- Submit job applications

---

## 🔄 تدفق البيانات / Data Flow

### Input Flow (تدفق المدخلات)
```
External Actor → API Gateway → Authentication → Authorization → Business Logic → Database/Storage
```

### Output Flow (تدفق المخرجات)
```
Database/Storage → Business Logic → Response Formatter → API Gateway → External Actor
```

---

## 🎯 الوظائف الرئيسية للنظام / Main System Functions

### 1. Authentication & Authorization (المصادقة والتفويض)
- **Input**: Credentials (email, password)
- **Output**: Authentication token, user information
- **Function**: Verify user identity and assign permissions

### 2. User Management (إدارة المستخدمين)
- **Input**: User data (create, update, delete)
- **Output**: User information, confirmation messages
- **Function**: Manage user accounts (clients, lawyers, employees, admins)

### 3. Consultation Management (إدارة الاستشارات)
- **Input**: Consultation requests, responses, messages
- **Output**: Consultation details, status updates
- **Function**: Handle consultation lifecycle (create, assign, accept, reject, complete)

### 4. Appointment Management (إدارة المواعيد)
- **Input**: Appointment booking requests, cancellations, rescheduling
- **Output**: Appointment confirmations, available slots
- **Function**: Schedule and manage appointments between clients and lawyers

### 5. Availability Management (إدارة التوفر)
- **Input**: Availability schedules, templates
- **Output**: Available time slots, schedules
- **Function**: Manage lawyer availability for appointments

### 6. Law Management (إدارة القوانين)
- **Input**: Law data (create, update, publish)
- **Output**: Law lists, details
- **Function**: Manage and publish laws for public viewing

### 7. Fixed Price Management (إدارة الأسعار الثابتة)
- **Input**: Price data (create, update, archive)
- **Output**: Price lists, details
- **Function**: Manage fixed service prices

### 8. Job Application Management (إدارة طلبات التوظيف)
- **Input**: Job application data, approval/rejection decisions
- **Output**: Application status, user creation
- **Function**: Process job applications and create user accounts

### 9. Notification Management (إدارة الإشعارات)
- **Input**: Events (consultation created, appointment booked, etc.)
- **Output**: Notifications (email, in-app)
- **Function**: Send notifications to relevant users

### 10. File Management (إدارة الملفات)
- **Input**: Files (photos, certificates, attachments)
- **Output**: File URLs, download links
- **Function**: Store and retrieve files

---

## 📊 Interaction Matrix / مصفوفة التفاعل

| Actor | Can Interact With | Main Interactions |
|-------|------------------|-------------------|
| **Client** | Lawyers, System | Create consultations, Book appointments, View laws, View prices |
| **Lawyer** | Clients, System | Accept consultations, Set availability, Manage appointments |
| **Employee** | Clients, Lawyers, System | Manage clients, Assign consultations, Manage prices |
| **Admin** | All, System | Manage all users, Manage laws, Review applications |
| **Guest** | System | Register, View public info, Submit applications |

---

## 🔐 Security Boundaries / حدود الأمان

### Public Access (الوصول العام)
- Guest registration
- Public lawyer profiles
- Published laws
- Public specializations
- Job application submission

### Authenticated Access (الوصول المصادق)
- All user-specific operations
- Consultation creation and management
- Appointment booking
- Profile management

### Role-Based Access (الوصول القائم على الدور)
- **Client**: Own data only
- **Lawyer**: Own consultations and appointments
- **Employee**: Client management, consultation assignment
- **Admin**: Full system access

---

## 📁 File Structure / هيكل الملفات

### PlantUML Files
- `SYSTEM_BLACK_BOX.puml` - Basic black box diagram
- `SYSTEM_BLACK_BOX_DETAILED.puml` - Detailed black box diagram with internal modules

### Documentation Files
- `BLACK_BOX_DOCUMENTATION.md` - This file
- `CONTROLLERS_AND_MODELS_DOCUMENTATION.md` - Detailed system documentation
- `SYSTEM_DIAGRAMS.puml` - Full system diagrams (ERD, Controllers)

---

## 🎨 Diagram Usage / استخدام المخططات

### Basic Black Box (`SYSTEM_BLACK_BOX.puml`)
- **Use for**: High-level system overview
- **Shows**: External actors, main services, data flow
- **Audience**: Stakeholders, non-technical users

### Detailed Black Box (`SYSTEM_BLACK_BOX_DETAILED.puml`)
- **Use for**: Technical documentation
- **Shows**: Internal modules, detailed data flow, system components
- **Audience**: Developers, system architects

---

## 📝 Notes / ملاحظات

1. **System is API-based**: All interactions happen through REST API endpoints
2. **Stateless Authentication**: Uses token-based authentication (Laravel Sanctum)
3. **Role-Based Access Control**: Different roles have different access levels
4. **File Storage**: Separate storage system for files (photos, documents, certificates)
5. **Notification System**: Integrated notification service for real-time updates
6. **Database**: MySQL database for all structured data
7. **Soft Deletes**: Most entities support soft deletion for data retention

---

**Last Updated**: 2025-01-20
**Version**: 1.0.0



