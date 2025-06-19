// src/AdminManagement/FinancialPage.jsx
import React from "react";
import FinancialDashboard from "./FinancialDashboard";
import FinancialTable from "./FinancialTable";

export default function FinancialPage() {
  return (
    <div>
      <FinancialDashboard />
      <FinancialTable />
    </div>
  );
}