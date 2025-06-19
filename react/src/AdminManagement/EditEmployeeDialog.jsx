import React, { useState, useEffect } from "react";
import { Dialog, DialogTitle, DialogContent, DialogActions, Button, TextField, Box } from "@mui/material";

export default function EditEmployeeDialog({ open, onClose, employee, onSave }) {
  const [values, setValues] = useState({
    name: "",
    position: "",
    email: "",
    phone: "",
    address: "",
    status: "active",
  });

  useEffect(() => {
    if (employee) setValues(employee);
  }, [employee]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setValues((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onSave(values);
  };

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle>تعديل بيانات الموظف</DialogTitle>
      <form onSubmit={handleSubmit}>
        <DialogContent>
          <Box display="flex" flexDirection="column" gap={2}>
            <TextField label="الاسم" name="name" value={values.name} onChange={handleChange} required />
            <TextField label="الوظيفة" name="position" value={values.position} onChange={handleChange} required />
            <TextField label="البريد الإلكتروني" name="email" value={values.email} onChange={handleChange} required />
            <TextField label="الهاتف" name="phone" value={values.phone} onChange={handleChange} required />
            <TextField label="العنوان" name="address" value={values.address} onChange={handleChange} required />
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={onClose}>إلغاء</Button>
          <Button type="submit" variant="contained" color="primary">حفظ</Button>
        </DialogActions>
      </form>
    </Dialog>
  );
}