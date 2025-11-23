import { Router } from "express";

export const userRouter = Router();

// Stub endpoints for the User Portal

userRouter.get("/account", (_req, res) => {
  res.json({ account: null });
});

userRouter.get("/numbers", (_req, res) => {
  res.json({ numbers: [] });
});

userRouter.get("/messages", (_req, res) => {
  res.json({ messages: [] });
});

userRouter.get("/balance", (_req, res) => {
  res.json({ balance: 0, currency: "USD", earningsToday: 0 });
});

userRouter.get("/api-keys", (_req, res) => {
  res.json({ apiKeys: [] });
});

userRouter.get("/support/tickets", (_req, res) => {
  res.json({ tickets: [] });
});