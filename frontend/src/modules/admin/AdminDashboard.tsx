import React, { useEffect, useState } from "react";
import { Link, Route, Routes, useLocation } from "react-router-dom";

type DashboardStats = {
  activeUsers: number;
  activeNumbers: number;
  messagesToday: number;
  revenueToday: number;
};

const useAdminStats = () => {
  const [stats, setStats] = useState<DashboardStats | null>(null);

  useEffect(() => {
    fetch("/admin/dashboard")
      .then((r) => r.json())
      .then((data) => setStats(data.stats))
      .catch(() => setStats(null));
  }, []);

  return stats;
};

const DashboardHome: React.FC = () => {
  const stats = useAdminStats();

  return (
    <div className="admin-grid">
      <div className="admin-header">
        <h1>Admin Panel</h1>
        <p className="muted">
          Monitor platform health, manage numbers, and review revenue
          performance.
        </p>
      </div>
      <div className="stat-grid">
        <div className="stat-card">
          <span className="stat-label">Active Users</span>
          <span className="stat-value">{stats?.activeUsers ?? 0}</span>
        </div>
        <div className="stat-card">
          <span className="stat-label">Active Numbers</span>
          <span className="stat-value">{stats?.activeNumbers ?? 0}</span>
        </div>
        <div className="stat-card">
          <span className="stat-label">Messages Today</span>
          <span className="stat-value">{stats?.messagesToday ?? 0}</span>
        </div>
        <div className="stat-card">
          <span className="stat-label">Revenue Today</span>
          <span className="stat-value">
            ${stats?.revenueToday?.toFixed?.(2) ?? "0.00"}
          </span>
        </div>
      </div>
    </div>
  );
};

const Inventory: React.FC = () => {
  const [numbers, setNumbers] = useState<Array<{ id: string; msisdn: string; country: string; rate: number; status: string }>>([]);

  useEffect(() => {
    fetch("/admin/numbers")
      .then((r) => r.json())
      .then((data) => setNumbers(data.numbers || []))
      .catch(() => setNumbers([]));
  }, []);

  return (
    <div className="panel">
      <h2>Number Inventory</h2>
      <p className="muted">
        This is a demo view powered by the stubbed backend endpoints. Integrate
        your real inventory and provisioning logic.
      </p>
      {numbers.length === 0 ? (
        <p>No numbers yet.</p>
      ) : (
        <div className="table">
          <div className="table-row table-header">
            <span>Number</span>
            <span>Country</span>
            <span>Rate</span>
            <span>Status</span>
          </div>
          {numbers.map((n) => (
            <div className="table-row" key={n.id}>
              <span>{n.msisdn}</span>
              <span>{n.country}</span>
              <span>{n.rate.toFixed(3)}</span>
              <span>{n.status}</span>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

const RevenueReports: React.FC = () => {
  const [rows, setRows] = useState<
    Array<{ date: string; messages: number; revenue: number }>
  >([]);

  useEffect(() => {
    fetch("/admin/reports/revenue")
      .then((r) => r.json())
      .then((data) => setRows(data.report || []))
      .catch(() => setRows([]));
  }, []);

  return (
    <div className="panel">
      <h2>Revenue Reports</h2>
      <p className="muted">
        Export daily/weekly/monthly revenue for accounting and partner
        settlements.
      </p>
      {rows.length === 0 ? (
        <p>No data yet.</p>
      ) : (
        <div className="table">
          <div className="table-row table-header">
            <span>Date</span>
            <span>Messages</span>
            <span>Revenue</span>
          </div>
          {rows.map((r) => (
            <div className="table-row" key={r.date}>
              <span>{r.date}</span>
              <span>{r.messages}</span>
              <span>${r.revenue.toFixed(2)}</span>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

const FraudAlerts: React.FC = () => {
  const [alerts, setAlerts] = useState<
    Array<{ id: string; number: string; pattern: string; riskScore: number }>
  >([]);

  useEffect(() => {
    fetch("/admin/fraud/alerts")
      .then((r) => r.json())
      .then((data) => setAlerts(data.alerts || []))
      .catch(() => setAlerts([]));
  }, []);

  return (
    <div className="panel">
      <h2>Fraud Detection</h2>
      <p className="muted">
        Monitor abnormal traffic patterns and suspicious message content.
      </p>
      {alerts.length === 0 ? (
        <p>No active alerts.</p>
      ) : (
        <div className="table">
          <div className="table-row table-header">
            <span>Number</span>
            <span>Pattern</span>
            <span>Risk</span>
          </div>
          {alerts.map((a) => (
            <div className="table-row" key={a.id}>
              <span>{a.number}</span>
              <span>{a.pattern}</span>
              <span>{a.riskScore}</span>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

const AdminNav: React.FC = () => {
  const location = useLocation();
  const current = location.pathname;

  return (
    <aside className="side-nav">
      <Link to="/admin" className={current === "/admin" ? "active" : ""}>
        Overview
      </Link>
      <Link
        to="/admin/inventory"
        className={current.startsWith("/admin/inventory") ? "active" : ""}
      >
        Number Inventory
      </Link>
      <Link
        to="/admin/revenue"
        className={current.startsWith("/admin/revenue") ? "active" : ""}
      >
        Revenue Reports
      </Link>
      <Link
        to="/admin/fraud"
        className={current.startsWith("/admin/fraud") ? "active" : ""}
      >
        Fraud Detection
      </Link>
    </aside>
  );
};

export const AdminDashboard: React.FC = () => {
  return (
    <div className="shell">
      <AdminNav />
      <div className="shell-main">
        <Routes>
          <Route index element={<DashboardHome />} />
          <Route path="inventory" element={<Inventory />} />
          <Route path="revenue" element={<RevenueReports />} />
          <Route path="fraud" element={<FraudAlerts />} />
        </Routes>
      </div>
    </div>
  );
};