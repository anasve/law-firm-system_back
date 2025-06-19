import React from "react";
import '../App.css';
import LawyerSidebar from "./LawyerSidebar"; // تأكد من المسار الصحيح

export default function LawyerLawsPage() {
  return (
    <div style={{ display: "flex" }}>
      <LawyerSidebar />
      <div style={{
        flex: 1,
        background: "linear-gradient(120deg, #fffbe6 0%, #f7e9c6 100%)",
        minHeight: "100vh"
      }}>
        <header className="laws-header laws-header-gold">
          <span className="laws-header-title">نظام أتمتة أعمال مكتب المحاماة</span>
        </header>
        <div className="laws-main-content" style={{ display: "flex", justifyContent: "center", alignItems: "center", minHeight: "70vh" }}>
          <div className="laws-welcome-box" style={{
            background: "#fff",
            borderRadius: "18px",
            boxShadow: "0 4px 24px #e9e7e1",
            padding: "48px 32px",
            maxWidth: 500,
            textAlign: "center"
          }}>
            <h2 style={{ color: "#D4AF37", marginBottom: 12 }}>مرحباً بك في نظام المحامي برو!</h2>
            <p style={{ fontSize: "1.2rem", color: "#222", marginBottom: 8 }}>
              يسعدنا وجودك معنا في منصة أتمتة أعمال مكتب المحاماة.
            </p>
            <p style={{ color: "#555", fontSize: "1rem", margin: "16px 0 0 0" }}>
              يمكنك من خلال هذه المنصة إدارة أعمالك القانونية بكل سهولة واحترافية.<br />
              نتمنى لك يوماً موفقاً في عملك القانوني!
            </p>
            <div style={{ marginTop: 32, color: "#bfa13a", fontWeight: "bold" }}>
              فريق المحامي برو
            </div>
          </div>
        </div>
        <footer className="laws-footer laws-footer-gold">
          <span>© 2025 نظام أتمتة أعمال مكتب المحاماة - جميع الحقوق محفوظة</span>
        </footer>
      </div>
    </div>
  );
}