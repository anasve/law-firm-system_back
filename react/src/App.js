import React from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
// المدير
import Login from "./AdminManagement/LoginAdmin";
import LawyersManagement from "./AdminManagement/LawyersManagement";
import ProfileEdit from "./AdminManagement/ProfileEdit";
import FinancialPage from "./AdminManagement/FinancialPage";
import ComplaintsList from "./AdminManagement/ComplaintsList";
import ComplaintDetails from "./AdminManagement/ComplaintDetails";
import JobApplicationsTable from "./AdminManagement/JobApplicationsTable";
import JobRequestDetails from "./AdminManagement/JobRequestDetails";
import AddLawyer from "./AdminManagement/AddLawyer";
import AddEmployee from "./AdminManagement/AddEmployee";
// المحامي
import LoginLawyer from "./LawyerManagement/LoginLawyer";
import LawyerLawsPage from "./LawyerManagement/LawyerLawsPage";
import PasswordChangeLawyer from "./LawyerManagement/PasswordChangeLawyer";
import LawyerAppointmentsTable from "./LawyerManagement/LawyerAppointmentsTable";
import ConsultationDetails from "./LawyerManagement/ConsultationDetails";
import LawsViewer from "./LawyerManagement/LawsViewer";
// الموظف
import EmployeeAppointmentsPage from "./EmployeeManagement/EmployeeAppointmentsPage";
import EditAppointmentDialog from "./EmployeeManagement/EditAppointmentDialog";
import DeleteAppointmentDialog from "./EmployeeManagement/DeleteAppointmentDialog";
import "./App.css";
import ClientManagement from "./ClientManagement/ClientManagement";
// العميل


function App() {
  return (
    <BrowserRouter>
      <Routes>
        {/* المدير */}
        <Route path="/" element={<Login />} />
        <Route path="/lawyers" element={<LawyersManagement />} />
        <Route path="/profile-edit" element={<ProfileEdit />} />
        <Route path="/financial-details" element={<FinancialPage />} />
        <Route path="/complaints" element={<ComplaintsList />} />
        <Route path="/complaints/:id" element={<ComplaintDetails />} />
        <Route path="/job-requests" element={<JobApplicationsTable />} />
        <Route path="/job-requests/:id" element={<JobRequestDetails />} />
        <Route path="/add-lawyer" element={<AddLawyer />} />
        <Route path="/add-employee" element={<AddEmployee />} />

        {/* المحامي */}
        <Route path="/login-lawyer" element={<LoginLawyer />} />
        <Route path="/lawyer" element={<LawyerLawsPage />} />
        <Route path="/lawyer-change-password" element={<PasswordChangeLawyer />} />
        <Route path="/lawyer-appointments" element={<LawyerAppointmentsTable />} />
        <Route path="/lawyer-consultations" element={<ConsultationDetails />} />

        {/* LawsViewer */}
        <Route path="/laws-viewer" element={<LawsViewer />} />
        
        {/* الموظف - المواعيد */}
        <Route path="/employee-appointments" element={<EmployeeAppointmentsPage />} />
        <Route path="/employee-appointments/edit" element={<EditAppointmentDialog />} />
        <Route path="/employee-appointments/delete" element={<DeleteAppointmentDialog />} />


        <Route path="/client-management" element={<ClientManagement />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;