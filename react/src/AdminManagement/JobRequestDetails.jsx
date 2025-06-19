import React from "react";
import { useParams } from "react-router-dom";
import {
  Box,
  Typography,
  Paper,
  Grid,
  Chip,
  Divider,
} from "@mui/material";
import SchoolIcon from "@mui/icons-material/School";
import WorkIcon from "@mui/icons-material/Work";
import EmailIcon from "@mui/icons-material/Email";
import PhoneIcon from "@mui/icons-material/Phone";

// بيانات جميع المتقدمين
const applicants = [
  {
    id: 1,
    name: "أحمد محمد العلي",
    status: "مقبول",
    specialty: "القانون المدني",
    experience: "5 سنوات خبرة",
    degree: "ماجستير في القانون",
    email: "ahmed.ali@example.com",
    phone: "0500000001",
    skills: "صياغة العقود، الاستشارات القانونية، التقاضي المدني",
    summary: "محامي ذو خبرة في القانون المدني، عمل في عدة شركات محاماة كبرى.",
  },
  {
    id: 2,
    name: "سارة أحمد الزهراني",
    status: "مقبول",
    specialty: "قانون الأسرة",
    experience: "8 سنوات خبرة",
    degree: "دكتوراه في القانون",
    email: "sara.zahrani@example.com",
    phone: "0500000002",
    skills: "قضايا الأسرة، الوساطة، الاستشارات الأسرية",
    summary: "خبيرة في قضايا الأسرة والوساطة، حاصلة على دكتوراه في القانون.",
  },
  {
    id: 3,
    name: "خالد عبدالله العمري",
    status: "مقبول",
    specialty: "القانون الجنائي",
    experience: "12 سنة خبرة",
    degree: "دكتوراه في القانون",
    email: "khalid.omari@example.com",
    phone: "0500000003",
    skills: "القضايا الجنائية، الدفاع، الاستشارات الجنائية",
    summary: "محامي جنائي ذو خبرة طويلة في الدفاع عن القضايا الجنائية.",
  },
  {
    id: 4,
    name: "نورة سعد القحطاني",
    status: "مقبول",
    specialty: "القانون التجاري",
    experience: "7 سنوات خبرة",
    degree: "ماجستير في القانون",
    email: "noura.qahtani@example.com",
    phone: "0500000004",
    skills: "القانون التجاري، تأسيس الشركات، العقود التجارية",
    summary: "متخصصة في القانون التجاري وتأسيس الشركات.",
  },
  {
    id: 5,
    name: "فهد سليمان الدوسري",
    status: "مقبول",
    specialty: "قانون العمل",
    experience: "4 سنوات خبرة",
    degree: "بكالوريوس في القانون",
    email: "fahad.dossary@example.com",
    phone: "0500000005",
    skills: "قانون العمل، قضايا الموظفين، الاستشارات العمالية",
    summary: "محامي عمل يهتم بقضايا الموظفين والشركات.",
  },
  {
    id: 6,
    name: "ريم عبدالعزيز الشمري",
    status: "مرفوض",
    specialty: "الملكية الفكرية",
    experience: "6 سنوات خبرة",
    degree: "ماجستير في القانون",
    email: "reem.shammari@example.com",
    phone: "0555876543",
    skills: "براءات الاختراع، العلامات التجارية، حقوق النشر، المنازعات القانونية للملكية الفكرية",
    summary: "محامية متخصصة في قانون الملكية الفكرية مع خبرة 6 سنوات. حاصلة على ماجستير في قانون الملكية الفكرية من جامعة لندن. عملت مع العديد من الشركات التقنية والإبداعية.",
  },
];

export default function JobRequestDetails() {
  const { id } = useParams();
  const applicant = applicants.find((a) => a.id === Number(id));

  if (!applicant) {
    return (
      <Box sx={{ p: 4, textAlign: "center" }}>
        <Typography color="error" variant="h6">
          لم يتم العثور على المتقدم.
        </Typography>
      </Box>
    );
  }

  return (
    <Box sx={{ direction: "rtl", p: { xs: 2, md: 5 }, background: "#fafbfc", minHeight: "100vh" }}>
      <Typography variant="h5" fontWeight="bold" mb={3}>
        تفاصيل طلب التوظيف
      </Typography>

      <Paper sx={{ p: { xs: 2, md: 4 }, borderRadius: 4, mb: 3 }}>
        <Grid container spacing={2} alignItems="center">
          <Grid item xs={12} md={8}>
            <Box display="flex" alignItems="center" gap={2}>
              <Typography variant="h6" fontWeight="bold">
                {applicant.name}
              </Typography>
              <Chip
                label={applicant.status}
                color={applicant.status === "مرفوض" ? "error" : "success"}
                sx={{ fontWeight: "bold", fontSize: 15 }}
              />
            </Box>
            <Typography color="text.secondary" fontSize={18}>
              {applicant.specialty} | {applicant.experience}
            </Typography>
          </Grid>
        </Grid>

        <Grid container spacing={2} mt={3}>
          <Grid item xs={12} md={6}>
            <Paper sx={{ p: 2, borderRadius: 3, mb: 2, background: "#f7f7fa" }}>
              <Typography fontWeight="bold" mb={1} fontSize={17}>
                المؤهلات
              </Typography>
              <Box display="flex" alignItems="center" gap={1} mb={1}>
                <SchoolIcon color="primary" />
                <Typography>{applicant.degree}</Typography>
              </Box>
              <Box display="flex" alignItems="center" gap={1}>
                <WorkIcon color="primary" />
                <Typography>{applicant.experience}</Typography>
              </Box>
            </Paper>
          </Grid>
          <Grid item xs={12} md={6}>
            <Paper sx={{ p: 2, borderRadius: 3, mb: 2, background: "#f7f7fa" }}>
              <Typography fontWeight="bold" mb={1} fontSize={17}>
                معلومات الاتصال
              </Typography>
              <Box display="flex" alignItems="center" gap={1} mb={1}>
                <EmailIcon color="primary" />
                <Typography>{applicant.email}</Typography>
              </Box>
              <Box display="flex" alignItems="center" gap={1}>
                <PhoneIcon color="primary" />
                <Typography>{applicant.phone}</Typography>
              </Box>
            </Paper>
          </Grid>
        </Grid>

        <Divider sx={{ my: 3 }} />

        <Typography fontWeight="bold" fontSize={17} mb={1}>
          المهارات والكفاءات
        </Typography>
        <Paper sx={{ p: 2, borderRadius: 3, background: "#f7f7fa", mb: 3 }}>
          <Typography>{applicant.skills}</Typography>
        </Paper>

        <Typography fontWeight="bold" fontSize={17} mb={1}>
          نبذة مختصرة
        </Typography>
        <Paper sx={{ p: 2, borderRadius: 3, background: "#f7f7fa" }}>
          <Typography>{applicant.summary}</Typography>
        </Paper>
      </Paper>
    </Box>
  );
}