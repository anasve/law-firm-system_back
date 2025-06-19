import React from "react";
import {
  Box,
  Typography,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  Chip,
  Stack,
} from "@mui/material";

// ألوان البادجات حسب نوع القضية
const caseTypeColor = {
  "تجارية": "#f7e9c6",
  "مدنية": "#c6f7e2",
  "جنائية": "#f7c6c6",
  "أحوال شخصية": "#d6d6fa",
  "عقارية": "#e6d6fa",
  "عمالية": "#faeac6",
};

const caseTypeTextColor = {
  "تجارية": "#bfa13a",
  "مدنية": "#2e8b57",
  "جنائية": "#d32f2f",
  "أحوال شخصية": "#5c6bc0",
  "عقارية": "#7c4dff",
  "عمالية": "#e6a700",
};

// دالة لجلب تاريخ اليوم بالعربي
function getTodayArabic() {
  const today = new Date();
  const days = ["الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"];
  const months = [
    "يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو",
    "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"
  ];
  return `اليوم\n${days[today.getDay()]} ${today.getDate()} ${months[today.getMonth()]} ${today.getFullYear()}`;
}

function getTodayHeader() {
  const today = new Date();
  const months = [
    "يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو",
    "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"
  ];
  return `تاريخ اليوم: ${today.getDate()} ${months[today.getMonth()]} ${today.getFullYear()}`;
}

// بيانات تجريبية حديثة (كلها بتاريخ اليوم)
const appointments = [
  {
    date: getTodayArabic(),
    time: "10:00 صباحاً",
    client: { name: "أحمد محمود", short: "أ", phone: "0599123456" },
    caseType: "تجارية",
    caseDetails: "نزاع تجاري مع شركة الأفق",
    status: "مؤكد",
    notes: "إحضار العقد الأصلي",
  },
  {
    date: getTodayArabic(),
    time: "1:30 مساءً",
    client: { name: "سعيد الخالدي", short: "سع", phone: "0598765432" },
    caseType: "عمالية",
    caseDetails: "مراجعة عقد عمل",
    status: "قيد الانتظار",
    notes: "تأكيد الموعد هاتفيًا",
  },
  {
    date: getTodayArabic(),
    time: "4:00 مساءً",
    client: { name: "ليلى العبدالله", short: "لي", phone: "0591234567" },
    caseType: "أحوال شخصية",
    caseDetails: "قضية حضانة",
    status: "مؤكد",
    notes: "إحضار الوثائق الرسمية",
  },
  {
    date: getTodayArabic(),
    time: "11:00 مساءً",
    client: { name: "محمد صك", short: "م", phone: "0597654321" },
    caseType: "عقارية",
    caseDetails: "نزاع على ملكية",
    status: "مؤكد",
    notes: "إحضار صك الملكية",
  },
];

const getStatusChip = (status) => {
  if (status === "مؤكد") {
    return <Chip label="مؤكد" color="success" sx={{ color: "#fff", fontWeight: "bold" }} />;
  }
  if (status === "قيد الانتظار") {
    return (
      <Chip
        label="قيد الانتظار"
        color="warning"
        sx={{
          color: "#fff",
          fontWeight: "bold",
          background: "#ffa726",
        }}
      />
    );
  }
  return <Chip label={status} />;
};

const getCaseTypeChip = (type) => (
  <Chip
    label={type}
    sx={{
      background: caseTypeColor[type] || "#eee",
      color: caseTypeTextColor[type] || "#333",
      fontWeight: "bold",
      fontSize: "14px",
      px: 1.5,
      mr: 1,
    }}
    size="small"
  />
);

export default function LawyerAppointmentsTable() {
  return (
    <Box
      sx={{
        background: "#fff",
        borderRadius: "16px",
        boxShadow: "0 2px 12px #eee",
        p: 3,
        mt: 3,
        direction: "rtl",
        maxWidth: "1100px",
        mx: "auto",
      }}
    >
      {/* العنوان والتاريخ */}
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
        <Typography variant="h5" fontWeight="bold">
          <span style={{ color: "#D4AF37" }}>جدول</span> المواعيد
        </Typography>
        <Typography color="#bfa13a" fontSize={16}>
          {getTodayHeader()}
        </Typography>
      </Box>

      {/* Tabs (ثابتة هنا للعرض فقط) */}
      <Stack direction="row" spacing={2} mb={2}>
        <Box
          sx={{
            background: "#D4AF37",
            color: "#fff",
            px: 3,
            py: 1,
            borderRadius: "6px",
            fontWeight: "bold",
            fontSize: "16px",
            cursor: "pointer",
          }}
        >
          {`${new Date().toLocaleString("ar-EG", { month: "long" })} ${new Date().getFullYear()}`}
        </Box>
        <Box
          sx={{
            background: "#fff",
            color: "#bfa13a",
            px: 3,
            py: 1,
            borderRadius: "6px",
            fontWeight: "bold",
            fontSize: "16px",
            border: "1px solid #eee",
            cursor: "pointer",
          }}
        >
          يونيو {new Date().getFullYear()}
        </Box>
        <Box
          sx={{
            background: "#fff",
            color: "#bfa13a",
            px: 3,
            py: 1,
            borderRadius: "6px",
            fontWeight: "bold",
            fontSize: "16px",
            border: "1px solid #eee",
            cursor: "pointer",
          }}
        >
          يوليو {new Date().getFullYear()}
        </Box>
      </Stack>

      {/* الجدول */}
      <TableContainer component={Paper} sx={{ boxShadow: "none" }}>
        <Table>
          <TableHead>
            <TableRow sx={{ background: "#f9f6ef" }}>
              <TableCell align="center" sx={{ fontWeight: "bold", fontSize: "16px" }}>التاريخ</TableCell>
              <TableCell align="center" sx={{ fontWeight: "bold", fontSize: "16px" }}>الوقت</TableCell>
              <TableCell align="center" sx={{ fontWeight: "bold", fontSize: "16px" }}>العميل</TableCell>
              <TableCell align="center" sx={{ fontWeight: "bold", fontSize: "16px" }}>نوع القضية</TableCell>
              <TableCell align="center" sx={{ fontWeight: "bold", fontSize: "16px" }}>تفاصيل القضية</TableCell>
              <TableCell align="center" sx={{ fontWeight: "bold", fontSize: "16px" }}>الحالة</TableCell>
              <TableCell align="center" sx={{ fontWeight: "bold", fontSize: "16px" }}>ملاحظات</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {appointments.map((row, idx) => (
              <TableRow key={idx} sx={{ background: idx % 2 === 0 ? "#fff" : "#f9f6ef" }}>
                <TableCell align="center" sx={{ whiteSpace: "pre-line", fontWeight: "bold" }}>{row.date}</TableCell>
                <TableCell align="center">{row.time}</TableCell>
                <TableCell align="center">
                  <Stack direction="row" spacing={1} alignItems="center" justifyContent="center">
                    <Box
                      sx={{
                        background: "#f7e9c6",
                        color: "#bfa13a",
                        borderRadius: "50%",
                        width: 32,
                        height: 32,
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        fontWeight: "bold",
                        fontSize: "16px",
                      }}
                    >
                      {row.client.short}
                    </Box>
                    <Box>
                      <Typography fontWeight="bold">{row.client.name}</Typography>
                      <Typography fontSize="12px" color="#888">{row.client.phone}</Typography>
                    </Box>
                  </Stack>
                </TableCell>
                <TableCell align="center">{getCaseTypeChip(row.caseType)}</TableCell>
                <TableCell align="center">{row.caseDetails}</TableCell>
                <TableCell align="center">{getStatusChip(row.status)}</TableCell>
                <TableCell align="center">{row.notes}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Box>
  );
}