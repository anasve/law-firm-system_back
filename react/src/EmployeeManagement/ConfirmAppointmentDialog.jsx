import React, { useState } from "react";
import {
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Typography,
  Button,
  Box,
  Avatar,
  Select,
  MenuItem,
  TextField,
  IconButton
} from "@mui/material";
import CloseIcon from "@mui/icons-material/Close";

const gold = "#D4AF37";

export default function ConfirmAppointmentDialog({
  open,
  onClose,
  onConfirm,
  appointment
}) {
  const [method, setMethod] = useState("البريد الإلكتروني");
  const [notes, setNotes] = useState("");

  const handleConfirm = () => {
    onConfirm({ method, notes });
    setMethod("البريد الإلكتروني");
    setNotes("");
  };

  const handleClose = () => {
    setMethod("البريد الإلكتروني");
    setNotes("");
    onClose();
  };

  return (
    <Dialog
      open={open}
      onClose={handleClose}
      maxWidth="xs"
      fullWidth
      dir="rtl"
      PaperProps={{
        sx: { borderRadius: 4, p: 1 }
      }}
    >
      <DialogTitle sx={{ fontWeight: "bold", fontSize: 22, pb: 0 }}>
        تأكيد موعد
        <IconButton
          aria-label="إغلاق"
          onClick={handleClose}
          sx={{
            position: "absolute",
            left: 16,
            top: 16,
            color: "#888"
          }}
        >
          <CloseIcon />
        </IconButton>
      </DialogTitle>
      <DialogContent sx={{ pt: 1 }}>
        <Typography sx={{ mb: 1.5, color: "#222", fontSize: 16 }}>
          هل تريد تأكيد هذا الموعد؟
        </Typography>
        <Typography sx={{ mb: 2, color: "#888", fontSize: 14 }}>
          سيتم إرسال إشعار للعميل بتأكيد الموعد.
        </Typography>
        {/* بيانات الموعد */}
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
              {appointment?.clientName || appointment?.client || "اسم العميل"}
            </Typography>
            <Typography color="#888" fontSize={14}>
              {appointment?.date || "20 مايو 2024"} · {appointment?.time || "10:00 صباحاً"} · {appointment?.type || "عقود تجارية"}
            </Typography>
          </Box>
        </Box>
        {/* طريقة إرسال التأكيد */}
        <Typography fontWeight="bold" mb={1} fontSize={16}>
          طريقة إرسال التأكيد
        </Typography>
        <Select
          fullWidth
          value={method}
          onChange={e => setMethod(e.target.value)}
          sx={{
            mb: 3,
            background: "#fff",
            borderRadius: 2,
            fontWeight: "bold"
          }}
        >
          <MenuItem value="البريد الإلكتروني">البريد الإلكتروني</MenuItem>
          <MenuItem value="رسالة نصية">رسالة نصية</MenuItem>
          <MenuItem value="واتساب">واتساب</MenuItem>
        </Select>
        {/* ملاحظات إضافية للعميل */}
        <Typography fontWeight="bold" mb={1} fontSize={16}>
          ملاحظات إضافية للعميل
        </Typography>
        <TextField
          fullWidth
          multiline
          minRows={2}
          placeholder="أي ملاحظات إضافية للعميل..."
          value={notes}
          onChange={e => setNotes(e.target.value)}
          sx={{ mb: 2, background: "#fff", borderRadius: 2 }}
        />
      </DialogContent>
      <DialogActions sx={{ justifyContent: "flex-start", px: 3, pb: 2 }}>
        <Button
          onClick={handleClose}
          variant="outlined"
          sx={{
            color: "#666",
            borderColor: "#ccc",
            background: "#f5f5f5",
            minWidth: 100,
            fontWeight: "bold",
            ml: 1
          }}
        >
          إلغاء
        </Button>
        <Button
          onClick={handleConfirm}
          variant="contained"
          sx={{
            background: "#D4AF37",
            color: "#fff",
            minWidth: 120,
            fontWeight: "bold",
            boxShadow: "none",
            "&:hover": { background: "#bfa13a" }
          }}
        >
          تأكيد الموعد
        </Button>
      </DialogActions>
    </Dialog>
  );
}