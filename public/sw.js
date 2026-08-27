// Life Changers Ministry Portal - Service Worker
const CACHE_NAME = 'lcm-portal-v1.1';
const STATIC_ASSETS = [
  './manifest.json',
  './assets/images/pwa/icon-192x192.png',
  './assets/images/pwa/icon-512x512.png',
  './assets/images/pwa/apple-touch-icon.png',
  './assets/images/logo-sm.svg',
  './assets/css/bootstrap.min.css',
  './assets/css/icons.min.css',
  './assets/css/app.min.css',
  './assets/css/admin-custom.css',
  './assets/css/premium-theme.css',
  './assets/js/mobile-pwa.css'
];

// Install Event
self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS).catch((err) => {
        console.warn('SW precache error (ignored for dynamic assets):', err);
      });
    })
  );
});

// Activate Event
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch Strategy
self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  // Ignore non-GET or cross-origin requests that we shouldn't cache
  if (request.method !== 'GET' || !url.protocol.startsWith('http')) {
    return;
  }

  // 1. Static Assets (Images, Fonts, CSS, JS): Cache-First with Network fallback
  if (
    request.destination === 'style' ||
    request.destination === 'script' ||
    request.destination === 'image' ||
    request.destination === 'font' ||
    url.pathname.includes('/assets/')
  ) {
    event.respondWith(
      caches.match(request).then((cachedResponse) => {
        if (cachedResponse) {
          // Fetch updated version in background (stale-while-revalidate)
          fetch(request).then((networkResponse) => {
            if (networkResponse && networkResponse.status === 200) {
              caches.open(CACHE_NAME).then((cache) => cache.put(request, networkResponse));
            }
          }).catch(() => {});
          return cachedResponse;
        }
        return fetch(request).then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
          }
          return networkResponse;
        });
      })
    );
    return;
  }

  // 2. HTML Navigation / Dynamic pages: Network-First with Cache / Offline fallback
  if (request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      fetch(request)
        .then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
          }
          return networkResponse;
        })
        .catch(() => {
          return caches.match(request).then((cachedResponse) => {
            if (cachedResponse) return cachedResponse;
            // Generic offline page
            return new Response(
              `<!DOCTYPE html>
              <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Offline | LCM Portal</title>
                <style>
                  body { font-family: -apple-system, system-ui, sans-serif; background: #0f172a; color: #fff; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; padding: 20px; text-align: center; }
                  .card { background: #1e293b; padding: 32px; border-radius: 16px; border: 1px solid #334155; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
                  h2 { margin-top: 0; color: #818cf8; }
                  p { color: #94a3b8; font-size: 0.95rem; line-height: 1.5; }
                  button { background: #4f46e5; color: #fff; border: none; padding: 12px 24px; border-radius: 24px; font-weight: 600; cursor: pointer; margin-top: 16px; }
                </style>
              </head>
              <body>
                <div class="card">
                  <h2>📶 Offline Mode</h2>
                  <p>You are currently offline or have an unstable connection. Reconnect to access live ministry data.</p>
                  <button onclick="window.location.reload()">Retry Connection</button>
                </div>
              </body>
              </html>`,
              { headers: { 'Content-Type': 'text/html' } }
            );
          });
        })
    );
    return;
  }

  // Fallback for everything else
  event.respondWith(
    fetch(request).catch(() => caches.match(request))
  );
});
