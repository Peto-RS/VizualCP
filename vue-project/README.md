# vue-project

This template should help get you started developing with Vue 3 in Vite.

## Recommended IDE Setup

[VS Code](https://code.visualstudio.com/) + [Vue (Official)](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur).

## Recommended Browser Setup

- Chromium-based browsers (Chrome, Edge, Brave, etc.):
  - [Vue.js devtools](https://chromewebstore.google.com/detail/vuejs-devtools/nhdogjmejiglipccpnnnanhbledajbpd) 
  - [Turn on Custom Object Formatter in Chrome DevTools](http://bit.ly/object-formatters)
- Firefox:
  - [Vue.js devtools](https://addons.mozilla.org/en-US/firefox/addon/vue-js-devtools/)
  - [Turn on Custom Object Formatter in Firefox DevTools](https://fxdx.dev/firefox-devtools-custom-object-formatters/)

## Customize configuration

See [Vite Configuration Reference](https://vite.dev/config/).

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Compile and Minify for Production

```sh
npm run build
```

### Playwright end-to-end tests

Install Playwright and its Chromium browser once:

```sh
npm install --save-dev @playwright/test
npx playwright install chromium
```

The app is served by PHP and the Vite dev server. Start both from the repository root / `vue-project` in separate terminals:

```sh
# Repository root
php -S 127.0.0.1:8000

# vue-project
npm run dev -- --host 127.0.0.1
```

Then, from `vue-project`, run:

```sh
npx playwright test
```

Tests live in `e2e/`. Set `BASE_URL` to test against a different app URL.
