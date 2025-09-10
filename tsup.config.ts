// Bundle config: outputs ESM and IIFE (global: TPBStoreModal) for CDN/WordPress
import { defineConfig } from 'tsup';

export default defineConfig({
  entry: ['src/index.ts'],
  format: ['esm', 'iife'],
  globalName: 'TPBStoreModal',
  splitting: false,
  sourcemap: true,
  // Keep TypeScript .d.ts files emitted by `tsc` in dist
  clean: false,
  minify: true,
});


