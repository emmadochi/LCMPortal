/**
 * Life Changers Ministry Portal - PWA Installation & Service Worker Registration
 */
(function () {
  'use strict';

  let deferredPrompt = null;
  const STORAGE_KEY_DISMISSED = 'lcm_pwa_dismissed_until';
  const COOLDOWN_DAYS = 5;

  // 1. Register Service Worker
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      // Determine correct sw.js path
      const swUrl = document.querySelector('meta[name="sw-path"]')?.getAttribute('content') || '/sw.js';
      navigator.serviceWorker.register(swUrl)
        .then((reg) => {
          console.log('[PWA] Service Worker registered successfully:', reg.scope);
        })
        .catch((err) => {
          console.warn('[PWA] Service Worker registration failed:', err);
        });
    });
  }

  // 2. Helper: Check if dismissed recently
  function isPromptDismissed() {
    const dismissedUntil = localStorage.getItem(STORAGE_KEY_DISMISSED);
    if (!dismissedUntil) return false;
    return new Date().getTime() < parseInt(dismissedUntil, 10);
  }

  function setPromptDismissed() {
    const expiry = new Date().getTime() + (COOLDOWN_DAYS * 24 * 60 * 60 * 1000);
    localStorage.setItem(STORAGE_KEY_DISMISSED, expiry.toString());
  }

  // 3. Helper: Check if already in standalone PWA mode
  function isStandalone() {
    return (
      window.matchMedia('(display-mode: standalone)').matches ||
      window.navigator.standalone === true ||
      document.referrer.includes('android-app://')
    );
  }

  // 4. Create and inject PWA Install Pop-up UI into the DOM
  function createInstallUI() {
    if (document.getElementById('pwaInstallBanner')) return;

    const banner = document.createElement('div');
    banner.id = 'pwaInstallBanner';
    banner.className = 'pwa-install-banner animate-slide-up';
    banner.innerHTML = `
      <div class="pwa-banner-content">
        <div class="pwa-banner-logo">
          <img src="${document.querySelector('meta[name="pwa-icon"]')?.getAttribute('content') || '/assets/images/pwa/icon-192x192.png'}" alt="LCM App">
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
      }, 1200); // graceful 1.2s delay after page load
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

  // 5. Trigger Native Install Prompt
  function triggerInstall() {
    if (deferredPrompt) {
      deferredPrompt.prompt();
      deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
          console.log('[PWA] User accepted install prompt');
        } else {
          console.log('[PWA] User dismissed install prompt');
        }
        deferredPrompt = null;
        hideBanner();
      });
    } else {
      // iOS / Safari or unsupported fallback
      showIosInstructions();
    }
  }

  // 6. iOS Installation Modal Fallback
  function showIosInstructions() {
    hideBanner();
    const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const modalId = 'pwaIosModal';
    if (document.getElementById(modalId)) return;

    const modal = document.createElement('div');
    modal.id = modalId;
    modal.className = 'pwa-modal-backdrop';
    modal.innerHTML = `
      <div class="pwa-modal-card">
        <div class="pwa-modal-header">
          <div class="d-flex align-items-center gap-2">
            <i class="bx bx-mobile-alt text-primary fs-3"></i>
            <h5 class="mb-0 fw-bold">Install on your Device</h5>
          </div>
          <button class="pwa-modal-close" onclick="document.getElementById('${modalId}').remove()">&times;</button>
        </div>
        <div class="pwa-modal-body">
          ${isIos ? `
            <p class="text-muted small mb-3">To install LCM Portal on your iPhone or iPad:</p>
            <ol class="pwa-steps">
              <li>Tap the <strong>Share</strong> button <i class="bx bx-share-alt text-primary"></i> at the bottom of Safari.</li>
              <li>Scroll down and tap <strong>"Add to Home Screen"</strong> <i class="bx bx-plus-square text-success"></i>.</li>
              <li>Tap <strong>"Add"</strong> at the top right to complete installation.</li>
            </ol>
          ` : `
            <p class="text-muted small mb-3">To install LCM Portal as a desktop or mobile application:</p>
            <ol class="pwa-steps">
              <li>Tap your browser menu <i class="bx bx-dots-vertical-rounded"></i> or install icon <i class="bx bx-download text-primary"></i> in the URL address bar.</li>
              <li>Select <strong>"Install Life Changers Ministry Portal"</strong> or <strong>"Add to Phone"</strong>.</li>
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

  // 7. Listen for beforeinstallprompt
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    // Show install buttons / menu items across the UI
    document.querySelectorAll('.pwa-install-trigger').forEach((btn) => {
      btn.style.display = 'inline-flex';
    });
    showBanner();
  });

  // 8. Listen for successful install
  window.addEventListener('appinstalled', () => {
    console.log('[PWA] LCM Portal app was installed successfully!');
    deferredPrompt = null;
    hideBanner();
    document.querySelectorAll('.pwa-install-trigger').forEach((btn) => {
      btn.style.display = 'none';
    });
  });

  // 9. Manual install trigger binds (e.g. sidebar or user menu buttons)
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.pwa-install-trigger').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        triggerInstall();
      });
    });

    // Check if on iOS Safari in browser mode and not standalone
    const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    if (isIos && isSafari && !isStandalone() && !isPromptDismissed()) {
      showBanner();
    }
  });

})();
