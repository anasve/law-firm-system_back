import React, { useState } from "react";
import {
  Box,
  Typography,
  Chip,
  MenuItem,
  Select,
  FormControl,
  InputLabel,
  TextField,
  Checkbox,
  FormGroup,
  FormControlLabel,
  Button,
  Stack,
} from "@mui/material";

const statusOptions = [
  "جديدة",
  "قيد المراجعة",
  "مكتملة",
  "بحاجة لمعلومات إضافية",
];

const requiredDocs = [
  "عقد الزواج",
  "شهادات ميلاد الأطفال",
  "صك الطلاق (إن وجد)",
];

export default function ConsultationDetails() {
  const [status, setStatus] = useState("جديدة");
  const [legalOpinion, setLegalOpinion] = useState("");
  const [recommendations, setRecommendations] = useState("");
  const [internalNotes, setInternalNotes] = useState("");
  const [checkedDocs, setCheckedDocs] = useState([]);

  const handleDocChange = (doc) => {
    setCheckedDocs((prev) =>
      prev.includes(doc)
        ? prev.filter((d) => d !== doc)
        : [...prev, doc]
    );
  };

  // بيانات الاستشارة (يمكنك جلبها من API لاحقاً)
  const consultation = {
    date: "14 مايو 2024",
    title: "استشارة حول قضية أحوال شخصية",
    content:
      "أريد معرفة حقوقي في قضية حضانة أطفالي بعد الطلاق. ما هي الشروط والإجراءات اللازمة؟ لدي طفلان، عمرهما 6 و8 سنوات. وأرغب في الحصول على حضانتهما بعد إتمام إجراءات الطلاق. ما هي حقوقي القانونية وكيف يمكنني ضمان مصلحة أطفالي؟",
    tags: ["أحوال شخصية", "حضانة"],
  };

  return (
    <Box
      sx={{
        maxWidth: 900,
        mx: "auto",
        mt: 4,
        mb: 4,
        background: "#fff",
        borderRadius: "18px",
        boxShadow: "0 4px 24px #e9e7e1",
        p: { xs: 2, md: 4 },
        direction: "rtl",
      }}
    >
      {/* عنوان الصفحة */}
      <Typography variant="h5" fontWeight="bold" mb={3} color="#181818">
        تفاصيل الاستشارة
      </Typography>

      {/* معلومات الاستشارة */}
      <Box
        sx={{
          background: "#faf7ef",
          borderRadius: "12px",
          p: 3,
          mb: 3,
          boxShadow: "0 1px 8px #f3e9c6",
        }}
      >
        <Typography color="#bfa13a" fontSize={15} mb={1}>
          تاريخ الطلب: {consultation.date}
        </Typography>
        <Typography variant="h6" fontWeight="bold" mb={1}>
          {consultation.title}
        </Typography>
        <Typography color="#444" fontSize={17} mb={2} lineHeight={2}>
          {consultation.content}
        </Typography>
        <Stack direction="row" spacing={1}>
          {consultation.tags.map((tag, idx) => (
            <Chip
              key={idx}
              label={tag}
              sx={{
                background:
                  tag === "حضانة"
                    ? "#ffe6b3"
                    : tag === "أحوال شخصية"
                    ? "#e6e6fa"
                    : "#eee",
                color:
                  tag === "حضانة"
                    ? "#e6a700"
                    : tag === "أحوال شخصية"
                    ? "#5c6bc0"
                    : "#888",
                fontWeight: "bold",
              }}
            />
          ))}
        </Stack>
      </Box>

      {/* حالة الاستشارة */}
      <Box mb={3}>
        <FormControl fullWidth variant="outlined" sx={{ direction: "rtl", textAlign: "right" }}>
          <InputLabel
            id="status-label"
            sx={{
              right: "unset",
              left: "unset",
              transformOrigin: "top right",
              direction: "rtl",
              textAlign: "right",
              fontWeight: "bold",
              background: "#fff",
              px: 0.5,
            }}
            shrink
          >
            حالة الاستشارة
          </InputLabel>
          <Select
            labelId="status-label"
            value={status}
            label="حالة الاستشارة"
            onChange={(e) => setStatus(e.target.value)}
            sx={{
              background: "#fff",
              borderRadius: "8px",
              fontWeight: "bold",
              textAlign: "right",
              "& .MuiSelect-select": {
                textAlign: "right",
                pr: 2,
              },
              "& .MuiOutlinedInput-notchedOutline": {
                borderColor: "#e6c96c",
              },
              "&:hover .MuiOutlinedInput-notchedOutline": {
                borderColor: "#d4af37",
              },
            }}
            MenuProps={{
              PaperProps: {
                sx: {
                  direction: "rtl",
                  textAlign: "right",
                },
              },
            }}
          >
            {statusOptions.map((option) => (
              <MenuItem key={option} value={option} sx={{ direction: "rtl", textAlign: "right" }}>
                {option}
              </MenuItem>
            ))}
          </Select>
        </FormControl>
      </Box>

      {/* الرأي القانوني */}
      <Box mb={3}>
        <Typography fontWeight="bold" mb={1}>
          الرأي القانوني
        </Typography>
        <TextField
          multiline
          minRows={4}
          fullWidth
          placeholder="اكتب الرأي القانوني هنا..."
          value={legalOpinion}
          onChange={(e) => setLegalOpinion(e.target.value)}
          sx={{
            background: "#fffbe6",
            borderRadius: "8px",
            "& .MuiOutlinedInput-notchedOutline": {
              borderColor: "#e6c96c",
            },
            "&:hover .MuiOutlinedInput-notchedOutline": {
              borderColor: "#d4af37",
            },
          }}
        />
      </Box>

      {/* التوصيات والإجراءات المقترحة */}
      <Box mb={3}>
        <Typography fontWeight="bold" mb={1}>
          التوصيات والإجراءات المقترحة
        </Typography>
        <TextField
          multiline
          minRows={4}
          fullWidth
          placeholder="اكتب التوصيات والإجراءات المقترحة هنا..."
          value={recommendations}
          onChange={(e) => setRecommendations(e.target.value)}
        />
      </Box>

      {/* ملاحظات داخلية */}
      <Box mb={3}>
        <Typography fontWeight="bold" mb={1}>
          ملاحظات داخلية
        </Typography>
        <TextField
          multiline
          minRows={3}
          fullWidth
          placeholder="ملاحظات داخلية للمكتب (لن تظهر للعميل)..."
          value={internalNotes}
          onChange={(e) => setInternalNotes(e.target.value)}
        />
      </Box>

      {/* المستندات المطلوبة */}
      <Box mb={3}>
        <Typography fontWeight="bold" mb={1}>
          المستندات المطلوبة
        </Typography>
        <FormGroup>
          {requiredDocs.map((doc) => (
            <FormControlLabel
              key={doc}
              control={
                <Checkbox
                  checked={checkedDocs.includes(doc)}
                  onChange={() => handleDocChange(doc)}
                  sx={{
                    color: "#d4af37",
                    "&.Mui-checked": { color: "#d4af37" },
                  }}
                />
              }
              label={doc}
              sx={{ fontWeight: "bold" }}
            />
          ))}
        </FormGroup>
      </Box>

      {/* زر الحفظ */}
      <Box textAlign="left" mt={4}>
        <Button
          variant="contained"
          sx={{
            background: "linear-gradient(90deg, #d4af37 0%, #e6c96c 100%)",
            color: "#fff",
            fontWeight: "bold",
            px: 5,
            py: 1.5,
            fontSize: "18px",
            borderRadius: "10px",
            boxShadow: "0 2px 8px #e9e7e1",
            transition: "0.2s",
            "&:hover": {
              background: "linear-gradient(90deg, #bfa13a 0%, #d4af37 100%)",
            },
          }}
        >
          حفظ التعديلات
        </Button>
      </Box>
    </Box>
  );
}