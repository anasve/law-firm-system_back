import React, { useState } from "react";
import {
  Box,
  Card,
  Typography,
  TextField,
  Avatar,
  IconButton,
  Divider,
  Grid,
  Button,
  Stack,
  InputAdornment,
  Tooltip
} from "@mui/material";
import PhotoCameraIcon from "@mui/icons-material/PhotoCamera";
import Visibility from "@mui/icons-material/Visibility";
import VisibilityOff from "@mui/icons-material/VisibilityOff";
import InfoOutlinedIcon from "@mui/icons-material/InfoOutlined";

export default function ProfileEdit() {
  const [profile, setProfile] = useState({
    fullName: "أحمد محمد عبدالله",
    jobTitle: "مدير النظام",
    username: "admin123",
    email: "admin@lawfirm.com",
    phone: "0512345678",
    address: "الرياض، المملكة العربية السعودية",
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setProfile((prev) => ({ ...prev, [name]: value }));
  };

  // إدارة كلمات المرور
  const [passwords, setPasswords] = useState({
    current: "",
    new: "",
    confirm: "",
  });
  const [showPassword, setShowPassword] = useState({
    current: false,
    new: false,
    confirm: false,
  });

  const handlePasswordChange = (e) => {
    const { name, value } = e.target;
    setPasswords((prev) => ({ ...prev, [name]: value }));
  };

  const handleClickShowPassword = (field) => {
    setShowPassword((prev) => ({ ...prev, [field]: !prev[field] }));
  };

  const handleSave = (e) => {
    e.preventDefault();
    alert("تم حفظ التغييرات!");
  };

  const handleCancel = () => {
    setPasswords({ current: "", new: "", confirm: "" });
  };

  return (
    <Box sx={{ background: "#f5f6fa", minHeight: "100vh", py: 5 }}>
      <Typography variant="h4" fontWeight="bold" align="center" mb={1}>
        تعديل الملف الشخصي
      </Typography>
      <Typography variant="subtitle1" align="center" color="text.secondary" mb={4}>
        قم بتعديل معلومات الملف الشخصي وبيانات الحساب
      </Typography>
      <Card
        sx={{
          maxWidth: 900,
          mx: "auto",
          p: 4,
          borderRadius: 4,
          boxShadow: "0 4px 24px 0 rgba(0,0,0,0.08)",
        }}
      >
        <form onSubmit={handleSave}>
          <Grid container spacing={3}>
            {/* الحقول */}
            <Grid item xs={12} md={8}>
              <Typography variant="h6" fontWeight="bold" mb={2}>
                المعلومات الشخصية
              </Typography>
              <Grid container spacing={2} mb={3}>
                <Grid item xs={12} md={6}>
                  <TextField
                    label="الاسم الكامل"
                    name="fullName"
                    value={profile.fullName}
                    onChange={handleChange}
                    fullWidth
                    dir="rtl"
                  />
                </Grid>
                <Grid item xs={12} md={6}>
                  <TextField
                    label="المسمى الوظيفي"
                    name="jobTitle"
                    value={profile.jobTitle}
                    onChange={handleChange}
                    fullWidth
                    dir="rtl"
                  />
                </Grid>
              </Grid>
              <Divider sx={{ mb: 3 }} />
              <Typography variant="h6" fontWeight="bold" mb={2}>
                معلومات الحساب
              </Typography>
              <Grid container spacing={2} mb={3}>
                <Grid item xs={12} md={6}>
                  <TextField
                    label="اسم المستخدم"
                    name="username"
                    value={profile.username}
                    onChange={handleChange}
                    fullWidth
                    dir="rtl"
                  />
                </Grid>
                <Grid item xs={12} md={6}>
                  <TextField
                    label="البريد الإلكتروني"
                    name="email"
                    value={profile.email}
                    onChange={handleChange}
                    fullWidth
                    dir="rtl"
                  />
                </Grid>
              </Grid>
              <Divider sx={{ mb: 3 }} />
              <Typography variant="h6" fontWeight="bold" mb={2}>
                معلومات الاتصال
              </Typography>
              <Grid container spacing={2} mb={3}>
                <Grid item xs={12} md={6}>
                  <TextField
                    label="رقم الهاتف"
                    name="phone"
                    value={profile.phone}
                    onChange={handleChange}
                    fullWidth
                    dir="rtl"
                  />
                </Grid>
                <Grid item xs={12} md={6}>
                  <TextField
                    label="العنوان"
                    name="address"
                    value={profile.address}
                    onChange={handleChange}
                    fullWidth
                    dir="rtl"
                  />
                </Grid>
              </Grid>
              <Divider sx={{ mb: 3 }} />
              {/* قسم تغيير كلمة المرور */}
              <Box sx={{ mb: 3 }}>
                <Typography
                  variant="h6"
                  fontWeight="bold"
                  mb={3}
                  sx={{
                    borderBottom: "1px solid #eee",
                    pb: 1,
                    textAlign: "right",
                  }}
                >
                  تغيير كلمة المرور
                </Typography>
                <Grid container spacing={2} mb={2} direction="row-reverse">
                  <Grid item xs={12} md={4}>
                    <TextField
                      label="كلمة المرور الحالية"
                      name="current"
                      type={showPassword.current ? "text" : "password"}
                      value={passwords.current}
                      onChange={handlePasswordChange}
                      fullWidth
                      dir="rtl"
                      InputProps={{
                        endAdornment: (
                          <InputAdornment position="end">
                            <IconButton
                              onClick={() => handleClickShowPassword("current")}
                              edge="end"
                            >
                              {showPassword.current ? <VisibilityOff /> : <Visibility />}
                            </IconButton>
                          </InputAdornment>
                        ),
                      }}
                    />
                  </Grid>
                  <Grid item xs={12} md={4}>
                    <TextField
                      label="كلمة المرور الجديدة"
                      name="new"
                      type={showPassword.new ? "text" : "password"}
                      value={passwords.new}
                      onChange={handlePasswordChange}
                      fullWidth
                      dir="rtl"
                      InputProps={{
                        endAdornment: (
                          <InputAdornment position="end">
                            <IconButton
                              onClick={() => handleClickShowPassword("new")}
                              edge="end"
                            >
                              {showPassword.new ? <VisibilityOff /> : <Visibility />}
                            </IconButton>
                          </InputAdornment>
                        ),
                      }}
                    />
                  </Grid>
                  <Grid item xs={12} md={4}>
                    <TextField
                      label="تأكيد كلمة المرور"
                      name="confirm"
                      type={showPassword.confirm ? "text" : "password"}
                      value={passwords.confirm}
                      onChange={handlePasswordChange}
                      fullWidth
                      dir="rtl"
                      InputProps={{
                        endAdornment: (
                          <InputAdornment position="end">
                            <IconButton
                              onClick={() => handleClickShowPassword("confirm")}
                              edge="end"
                            >
                              {showPassword.confirm ? <VisibilityOff /> : <Visibility />}
                            </IconButton>
                          </InputAdornment>
                        ),
                      }}
                    />
                  </Grid>
                </Grid>
                <Stack direction="row" alignItems="center" justifyContent="center" mb={2}>
                  <Tooltip title="معلومة عن كلمة المرور">
                    <InfoOutlinedIcon color="primary" sx={{ mr: 1 }} />
                  </Tooltip>
                  <Typography color="text.secondary" fontSize={15}>
                    كلمة المرور يجب أن تحتوي على الأقل 8 أحرف، وتتضمن حرف كبير ورقم ورمز خاص.
                  </Typography>
                </Stack>
                <Stack direction="row" spacing={2} justifyContent="flex-start" mt={3}>
                  <Button type="submit" variant="contained" sx={{ minWidth: 150, fontWeight: "bold", fontSize: 16, bgcolor: "#4B4DED" }}>
                    حفظ التغييرات
                  </Button>
                  <Button variant="outlined" color="inherit" sx={{ minWidth: 120, fontWeight: "bold", fontSize: 16 }} onClick={handleCancel}>
                    إلغاء
                  </Button>
                </Stack>
              </Box>
            </Grid>
            {/* الصورة الجانبية */}
            <Grid item xs={12} md={4} display="flex" flexDirection="column" alignItems="center" justifyContent="center">
              <Box position="relative" mb={2}>
                <Avatar sx={{ width: 110, height: 110, bgcolor: "#5b6be8", fontSize: 48 }}>
                  {profile.fullName.split(" ").map(w => w[0]).join("").slice(0,2)}
                </Avatar>
                <IconButton
                  sx={{
                    position: "absolute",
                    bottom: 0,
                    right: 0,
                    bgcolor: "#fff",
                    border: "1px solid #eee",
                    boxShadow: 1,
                    "&:hover": { bgcolor: "#f5f6fa" },
                  }}
                  size="small"
                  component="span"
                >
                  <PhotoCameraIcon color="primary" />
                </IconButton>
              </Box>
              <Typography fontWeight="bold" fontSize={18} mb={0.5}>
                {profile.fullName}
              </Typography>
              <Typography color="text.secondary">{profile.jobTitle}</Typography>
            </Grid>
          </Grid>
        </form>
      </Card>
    </Box>
  );
}