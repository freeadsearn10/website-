import express from "express";
import cors from "cors";
import helmet from "helmet";
import morgan from "morgan";

import { adminRouter } from "./routes/admin";
import { userRouter } from "./routes/user";
import { apiRouter } from "./routes/api";
import { webhookRouter } from "./routes/webhooks";

const app = express();

app.use(helmet());
app.use(cors());
app.use(express.json());
app.use(morgan("dev"));

// Basic health endpoint
app.get("/health", (_req, res) => {
  res.json({ status: "ok" });
});

// High-level areas
app.use("/admin", adminRouter);
app.use("/user", userRouter);
app.use("/api/v1", apiRouter);
app.use("/webhooks", webhookRouter);

const port = process.env.PORT || 3000;

app.listen(port, () => {
  console.log(`IPRN SMS backend listening on port ${port}`);
});