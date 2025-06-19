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
import GavelIcon from "@mui/icons-material/Gavel"; // أيقونة القوانين
import EventNoteIcon from "@mui/icons-material/EventNote"; // أيقونة المواعيد
import ForumIcon from "@mui/icons-material/Forum"; // أيقونة الاستشارة القانونية
import LockResetIcon from "@mui/icons-material/LockReset"; // أيقونة تغيير كلمة المرور
import SchoolIcon from "@mui/icons-material/School"; // شعار
import { useNavigate } from "react-router-dom";
export default function LawyerSidebar() {
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
        <ListItemButton onClick={() => navigate("/laws-viewer")}>
          <ListItemIcon>
            <GavelIcon sx={{ color: "#D4AF37" }} />
          </ListItemIcon>
          <ListItemText primary="عرض القوانين" />
        </ListItemButton>

        <ListItemButton onClick={() => navigate("/lawyer-appointments")}>
          <ListItemIcon>
            <EventNoteIcon sx={{ color: "#D4AF37" }} />
          </ListItemIcon>
          <ListItemText primary="مراجعة الموعد" />
        </ListItemButton>

        <ListItemButton onClick={() => navigate("/lawyer-consultations")}>
          <ListItemIcon>
            <ForumIcon sx={{ color: "#D4AF37" }} />
          </ListItemIcon>
          <ListItemText primary="مراجعة الاستشارة القانونية" />
        </ListItemButton>

        <ListItemButton onClick={() => navigate("/lawyer-change-password")}>
          <ListItemIcon>
            <LockResetIcon sx={{ color: "#D4AF37" }} />
          </ListItemIcon>
          <ListItemText primary="تغيير كلمة المرور" />
        </ListItemButton>
      </List>
    </Box>
  );
}