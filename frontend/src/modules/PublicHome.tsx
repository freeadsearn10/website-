import React from "react";
import { Link } from "react-router-dom";

export const PublicHome: React.FC = () => {
  return (
    <section className="layout">
      <div className="hero">
        <h1>Carrier-Grade IPRN SMS Monetization</h1>
        <p>
          Launch, manage, and monetize international premium rate numbers with a
          carrier-grade SMS platform. Real-time routing, transparent billing,
          and built-in compliance.
        </p>
        <div className="hero-actions">
          <Link className="btn primary" to="/user">
            Go to User Portal
          </Link>
          <a className="btn ghost" href="#api">
            View API Docs
          </a>
        </div>
      </div>

      <div className="cards-grid">
        <div className="card">
          <h2>For Carriers and Aggregators</h2>
          <p>
            Onboard IPRNs, manage rates per country, and monitor QoS with
            real-time insights across your routes.
          </p>
        </div>
        <div className="card">
          <h2>Real-time Billing</h2>
          <p>
            Revenue-per-message calculation, transparent sharing rules, and
            exportable reports for accounting.
          </p>
        </div>
        <div className="card">
          <h2>Compliance Built-in</h2>
          <p>
            KYC, content monitoring hooks, and retention policies aligned with
            telecom regulations.
          </p>
        </div>
      </div>

      <section id="api" className="api-preview">
        <h2>API at a Glance</h2>
        <div className="api-columns">
          <div>
            <h3>Send SMS</h3>
            <pre>
{`POST /api/v1/messages
{
  "from": "441234567890",
  "to": "4915112345678",
  "text": "Hello from IPRN!"
}`}
            </pre>
          </div>
          <div>
            <h3>Webhook: Inbound SMS</h3>
            <pre>
{`POST /webhooks/inbound-sms
{
  "to": "441234567890",
  "from": "4915112345678",
  "text": "STOP"
}`}
            </pre>
          </div>
        </div>
        <p className="muted">
          This demo UI uses stubbed backend endpoints. Replace them with your
          live environment and real provider connections.
        </p>
      </section>
    </section>
  );
};