# Requirements Traceability Matrix (RTM) - Controllers Documentation
# مصفوفة تتبع المتطلبات - توثيق الـ Controllers

## نظرة عامة / Overview

هذا الملف يوثق جميع الـ Controllers في النظام والوظائف التي يقوم بها كل واحد (Requirements Traceability Matrix).
This document describes all Controllers in the system and the functions each one performs (Requirements Traceability Matrix).

---

## 📊 إحصائيات النظام / System Statistics

- **إجمالي عدد الـ Controllers**: 36 Controller
- **عدد الـ Methods**: 186+ Method
- **عدد الأدوار (Roles)**: 5 (Admin, Client, Employee, Lawyer, Guest)

---

## 🔐 Admin Controllers (8 Controllers)

### 1. AdminAuthController
**Path**: `app/Http/Controllers/API/Admin/AdminAuthController.php`
**Route Prefix**: `/api/admin`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `login()` | POST `/login` | تسجيل دخول المدير | Login admin user with email and password |
| `logout()` | POST `/logout` | تسجيل خروج المدير | Logout admin and revoke tokens |

---

### 2. AdminProfileController
**Path**: `app/Http/Controllers/API/Admin/AdminProfileController.php`
**Route Prefix**: `/api/admin`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `show()` | GET `/profile` | عرض ملف المدير الشخصي | Get admin profile information |
| `update()` | PUT `/profile` | تحديث ملف المدير الشخصي | Update admin profile data |

---

### 3. LawyerController
**Path**: `app/Http/Controllers/API/Admin/LawyerController.php`
**Route Prefix**: `/api/admin/lawyers`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قائمة المحامين | List all lawyers with search and filtering |
| `show($id)` | GET `/{id}` | عرض تفاصيل محامي | Show lawyer details |
| `store()` | POST `/` | إضافة محامي جديد | Create new lawyer account |
| `update($id)` | PUT `/{id}` | تحديث بيانات محامي | Update lawyer information |
| `destroy($id)` | DELETE `/{id}` | أرشفة محامي | Archive (soft delete) lawyer |
| `archived()` | GET `/archived` | عرض المحامين المؤرشفين | List archived lawyers |
| `restore($id)` | PUT `/{id}/restore` | استعادة محامي مؤرشف | Restore archived lawyer |
| `forceDelete($id)` | DELETE `/{id}/force` | حذف محامي نهائياً | Permanently delete lawyer |
| `total()` | GET `/total` | عدد المحامين الإجمالي | Get total count of lawyers |

---

### 4. EmployeeController
**Path**: `app/Http/Controllers/API/Admin/EmployeeController.php`
**Route Prefix**: `/api/admin/employees`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قائمة الموظفين | List all employees with search |
| `show($id)` | GET `/{id}` | عرض تفاصيل موظف | Show employee details |
| `store()` | POST `/` | إضافة موظف جديد | Create new employee account |
| `update($id)` | PUT `/{id}` | تحديث بيانات موظف | Update employee information |
| `destroy($id)` | DELETE `/{id}` | أرشفة موظف | Archive (soft delete) employee |
| `archived()` | GET `/archived` | عرض الموظفين المؤرشفين | List archived employees |
| `restore($id)` | PUT `/{id}/restore` | استعادة موظف مؤرشف | Restore archived employee |
| `forceDelete($id)` | DELETE `/{id}/force` | حذف موظف نهائياً | Permanently delete employee |
| `total()` | GET `/total` | عدد الموظفين الإجمالي | Get total count of employees |

---

### 5. LawController
**Path**: `app/Http/Controllers/API/Admin/LawController.php`
**Route Prefix**: `/api/admin/laws`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قائمة القوانين | List all laws with search and status filter |
| `published()` | GET `/published` | عرض القوانين المنشورة | List published laws only |
| `draft()` | GET `/draft` | عرض القوانين المسودة | List draft laws only |
| `archived()` | GET `/archived` | عرض القوانين المؤرشفة | List archived laws |
| `show($id)` | GET `/{id}` | عرض تفاصيل قانون | Show law details |
| `store()` | POST `/` | إضافة قانون جديد | Create new law |
| `update($id)` | PUT `/{id}` | تحديث قانون | Update law information |
| `toggleStatus($id)` | POST `/{id}/toggle-status` | تغيير حالة القانون | Toggle law status (draft/published) |
| `destroy($id)` | DELETE `/{id}` | أرشفة قانون | Archive (soft delete) law |
| `restore($id)` | PUT `/{id}/restore` | استعادة قانون مؤرشف | Restore archived law |
| `forceDelete($id)` | DELETE `/{id}/force` | حذف قانون نهائياً | Permanently delete law |

---

### 6. SpecializationController
**Path**: `app/Http/Controllers/API/Admin/SpecializationController.php`
**Route Prefix**: `/api/admin/specializations`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قائمة التخصصات | List all specializations |
| `show($id)` | GET `/{id}` | عرض تفاصيل تخصص | Show specialization details |
| `store()` | POST `/` | إضافة تخصص جديد | Create new specialization |
| `update($id)` | PUT `/{id}` | تحديث تخصص | Update specialization |
| `destroy($id)` | DELETE `/{id}` | أرشفة تخصص | Archive (soft delete) specialization |
| `archived()` | GET `/archived` | عرض التخصصات المؤرشفة | List archived specializations |
| `restore($id)` | PUT `/{id}/restore` | استعادة تخصص مؤرشف | Restore archived specialization |
| `forceDelete($id)` | DELETE `/{id}/force` | حذف تخصص نهائياً | Permanently delete specialization |

---

### 7. AdminConsultationController
**Path**: `app/Http/Controllers/API/Admin/AdminConsultationController.php`
**Route Prefix**: `/api/admin/consultations`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قائمة الاستشارات | List all consultations (read-only) |
| `show($id)` | GET `/{id}` | عرض تفاصيل استشارة | Show consultation details |
| `statistics()` | GET `/statistics` | إحصائيات الاستشارات | Get consultation statistics |

---

### 8. JobApplicationController (Admin)
**Path**: `app/Http/Controllers/API/Admin/JobApplicationController.php`
**Route Prefix**: `/api/admin/job-applications`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قائمة طلبات التوظيف | List all job applications with filters |
| `show($id)` | GET `/{id}` | عرض تفاصيل طلب توظيف | Show job application details |
| `approve($id)` | POST `/{id}/approve` | الموافقة على طلب توظيف | Approve job application and create user account |
| `reject($id)` | POST `/{id}/reject` | رفض طلب توظيف | Reject job application |
| `destroy($id)` | DELETE `/{id}` | حذف طلب توظيف | Delete job application |
| `pendingCount()` | GET `/pending-count` | عدد الطلبات المعلقة | Get count of pending applications |

---

## 👤 Client Controllers (7 Controllers)

### 9. ClientAuthController
**Path**: `app/Http/Controllers/API/Client/ClientAuthController.php`
**Route Prefix**: `/api/client`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `register()` | POST `/register` | تسجيل عميل جديد | Register new client account |
| `login()` | POST `/login` | تسجيل دخول العميل | Login client with email and password |
| `logout()` | POST `/logout` | تسجيل خروج العميل | Logout client and revoke tokens |

---

### 10. ClientProfileController
**Path**: `app/Http/Controllers/API/Client/ClientProfileController.php`
**Route Prefix**: `/api/client`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `show()` | GET `/profile` | عرض ملف العميل الشخصي | Get client profile information |
| `update()` | PUT/PATCH `/profile` | تحديث ملف العميل الشخصي | Update client profile data |

---

### 11. ConsultationController (Client)
**Path**: `app/Http/Controllers/API/Client/ConsultationController.php`
**Route Prefix**: `/api/client/consultations`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض استشاراتي | List client's consultations |
| `show($id)` | GET `/{id}` | عرض تفاصيل استشارة | Show consultation details |
| `store()` | POST `/` | إنشاء استشارة جديدة | Create new consultation request |
| `update($id)` | PUT `/{id}` | تحديث استشارة | Update consultation |
| `destroy($id)` | DELETE `/{id}` | حذف استشارة | Delete consultation |
| `cancel($id)` | POST `/{id}/cancel` | إلغاء استشارة | Cancel consultation |
| `complete($id)` | POST `/{id}/complete` | إكمال استشارة | Mark consultation as completed |
| `sendMessage($consultationId)` | POST `/{consultationId}/messages` | إرسال رسالة في استشارة | Send message in consultation |
| `getMessages($consultationId)` | GET `/{consultationId}/messages` | عرض رسائل الاستشارة | Get consultation messages |
| `createReview($consultationId)` | POST `/{consultationId}/review` | إضافة تقييم للاستشارة | Create review for consultation |

---

### 12. AppointmentController (Client)
**Path**: `app/Http/Controllers/API/Client/AppointmentController.php`
**Route Prefix**: `/api/client/appointments`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `getAvailableSlots($lawyerId)` | GET `/lawyers/{lawyerId}/available-slots` | عرض الأوقات المتاحة | Get available time slots for lawyer |
| `myAppointments()` | GET `/` | عرض مواعيدي | List client's appointments |
| `bookAppointment($consultationId)` | POST `/consultations/{consultationId}/appointments` | حجز موعد من استشارة | Book appointment from consultation |
| `bookDirectAppointment()` | POST `/direct` | حجز موعد مباشر | Book appointment directly without consultation |
| `show($id)` | GET `/{id}` | عرض تفاصيل موعد | Show appointment details |
| `cancel($id)` | POST `/{id}/cancel` | إلغاء موعد | Cancel appointment |
| `reschedule($id)` | POST `/{id}/reschedule` | إعادة جدولة موعد | Reschedule appointment |
| `calendarMonth()` | GET `/calendar/month` | تقويم شهري للمواعيد | Get monthly calendar view |

---

### 13. ClientLawController
**Path**: `app/Http/Controllers/API/Client/ClientLawController.php`
**Route Prefix**: `/api/client/laws`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض القوانين المنشورة | List published laws with search |
| `show($id)` | GET `/{id}` | عرض تفاصيل قانون | Show law details |
| `categories()` | GET `/categories` | عرض تصنيفات القوانين | Get law categories list |

---

### 14. ClientFixedPriceController
**Path**: `app/Http/Controllers/API/Client/ClientFixedPriceController.php`
**Route Prefix**: `/api/client/fixed-prices`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض الأسعار الثابتة النشطة | List active fixed prices (read-only) |

---

### 15. NotificationController (Client)
**Path**: `app/Http/Controllers/API/Client/NotificationController.php`
**Route Prefix**: `/api/client/notifications`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض الإشعارات | List client notifications |
| `unread()` | GET `/unread` | عرض الإشعارات غير المقروءة | List unread notifications |
| `unreadCount()` | GET `/unread-count` | عدد الإشعارات غير المقروءة | Get unread notifications count |
| `markAsRead($id)` | PUT `/{id}/read` | تحديد إشعار كمقروء | Mark notification as read |
| `markAllAsRead()` | PUT `/read-all` | تحديد جميع الإشعارات كمقروءة | Mark all notifications as read |
| `destroy($id)` | DELETE `/{id}` | حذف إشعار | Delete notification |
| `destroyAll()` | DELETE `/` | حذف جميع الإشعارات | Delete all notifications |

---

## 👔 Employee Controllers (9 Controllers)

### 16. EmployeeAuthController
**Path**: `app/Http/Controllers/API/Employee/EmployeeAuthController.php`
**Route Prefix**: `/api/employee`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `login()` | POST `/login` | تسجيل دخول الموظف | Login employee with email and password |
| `logout()` | POST `/logout` | تسجيل خروج الموظف | Logout employee and revoke tokens |

---

### 17. EmployeeProfileController
**Path**: `app/Http/Controllers/API/Employee/EmployeeProfileController.php`
**Route Prefix**: `/api/employee`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `show()` | GET `/profile` | عرض ملف الموظف الشخصي | Get employee profile information |
| `update()` | POST `/profile` | تحديث ملف الموظف الشخصي | Update employee profile data |

---

### 18. ClientManagementController
**Path**: `app/Http/Controllers/API/Employee/ClientManagementController.php`
**Route Prefix**: `/api/employee/clients`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قائمة العملاء | List all clients with search and filters |
| `show($id)` | GET `/{id}` | عرض تفاصيل عميل | Show client details |
| `update($id)` | PUT `/{id}` | تحديث بيانات عميل | Update client information |
| `destroy($id)` | DELETE `/{id}` | أرشفة عميل | Archive (soft delete) client |
| `pendingVerified()` | GET `/pending-verified` | عرض العملاء المنتظرين الموافقة | List email-verified clients pending approval |
| `approved()` | GET `/approved` | عرض العملاء المعتمدين | List approved (active) clients |
| `suspended()` | GET `/suspended` | عرض العملاء المعلقين | List suspended clients |
| `rejected()` | GET `/rejected` | عرض العملاء المرفوضين | List rejected clients |
| `archived()` | GET `/archived` | عرض العملاء المؤرشفين | List archived clients |
| `activate($id)` | POST `/{id}/activate` | تفعيل حساب عميل | Activate client account |
| `reject($id)` | POST `/{id}/reject` | رفض حساب عميل | Reject client account |
| `suspend($id)` | POST `/{id}/suspend` | تعليق حساب عميل | Suspend client account |
| `restore($id)` | PUT `/{id}/restore` | استعادة عميل مؤرشف | Restore archived client |
| `forceDelete($id)` | DELETE `/{id}/force` | حذف عميل نهائياً | Permanently delete client |

---

### 19. EmployeeConsultationController
**Path**: `app/Http/Controllers/API/Employee/EmployeeConsultationController.php`
**Route Prefix**: `/api/employee/consultations`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قائمة الاستشارات | List all consultations with filters |
| `pending()` | GET `/pending` | عرض الاستشارات المعلقة | List pending consultations |
| `show($id)` | GET `/{id}` | عرض تفاصيل استشارة | Show consultation details |
| `assign($id)` | POST `/{id}/assign` | تعيين استشارة لمحامي | Assign consultation to lawyer |
| `autoAssign($id)` | POST `/{id}/auto-assign` | تعيين تلقائي للاستشارة | Auto-assign consultation to available lawyer |
| `statistics()` | GET `/statistics` | إحصائيات الاستشارات | Get consultation statistics |

---

### 20. EmployeeAppointmentController
**Path**: `app/Http/Controllers/API/Employee/EmployeeAppointmentController.php`
**Route Prefix**: `/api/employee/appointments`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قائمة المواعيد | List all appointments |
| `customTimeRequests()` | GET `/custom-time-requests` | عرض طلبات المواعيد المخصصة | List custom time appointment requests |
| `show($id)` | GET `/{id}` | عرض تفاصيل موعد | Show appointment details |
| `accept($id)` | POST `/{id}/accept` | قبول موعد | Accept appointment request |
| `reject($id)` | POST `/{id}/reject` | رفض موعد | Reject appointment request |
| `calendarMonth()` | GET `/calendar/month` | تقويم شهري | Get monthly calendar view |
| `calendarWeek()` | GET `/calendar/week` | تقويم أسبوعي | Get weekly calendar view |
| `calendarDay()` | GET `/calendar/day` | تقويم يومي | Get daily calendar view |

---

### 21. EmployeeAvailabilityController
**Path**: `app/Http/Controllers/API/Employee/EmployeeAvailabilityController.php`
**Route Prefix**: `/api/employee/availability`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قائمة التوفر | List lawyer availability slots |
| `show($id)` | GET `/{id}` | عرض تفاصيل توفر | Show availability details |
| `store()` | POST `/` | إضافة وقت توفر جديد | Create new availability slot |
| `update($id)` | PUT `/{id}` | تحديث وقت توفر | Update availability slot |
| `destroy($id)` | DELETE `/{id}` | حذف وقت توفر | Delete availability slot |
| `storeBatch()` | POST `/batch` | إضافة عدة أوقات توفر | Create multiple availability slots |
| `createSchedule()` | POST `/create-schedule` | إنشاء جدول عمل | Create work schedule for lawyer |

---

### 22. EmployeeAvailabilityTemplateController
**Path**: `app/Http/Controllers/API/Employee/EmployeeAvailabilityTemplateController.php`
**Route Prefix**: `/api/employee/availability-templates`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قوالب التوفر | List availability templates |
| `show($id)` | GET `/{id}` | عرض تفاصيل قالب | Show template details |
| `store()` | POST `/` | إضافة قالب جديد | Create new availability template |
| `update($id)` | PUT `/{id}` | تحديث قالب | Update availability template |
| `destroy($id)` | DELETE `/{id}` | حذف قالب | Delete availability template |
| `apply($id)` | POST `/{id}/apply` | تطبيق قالب | Apply template to create availability slots |

---

### 23. EmployeeFixedPriceController
**Path**: `app/Http/Controllers/API/Employee/EmployeeFixedPriceController.php`
**Route Prefix**: `/api/employee/fixed-prices`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض جميع الأسعار | List all fixed prices with filters |
| `active()` | GET `/active` | عرض الأسعار النشطة | List active fixed prices only |
| `archived()` | GET `/archived` | عرض الأسعار المؤرشفة | List archived fixed prices |
| `show($id)` | GET `/{id}` | عرض تفاصيل سعر | Show fixed price details |
| `store()` | POST `/` | إضافة سعر جديد | Create new fixed price |
| `update($id)` | PUT `/{id}` | تحديث سعر | Update fixed price |
| `destroy($id)` | DELETE `/{id}` | أرشفة سعر | Archive (soft delete) fixed price |
| `restore($id)` | PUT `/{id}/restore` | استعادة سعر مؤرشف | Restore archived fixed price |
| `forceDelete($id)` | DELETE `/{id}/force` | حذف سعر نهائياً | Permanently delete fixed price |

---

### 24. NotificationController (Employee)
**Path**: `app/Http/Controllers/API/Employee/NotificationController.php`
**Route Prefix**: `/api/employee/notifications`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض الإشعارات | List employee notifications |
| `unread()` | GET `/unread` | عرض الإشعارات غير المقروءة | List unread notifications |
| `unreadCount()` | GET `/unread-count` | عدد الإشعارات غير المقروءة | Get unread notifications count |
| `markAsRead($id)` | PUT `/{id}/read` | تحديد إشعار كمقروء | Mark notification as read |
| `markAllAsRead()` | PUT `/read-all` | تحديد جميع الإشعارات كمقروءة | Mark all notifications as read |
| `destroy($id)` | DELETE `/{id}` | حذف إشعار | Delete notification |

---

## ⚖️ Lawyer Controllers (7 Controllers)

### 25. LawyerAuthController
**Path**: `app/Http/Controllers/API/Lawyer/LawyerAuthController.php`
**Route Prefix**: `/api/lawyer`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `login()` | POST `/login` | تسجيل دخول المحامي | Login lawyer with email and password |
| `logout()` | POST `/logout` | تسجيل خروج المحامي | Logout lawyer and revoke tokens |

---

### 26. LawyerProfileController
**Path**: `app/Http/Controllers/API/Lawyer/LawyerProfileController.php`
**Route Prefix**: `/api/lawyer`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `show()` | GET `/profile` | عرض ملف المحامي الشخصي | Get lawyer profile information |
| `update()` | PUT `/profile` | تحديث ملف المحامي الشخصي | Update lawyer profile data |

---

### 27. LawyerConsultationController
**Path**: `app/Http/Controllers/API/Lawyer/LawyerConsultationController.php`
**Route Prefix**: `/api/lawyer/consultations`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض استشاراتي | List lawyer's consultations |
| `pending()` | GET `/pending` | عرض الاستشارات المعلقة | List pending consultations for lawyer |
| `show($id)` | GET `/{id}` | عرض تفاصيل استشارة | Show consultation details |
| `accept($id)` | POST `/{id}/accept` | قبول استشارة | Accept consultation request |
| `reject($id)` | POST `/{id}/reject` | رفض استشارة | Reject consultation request |
| `complete($id)` | POST `/{id}/complete` | إكمال استشارة | Mark consultation as completed |
| `sendMessage($consultationId)` | POST `/{consultationId}/messages` | إرسال رسالة | Send message in consultation |
| `getMessages($consultationId)` | GET `/{consultationId}/messages` | عرض الرسائل | Get consultation messages |
| `markMessageAsRead($consultationId, $messageId)` | PUT `/{consultationId}/messages/{messageId}/read` | تحديد رسالة كمقروءة | Mark message as read |
| `createAppointment($consultationId)` | POST `/{consultationId}/appointments` | إنشاء موعد من استشارة | Create appointment from consultation |

---

### 28. LawyerAppointmentController
**Path**: `app/Http/Controllers/API/Lawyer/LawyerAppointmentController.php`
**Route Prefix**: `/api/lawyer/appointments`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض مواعيدي | List lawyer's appointments |
| `upcoming()` | GET `/upcoming` | عرض المواعيد القادمة | List upcoming appointments |
| `show($id)` | GET `/{id}` | عرض تفاصيل موعد | Show appointment details |
| `calendarMonth()` | GET `/calendar/month` | تقويم شهري | Get monthly calendar view |

---

### 29. LawyerAvailabilityController
**Path**: `app/Http/Controllers/API/Lawyer/LawyerAvailabilityController.php`
**Route Prefix**: `/api/lawyer/availability`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض أوقات التوفر | List lawyer's availability slots |
| `store()` | POST `/` | إضافة وقت توفر | Create new availability slot |
| `update($id)` | PUT `/{id}` | تحديث وقت توفر | Update availability slot |
| `destroy($id)` | DELETE `/{id}` | حذف وقت توفر | Delete availability slot |
| `storeBatch()` | POST `/batch` | إضافة عدة أوقات توفر | Create multiple availability slots |

---

### 30. LawyerLawController
**Path**: `app/Http/Controllers/API/Lawyer/LawyerLawController.php`
**Route Prefix**: `/api/lawyer/laws`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض القوانين المنشورة | List published laws with search |
| `show($id)` | GET `/{id}` | عرض تفاصيل قانون | Show law details |
| `categories()` | GET `/categories` | عرض تصنيفات القوانين | Get law categories list |

---

### 31. NotificationController (Lawyer)
**Path**: `app/Http/Controllers/API/Lawyer/NotificationController.php`
**Route Prefix**: `/api/lawyer/notifications`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض الإشعارات | List lawyer notifications |
| `unread()` | GET `/unread` | عرض الإشعارات غير المقروءة | List unread notifications |
| `unreadCount()` | GET `/unread-count` | عدد الإشعارات غير المقروءة | Get unread notifications count |
| `markAsRead($id)` | PUT `/{id}/read` | تحديد إشعار كمقروء | Mark notification as read |
| `markAllAsRead()` | PUT `/read-all` | تحديد جميع الإشعارات كمقروءة | Mark all notifications as read |
| `destroy($id)` | DELETE `/{id}` | حذف إشعار | Delete notification |

---

## 🌐 Guest Controllers (5 Controllers)

### 32. GuestAuthController
**Path**: `app/Http/Controllers/API/Guest/GuestAuthController.php`
**Route Prefix**: `/api/guest`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `register()` | POST `/register` | تسجيل عميل جديد | Register new client account (public) |
| `login()` | POST `/login` | تسجيل دخول | Login as client (public) |

---

### 33. LawyerController (Guest)
**Path**: `app/Http/Controllers/API/Guest/LawyerController.php`
**Route Prefix**: `/api/guest/lawyers`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قائمة المحامين (عام) | List public lawyer profiles |
| `show($id)` | GET `/{id}` | عرض تفاصيل محامي (عام) | Show public lawyer profile |

---

### 34. SpecializationController (Guest)
**Path**: `app/Http/Controllers/API/Guest/SpecializationController.php`
**Route Prefix**: `/api/guest/specializations`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض قائمة التخصصات (عام) | List public specializations |
| `show($id)` | GET `/{id}` | عرض تفاصيل تخصص (عام) | Show public specialization details |

---

### 35. LawController (Guest)
**Path**: `app/Http/Controllers/API/Guest/LawController.php`
**Route Prefix**: `/api/guest/laws`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `index()` | GET `/` | عرض القوانين المنشورة (عام) | List published laws (public) |
| `show($id)` | GET `/{id}` | عرض تفاصيل قانون (عام) | Show published law details (public) |

---

### 36. JobApplicationController (Guest)
**Path**: `app/Http/Controllers/API/Guest/JobApplicationController.php`
**Route Prefix**: `/api/guest/job-applications`

| Method | Route | الوظيفة / Function | الوصف / Description |
|--------|-------|-------------------|---------------------|
| `store()` | POST `/` | تقديم طلب توظيف | Submit job application (lawyer/employee) |

---

## 📊 Summary / الملخص

### Controllers by Role

| Role | Number of Controllers | Total Methods |
|------|----------------------|---------------|
| **Admin** | 8 | ~50 |
| **Client** | 7 | ~40 |
| **Employee** | 9 | ~60 |
| **Lawyer** | 7 | ~35 |
| **Guest** | 5 | ~10 |
| **Total** | **36** | **~195** |

---

## 🔑 Key Features by Controller Type

### Authentication Controllers
- User login/logout
- Token management
- Registration (Client/Guest)

### Profile Controllers
- View profile
- Update profile information

### Management Controllers
- CRUD operations
- Status management
- Archive/Restore
- Force delete

### Consultation Controllers
- Create/View consultations
- Accept/Reject consultations
- Messaging system
- Review system
- Assignment (Employee)

### Appointment Controllers
- Book appointments
- View appointments
- Cancel/Reschedule
- Calendar views
- Custom time requests

### Availability Controllers
- Manage availability slots
- Templates
- Batch operations
- Schedule creation

### Law Controllers
- View published laws
- Search and filter
- Categories
- CRUD (Admin)

### Fixed Price Controllers
- View prices (Client/Lawyer)
- Full management (Employee)
- Archive/Restore

### Notification Controllers
- View notifications
- Mark as read
- Delete notifications

### Job Application Controllers
- Submit application (Guest)
- Review/Approve/Reject (Admin)

---

**Last Updated**: 2025-01-20
**Version**: 1.0.0



