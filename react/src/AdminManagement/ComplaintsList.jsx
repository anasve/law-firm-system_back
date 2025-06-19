// src/AdminManagement/ComplaintsList.jsx
import React from "react";
import { useNavigate } from "react-router-dom";

const complaints = [
  {
    id: 1,
    name: "خالد العمري",
    submitter: "فاطمة أحمد",
    type: "رسوم مبالغ فيها",
    date: "1445/03/25 هـ",
    summary: "قام المحامي بفرض رسوم إضافية رسوم لم يتم الاتفاق عليها مسبقاً، وعندما طلبت توضيحاً رفض...",
  },
  {
    id: 2,
    name: "أحمد محمد",
    submitter: "سامي عبدالله",
    type: "سوء سلوك مهني",
    date: "1445/03/30 هـ",
    summary: "لم يلتزم المحامي بالحضور في جلسات المحكمة المتفق عليها، مما أدى إلى تأخير القضية واللحاق...",
  },
  {
    id: 3,
    name: "محمد العلي",
    submitter: "نورة سعد",
    type: "تضارب المصالح",
    date: "1445/03/13 هـ",
    summary: "اكتشفت أن المحامي يمثل الطرف الآخر في القضية...",
  },
  {
    id: 4,
    name: "سارة الأحمد",
    submitter: "عمر خالد",
    type: "إهمال",
    date: "1445/03/10 هـ",
    summary: "لم تقم المحامية بإعداد المستندات المطلوبة في الوقت المحدد...",
  },
];

export default function ComplaintsList() {
  const navigate = useNavigate();

  return (
    <div className="complaints-list-page" dir="rtl">
      <h2 className="complaints-title">نظام إدارة الشكاوى</h2>
      <div className="complaints-header-row">
        <button className="complaints-btn-main" disabled>
          جميع الشكاوى
        </button>
      </div>
      <div className="complaints-grid">
        {complaints.map((c) => (
          <div className="complaint-card" key={c.id}>
            <h3 className="complaint-card-title">{c.name}</h3>
            <div className="complaint-info">
              <div>مقدم الشكوى: <b>{c.submitter}</b></div>
              <div>نوع الشكوى: <b>{c.type}</b></div>
              <div>تاريخ التقديم: <b>{c.date}</b></div>
            </div>
            <div className="complaint-summary">{c.summary}</div>
            <button
              className="complaints-btn-details"
              onClick={() => navigate(`/complaints/${c.id}`)}
            >
              عرض التفاصيل
            </button>
          </div>
        ))}
      </div>
    </div>
  );
}