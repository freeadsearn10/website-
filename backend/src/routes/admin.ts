import { Router } from "express";

export const adminRouter = Router();

// Stub endpoints for the Admin Panel

adminRouter.get("/dashboard", (_req, res) => {
  res.json({
    stats: {
      activeUsers: 0,
      activeNumbers: 0,
      messagesToday: 0,
      revenueToday: 0
    }
  });
});

adminRouter.get("/users", (_req, res) => {
  res.json({ users: [] });
});

adminRouter.get("/numbers", (_req, res) => {
  res.json({ numbers: [] });
});

adminRouter.get("/reports/revenue", (_req, res) => {
  res.json({ report: [] });
});

adminRouter.get("/fraud/alerts", (_req, res) => {
  res.json({ alerts: [] });
});