// src/AdminManagement/ComplaintDetails.jsx
import React from "react";
import { useParams } from "react-router-dom";

const complaintData = {
  2: {
    id: 2,
    lawyer: "أحمد محمد",
    submitter: "سامي عبدالله",
    type: "سوء سلوك مهني",
    date: "1445/03/30 هـ",
    phone: "0555123456",
    status: "قيد الانتظار",
    details: "لم يلتزم المحامي بالحضور في جلسات المحكمة المتفق عليها، مما أدى إلى تأخير القضية وإلحاق الضرر بمصالحي.",
    actions: ["قيد الانتظار", "قيد المعالجة", "تم الحل", "رفض"],
    comments: [
      { date: "1445/03/30 هـ", text: "النظام" },
      // أضف تعليقات أخرى هنا
    ],
  },
  // أضف بيانات شكاوى أخرى حسب الحاجة
};

function getStatusColor(status) {
  if (status === "قيد الانتظار") return { background: "#fde68a", color: "#b45309", border: "1.5px solid #eab308" };
  if (status === "قيد المعالجة") return { background: "#dbeafe", color: "#2563eb", border: "1.5px solid #2563eb" };
  if (status === "تم الحل") return { background: "#bbf7d0", color: "#166534", border: "1.5px solid #22c55e" };
  if (status === "رفض") return { background: "#fecaca", color: "#b91c1c", border: "1.5px solid #ef4444" };
  return {};
}

export default function ComplaintDetails() {
  const { id } = useParams();
  const c = complaintData[id];

  if (!c) return <div>الشكوى غير موجودة</div>;

  return (
    <div className="complaint-details-page" dir="rtl">
      <div className="complaint-details-header">
        <div>
          <div>رقم الشكوى: <b>#{c.id}</b></div>
          <div>اسم المحامي: <b>{c.lawyer}</b></div>
          <div>مقدم الشكوى: <b>{c.submitter}</b></div>
          <div>نوع الشكوى: <b>{c.type}</b></div>
        </div>
        <div>
          <div>تاريخ التقديم: <b>{c.date}</b></div>
          <div>معلومات الاتصال: <b>هاتف: {c.phone}</b></div>
          <div>
            الحالة:{" "}
            <span
              className="complaint-status"
              style={{
                ...getStatusColor(c.status),
                padding: "4px 16px",
                borderRadius: "12px",
                fontWeight: "bold",
                fontSize: "1rem",
                marginRight: "8px",
                display: "inline-block",
              }}
            >
              {c.status}
            </span>
          </div>
        </div>
      </div>

      <div className="complaint-details-section">
        <strong>تفاصيل الشكوى:</strong>
        <div className="complaint-details-box">{c.details}</div>
      </div>

      <div className="complaint-actions-section">
        <strong>الإجراءات</strong>
        <div className="complaint-actions">
          <button className="complaint-action-btn" style={getStatusColor("قيد الانتظار")}>
            <span style={{ marginLeft: 6 }}>قيد الانتظار</span>
            <span role="img" aria-label="clock">🕒</span>
          </button>
          <button className="complaint-action-btn" style={getStatusColor("قيد المعالجة")}>
            <span style={{ marginLeft: 6 }}>قيد المعالجة</span>
            <span role="img" aria-label="processing">⏳</span>
          </button>
          <button className="complaint-action-btn" style={getStatusColor("تم الحل")}>
            <span style={{ marginLeft: 6 }}>تم الحل</span>
            <span role="img" aria-label="done">✔️</span>
          </button>
          <button className="complaint-action-btn" style={getStatusColor("رفض")}>
            <span style={{ marginLeft: 6 }}>رفض</span>
            <span role="img" aria-label="cancel">❌</span>
          </button>
        </div>
        <div className="complaint-add-comment">
          <label style={{ fontWeight: "bold", marginBottom: 6, display: "block" }}>إضافة تعليق</label>
          <textarea placeholder="أضف تعليقك هنا..." style={{ width: "100%", borderRadius: 8, border: "1px solid #ddd", padding: 8, fontSize: "1rem" }} />
          <button className="complaints-btn-main" style={{ marginTop: 10, display: "flex", alignItems: "center" }}>
            <span style={{ marginLeft: 6 }}>إضافة تعليق</span>
            <span role="img" aria-label="send">📤</span>
          </button>
        </div>
      </div>

      <div className="complaint-comments-section">
        <strong>التعليقات والإجراءات السابقة</strong>
        {c.comments.map((com, i) => (
          <div key={i} className="complaint-comment" style={{ marginTop: 8 }}>
            <span>{com.date}</span> - <span>{com.text}</span>
          </div>
        ))}
      </div>
    </div>
  );
}