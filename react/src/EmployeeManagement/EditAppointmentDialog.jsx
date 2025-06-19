// EditAppointmentPage.jsx
import React, { useState, useEffect } from "react";
import { useNavigate, useLocation } from "react-router-dom";
import {
  Button,
  TextField,
  MenuItem,
  Box,
  Select,
  FormControl,
  Grid,
  Typography,
  Paper
} from "@mui/material";

const lawyers = [
  "أ. خالد الفهد",
  "أ. سارة المطيري",
  "أ. عبدالله الحربي"
];

const types = [
  "عقود تجارية",
  "قضية عمالية",
  "أحوال شخصية",
  "عقود عقارية"
];

const statuses = [
  "مؤكدة",
  "بانتظار التأكيد",
  "ملغية"
];

export default function EditAppointmentPage() {
  const navigate = useNavigate();
  const location = useLocation();
  const appointmentData = location.state?.appointment;

  const [form, setForm] = useState({
    client: "",
    lawyer: "",
    date: "",
    time: "",
    type: "",
    status: "",
    notes: ""
  });

  useEffect(() => {
    if (appointmentData) setForm({ ...appointmentData });
  }, [appointmentData]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleSave = () => {
    // هنا يمكنك إضافة المنطق الخاص بحفظ التعديلات
    console.log("تم حفظ التعديلات:", form);
    navigate("/appointments");
  };

  const handleCancel = () => {
    navigate("/appointments");
  };

  return (
    <Box sx={{ p: 4, background: "#f7f7f7", minHeight: "100vh" }}>
      <Paper sx={{ maxWidth: 600, mx: "auto", p: 3, borderRadius: 4 }}>
        <Typography variant="h5" sx={{ textAlign: "center", mb: 3, fontWeight: "bold" }}>
          تعديل موعد
        </Typography>
        
        <Box
          component="form"
          sx={{
            display: "flex",
            flexDirection: "column",
            gap: 2,
          }}
        >
          <Typography fontWeight="bold" mb={0.5}>اسم العميل</Typography>
          <TextField
            name="client"
            value={form.client}
            onChange={handleChange}
            fullWidth
            placeholder="اسم العميل"
            variant="outlined"
          />
          
          <Typography fontWeight="bold" mb={0.5}>المحامي</Typography>
          <FormControl fullWidth>
            <Select
              name="lawyer"
              value={form.lawyer}
              onChange={handleChange}
              displayEmpty
              variant="outlined"
            >
              <MenuItem value="">
                <em>اختر المحامي</em>
              </MenuItem>
              {lawyers.map((lawyer) => (
                <MenuItem key={lawyer} value={lawyer}>{lawyer}</MenuItem>
              ))}
            </Select>
          </FormControl>

          <Grid container spacing={2}>
            <Grid item xs={6}>
              <Typography fontWeight="bold" mb={0.5}>تاريخ الموعد</Typography>
              <TextField
                name="date"
                type="date"
                value={form.date}
                onChange={handleChange}
                fullWidth
                variant="outlined"
                InputLabelProps={{ shrink: true }}
              />
            </Grid>
            <Grid item xs={6}>
              <Typography fontWeight="bold" mb={0.5}>وقت الموعد</Typography>
              <TextField
                name="time"
                type="time"
                value={form.time}
                onChange={handleChange}
                fullWidth
                variant="outlined"
                InputLabelProps={{ shrink: true }}
                inputProps={{ step: 300 }}
              />
            </Grid>
          </Grid>

          <Typography fontWeight="bold" mb={0.5}>نوع الاستشارة</Typography>
          <FormControl fullWidth>
            <Select
              name="type"
              value={form.type}
              onChange={handleChange}
              displayEmpty
              variant="outlined"
            >
              <MenuItem value="">
                <em>اختر نوع الاستشارة</em>
              </MenuItem>
              {types.map((type) => (
                <MenuItem key={type} value={type}>{type}</MenuItem>
              ))}
            </Select>
          </FormControl>

          <Typography fontWeight="bold" mb={0.5}>الحالة</Typography>
          <FormControl fullWidth>
            <Select
              name="status"
              value={form.status}
              onChange={handleChange}
              displayEmpty
              variant="outlined"
            >
              <MenuItem value="">
                <em>اختر الحالة</em>
              </MenuItem>
              {statuses.map((status) => (
                <MenuItem key={status} value={status}>{status}</MenuItem>
              ))}
            </Select>
          </FormControl>

          <Typography fontWeight="bold" mb={0.5}>ملاحظات</Typography>
          <TextField
            name="notes"
            value={form.notes}
            onChange={handleChange}
            fullWidth
            multiline
            minRows={2}
            placeholder="ملاحظات"
            variant="outlined"
          />

          <Box sx={{ display: "flex", justifyContent: "center", gap: 2, mt: 2 }}>
            <Button 
              variant="outlined" 
              color="inherit" 
              onClick={handleCancel}
              sx={{ minWidth: 120 }}
            >
              إلغاء
            </Button>
            <Button
              variant="contained"
              color="primary"
              onClick={handleSave}
              sx={{ minWidth: 120 }}
            >
              حفظ
            </Button>
          </Box>
        </Box>
      </Paper>
    </Box>
  );
}