import React from "react";
import {
  Box,
  List,
  ListItemButton,
  ListItemIcon,
  ListItemText,
  Typography,
  Avatar,
} from "@mui/material";
import PieChartIcon from "@mui/icons-material/PieChart";
import MonetizationOnIcon from "@mui/icons-material/MonetizationOn";
import AssignmentIndIcon from "@mui/icons-material/AssignmentInd";
import AccountCircleIcon from "@mui/icons-material/AccountCircle";
import SchoolIcon from "@mui/icons-material/School";
import ReportIcon from "@mui/icons-material/Report"; // أيقونة الشكاوى
import { useNavigate } from "react-router-dom";

export default function GoldenSidebar() {
  const navigate = useNavigate();

  return (
    <Box
      sx={{
        width: 260,
        minHeight: "100vh",
        background: "linear-gradient(90deg, #fffbe6 0%, #f7e9c6 100%)",
        borderTopRightRadius: 24,
        borderBottomRightRadius: 24,
        boxShadow: "2px 0 16px 0 rgba(212,175,55,0.08)",
        display: "flex",
        flexDirection: "column",
        p: 2,
      }}
    >
      {/* Header */}
      <Box display="flex" alignItems="center" mb={3} mt={1}>
        <Avatar
          sx={{
            bgcolor: "#D4AF37",
            width: 48,
            height: 48,
            mr: 1,
          }}
        >
          <SchoolIcon sx={{ fontSize: 32, color: "#fff" }} />
        </Avatar>
        <Typography variant="h6" fontWeight="bold" color="#181818">
          المحامي برو
        </Typography>
      </Box>

      <List sx={{ flex: 1 }}>
        <ListItemButton onClick={() => navigate("/management")}>
          <ListItemIcon>
            <PieChartIcon sx={{ color: "#D4AF37" }} />
          </ListItemIcon>
          <ListItemText primary="الرئيسية" />
        </ListItemButton>

        <ListItemButton onClick={() => navigate("/financial-details")}>
          <ListItemIcon>
            <MonetizationOnIcon sx={{ color: "#D4AF37" }} />
          </ListItemIcon>
          <ListItemText primary="عرض التفاصيل المالية" />
        </ListItemButton>

        <ListItemButton onClick={() => navigate("/complaints")}>
          <ListItemIcon>
            <ReportIcon sx={{ color: "#D4AF37" }} />
          </ListItemIcon>
          <ListItemText primary="عرض الشكاوى" />
        </ListItemButton>

        <ListItemButton onClick={() => navigate("/job-requests")}>
          <ListItemIcon>
            <AssignmentIndIcon sx={{ color: "#D4AF37" }} />
          </ListItemIcon>
          <ListItemText primary="عرض طلبات التوظيف" />
        </ListItemButton>

        <ListItemButton onClick={() => navigate("/profile-edit")}>
          <ListItemIcon>
            <AccountCircleIcon sx={{ color: "#D4AF37" }} />
          </ListItemIcon>
          <ListItemText primary="تعديل الملف الشخصي" />
        </ListItemButton>
      </List>
    </Box>
  );
}