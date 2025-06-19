import React, { useState } from "react";

function AddEmployee({ onEmployeeAdded }) {
  const [form, setForm] = useState({
    name: "",
    phone: "",
    address: "",
    image: null,
  });
  const [imagePreview, setImagePreview] = useState(null);

  const handleChange = (e) => {
    const { name, value, files } = e.target;
    if (name === "image") {
      setForm((prev) => ({ ...prev, image: files[0] }));
      setImagePreview(URL.createObjectURL(files[0]));
    } else {
      setForm((prev) => ({ ...prev, [name]: value }));
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    // هنا يمكنك إرسال البيانات إلى الباك-إند باستخدام fetch أو axios
    alert("تمت إضافة الموظف بنجاح (تجريبي)!");
    if (onEmployeeAdded) onEmployeeAdded(form);
  };

  return (
    <div className="add-lawyer-container">
      <form className="add-lawyer-form" onSubmit={handleSubmit} dir="rtl">
        <h2>إضافة موظف جديد</h2>
        <div className="form-group">
          <label>الاسم الكامل</label>
          <input type="text" name="name" required onChange={handleChange} />
        </div>
        <div className="form-group">
          <label>رقم الهاتف</label>
          <input type="tel" name="phone" required onChange={handleChange} />
        </div>
        <div className="form-group">
          <label>العنوان</label>
          <input type="text" name="address" required onChange={handleChange} />
        </div>
        <div className="form-group">
          <label>صورة الموظف</label>
          <input type="file" name="image" accept="image/*" required onChange={handleChange} />
          {imagePreview && (
            <img src={imagePreview} alt="معاينة الصورة" className="image-preview" />
          )}
        </div>
        <button type="submit">إضافة الموظف</button>
      </form>
    </div>
  );
}

export default AddEmployee;