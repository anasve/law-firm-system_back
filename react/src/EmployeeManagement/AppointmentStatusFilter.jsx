import React, { useState } from "react";
import { Box, Chip, Stack, Tabs, Tab, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Paper, IconButton, Tooltip } from "@mui/material";
import CheckCircleIcon from "@mui/icons-material/CheckCircle";
import EditIcon from "@mui/icons-material/Edit";
import DeleteIcon from "@mui/icons-material/Delete";

const statuses = [
  { label: "جميع الحالات", value: "جميع الحالات" },
  { label: "مؤكدة", value: "مؤكدة" },
  { label: "بانتظار التأكيد", value: "بانتظار التأكيد" },
  { label: "ملغية", value: "ملغية" }
];

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
  }
];

// بيانات مالية تجريبية
const financialData = [
  {
    id: 1,
    date: "2024-05-20",
    client: "محمد العبدالله",
    service: "استشارة قانونية",
    amount: 500,
    status: "مدفوع"
  },
  {
    id: 2,
    date: "2024-05-21",
    client: "سعيد الخالدي",
    service: "عقد تجاري",
    amount: 1500,
    status: "معلق"
  }
];

export default function AppointmentManagement() {
  const [currentTab, setCurrentTab] = useState(0);
  const [statusFilter, setStatusFilter] = useState("جميع الحالات");

  const handleTabChange = (event, newValue) => {
    setCurrentTab(newValue);
  };

  const handleStatusChange = (newStatus) => {
    setStatusFilter(newStatus);
  };

  // فلترة المواعيد حسب الحالة المختارة
  const filteredAppointments =
    statusFilter === "جميع الحالات"
      ? appointments
      : appointments.filter((a) => a.status === statusFilter);

  const handleConfirm = (row) => {
    console.log("تأكيد:", row);
  };

  const handleEdit = (row) => {
    console.log("تعديل:", row);
  };

  const handleDelete = (row) => {
    console.log("حذف:", row);
  };

  return (
    <Box sx={{ width: '100%' }}>
      {/* التبويبات */}
      <Box sx={{ borderBottom: 1, borderColor: 'divider', mb: 3 }}>
        <Tabs 
          value={currentTab} 
          onChange={handleTabChange}
          sx={{
            '& .MuiTab-root': {
              fontSize: '16px',
              fontWeight: 'bold',
              color: '#666',
              '&.Mui-selected': {
                color: '#bfa046',
              }
            },
            '& .MuiTabs-indicator': {
              backgroundColor: '#bfa046',
            }
          }}
        >
          <Tab label="إدارة المواعيد" />
          <Tab label="التفاصيل المالية" />
        </Tabs>
      </Box>

      {/* فلتر الحالات - يظهر فقط في تبويب المواعيد */}
      {currentTab === 0 && (
        <Box sx={{ mb: 3, px: 3 }}>
          <Stack direction="row" spacing={1} justifyContent="flex-end">
            {statuses.map((status) => (
              <Chip
                key={status.value}
                label={status.label}
                onClick={() => handleStatusChange(status.value)}
                sx={{
                  fontWeight: "bold",
                  fontSize: "15px",
                  px: 1,
                  background: statusFilter === status.value ? "#bfa046" : "#f5f5f5",
                  color: statusFilter === status.value ? "#fff" : "#666",
                  borderRadius: "8px",
                  '&:hover': {
                    background: statusFilter === status.value ? "#ab8f3d" : "#e0e0e0"
                  }
                }}
              />
            ))}
          </Stack>
        </Box>
      )}

      {/* محتوى التبويبات */}
      {currentTab === 0 ? (
        // جدول المواعيد
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
              {filteredAppointments.map((row) => (
                <TableRow key={row.id}>
                  <TableCell align="center">{row.client}</TableCell>
                  <TableCell align="center">{row.date}</TableCell>
                  <TableCell align="center">{row.time}</TableCell>
                  <TableCell align="center">{row.type}</TableCell>
                  <TableCell align="center">
                    <Chip
                      label={row.status}
                      sx={{
                        color: "#fff",
                        fontWeight: "bold",
                        fontSize: "15px",
                        background:
                          row.status === "بانتظار التأكيد"
                            ? "#ffa726"
                            : row.status === "مؤكدة"
                            ? "#43a047"
                            : "#e53935",
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
              ))}
            </TableBody>
          </Table>
        </TableContainer>
      ) : (
        // جدول التفاصيل المالية
        <TableContainer component={Paper} sx={{ borderRadius: 3, maxWidth: 1000, margin: "0 auto" }}>
          <Table>
            <TableHead>
              <TableRow>
                <TableCell align="center">التاريخ</TableCell>
                <TableCell align="center">العميل</TableCell>
                <TableCell align="center">الخدمة</TableCell>
                <TableCell align="center">المبلغ</TableCell>
                <TableCell align="center">الحالة</TableCell>
                <TableCell align="center">الإجراءات</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {financialData.map((row) => (
                <TableRow key={row.id}>
                  <TableCell align="center">{row.date}</TableCell>
                  <TableCell align="center">{row.client}</TableCell>
                  <TableCell align="center">{row.service}</TableCell>
                  <TableCell align="center">{row.amount} ريال</TableCell>
                  <TableCell align="center">
                    <Chip
                      label={row.status}
                      sx={{
                        color: "#fff",
                        fontWeight: "bold",
                        fontSize: "15px",
                        background: row.status === "مدفوع" ? "#43a047" : "#ffa726",
                      }}
                    />
                  </TableCell>
                  <TableCell align="center">
                    <Tooltip title="تأكيد الدفع">
                      <IconButton color="success" onClick={() => handleConfirm(row)}>
                        <CheckCircleIcon />
                      </IconButton>
                    </Tooltip>
                    <Tooltip title="تعديل">
                      <IconButton color="primary" onClick={() => handleEdit(row)}>
                        <EditIcon />
                      </IconButton>
                    </Tooltip>
                    <Tooltip title="حذف">
                      <IconButton color="error" onClick={() => handleDelete(row)}>
                        <DeleteIcon />
                      </IconButton>
                    </Tooltip>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>
      )}
    </Box>
  );
}