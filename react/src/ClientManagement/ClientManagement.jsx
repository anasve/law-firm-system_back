import React from 'react';
import { Box, Container, Typography, Button, Grid, Paper } from '@mui/material';
import { styled } from '@mui/material/styles';
import LockOutlinedIcon from '@mui/icons-material/LockOutlined';
import PersonAddOutlinedIcon from '@mui/icons-material/PersonAddOutlined';

// تعريف الألوان الرئيسية
const colors = {
  gold: '#D4AF37',
  darkGold: '#B4943C',
  black: '#1A1A1A',
  white: '#FFFFFF',
};

// تخصيص زر بتصميم خاص
const StyledButton = styled(Button)(({ theme }) => ({
  borderRadius: '8px',
  padding: '12px 32px',
  fontSize: '1.1rem',
  fontWeight: 'bold',
  transition: 'all 0.3s ease',
  '&:hover': {
    transform: 'translateY(-2px)',
    boxShadow: '0 5px 15px rgba(212, 175, 55, 0.3)',
  },
}));

// تخصيص ورقة للأزرار
const ActionCard = styled(Paper)(({ theme }) => ({
  padding: '2rem',
  textAlign: 'center',
  background: 'rgba(255, 255, 255, 0.95)',
  borderRadius: '15px',
  transition: 'all 0.3s ease',
  cursor: 'pointer',
  '&:hover': {
    transform: 'translateY(-5px)',
    boxShadow: '0 8px 25px rgba(0, 0, 0, 0.1)',
  },
}));

export default function LandingPage() {
  return (
    <Box
      sx={{
        minHeight: '100vh',
        background: `linear-gradient(135deg, ${colors.black} 0%, #2C2C2C 100%)`,
        position: 'relative',
        overflow: 'hidden',
      }}
    >
      {/* زخرفة خلفية */}
      <Box
        sx={{
          position: 'absolute',
          top: 0,
          right: 0,
          width: '100%',
          height: '100%',
          opacity: 0.03,
          background: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23D4AF37' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`,
        }}
      />

      {/* المحتوى الرئيسي */}
      <Container maxWidth="lg">
        {/* الشعار والعنوان */}
        <Box sx={{ textAlign: 'center', pt: 8, pb: 6 }}>
          <Typography
            variant="h1"
            component="h1"
            sx={{
              fontSize: { xs: '2.5rem', md: '4rem' },
              fontWeight: 'bold',
              color: colors.gold,
              mb: 3,
              textShadow: '2px 2px 4px rgba(0,0,0,0.3)',
            }}
          >
            محامي تك
          </Typography>
          <Typography
            variant="h2"
            sx={{
              fontSize: { xs: '1.5rem', md: '2rem' },
              color: colors.white,
              mb: 5,
              maxWidth: '800px',
              mx: 'auto',
              lineHeight: 1.6,
            }}
          >
            نظام متكامل لإدارة مكتب المحاماة الخاص بك
            <br />
            حلول قانونية ذكية لمستقبل أكثر نجاحاً
          </Typography>
        </Box>

        {/* بطاقات الإجراءات */}
        <Grid container spacing={4} justifyContent="center" sx={{ mb: 8 }}>
          <Grid item xs={12} sm={6} md={5}>
            <ActionCard elevation={5}>
              <LockOutlinedIcon
                sx={{ fontSize: 40, color: colors.gold, mb: 2 }}
              />
              <Typography
                variant="h5"
                component="h3"
                sx={{ color: colors.black, mb: 3, fontWeight: 'bold' }}
              >
                تسجيل الدخول
              </Typography>
              <StyledButton
                variant="contained"
                fullWidth
                sx={{
                  bgcolor: colors.gold,
                  color: colors.white,
                  '&:hover': {
                    bgcolor: colors.darkGold,
                  },
                }}
              >
                دخول
              </StyledButton>
            </ActionCard>
          </Grid>
          
          <Grid item xs={12} sm={6} md={5}>
            <ActionCard elevation={5}>
              <PersonAddOutlinedIcon
                sx={{ fontSize: 40, color: colors.gold, mb: 2 }}
              />
              <Typography
                variant="h5"
                component="h3"
                sx={{ color: colors.black, mb: 3, fontWeight: 'bold' }}
              >
                إنشاء حساب جديد
              </Typography>
              <StyledButton
                variant="outlined"
                fullWidth
                sx={{
                  borderColor: colors.gold,
                  color: colors.gold,
                  '&:hover': {
                    borderColor: colors.darkGold,
                    color: colors.darkGold,
                    bgcolor: 'rgba(212, 175, 55, 0.05)',
                  },
                }}
              >
                تسجيل
              </StyledButton>
            </ActionCard>
          </Grid>
        </Grid>

        {/* نص ترحيبي إضافي */}
        <Box sx={{ textAlign: 'center', pb: 8 }}>
          <Typography
            variant="h6"
            sx={{
              color: colors.white,
              opacity: 0.9,
              maxWidth: '700px',
              mx: 'auto',
              lineHeight: 1.8,
            }}
          >
            نرحب بك في منصتنا المتخصصة لإدارة مكاتب المحاماة. نقدم لك حلولاً متكاملة
            تجمع بين الخبرة القانونية والتكنولوجيا الحديثة لتحسين كفاءة عملك وتطوير
            مكتبك القانوني.
          </Typography>
        </Box>
      </Container>
    </Box>
  );
}