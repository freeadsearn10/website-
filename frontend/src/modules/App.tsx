import React from "react";
import { Link, Navigate, Route, Routes } from "react-router-dom";
import { PublicHome } from "./PublicHome";
import { AdminDashboard } from "./admin/AdminDashboard";
import { UserDashboard } from "./user/UserDashboard";

export const App: React.FC = () => {
  return (
    <div className="app-root">
      <header className="top-nav">
        <div className="brand">
          <span className="brand-mark">IPRN</span> SMS Platform
        </div>
        <nav className="top-links">
          <Link to="/">Home</Link>
          <Link to="/user">User Portal</Link>
          <Link to="/admin">Admin Panel</Link>
        </nav>
      </header>

      <main className="app-main">
        <Routes>
          <Route path="/" element={<PublicHome />} />
          <Route path="/user/*" element={<UserDashboard />} />
          <Route path="/admin/*" element={<AdminDashboard />} />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </main>

      <footer className="app-footer">
        <span>© {new Date().getFullYear()} IPRN SMS Platform</span>
      </footer>
    </div>
  );
};