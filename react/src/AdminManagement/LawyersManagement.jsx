import React, { useState } from "react";
import {
  Box,
  Typography,
  Button,
  Tabs,
  Tab,
  Card,
  Avatar,
} from "@mui/material";
import PeopleAltOutlinedIcon from "@mui/icons-material/PeopleAltOutlined";
import Groups2OutlinedIcon from "@mui/icons-material/Groups2Outlined";
import SearchBar from "../SearchBar";
import LawyersGrid from "./LawyersGrid";
import EditLawyerDialog from "./EditLawyerDialog";
import EmployeesGrid from "./EmployeesGrid";
import EditEmployeeDialog from "./EditEmployeeDialog";
import GoldenSidebar from "./GoldenSidebar";
import { useNavigate } from "react-router-dom"; // أضف هذا السطر

const initialLawyers = [
  {
    id: 1,
    name: "John Doe",
    specialty: "Corporate Law",
    email: "john.doe@lexicon.com",
    phone: "(555) 123-4567",
    barNumber: "#123456",
    address: "New York",
    status: "active",
    cases: 12,
  },
  {
    id: 2,
    name: "Sarah Johnson",
    specialty: "Family Law",
    email: "sarah.johnson@lexicon.com",
    phone: "(555) 234-5678",
    barNumber: "#234567",
    address: "Los Angeles",
    status: "active",
    cases: 8,
  },
  // ... أضف المزيد من المحامين
];

const initialEmployees = [
  {
    id: 1,
    name: "Ali Ahmad",
    position: "Secretary",
    email: "ali.ahmad@lexicon.com",
    phone: "(555) 111-2222",
    address: "Damascus",
    status: "active",
  },
  {
    id: 2,
    name: "Mona Khaled",
    position: "Accountant",
    email: "mona.khaled@lexicon.com",
    phone: "(555) 333-4444",
    address: "Aleppo",
    status: "active",
  },
  // ... أضف المزيد من الموظفين
];

export default function ManagementPage() {
  const [mainTab, setMainTab] = useState(0);

  // محامين
  const [lawyers, setLawyers] = useState(initialLawyers);
  const [lawyerSearch, setLawyerSearch] = useState("");
  const [lawyerTab, setLawyerTab] = useState(0);
  const [editLawyerOpen, setEditLawyerOpen] = useState(false);
  const [selectedLawyer, setSelectedLawyer] = useState(null);

  const filteredLawyers = lawyers
    .filter(lawyer => {
      if (lawyerTab === 1) return lawyer.status === "active";
      if (lawyerTab === 2) return lawyer.status === "archived";
      return true;
    })
    .filter(lawyer =>
      lawyer.name.toLowerCase().includes(lawyerSearch.toLowerCase())
    );

  const handleEditLawyer = (lawyer) => {
    setSelectedLawyer(lawyer);
    setEditLawyerOpen(true);
  };

  const handleSaveEditLawyer = (updatedLawyer) => {
    setLawyers(lawyers.map(l => l.id === updatedLawyer.id ? updatedLawyer : l));
    setEditLawyerOpen(false);
  };

  const handleDeleteLawyer = (lawyer) => setLawyers(lawyers.filter(l => l.id !== lawyer.id));
  const handleArchiveLawyer = (lawyer) => setLawyers(lawyers.map(l => l.id === lawyer.id ? { ...l, status: "archived" } : l));
  const handleViewLawyer = (lawyer) => alert("View: " + lawyer.name);

  // موظفين
  const [employees, setEmployees] = useState(initialEmployees);
  const [employeeSearch, setEmployeeSearch] = useState("");
  const [employeeTab, setEmployeeTab] = useState(0);
  const [editEmployeeOpen, setEditEmployeeOpen] = useState(false);
  const [selectedEmployee, setSelectedEmployee] = useState(null);

  const filteredEmployees = employees
    .filter(employee => {
      if (employeeTab === 1) return employee.status === "active";
      if (employeeTab === 2) return employee.status === "archived";
      return true;
    })
    .filter(employee =>
      employee.name.toLowerCase().includes(employeeSearch.toLowerCase())
    );

  const handleEditEmployee = (employee) => {
    setSelectedEmployee(employee);
    setEditEmployeeOpen(true);
  };

  const handleSaveEditEmployee = (updatedEmployee) => {
    setEmployees(employees.map(e => e.id === updatedEmployee.id ? updatedEmployee : e));
    setEditEmployeeOpen(false);
  };

  const handleDeleteEmployee = (employee) => setEmployees(employees.filter(e => e.id !== employee.id));
  const handleArchiveEmployee = (employee) => setEmployees(employees.map(e => e.id === employee.id ? { ...e, status: "archived" } : e));
  const handleViewEmployee = (employee) => alert("View: " + employee.name);

  // زر إضافة محامي
  const navigate = useNavigate();
  const handleAddLawyer = () => navigate("/add-lawyer");

  // زر إضافة موظف (يمكنك تعديله لاحقاً)
  const handleAddEmployee = () => navigate("/add-employee");

  return (
    <Box sx={{ display: "flex" }}>
      <GoldenSidebar onNavigate={(page) => { /* يمكنك وضع كود التنقل هنا */ }} />
      <Box sx={{ flex: 1, p: 3 }}>
        {/* ديزاين العنوان الجديد */}
        <Card
          sx={{
            display: "flex",
            alignItems: "center",
            mb: 4,
            p: 3,
            borderRadius: 3,
            boxShadow: "0 4px 24px 0 rgba(0,0,0,0.08)",
            background: "linear-gradient(90deg, #fffbe6 0%, #f7e9c6 100%)",
          }}
        >
          <Avatar
            sx={{
              bgcolor: "#D4AF37",
              width: 56,
              height: 56,
              mr: 2,
              boxShadow: "0 2px 8px 0 rgba(212,175,55,0.15)",
            }}
          >
            <PeopleAltOutlinedIcon sx={{ fontSize: 32, color: "#181818" }} />
          </Avatar>
          <Box>
            <Typography variant="h5" fontWeight="bold" color="#181818">
              Employee & Lawyer Management
            </Typography>
            <Typography variant="subtitle2" color="#D4AF37" fontWeight="bold">
              إدارة الموظفين والمحامين في المكتب
            </Typography>
          </Box>
        </Card>

        {/* Tabs رئيسية: محامين/موظفين */}
        <Tabs value={mainTab} onChange={(_, v) => setMainTab(v)} sx={{ mb: 3 }}>
          <Tab label="All Lawyers" icon={<PeopleAltOutlinedIcon />} iconPosition="start" />
          <Tab label="All Employees" icon={<Groups2OutlinedIcon />} iconPosition="start" />
        </Tabs>

        {/* محتوى تبويب المحامين */}
        {mainTab === 0 && (
          <>
            <Typography variant="h4" fontWeight="bold" mb={2}>Lawyers Management</Typography>
            <Box display="flex" alignItems="center" gap={2} mb={2}>
              <Tabs value={lawyerTab} onChange={(_, v) => setLawyerTab(v)}>
                <Tab label="All Lawyers" />
                <Tab label="Active" />
                <Tab label="Archived" />
              </Tabs>
              <Box flex={1} />
              <SearchBar value={lawyerSearch} onChange={e => setLawyerSearch(e.target.value)} />
              <Button variant="contained" color="primary" onClick={handleAddLawyer}>+ Add Lawyer</Button>
            </Box>
            <LawyersGrid
              lawyers={filteredLawyers}
              onEdit={handleEditLawyer}
              onDelete={handleDeleteLawyer}
              onArchive={handleArchiveLawyer}
              onView={handleViewLawyer}
            />
            <EditLawyerDialog
              open={editLawyerOpen}
              onClose={() => setEditLawyerOpen(false)}
              lawyer={selectedLawyer}
              onSave={handleSaveEditLawyer}
            />
          </>
        )}

        {/* محتوى تبويب الموظفين */}
        {mainTab === 1 && (
          <>
            <Typography variant="h4" fontWeight="bold" mb={2}>Employees Management</Typography>
            <Box display="flex" alignItems="center" gap={2} mb={2}>
              <Tabs value={employeeTab} onChange={(_, v) => setEmployeeTab(v)}>
                <Tab label="All Employees" />
                <Tab label="Active" />
                <Tab label="Archived" />
              </Tabs>
              <Box flex={1} />
              <SearchBar value={employeeSearch} onChange={e => setEmployeeSearch(e.target.value)} />
              <Button variant="contained" color="primary" onClick={handleAddEmployee}>+ Add Employee</Button>
            </Box>
            <EmployeesGrid
              employees={filteredEmployees}
              onEdit={handleEditEmployee}
              onDelete={handleDeleteEmployee}
              onArchive={handleArchiveEmployee}
              onView={handleViewEmployee}
            />
            <EditEmployeeDialog
              open={editEmployeeOpen}
              onClose={() => setEditEmployeeOpen(false)}
              employee={selectedEmployee}
              onSave={handleSaveEditEmployee}
            />
          </>
        )}
      </Box>
    </Box>
  );
}