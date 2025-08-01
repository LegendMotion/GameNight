import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    outDir: 'public',
    emptyOutDir: false,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/main.js'),
        adminLogin: resolve(__dirname, 'src/admin/login.js')
      },
      output: {
        entryFileNames: ({ name }) => {
          if (name === 'adminLogin') {
            return 'admin/login.js';
          }
          return '[name].js';
        },
        assetFileNames: 'assets/[name][extname]'
      }
    }
  },
  publicDir: false
});
