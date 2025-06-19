import React from "react";
import {
  Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Paper, Avatar, IconButton, Typography, Box
} from "@mui/material";
import VisibilityIcon from "@mui/icons-material/Visibility";
import { useNavigate } from "react-router-dom";

const applications = [
  {
    id: 1,
    name: "أحمد محمد العلي",
    email: "ahmed.ali@example.com",
    specialty: "القانون المدني",
    experience: "5 سنوات",
    degree: "ماجستير في القانون",
    date: "1445/4/17 هـ",
  },
  {
    id: 2,
    name: "سارة أحمد الزهراني",
    email: "sara.zahrani@example.com",
    specialty: "قانون الأسرة",
    experience: "8 سنوات",
    degree: "دكتوراه في القانون",
    date: "1445/4/10 هـ",
  },
  {
    id: 3,
    name: "خالد عبدالله العمري",
    email: "khalid.omari@example.com",
    specialty: "القانون الجنائي",
    experience: "12 سنة",
    degree: "دكتوراه في القانون",
    date: "1445/4/5 هـ",
  },
  {
    id: 4,
    name: "نورة سعد القحطاني",
    email: "noura.qahtani@example.com",
    specialty: "القانون التجاري",
    experience: "7 سنوات",
    degree: "ماجستير في القانون",
    date: "1445/3/30 هـ",
  },
  {
    id: 5,
    name: "فهد سليمان الدوسري",
    email: "fahad.dossary@example.com",
    specialty: "قانون العمل",
    experience: "4 سنوات",
    degree: "بكالوريوس في القانون",
    date: "1445/3/25 هـ",
  },
  {
    id: 6,
    name: "ريم عبدالعزيز الشمري",
    email: "reem.shammari@example.com",
    specialty: "الملكية الفكرية",
    experience: "6 سنوات",
    degree: "ماجستير في القانون",
    date: "1445/3/20 هـ",
  },
];

const getInitial = (name) => name.trim()[0];

export default function JobApplicationsTable() {
  const navigate = useNavigate();

  return (
    <Box sx={{ direction: "rtl", p: 3, background: "#f5f7fa", minHeight: "100vh" }}>
      <Typography variant="h5" sx={{ mb: 3, fontWeight: "bold" }}>
        طلبات التوظيف
      </Typography>
      <TableContainer component={Paper} sx={{ borderRadius: 3, boxShadow: 3 }}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell align="center">المتقدم</TableCell>
              <TableCell align="center">التخصص</TableCell>
              <TableCell align="center">الخبرة</TableCell>
              <TableCell align="center">تاريخ التقديم</TableCell>
              <TableCell align="center">إجراءات</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {applications.map((app) => (
              <TableRow key={app.id} hover>
                <TableCell align="center">
                  <Box sx={{ display: "flex", alignItems: "center", gap: 2, justifyContent: "center" }}>
                    <Avatar sx={{ bgcolor: "#e3eafc", color: "#1976d2", fontWeight: "bold" }}>
                      {getInitial(app.name)}
                    </Avatar>
                    <Box>
                      <Typography sx={{ fontWeight: "bold" }}>{app.name}</Typography>
                      <Typography variant="body2" color="text.secondary">{app.email}</Typography>
                    </Box>
                  </Box>
                </TableCell>
                <TableCell align="center">{app.specialty}</TableCell>
                <TableCell align="center">
                  <Typography>{app.experience}</Typography>
                  <Typography variant="body2" color="text.secondary">{app.degree}</Typography>
                </TableCell>
                <TableCell align="center">{app.date}</TableCell>
                <TableCell align="center">
                  <IconButton color="primary" onClick={() => navigate(`/job-requests/${app.id}`)}>
                    <VisibilityIcon />
                  </IconButton>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Box>
  );
}