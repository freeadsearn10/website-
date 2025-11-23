import { Router } from "express";

export const apiRouter = Router();

// Public REST API stubs

apiRouter.get("/numbers", (_req, res) => {
  res.json({ numbers: [] });
});

apiRouter.post("/messages", (req, res) => {
  const { to, from, text } = req.body || {};
  res.status(202).json({
    status: "accepted",
    to,
    from,
    text,
    messageId: "msg_stub_1"
  });
});

apiRouter.get("/messages/:id", (req, res) => {
  res.json({
    id: req.params.id,
    status: "delivered",
    revenue: 0
  });
});