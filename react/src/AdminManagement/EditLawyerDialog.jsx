import React, { useState, useEffect } from "react";
import {
  Dialog, DialogTitle, DialogContent, DialogActions,
  TextField, Button, Box, MenuItem
} from "@mui/material";

const specialties = [
  "Corporate Law", "Family Law", "Intellectual Property", "Criminal Law", "Real Estate Law", "Immigration Law"
];

export default function EditLawyerDialog({ open, onClose, lawyer, onSave }) {
  const [form, setForm] = useState({
    name: "",
    email: "",
    phone: "",
    barNumber: "",
    address: "",
    specialty: "",
    status: "active"
  });

  useEffect(() => {
    if (lawyer) setForm(lawyer);
  }, [lawyer]);

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = () => {
    onSave(form);
  };

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle>تعديل بيانات المحامي</DialogTitle>
      <DialogContent>
        <Box component="form" sx={{ mt: 1 }}>
          <TextField
            margin="normal"
            label="الاسم"
            name="name"
            fullWidth
            value={form.name}
            onChange={handleChange}
            required
          />
          <TextField
            margin="normal"
            label="البريد الإلكتروني"
            name="email"
            fullWidth
            value={form.email}
            onChange={handleChange}
            required
          />
          <TextField
            margin="normal"
            label="رقم الهاتف"
            name="phone"
            fullWidth
            value={form.phone}
            onChange={handleChange}
            required
          />
          <TextField
            margin="normal"
            label="رقم النقابة"
            name="barNumber"
            fullWidth
            value={form.barNumber}
            onChange={handleChange}
            required
          />
          <TextField
            margin="normal"
            label="العنوان"
            name="address"
            fullWidth
            value={form.address}
            onChange={handleChange}
          />
          <TextField
            margin="normal"
            label="التصنيف"
            name="specialty"
            select
            fullWidth
            value={form.specialty}
            onChange={handleChange}
            required
          >
            {specialties.map((spec) => (
              <MenuItem key={spec} value={spec}>
                {spec}
              </MenuItem>
            ))}
          </TextField>
          <TextField
            margin="normal"
            label="الحالة"
            name="status"
            select
            fullWidth
            value={form.status}
            onChange={handleChange}
          >
            <MenuItem value="active">نشط</MenuItem>
            <MenuItem value="archived">مؤرشف</MenuItem>
          </TextField>
        </Box>
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose} color="secondary">إلغاء</Button>
        <Button onClick={handleSubmit} variant="contained" color="primary">حفظ التعديلات</Button>
      </DialogActions>
    </Dialog>
  );
}