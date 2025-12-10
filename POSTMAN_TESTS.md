# اختبارات Postman - نظام الاستشارات والمواعيد

## 1. حجز موعد مباشر بدون استشارة

**POST** `/api/client/appointments/direct`

**Headers:**
```
Authorization: Bearer {client_token}
Content-Type: application/json
```

**Body:**
```json
{
    "lawyer_id": 1,
    "availability_id": 5,
    "subject": "استشارة قانونية عاجلة",
    "description": "أحتاج استشارة حول عقد تجاري",
    "type": "online",
    "meeting_link": "https://meet.google.com/xxx",
    "notes": "ملاحظات إضافية"
}
```

---

## 2. إنشاء استشارة مع حجز موعد مباشر

**POST** `/api/client/consultations`

**Headers:**
```
Authorization: Bearer {client_token}
Content-Type: application/json
```

**Body:**
```json
{
    "lawyer_id": 1,
    "subject": "استشارة قانونية",
    "description": "وصف المشكلة القانونية",
    "priority": "urgent",
    "preferred_channel": "appointment",
    "appointment_availability_id": 5,
    "appointment_type": "in_office",
    "appointment_notes": "ملاحظات"
}
```

---

## 3. عرض الأوقات المتاحة لمحامي

**GET** `/api/client/lawyers/{lawyerId}/available-slots?date=2025-12-15`

**Headers:**
```
Authorization: Bearer {client_token}
```

---

## 4. حجز موعد لاستشارة موجودة

**POST** `/api/client/consultations/{consultationId}/appointments`

**Headers:**
```
Authorization: Bearer {client_token}
Content-Type: application/json
```

**Body:**
```json
{
    "availability_id": 5,
    "type": "online",
    "meeting_link": "https://meet.google.com/xxx",
    "notes": "ملاحظات"
}
```

---

## 5. إعادة جدولة موعد

**POST** `/api/client/appointments/{id}/reschedule`

**Headers:**
```
Authorization: Bearer {client_token}
Content-Type: application/json
```

**Body:**
```json
{
    "availability_id": 10
}
```

---

## 6. إلغاء موعد (عميل)

**POST** `/api/client/appointments/{id}/cancel`

**Headers:**
```
Authorization: Bearer {client_token}
Content-Type: application/json
```

**Body:**
```json
{
    "cancellation_reason": "سبب الإلغاء"
}
```

---

## 7. عرض مواعيد العميل

**GET** `/api/client/appointments`

**Headers:**
```
Authorization: Bearer {client_token}
```

**Query Parameters:**
- `status`: pending, confirmed, done, cancelled

---

## 8. تأكيد موعد (موظف)

**POST** `/api/employee/appointments/{id}/confirm`

**Headers:**
```
Authorization: Bearer {employee_token}
```

---

## 9. إلغاء موعد (موظف)

**POST** `/api/employee/appointments/{id}/cancel`

**Headers:**
```
Authorization: Bearer {employee_token}
Content-Type: application/json
```

**Body:**
```json
{
    "cancellation_reason": "سبب الإلغاء"
}
```

---

## 10. إضافة وقت متاح (موظف)

**POST** `/api/employee/availability`

**Headers:**
```
Authorization: Bearer {employee_token}
Content-Type: application/json
```

**Body:**
```json
{
    "lawyer_id": 1,
    "date": "2025-12-15",
    "start_time": "09:00",
    "end_time": "10:00",
    "notes": "ملاحظات"
}
```

---

## 11. إضافة أوقات متعددة دفعة واحدة (موظف)

**POST** `/api/employee/availability/batch`

**Headers:**
```
Authorization: Bearer {employee_token}
Content-Type: application/json
```

**Body:**
```json
{
    "lawyer_id": 1,
    "availabilities": [
        {
            "date": "2025-12-15",
            "start_time": "09:00",
            "end_time": "10:00"
        },
        {
            "date": "2025-12-15",
            "start_time": "10:00",
            "end_time": "11:00"
        }
    ]
}
```

---

## 12. إنشاء قالب أوقات (موظف)

**POST** `/api/employee/availability-templates`

**Headers:**
```
Authorization: Bearer {employee_token}
Content-Type: application/json
```

**Body:**
```json
{
    "lawyer_id": 1,
    "name": "أوقات العمل الأسبوعية",
    "start_time": "09:00",
    "end_time": "17:00",
    "days_of_week": [1, 2, 3, 4, 5],
    "start_date": "2025-12-01",
    "end_date": "2025-12-31",
    "is_active": true
}
```

---

## 13. تطبيق قالب أوقات (موظف)

**POST** `/api/employee/availability-templates/{id}/apply`

**Headers:**
```
Authorization: Bearer {employee_token}
Content-Type: application/json
```

**Body:**
```json
{
    "start_date": "2025-12-01",
    "end_date": "2025-12-31"
}
```

---

## 14. تعطيل وقت (إجازة) (موظف)

**PUT** `/api/employee/availability/{id}`

**Headers:**
```
Authorization: Bearer {employee_token}
Content-Type: application/json
```

**Body:**
```json
{
    "is_vacation": true,
    "vacation_reason": "إجازة سنوية"
}
```

---

## 15. عرض جميع المواعيد (موظف)

**GET** `/api/employee/appointments`

**Headers:**
```
Authorization: Bearer {employee_token}
```

**Query Parameters:**
- `lawyer_id`: فلترة حسب المحامي
- `client_id`: فلترة حسب العميل
- `status`: فلترة حسب الحالة
- `date`: فلترة حسب التاريخ

---

## ملاحظات مهمة:

1. **حجز موعد مباشر**: لا يتطلب استشارة موجودة
2. **حجز موعد مع استشارة**: يتطلب استشارة مقبولة (`accepted`)
3. **التذكير التلقائي**: يتم إرساله قبل 24 ساعة وساعة واحدة (يتم تشغيله تلقائياً)
4. **تأكيد الموعد**: يجب أن يؤكد الموظف الموعد ليصبح `confirmed`
5. **إلغاء الموعد**: العميل لا يمكنه الإلغاء قبل ساعة واحدة من الموعد
6. **إعادة الجدولة**: متاحة قبل ساعة واحدة من الموعد
7. **الأوقات المتاحة**: لا تظهر الأوقات المحجوزة أو المعطلة (إجازات)

