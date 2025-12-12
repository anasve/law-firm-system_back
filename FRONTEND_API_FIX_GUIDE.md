# 🔧 إصلاح مشكلة 404 - Frontend API Configuration

## ❌ المشكلة

الـ frontend يحاول الوصول إلى:
```
http://localhost:5173/api/client/notifications/unread-count
http://localhost:5173/api/client/notifications?limit=10
http://localhost:5173/api/client/appointments
```

**النتيجة:** 404 Not Found

---

## ✅ الحل

### المشكلة: Base URL خاطئ

الـ React dev server يعمل على `localhost:5173`، لكن **Laravel API يعمل على `localhost:8000`**.

يجب تغيير **Base URL** في axios configuration.

---

## 📝 خطوات الإصلاح

### 1. إيجاد ملف API Configuration

ابحث عن ملف مثل:
- `src/services/api.js`
- `src/config/api.js`
- `src/utils/axios.js`
- أو أي ملف يحتوي على `axios.create()`

---

### 2. تحديث Base URL

**قبل (خطأ):**
```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: '/api', // ❌ يستخدم نفس الـ port (5173)
  // أو
  baseURL: 'http://localhost:5173/api', // ❌ خطأ
});
```

**بعد (صحيح):**
```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api', // ✅ Laravel API port
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});
```

---

### 3. إضافة Authentication Interceptor

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// إضافة token تلقائياً
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('client_token'); // أو 'token' حسب ما تستخدم
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// معالجة الأخطاء
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // تسجيل خروج تلقائي
      localStorage.removeItem('client_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default api;
```

---

### 4. استخدام Environment Variables (موصى به)

**إنشاء ملف `.env` في React:**
```env
VITE_API_BASE_URL=http://localhost:8000/api
```

**في ملف API:**
```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});
```

---

## 🔍 مثال كامل: `src/services/api.js`

```javascript
import axios from 'axios';

// Base URL للـ Laravel API
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  timeout: 10000, // 10 ثواني
});

// Request Interceptor - إضافة token
api.interceptors.request.use(
  (config) => {
    // جلب token من localStorage
    const token = localStorage.getItem('client_token') || 
                  localStorage.getItem('token') || 
                  localStorage.getItem('auth_token');
    
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response Interceptor - معالجة الأخطاء
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    // 401 Unauthorized - تسجيل خروج تلقائي
    if (error.response?.status === 401) {
      localStorage.removeItem('client_token');
      localStorage.removeItem('token');
      localStorage.removeItem('auth_token');
      
      // إعادة توجيه لصفحة تسجيل الدخول
      if (window.location.pathname !== '/login') {
        window.location.href = '/login';
      }
    }
    
    // 404 Not Found
    if (error.response?.status === 404) {
      console.error('API Endpoint not found:', error.config.url);
    }
    
    // 500 Server Error
    if (error.response?.status === 500) {
      console.error('Server error:', error.response.data);
    }
    
    return Promise.reject(error);
  }
);

export default api;
```

---

## 📋 تحديث Services

### `src/services/notificationsService.js`

```javascript
import api from './api'; // ✅ استيراد من api.js

export const notificationsService = {
  // جلب الإشعارات
  getNotifications: async (limit = 10) => {
    const response = await api.get('/client/notifications', {
      params: { limit }
    });
    return response.data;
  },

  // عدد الإشعارات غير المقروءة
  getUnreadCount: async () => {
    const response = await api.get('/client/notifications/unread-count');
    return response.data;
  },

  // تحديد إشعار كمقروء
  markAsRead: async (id) => {
    const response = await api.put(`/client/notifications/${id}/read`);
    return response.data;
  },

  // تحديد الكل كمقروء
  markAllAsRead: async () => {
    const response = await api.put('/client/notifications/read-all');
    return response.data;
  },
};
```

---

### `src/services/appointmentsService.js`

```javascript
import api from './api'; // ✅ استيراد من api.js

export const appointmentsService = {
  // جلب المواعيد
  getAppointments: async (filters = {}) => {
    const response = await api.get('/client/appointments', {
      params: filters
    });
    return response.data;
  },

  // جلب موعد محدد
  getAppointment: async (id) => {
    const response = await api.get(`/client/appointments/${id}`);
    return response.data;
  },

  // حجز موعد مباشر
  bookDirectAppointment: async (data) => {
    const response = await api.post('/client/appointments/direct', data);
    return response.data;
  },

  // إلغاء موعد
  cancelAppointment: async (id, reason) => {
    const response = await api.post(`/client/appointments/${id}/cancel`, {
      cancellation_reason: reason
    });
    return response.data;
  },
};
```

---

## ✅ Checklist

- [ ] تحديث `baseURL` في axios إلى `http://localhost:8000/api`
- [ ] إضافة authentication interceptor
- [ ] إضافة error handling interceptor
- [ ] تحديث جميع services لاستخدام `api` من `api.js`
- [ ] التأكد من أن Laravel server يعمل على port 8000
- [ ] اختبار الطلبات في Browser DevTools

---

## 🧪 اختبار

### 1. تأكد من أن Laravel يعمل:
```bash
php artisan serve
```
يجب أن يعمل على: `http://localhost:8000`

### 2. اختبار في Browser Console:
```javascript
// افتح Browser Console واختبر:
fetch('http://localhost:8000/api/client/notifications/unread-count', {
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Accept': 'application/json'
  }
})
.then(r => r.json())
.then(console.log)
.catch(console.error);
```

### 3. تحقق من Network Tab:
- افتح Browser DevTools → Network
- يجب أن ترى الطلبات تذهب إلى `localhost:8000` وليس `localhost:5173`

---

## 🔗 Endpoints الصحيحة

بعد الإصلاح، يجب أن تعمل هذه الـ endpoints:

```
✅ GET  http://localhost:8000/api/client/notifications
✅ GET  http://localhost:8000/api/client/notifications/unread-count
✅ GET  http://localhost:8000/api/client/appointments
✅ POST http://localhost:8000/api/client/appointments/direct
```

---

## ⚠️ ملاحظات مهمة

1. **CORS**: تأكد من أن Laravel يسمح بـ CORS من `localhost:5173`
   - في `config/cors.php`:
   ```php
   'allowed_origins' => ['http://localhost:5173'],
   ```

2. **Token Storage**: تأكد من أن token محفوظ بشكل صحيح:
   ```javascript
   localStorage.setItem('client_token', token);
   ```

3. **Environment Variables**: في Production، استخدم environment variables:
   ```env
   VITE_API_BASE_URL=https://api.yourdomain.com/api
   ```

---

## 🎯 النتيجة المتوقعة

بعد الإصلاح:
- ✅ الطلبات تذهب إلى `localhost:8000`
- ✅ لا توجد أخطاء 404
- ✅ الإشعارات والمواعيد تعمل بشكل صحيح

---

**آخر تحديث:** 2025-12-09

