import React, { useState } from "react";
import {
  Box,
  Button,
  TextField,
  Typography,
  InputAdornment,
  IconButton,
  Card,
  Avatar,
  Alert,
  CircularProgress
} from "@mui/material";
import LockOutlinedIcon from "@mui/icons-material/LockOutlined";
import Visibility from "@mui/icons-material/Visibility";
import VisibilityOff from "@mui/icons-material/VisibilityOff";
import { useNavigate } from "react-router-dom";

const gold = "#D4AF37";
const dark = "#181818";

export default function LoginLawer() {
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [values, setValues] = useState({
    email: "",
    password: "",
  });

  const navigate = useNavigate();

  const handleChange = (e) => {
    const { name, value } = e.target;
    setValues((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleClickShowPassword = () => setShowPassword((show) => !show);

  const handleSubmit = (e) => {
    e.preventDefault();
    setError("");
    setLoading(true);

    setTimeout(() => {
      setLoading(false);
      // بيانات تجريبية للمحامي
      if (
        values.email === "lawyer@lawoffice.com" &&
        values.password === "lawyer123"
      ) {
        navigate("/lawyer"); // ينقلك لصفحة عرض القوانين
      } else {
        setError("البريد الإلكتروني أو كلمة المرور غير صحيحة.");
      }
    }, 1500);
  };

  return (
    <Box
      sx={{
        minHeight: "100vh",
        width: "100vw",
        position: "relative",
        overflow: "hidden",
        fontFamily: "Tajawal, Cairo, Arial, sans-serif",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
      }}
    >
      {/* Blurred and darkened background */}
      <Box
        sx={{
          position: "absolute",
          inset: 0,
          width: "100%",
          height: "100%",
          zIndex: 0,
          background: `linear-gradient(rgba(24,24,24,0.25),rgba(24,24,24,0.25)), url('https://lucrativelegal.com/wp-content/uploads/2024/03/Lawyer.jpeg') center/cover no-repeat`,
          filter: "blur(8px)",
          WebkitFilter: "blur(8px)",
        }}
      />
      {/* Glassmorphism Card */}
      <Card
        sx={{
          width: { xs: "95vw", sm: 420 },
          borderRadius: 4,
          background: "rgba(255,255,255,0.18)",
          border: "none",
          boxShadow: "none",
          backdropFilter: "blur(16px)",
          WebkitBackdropFilter: "blur(16px)",
          zIndex: 1,
          p: { xs: 2, sm: 5 },
          display: "flex",
          flexDirection: "column",
          alignItems: "center",
          justifyContent: "center",
          textAlign: "center",
        }}
      >
        <Avatar
          sx={{
            bgcolor: gold,
            width: 70,
            height: 70,
            mx: "auto",
            mb: 2,
          }}
        >
          <LockOutlinedIcon sx={{ fontSize: 40, color: dark }} />
        </Avatar>
        <Typography
          variant="h5"
          sx={{
            fontWeight: "bold",
            color: dark,
            mb: 1,
            letterSpacing: 1,
          }}
        >
          تسجيل دخول المحامي
        </Typography>
        <Typography
          variant="body2"
          sx={{
            color: gold,
            mb: 2,
            fontWeight: "bold",
            letterSpacing: 1,
          }}
        >
          نظام أتمتة أعمال مكتب المحاماة
        </Typography>
        <Box sx={{ mb: 2 }}>
          <img
            src="/Gold Modern And Minimalist For Law Firm Template.jpg"
            alt="شعار المكتب"
            style={{ width: 75, height: 50, objectFit: "contain" }}
          />
        </Box>

        {error && (
          <Alert severity="error" sx={{ mb: 2 }}>
            {error}
          </Alert>
        )}
        <Box component="form" onSubmit={handleSubmit} sx={{ textAlign: "right", width: "100%" }}>
          <TextField
            margin="normal"
            required
            fullWidth
            id="email"
            label="البريد الإلكتروني"
            name="email"
            autoComplete="email"
            autoFocus
            value={values.email}
            onChange={handleChange}
            dir="rtl"
            InputProps={{
              sx: { background: "rgba(255,255,255,0.85)", borderRadius: 2 },
            }}
          />
          <TextField
            margin="normal"
            required
            fullWidth
            name="password"
            label="كلمة المرور"
            type={showPassword ? "text" : "password"}
            id="password"
            autoComplete="current-password"
            value={values.password}
            onChange={handleChange}
            dir="rtl"
            InputProps={{
              sx: { background: "rgba(255,255,255,0.85)", borderRadius: 2 },
              endAdornment: (
                <InputAdornment position="end">
                  <IconButton
                    aria-label="إظهار/إخفاء كلمة المرور"
                    onClick={handleClickShowPassword}
                    edge="end"
                  >
                    {showPassword ? <VisibilityOff /> : <Visibility />}
                  </IconButton>
                </InputAdornment>
              ),
            }}
          />
          <Button
            type="submit"
            fullWidth
            variant="contained"
            sx={{
              mt: 3,
              mb: 2,
              background: `linear-gradient(90deg, ${gold} 0%, #fffbe6 100%)`,
              color: dark,
              fontWeight: "bold",
              fontSize: "1.1rem",
              borderRadius: 2,
              transition: "0.3s",
              "&:hover": {
                background: `linear-gradient(90deg, #fffbe6 0%, ${gold} 100%)`,
                color: dark,
                transform: "scale(1.03)",
              },
            }}
            disabled={loading}
          >
            {loading ? <CircularProgress size={24} color="inherit" /> : "تسجيل الدخول"}
          </Button>
        </Box>
        <Typography
          variant="caption"
          sx={{ color: "#888", mt: 2, display: "block" }}
        >
          جميع الحقوق محفوظة &copy; مكتب المحاماة 2025
        </Typography>
      </Card>
    </Box>
  );
}