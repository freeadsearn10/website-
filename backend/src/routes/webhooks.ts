import { Router } from "express";

export const webhookRouter = Router();

// Inbound SMS webhook
webhookRouter.post("/inbound-sms", (req, res) => {
  const payload = req.body;
  // In a full implementation, this would enqueue a job to the message processor
  res.status(202).json({ status: "accepted", payload });
});

// Delivery receipt webhook
webhookRouter.post("/dlr", (req, res) => {
  const payload = req.body;
  res.status(202).json({ status: "accepted", payload });
});