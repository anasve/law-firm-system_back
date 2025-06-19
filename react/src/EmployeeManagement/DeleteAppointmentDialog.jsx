// DeleteAppointmentPage.jsx
import React, { useState } from "react";
import { useNavigate, useLocation } from "react-router-dom";
import {
  Typography,
  Button,
  Box,
  Avatar,
  Select,
  MenuItem,
  TextField,
  Paper
} from "@mui/material";

const gold = "#D4AF37";
const deleteReasons = [
  "تم الإلغاء من قبل العميل",
  "تم الإلغاء من قبل المحامي",
  "تعارض في المواعيد",
  "سبب آخر"
];

export default function DeleteAppointmentPage() {
  const navigate = useNavigate();
  const location = useLocation();
  const appointment = location.state?.appointment;

  const [reason, setReason] = useState("");
  const [notes, setNotes] = useState("");

  const handleConfirm = () => {
    // هنا يمكنك إضافة المنطق الخاص بحذف الموعد
    console.log("تم حذف الموعد:", { appointment, reason, notes });
    navigate("/employee-appointments");
  };

  const handleClose = () => {
    navigate("/employee-appointments");
  };

  return (
    <Box sx={{ p: 4, background: "#f7f7f7", minHeight: "100vh" }}>
      <Paper sx={{ maxWidth: 600, mx: "auto", p: 3, borderRadius: 4 }}>
        <Typography sx={{ fontWeight: "bold", fontSize: 22, mb: 2 }}>
          حذف موعد
        </Typography>

        <Typography sx={{ mb: 1.5, color: "#222", fontSize: 16 }}>
          هل أنت متأكد من رغبتك في حذف هذا الموعد؟
        </Typography>
        
        <Typography sx={{ mb: 2, color: "#888", fontSize: 14 }}>
          هذا الإجراء لا يمكن التراجع عنه.
        </Typography>

        <Box
          sx={{
            background: "#fafafa",
            borderRadius: 2,
            p: 2,
            mb: 3,
            display: "flex",
            alignItems: "center",
            gap: 2
          }}
        >
          <Avatar
            sx={{
              bgcolor: gold,
              color: "#fff",
              width: 48,
              height: 48,
              fontWeight: "bold",
              fontSize: 20
            }}
          >
            {appointment?.clientShort || "ع"}
          </Avatar>
          <Box sx={{ flex: 1 }}>
            <Typography fontWeight="bold" fontSize={17}>
              {appointment?.client || "اسم العميل"}
            </Typography>
            <Typography color="#888" fontSize={14}>
              {appointment?.date || "التاريخ"} · {appointment?.time || "الوقت"} · {appointment?.type || "نوع الاستشارة"}
            </Typography>
          </Box>
        </Box>

        <Typography fontWeight="bold" mb={1} fontSize={16}>
          سبب الحذف
        </Typography>
        <Select
          fullWidth
          displayEmpty
          value={reason}
          onChange={e => setReason(e.target.value)}
          sx={{
            mb: 3,
            background: "#fff",
            borderRadius: 2,
            fontWeight: "bold"
          }}
          renderValue={selected => selected || "اختر سبب الحذف..."}
        >
          <MenuItem disabled value="">
            اختر سبب الحذف...
          </MenuItem>
          {deleteReasons.map((r, i) => (
            <MenuItem key={i} value={r}>
              {r}
            </MenuItem>
          ))}
        </Select>

        <Typography fontWeight="bold" mb={1} fontSize={16}>
          ملاحظات إضافية
        </Typography>
        <TextField
          fullWidth
          multiline
          minRows={2}
          placeholder="أي ملاحظات إضافية..."
          value={notes}
          onChange={e => setNotes(e.target.value)}
          sx={{ mb: 3, background: "#fff", borderRadius: 2 }}
        />

        <Box sx={{ display: "flex", gap: 2 }}>
          <Button
            onClick={handleClose}
            variant="outlined"
            sx={{
              color: "#666",
              borderColor: "#ccc",
              background: "#f5f5f5",
              minWidth: 100,
              fontWeight: "bold"
            }}
          >
            إلغاء
          </Button>
          <Button
            onClick={handleConfirm}
            variant="contained"
            color="error"
            sx={{
              background: "#e53935",
              color: "#fff",
              minWidth: 120,
              fontWeight: "bold",
              boxShadow: "none",
              "&:hover": { background: "#c62828" }
            }}
            disabled={!reason}
          >
            تأكيد الحذف
          </Button>
        </Box>
      </Paper>
    </Box>
  );
}