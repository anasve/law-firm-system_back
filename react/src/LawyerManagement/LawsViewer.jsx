import React, { useState } from "react";
import '../App.css';

const LAWS_CATEGORIES = [
  { label: "القانون المدني", icon: "🏛️" },
  { label: "القانون الجنائي", icon: "⚖️" },
  { label: "القانون التجاري", icon: "💼" },
  { label: "قانون العمل", icon: "🧑‍💼" },
  { label: "قانون الأسرة", icon: "👨‍👩‍👧‍👦" },
];

const LAWS = [
  {
    id: 1,
    category: "القانون المدني",
    title: "المادة 1: تعريف العقد",
    content: "العقد هو توافق إرادتين أو أكثر على إحداث أثر قانوني...",
    date: "10/03/2022",
  },
  {
    id: 2,
    category: "القانون المدني",
    title: "المادة 2: أركان العقد",
    content: "أركان العقد هي: التراضي والمحل والسبب...",
    date: "10/03/2022",
  },
  {
    id: 3,
    category: "القانون المدني",
    title: "المادة 3: التراضي",
    content: "التراضي هو تطابق الإيجاب والقبول...",
    date: "10/03/2022",
  },
  {
    id: 4,
    category: "القانون المدني",
    title: "المادة 4: المحل",
    content: "محل العقد هو الشيء الذي يلتزم المدين بأدائه...",
    date: "10/03/2022",
  },
  {
    id: 5,
    category: "القانون الجنائي",
    title: "المادة 1: تعريف الجريمة",
    content: "الجريمة هي كل فعل أو امتناع يعاقب عليه القانون...",
    date: "15/06/2023",
  },
  // أضف المزيد حسب الحاجة
];

export default function LawsViewer() {
  const [selectedCategory, setSelectedCategory] = useState(LAWS_CATEGORIES[0].label);
  const [search, setSearch] = useState("");

  const filteredLaws = LAWS.filter(
    (law) =>
      law.category === selectedCategory &&
      (law.title.includes(search) || law.content.includes(search))
  );

  return (
    <div style={{ display: "flex", width: "100%" }}>
      <aside className="laws-categories laws-categories-gold">
        <div className="laws-search-box">
          <input
            type="text"
            placeholder="بحث في القوانين..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
          />
        </div>
        <div className="laws-categories-list">
          <span className="laws-categories-title">تصنيفات القوانين</span>
          {LAWS_CATEGORIES.map((cat) => (
            <button
              key={cat.label}
              className={`laws-category-btn${selectedCategory === cat.label ? " active" : ""}`}
              onClick={() => setSelectedCategory(cat.label)}
            >
              <span className="laws-category-icon">{cat.icon}</span>
              {cat.label}
            </button>
          ))}
        </div>
      </aside>
      <main className="laws-cards-section" style={{ flex: 1 }}>
        <h2 className="laws-section-title">{selectedCategory}</h2>
        <div className="laws-cards-list">
          {filteredLaws.length === 0 && (
            <div className="laws-empty">لا يوجد مواد مطابقة للبحث.</div>
          )}
          {filteredLaws.map((law) => (
            <div className="law-card law-card-gold" key={law.id}>
              <div className="law-card-title">{law.title}</div>
              <div className="law-card-content">{law.content}</div>
              <div className="law-card-footer">
                <span>تاريخ التعديل: {law.date}</span>
              </div>
            </div>
          ))}
        </div>
      </main>
    </div>
  );
}