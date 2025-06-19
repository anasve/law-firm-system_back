import React, { useState } from "react";

const PasswordChangeLawyer = () => {
  const [oldPassword, setOldPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  const handleSubmit = (e) => {
    e.preventDefault();
    setError("");
    setSuccess("");

    if (newPassword.length < 8) {
      setError("كلمة المرور يجب أن تحتوي على الأقل 8 أحرف.");
      return;
    }
    if (newPassword !== confirmPassword) {
      setError("كلمتا المرور غير متطابقتين.");
      return;
    }
    // هنا تضع منطق إرسال البيانات للسيرفر
    setSuccess("تم تغيير كلمة المرور بنجاح!");
  };

  return (
    <div style={{
      maxWidth: "800px",
      margin: "40px auto",
      background: "#fff",
      borderRadius: "10px",
      boxShadow: "0 2px 8px #eee",
      padding: "32px"
    }}>
      <h2 style={{ textAlign: "right", marginBottom: "32px" }}>تغيير كلمة المرور للمحامي</h2>
      <form onSubmit={handleSubmit}>
        <div style={{ display: "flex", gap: "16px", marginBottom: "24px" }}>
          <input
            type="password"
            placeholder="كلمة المرور الحالية"
            value={oldPassword}
            onChange={e => setOldPassword(e.target.value)}
            style={{ flex: 1, padding: "16px", fontSize: "16px", borderRadius: "8px", border: "1px solid #ddd" }}
            required
          />
          <input
            type="password"
            placeholder="كلمة المرور الجديدة"
            value={newPassword}
            onChange={e => setNewPassword(e.target.value)}
            style={{ flex: 1, padding: "16px", fontSize: "16px", borderRadius: "8px", border: "1px solid #ddd" }}
            required
          />
          <input
            type="password"
            placeholder="تأكيد كلمة المرور"
            value={confirmPassword}
            onChange={e => setConfirmPassword(e.target.value)}
            style={{ flex: 1, padding: "16px", fontSize: "16px", borderRadius: "8px", border: "1px solid #ddd" }}
            required
          />
        </div>
        <div style={{ color: "#888", marginBottom: "16px", textAlign: "right" }}>
          كلمة المرور يجب أن تحتوي على الأقل 8 أحرف، وتتضمن حرف كبير ورقم ورمز خاص.
        </div>
        {error && <div style={{ color: "red", marginBottom: "16px", textAlign: "right" }}>{error}</div>}
        {success && <div style={{ color: "green", marginBottom: "16px", textAlign: "right" }}>{success}</div>}
        <div style={{ display: "flex", gap: "16px" }}>
          <button
            type="submit"
            style={{
              background: "#3b82f6",
              color: "#fff",
              border: "none",
              borderRadius: "8px",
              padding: "12px 32px",
              fontSize: "18px",
              cursor: "pointer"
            }}
          >
            حفظ التغييرات
          </button>
          <button
            type="button"
            style={{
              background: "#fff",
              color: "#3b82f6",
              border: "2px solid #3b82f6",
              borderRadius: "8px",
              padding: "12px 32px",
              fontSize: "18px",
              cursor: "pointer"
            }}
            onClick={() => {
              setOldPassword("");
              setNewPassword("");
              setConfirmPassword("");
              setError("");
              setSuccess("");
            }}
          >
            إلغاء
          </button>
        </div>
      </form>
    </div>
  );
};

export default PasswordChangeLawyer;