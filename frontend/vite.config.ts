import { defineConfig } from "vite";
import react from "@vitejs/plugin-react-swc";

export default defineConfig({
  plugins: [react()],
  server: {
    port: 5173,
    proxy: {
      "/api": "http://localhost:3000",
      "/admin": "http://localhost:3000",
      "/user": "http://localhost:3000",
      "/webhooks": "http://localhost:3000"
    }
  }
});