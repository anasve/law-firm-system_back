import React from "react";

const transactions = [
  {
    id: "INV-2023-001",
    date: "١٥ سبتمبر ٢٠٢٣",
    desc: "استشارة قانونية - شركة الأمل",
    amount: "+ ١٥,٠٠٠ ر.س",
    type: "دائن",
    status: "مكتمل",
  },
  {
    id: "EXP-2023-042",
    date: "١٢ سبتمبر ٢٠٢٣",
    desc: "رواتب الموظفين - سبتمبر",
    amount: "- ٤٥,٠٠٠ ر.س",
    type: "مدين",
    status: "مكتمل",
  },
  {
    id: "INV-2023-002",
    date: "١٠ سبتمبر ٢٠٢٣",
    desc: "تمثيل قانوني - قضية رقم ٣٤٩",
    amount: "+ ٢٥,٠٠٠ ر.س",
    type: "دائن",
    status: "معلق",
  },
  {
    id: "EXP-2023-041",
    date: "٩ سبتمبر ٢٠٢٣",
    desc: "إيجار المكتب - سبتمبر",
    amount: "- ١٢,٠٠٠ ر.س",
    type: "مدين",
    status: "مكتمل",
  },
  {
    id: "INV-2023-003",
    date: "٧ سبتمبر ٢٠٢٣",
    desc: "صياغة عقود - شركة النور",
    amount: "+ ٨,٥٠٠ ر.س",
    type: "دائن",
    status: "متأخر",
  },
];

function getStatusClass(status) {
  if (status === "مكتمل") return "status-complete";
  if (status === "معلق") return "status-pending";
  if (status === "متأخر") return "status-late";
  return "";
}

export default function FinancialTable() {
  return (
    <div className="financial-table-container" dir="rtl">
      <div className="financial-table-title">أحدث المعاملات المالية</div>
      <table className="financial-table">
        <thead>
          <tr>
            <th>رقم المعاملة</th>
            <th>التاريخ</th>
            <th>الوصف</th>
            <th>المبلغ</th>
            <th>النوع</th>
            <th>الحالة</th>
          </tr>
        </thead>
        <tbody>
          {transactions.map((t, idx) => (
            <tr key={idx}>
              <td>{t.id}</td>
              <td>{t.date}</td>
              <td>{t.desc}</td>
              <td>{t.amount}</td>
              <td>{t.type}</td>
              <td>
                <span className={`status-badge ${getStatusClass(t.status)}`}>
                  {t.status}
                </span>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}