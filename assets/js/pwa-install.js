/**
 * Life Changers Ministry Portal - PWA Installation & Service Worker Engine
 */
(function () {
  'use strict';

  let deferredPrompt = null;
  const STORAGE_KEY_DISMISSED = 'lcm_pwa_dismissed_until';
  const COOLDOWN_DAYS = 3;

  // 1. Register Service Worker
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      const swUrl = document.querySelector('meta[name="sw-path"]')?.getAttribute('content') || '/sw.js';
      navigator.serviceWorker.register(swUrl)
        .then((reg) => {
          console.log('[PWA] Service Worker registered scope:', reg.scope);
        })
        .catch((err) => {
          console.warn('[PWA] Service Worker registration info:', err);
        });
    });
  }

  // 2. Check standalone display mode
  function isStandalone() {
    return (
      window.matchMedia('(display-mode: standalone)').matches ||
      window.navigator.standalone === true ||
      document.referrer.includes('android-app://')
    );
  }

  // 3. Helper: Check if dismissed recently
  function isPromptDismissed() {
    const dismissedUntil = localStorage.getItem(STORAGE_KEY_DISMISSED);
    if (!dismissedUntil) return false;
    return new Date().getTime() < parseInt(dismissedUntil, 10);
  }

  function setPromptDismissed() {
    const expiry = new Date().getTime() + (COOLDOWN_DAYS * 24 * 60 * 60 * 1000);
    localStorage.setItem(STORAGE_KEY_DISMISSED, expiry.toString());
  }

  // 4. Create and inject PWA Install Pop-up UI into the DOM
  function createInstallUI() {
    if (document.getElementById('pwaInstallBanner')) return;

    const iconUrl = document.querySelector('meta[name="pwa-icon"]')?.getAttribute('content') || '/assets/images/pwa/icon-192x192.png';
    const banner = document.createElement('div');
    banner.id = 'pwaInstallBanner';
    banner.className = 'pwa-install-banner';
    banner.innerHTML = `
      <div class="pwa-banner-content">
        <div class="pwa-banner-logo">
          <img src="${iconUrl}" alt="LCM App">
        </div>
        <div class="pwa-banner-text">
          <div class="pwa-banner-title">Install LCM Portal App</div>
          <div class="pwa-banner-desc">Fast 1-tap mobile access, full screen & offline support.</div>
        </div>
        <div class="pwa-banner-actions">
          <button id="pwaDismissBtn" class="pwa-btn pwa-btn-secondary" type="button">Not Now</button>
          <button id="pwaInstallActionBtn" class="pwa-btn pwa-btn-primary" type="button">
            <i class="bx bx-download"></i> Install App
          </button>
        </div>
        <button id="pwaCloseBtn" class="pwa-banner-close" aria-label="Close">&times;</button>
      </div>
    `;

    document.body.appendChild(banner);

    // Event handlers
    document.getElementById('pwaInstallActionBtn').addEventListener('click', triggerInstall);
    document.getElementById('pwaDismissBtn').addEventListener('click', dismissBanner);
    document.getElementById('pwaCloseBtn').addEventListener('click', dismissBanner);
  }

  function showBanner() {
    if (isStandalone() || isPromptDismissed()) return;
    createInstallUI();
    const banner = document.getElementById('pwaInstallBanner');
    if (banner) {
      setTimeout(() => {
        banner.classList.add('show');
      }, 1000); // 1.0s delay after load
    }
  }

  function hideBanner() {
    const banner = document.getElementById('pwaInstallBanner');
    if (banner) {
      banner.classList.remove('show');
      setTimeout(() => banner.remove(), 400);
    }
  }

  function dismissBanner() {
    setPromptDismissed();
    hideBanner();
  }

  // 5. Trigger Install Prompt (Native or Fallback Modal)
  function triggerInstall() {
    if (deferredPrompt) {
      deferredPrompt.prompt();
      deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
          console.log('[PWA] User accepted install prompt');
        }
        deferredPrompt = null;
        hideBanner();
      });
    } else {
      showDeviceInstructions();
    }
  }

  // Expose global trigger for any button
  window.triggerPwaInstall = triggerInstall;

  // 6. Device-Specific Installation Instructions Modal
  function showDeviceInstructions() {
    hideBanner();
    const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const isAndroid = /Android/.test(navigator.userAgent);
    const modalId = 'pwaInstallModal';
    if (document.getElementById(modalId)) return;

    const modal = document.createElement('div');
    modal.id = modalId;
    modal.className = 'pwa-modal-backdrop';
    modal.innerHTML = `
      <div class="pwa-modal-card">
        <div class="pwa-modal-header">
          <div class="d-flex align-items-center gap-2">
            <i class="bx bx-mobile-alt text-primary fs-3"></i>
            <h5 class="mb-0 fw-bold">Install LCM Portal App</h5>
          </div>
          <button class="pwa-modal-close" onclick="document.getElementById('${modalId}').remove()">&times;</button>
        </div>
        <div class="pwa-modal-body">
          ${isIos ? `
            <p class="text-muted small mb-3">To install LCM Portal on your iPhone / iPad:</p>
            <ol class="pwa-steps">
              <li>Tap the <strong>Share</strong> button <i class="bx bx-share-alt text-primary"></i> at the bottom of Safari.</li>
              <li>Scroll down and tap <strong>"Add to Home Screen"</strong> <i class="bx bx-plus-square text-success"></i>.</li>
              <li>Tap <strong>"Add"</strong> at the top right to complete.</li>
            </ol>
          ` : isAndroid ? `
            <p class="text-muted small mb-3">To install LCM Portal on your Android device:</p>
            <ol class="pwa-steps">
              <li>Tap your browser menu <i class="bx bx-dots-vertical-rounded"></i> at the top right.</li>
              <li>Tap <strong>"Install app"</strong> or <strong>"Add to Home screen"</strong> <i class="bx bx-download text-primary"></i>.</li>
              <li>Confirm when prompted to install.</li>
            </ol>
          ` : `
            <p class="text-muted small mb-3">To install LCM Portal on your Computer / Desktop:</p>
            <ol class="pwa-steps">
              <li>Look at the right side of your browser URL address bar for the <strong>Install icon</strong> <i class="bx bx-download text-primary"></i>.</li>
              <li>Click <strong>Install</strong> to add the portal to your desktop / apps.</li>
              <li>Alternatively, open your browser menu <i class="bx bx-dots-vertical-rounded"></i> ➔ <strong>"Install Life Changers Ministry Portal"</strong>.</li>
            </ol>
          `}
        </div>
        <div class="pwa-modal-footer">
          <button class="btn btn-primary w-100 rounded-pill" onclick="document.getElementById('${modalId}').remove()">Got it!</button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
  }

  // 7. Listen for beforeinstallprompt event
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    console.log('[PWA] Captured beforeinstallprompt event');
    document.querySelectorAll('.pwa-install-trigger').forEach((btn) => {
      btn.style.display = 'inline-flex';
    });
    showBanner();
  });

  // 8. Listen for successful install
  window.addEventListener('appinstalled', () => {
    console.log('[PWA] App installed successfully');
    deferredPrompt = null;
    hideBanner();
    document.querySelectorAll('.pwa-install-trigger').forEach((btn) => {
      btn.style.display = 'none';
    });
  });

  // 9. Auto-initialize on DOM ready
  document.addEventListener('DOMContentLoaded', () => {
    // Show sidebar trigger buttons
    document.querySelectorAll('.pwa-install-trigger').forEach((btn) => {
      btn.style.display = 'block';
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        triggerInstall();
      });
    });

    // Automatically trigger banner on all non-standalone browsers
    if (!isStandalone()) {
      showBanner();
    }
  });

})();
