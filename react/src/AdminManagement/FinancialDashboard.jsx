import React from "react";

const cards = [
  {
    icon: "💸",
    title: "إجمالي المصروفات",
    value: "١٢٣,٤٥٦ ر.س",
    desc: "مقارنة بالشهر السابق: ↑ ٨٪",
    color: "red",
    progress: 60,
    progressLabel: "الميزانية: ١٥٠,٠٠٠ ر.س",
    progressValue: "٦٠٪"
  },
  {
    icon: "💰",
    title: "إجمالي الإيرادات",
    value: "٢٥٦,٧٨٩ ر.س",
    desc: "مقارنة بالشهر السابق: ↑ ١٢٪",
    color: "purple",
    progress: 75,
    progressLabel: "الهدف: ٣٠٠,٠٠٠ ر.س",
    progressValue: "٧٥٪"
  },
  {
    icon: "⚖️",
    title: "عدد القضايا النشطة",
    value: "٤٢ قضية",
    desc: "مقارنة بالشهر السابق: ↑ ٢٥٪",
    color: "blue",
    progress: 80,
    progressLabel: "السعة: ٥٠ قضية",
    progressValue: "٨٠٪"
  },
  {
    icon: "📈",
    title: "صافي الربح",
    value: "١٣٣,٣٣٣ ر.س",
    desc: "مقارنة بالشهر السابق: ↑ ١٥٪",
    color: "green",
    progress: 70,
    progressLabel: "الهدف: ١٥٠,٠٠٠ ر.س",
    progressValue: "٧٠٪"
  },
];

export default function FinancialDashboard() {
  return (
    <div className="financial-dashboard">
      {cards.map((card, idx) => (
        <div className={`financial-card ${card.color}`} key={idx}>
          <div className="icon">{card.icon}</div>
          <div className="title">{card.title}</div>
          <div className="value">{card.value}</div>
          <div className="desc">{card.desc}</div>
          <div className="progress-bar-bg">
            <div
              className="progress-bar"
              style={{ width: `${card.progress}%` }}
            ></div>
          </div>
          <div className="progress-label">
            <span>{card.progressValue}</span>
            <span>{card.progressLabel}</span>
          </div>
        </div>
      ))}
    </div>
  );
}