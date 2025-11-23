import React, { useEffect, useState } from "react";
import { Link, Route, Routes, useLocation } from "react-router-dom";

type UserBalance = {
  balance: number;
  currency: string;
  earningsToday: number;
};

const Overview: React.FC = () => {
  const [balance, setBalance] = useState<UserBalance | null>(null);

  useEffect(() => {
    fetch("/user/balance")
      .then((r) => r.json())
      .then((data) => setBalance(data))
      .catch(() =>
        setBalance({ balance: 0, currency: "USD", earningsToday: 0 })
      );
  }, []);

  return (
    <div className="panel">
      <h1>User Portal</h1>
      <p className="muted">
        Manage your IPRN numbers, review traffic, and track your earnings.
      </p>
      <div className="stat-grid compact">
        <div className="stat-card">
          <span className="stat-label">Current Balance</span>
          <span className="stat-value">
            {balance?.currency ?? "USD"} {balance?.balance.toFixed?.(2) ?? "0.00"}
          </span>
        </div>
        <div className="stat-card">
          <span className="stat-label">Earnings Today</span>
          <span className="stat-value">
            {balance?.currency ?? "USD"}{" "}
            {balance?.earningsToday.toFixed?.(2) ?? "0.00"}
          </span>
        </div>
      </div>
    </div>
  );
};

const Numbers: React.FC = () => {
  const [numbers, setNumbers] = useState<
    Array<{ id: string; msisdn: string; country: string; rate: number; status: string }>
  >([]);

  useEffect(() => {
    fetch("/user/numbers")
      .then((r) => r.json())
      .then((data) => setNumbers(data.numbers || []))
      .catch(() => setNumbers([]));
  }, []);

  return (
    <div className="panel">
      <h2>Your Numbers</h2>
      <p className="muted">
        Browse your active IPRN numbers and request new ones for additional
        geographies.
      </p>
      {numbers.length === 0 ? (
        <p>No numbers assigned yet.</p>
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

const Messages: React.FC = () => {
  const [messages, setMessages] = useState<
    Array<{ id: string; from: string; to: string; text: string; receivedAt: string }>
  >([]);

  useEffect(() => {
    fetch("/user/messages")
      .then((r) => r.json())
      .then((data) => setMessages(data.messages || []))
      .catch(() => setMessages([]));
  }, []);

  return (
    <div className="panel">
      <h2>Message Log</h2>
      <p className="muted">
        View recent inbound messages. Connect webhooks to push events into your
        own systems.
      </p>
      {messages.length === 0 ? (
        <p>No messages yet.</p>
      ) : (
        <div className="table">
          <div className="table-row table-header">
            <span>From</span>
            <span>To</span>
            <span>Text</span>
            <span>Received</span>
          </div>
          {messages.map((m) => (
            <div className="table-row" key={m.id}>
              <span>{m.from}</span>
              <span>{m.to}</span>
              <span className="truncate">{m.text}</span>
              <span>{new Date(m.receivedAt).toLocaleString()}</span>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

const ApiKeys: React.FC = () => {
  const [apiKeys, setApiKeys] = useState<
    Array<{ id: string; label: string; createdAt: string }>
  >([]);

  useEffect(() => {
    fetch("/user/api-keys")
      .then((r) => r.json())
      .then((data) => setApiKeys(data.apiKeys || []))
      .catch(() => setApiKeys([]));
  }, []);

  return (
    <div className="panel">
      <h2>API Keys</h2>
      <p className="muted">
        Generate API keys to integrate with your billing, CRM, or campaign
        tools.
      </p>
      {apiKeys.length === 0 ? (
        <p>No API keys yet.</p>
      ) : (
        <ul className="simple-list">
          {apiKeys.map((k) => (
            <li key={k.id}>
              <div className="list-title">{k.label}</div>
              <div className="list-sub">
                Created: {new Date(k.createdAt).toLocaleString()}
              </div>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

const Support: React.FC = () => {
  const [tickets, setTickets] = useState<
    Array<{ id: string; subject: string; status: string; createdAt: string }>
  >([]);

  useEffect(() => {
    fetch("/user/support/tickets")
      .then((r) => r.json())
      .then((data) => setTickets(data.tickets || []))
      .catch(() => setTickets([]));
  }, []);

  return (
    <div className="panel">
      <h2>Support Tickets</h2>
      <p className="muted">
        Reach out to operations for routing issues, settlement questions, or
        compliance guidance.
      </p>
      {tickets.length === 0 ? (
        <p>No tickets yet.</p>
      ) : (
        <ul className="simple-list">
          {tickets.map((t) => (
            <li key={t.id}>
              <div className="list-title">{t.subject}</div>
              <div className="list-sub">
                Status: {t.status} • Created:{" "}
                {new Date(t.createdAt).toLocaleString()}
              </div>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

const UserNav: React.FC = () => {
  const location = useLocation();
  const current = location.pathname;

  return (
    <aside className="side-nav">
      <Link to="/user" className={current === "/user" ? "active" : ""}>
        Overview
      </Link>
      <Link
        to="/user/numbers"
        className={current.startsWith("/user/numbers") ? "active" : ""}
      >
        Numbers
      </Link>
      <Link
        to="/user/messages"
        className={current.startsWith("/user/messages") ? "active" : ""}
      >
        Messages
      </Link>
      <Link
        to="/user/api-keys"
        className={current.startsWith("/user/api-keys") ? "active" : ""}
      >
        API Keys
      </Link>
      <Link
        to="/user/support"
        className={current.startsWith("/user/support") ? "active" : ""}
      >
        Support
      </Link>
    </aside>
  );
};

export const UserDashboard: React.FC = () => {
  return (
    <div className="shell">
      <UserNav />
      <div className="shell-main">
        <Routes>
          <Route index element={<Overview />} />
          <Route path="numbers" element={<Numbers />} />
          <Route path="messages" element={<Messages />} />
          <Route path="api-keys" element={<ApiKeys />} />
          <Route path="support" element={<Support />} />
        </Routes>
      </div>
    </div>
  );
};