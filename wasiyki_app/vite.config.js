import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  // resolve: {
  //   alias: {
  //     "@": path.resolve(__dirname, "./src"),
  //     "@components": path.resolve(__dirname, "./src/components"),
  //     "@pages": path.resolve(__dirname, "./src/pages"),
  //     "@services": path.resolve(__dirname, "./src/services"),
  //     "@utils": path.resolve(__dirname, "./src/utils"),
  //     "@hooks": path.resolve(__dirname, "./src/hooks"),
  //     "@contexts": path.resolve(__dirname, "./src/contexts"),
  //     "@assets": path.resolve(__dirname, "./src/assets"),
  //     "@styles": path.resolve(__dirname, "./src/styles"),
  //   },
  // },
  server: {
    port: 5173,
    // port: 3000,
    proxy: {
      "/api": {
        target: "http://localhost:8000",
        changeOrigin: true,
      },
    },
  },
});
