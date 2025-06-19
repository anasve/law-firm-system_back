import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import AppointmentStatusFilter from "./AppointmentStatusFilter";
import {
  Box,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  Chip,
  IconButton,
  Tooltip
} from "@mui/material";
import CheckCircleIcon from "@mui/icons-material/CheckCircle";
import EditIcon from "@mui/icons-material/Edit";
import DeleteIcon from "@mui/icons-material/Delete";
import ConfirmAppointmentDialog from "./ConfirmAppointmentDialog";

// بيانات مواعيد تجريبية
const appointments = [
  {
    id: 1,
    client: "محمد العبدالله",
    clientShort: "مع",
    date: "2024-05-20",
    time: "10:00 صباحاً",
    type: "عقود تجارية",
    status: "بانتظار التأكيد",
  },
  {
    id: 2,
    client: "سعيد الخالدي",
    clientShort: "سع",
    date: "2024-05-21",
    time: "11:30 صباحاً",
    type: "قضية عمالية",
    status: "مؤكدة",
  },
  {
    id: 3,
    client: "نورة السالم",
    clientShort: "نو",
    date: "2024-05-22",
    time: "8:00 مساءً",
    type: "أحوال شخصية",
    status: "ملغية",
  },
  {
    id: 4,
    client: "فاطمة الزهراني",
    clientShort: "فا",
    date: "2024-05-23",
    time: "3:30 مساءً",
    type: "عقود عقارية",
    status: "بانتظار التأكيد",
  },
];

// ألوان الحالات
const statusColors = {
  "مؤكدة": "success",
  "بانتظار التأكيد": "warning",
  "ملغية": "error",
};

function EmployeeAppointmentsPage() {
  const navigate = useNavigate();
  const [selectedStatus, setSelectedStatus] = useState("جميع الحالات");
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [selectedAppointment, setSelectedAppointment] = useState(null);

  // فلترة المواعيد حسب الحالة المختارة
  const filteredAppointments =
    selectedStatus === "جميع الحالات"
      ? appointments
      : appointments.filter((a) => a.status === selectedStatus);

  const handleStatusChange = (newStatus) => {
    setSelectedStatus(newStatus);
  };

  // دوال الإجراءات
  const handleConfirm = (row) => {
    setSelectedAppointment(row);
    setConfirmOpen(true);
  };

  const handleEdit = (row) => {
    navigate("/employee-appointments/edit", { state: { appointment: row } });
  };

  const handleDelete = (row) => {
    navigate("/employee-appointments/delete", { state: { appointment: row } });
  };

  const handleConfirmDialog = (data) => {
    // هنا منطق تأكيد الموعد
    console.log("تم تأكيد الموعد:", data);
    setConfirmOpen(false);
  };

  return (
    <Box sx={{ p: 4, background: "#f7f7f7", minHeight: "100vh" }}>
      <Box sx={{ mb: 3 }}>
        <AppointmentStatusFilter value={selectedStatus} onChange={handleStatusChange} />
      </Box>
      <TableContainer component={Paper} sx={{ borderRadius: 3, maxWidth: 1000, margin: "0 auto" }}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell align="center">العميل</TableCell>
              <TableCell align="center">التاريخ</TableCell>
              <TableCell align="center">الوقت</TableCell>
              <TableCell align="center">نوع الاستشارة</TableCell>
              <TableCell align="center">الحالة</TableCell>
              <TableCell align="center">الإجراءات</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {filteredAppointments.length === 0 ? (
              <TableRow>
                <TableCell colSpan={6} align="center">
                  لا يوجد مواعيد مطابقة.
                </TableCell>
              </TableRow>
            ) : (
              filteredAppointments.map((row) => (
                <TableRow key={row.id}>
                  <TableCell align="center">{row.client}</TableCell>
                  <TableCell align="center">{row.date}</TableCell>
                  <TableCell align="center">{row.time}</TableCell>
                  <TableCell align="center">{row.type}</TableCell>
                  <TableCell align="center">
                    <Chip
                      label={row.status}
                      color={statusColors[row.status] || "default"}
                      sx={{
                        color: "#fff",
                        fontWeight: "bold",
                        fontSize: "15px",
                        background:
                          row.status === "بانتظار التأكيد"
                            ? "#ffa726"
                            : row.status === "مؤكدة"
                            ? "#43a047"
                            : row.status === "ملغية"
                            ? "#e53935"
                            : "#ccc",
                      }}
                    />
                  </TableCell>
                  <TableCell align="center">
                    <Tooltip title="تأكيد موعد">
                      <IconButton color="success" onClick={() => handleConfirm(row)}>
                        <CheckCircleIcon />
                      </IconButton>
                    </Tooltip>
                    <Tooltip title="تعديل موعد">
                      <IconButton color="primary" onClick={() => handleEdit(row)}>
                        <EditIcon />
                      </IconButton>
                    </Tooltip>
                    <Tooltip title="حذف موعد">
                      <IconButton color="error" onClick={() => handleDelete(row)}>
                        <DeleteIcon />
                      </IconButton>
                    </Tooltip>
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </TableContainer>
      {/* نافذة تأكيد الموعد */}
      <ConfirmAppointmentDialog
        open={confirmOpen}
        onClose={() => setConfirmOpen(false)}
        onConfirm={handleConfirmDialog}
        appointment={selectedAppointment}
      />
    </Box>
  );
}

export default EmployeeAppointmentsPage;