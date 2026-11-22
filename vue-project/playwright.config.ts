import {defineConfig, devices} from '@playwright/test'

export default defineConfig({
  testDir: './e2e',
  fullyParallel: true,
  reporter: 'list',
  use: {
    headless: false,
    baseURL: process.env.BASE_URL ?? 'http://localhost:8080',
    trace: 'on-first-retry',
    ...devices['Desktop Chrome'],
  },
})
