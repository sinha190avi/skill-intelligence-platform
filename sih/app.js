

(function () {
  'use strict';

  const THEME_KEY = 'si_theme_preference';

  function initTheme() {

    const savedTheme = localStorage.getItem(THEME_KEY) || 'light';
    setTheme(savedTheme, false);
  }

  window.toggleTheme = function () {
    const isLight = document.documentElement.getAttribute('data-theme') === 'light';
    const nextTheme = isLight ? 'dark' : 'light';
    setTheme(nextTheme, true);
  };

  function setTheme(theme, showFeedback) {
    if (theme === 'light') {
      document.documentElement.setAttribute('data-theme', 'light');
      localStorage.setItem(THEME_KEY, 'light');
    } else {
      document.documentElement.removeAttribute('data-theme');
      localStorage.setItem(THEME_KEY, 'dark');
    }

    document.querySelectorAll('.theme-toggle-icon').forEach(el => {
      if (el.tagName && el.tagName.toLowerCase() === 'svg') {
        if (theme === 'light') {
          el.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
        } else {
          el.innerHTML = '<circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2" fill="none"/><line x1="12" y1="1" x2="12" y2="3" stroke="currentColor" stroke-width="2"/><line x1="12" y1="21" x2="12" y2="23" stroke="currentColor" stroke-width="2"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64" stroke="currentColor" stroke-width="2"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78" stroke="currentColor" stroke-width="2"/><line x1="1" y1="12" x2="3" y2="12" stroke="currentColor" stroke-width="2"/><line x1="21" y1="12" x2="23" y2="12" stroke="currentColor" stroke-width="2"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36" stroke="currentColor" stroke-width="2"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22" stroke="currentColor" stroke-width="2"/>';
        }
      } else {
        el.textContent = theme === 'light' ? '🌙' : '☀️';
      }
    });

    if (showFeedback && window.showToast) {
      window.showToast('Theme Changed', `Switched to ${theme} mode`, theme === 'light' ? '☀️' : '🌙');
    }
  }

  window.showToast = function (title, message, icon = '✨', type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
      <div class="toast-icon">${icon}</div>
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        <div class="toast-message">${message}</div>
      </div>
    `;

    container.appendChild(toast);

    setTimeout(() => {
      toast.classList.add('hide');
      setTimeout(() => toast.remove(), 200);
    }, 3500);
  };

  const SEARCH_ITEMS = [
    { title: 'Dashboard', desc: 'Command center, real-time KPI stats & daily goals', url: 'dashboard.html', category: 'Navigation', icon: '⌂' },
    { title: 'My Profile', desc: 'Skill matrix, role readiness & verified badges', url: 'profile.html', category: 'Navigation', icon: '👤' },
    { title: 'Skill Assessments', desc: 'Timed tests, interactive quizzes & certifications', url: 'assessments.html', category: 'Navigation', icon: '▣' },
    { title: 'Learning Path', desc: 'AI/ML Engineer 4-stage journey roadmap', url: 'learning-path.html', category: 'Navigation', icon: '◇' },
    { title: 'Course Catalog', desc: 'Browse AI, MLOps, Deep Learning & Python courses', url: 'courses.html', category: 'Navigation', icon: '▤' },
    { title: 'AI Recommendations', desc: 'Personalized course & skill gap recommendations', url: 'recommendations.html', category: 'Navigation', icon: '✦' },
    { title: 'My Progress', desc: 'Learning analytics, study time bars & streak badges', url: 'progress.html', category: 'Navigation', icon: '▥' },
    { title: 'AI Assistant (Copilot)', desc: 'Chat with SI AI tutor for instant mentorship', url: 'ai-assistant.html', category: 'Navigation', icon: '◉' },
    { title: 'Settings', desc: 'Preferences, security & appearance controls', url: 'settings.html', category: 'Navigation', icon: '⚙' },
    { title: 'System Design for Generative AI Applications', desc: 'Course • 14 hrs • 98% Match', url: 'courses.html', category: 'Course', icon: '🤖' },
    { title: 'Production Kubernetes for AI Workloads', desc: 'Course • 18 hrs • 94% Match', url: 'courses.html', category: 'Course', icon: '☸️' },
    { title: 'FlashAttention-2 & Custom Triton CUDA Kernels', desc: 'Course • 22 hrs • 95% Match', url: 'courses.html', category: 'Course', icon: '⚡' },
    { title: 'System Design for Large Language Models', desc: 'Assessment • 30 min • Advanced', url: 'assessments.html', category: 'Assessment', icon: '📐' },
    { title: 'Transformer Architectures & Attention', desc: 'Assessment • 25 min • Expert', url: 'assessments.html', category: 'Assessment', icon: '🧠' }
  ];

  function initSearchModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.id = 'global-search-modal';
    modal.innerHTML = `
      <div class="modal-content" style="max-width: 580px; padding: 20px;">
        <div style="display: flex; align-items: center; gap: 12px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 14px;">
          <span style="font-size: 18px; color: var(--brand-cyan);">🔍</span>
          <input type="text" id="global-search-input" placeholder="Search pages, courses, skills, assessments..." style="border: none; outline: none; background: transparent; width: 100%; font-size: 15px; color: var(--text-main); font-family: var(--font-main);" autofocus>
          <button class="modal-close-btn" onclick="window.closeSearchModal()">✕</button>
        </div>
        <div id="search-results-list" style="margin-top: 14px; max-height: 380px; overflow-y: auto; display: flex; flex-direction: column; gap: 6px;">
          
        </div>
        <div style="margin-top: 14px; padding-top: 10px; border-top: 1px solid var(--border-subtle); display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted);">
          <span>Navigation: <kbd style="background:var(--bg-elevated); padding:2px 5px; border-radius:4px;">↑</kbd> <kbd style="background:var(--bg-elevated); padding:2px 5px; border-radius:4px;">↓</kbd></span>
          <span>Close: <kbd style="background:var(--bg-elevated); padding:2px 5px; border-radius:4px;">ESC</kbd></span>
        </div>
      </div>
    `;
    document.body.appendChild(modal);

    const input = modal.querySelector('#global-search-input');
    input.addEventListener('input', (e) => renderSearchResults(e.target.value));

    document.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        window.openSearchModal();
      }
      if (e.key === 'Escape' && modal.classList.contains('active')) {
        window.closeSearchModal();
      }
    });

    modal.addEventListener('click', (e) => {
      if (e.target === modal) window.closeSearchModal();
    });
  }

  function renderSearchResults(query) {
    const list = document.getElementById('search-results-list');
    if (!list) return;

    const filtered = query.trim() === ''
      ? SEARCH_ITEMS.slice(0, 7)
      : SEARCH_ITEMS.filter(item =>
        item.title.toLowerCase().includes(query.toLowerCase()) ||
        item.desc.toLowerCase().includes(query.toLowerCase()) ||
        item.category.toLowerCase().includes(query.toLowerCase())
      );

    if (filtered.length === 0) {
      list.innerHTML = `<div style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 13px;">No results found for "${query}"</div>`;
      return;
    }

    list.innerHTML = filtered.map(item => `
      <a href="${item.url}" style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: var(--radius-md); background: var(--bg-elevated); text-decoration: none; color: inherit; transition: all 0.15s;" onmouseover="this.style.background='linear-gradient(135deg, rgba(99,102,241,0.3), rgba(6,182,212,0.3))'; this.style.borderColor='var(--brand-primary)';" onmouseout="this.style.background='var(--bg-elevated)';">
        <span style="font-size: 18px; width: 28px; text-align: center;">${item.icon}</span>
        <div style="flex: 1;">
          <div style="font-size: 13.5px; font-weight: 600; color: #fff;">${item.title}</div>
          <div style="font-size: 11.5px; color: var(--text-secondary);">${item.desc}</div>
        </div>
        <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 99px; background: rgba(99,102,241,0.2); color: #a5b4fc;">${item.category}</span>
      </a>
    `).join('');
  }

  window.openSearchModal = function () {
    const modal = document.getElementById('global-search-modal');
    if (!modal) return;
    modal.classList.add('active');
    const input = document.getElementById('global-search-input');
    if (input) {
      input.value = '';
      renderSearchResults('');
      setTimeout(() => input.focus(), 50);
    }
  };

  window.closeSearchModal = function () {
    const modal = document.getElementById('global-search-modal');
    if (modal) modal.classList.remove('active');
  };

  function initPopovers() {
    const notifBtn = document.getElementById('notif-btn');
    const notifMenu = document.getElementById('notif-dropdown');
    if (notifBtn && notifMenu) {
      notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        closeAllDropdowns(notifMenu);
        notifMenu.classList.toggle('show');
      });
    }

    const userBtn = document.getElementById('user-menu-btn');
    const userMenu = document.getElementById('user-dropdown');
    if (userBtn && userMenu) {
      userBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        closeAllDropdowns(userMenu);
        userMenu.classList.toggle('show');
      });
    }

    document.addEventListener('click', () => closeAllDropdowns());
  }

  function closeAllDropdowns(except = null) {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
      if (menu !== except) menu.classList.remove('show');
    });
  }

  window.clearNotifications = function (e) {
    if (e) e.stopPropagation();
    const list = document.getElementById('notification-list');
    const dot = document.querySelector('.notification-badge-dot');
    if (list) {
      list.innerHTML = `<div style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 12px;">No new notifications</div>`;
    }
    if (dot) dot.style.display = 'none';
    window.showToast('Notifications Cleared', 'All caught up!', '🔔');
  };

  function initMobileDrawer() {
    const toggleBtn = document.getElementById('mobile-drawer-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('mobile-open');
      });

      document.addEventListener('click', (e) => {
        if (window.innerWidth <= 1080 && sidebar.classList.contains('mobile-open')) {
          if (!sidebar.contains(e.target) && e.target !== toggleBtn && !toggleBtn.contains(e.target)) {
            sidebar.classList.remove('mobile-open');
          }
        }
      });
    }
  }

  function initMobileNav() {
    const topbar = document.querySelector('.topbar');
    if (!topbar) return;

    const navLinks = Array.from(document.querySelectorAll('.top-nav-links .top-nav-link'));
    if (navLinks.length === 0) return;

    const topbarActions = topbar.querySelector('.topbar-actions');
    let toggleBtn = topbar.querySelector('.mobile-nav-toggle');
    if (!toggleBtn) {
      toggleBtn = document.createElement('button');
      toggleBtn.className = 'mobile-nav-toggle';
      toggleBtn.setAttribute('aria-label', 'Open navigation');
      toggleBtn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>`;
      if (topbarActions) {
        topbar.insertBefore(toggleBtn, topbarActions);
      } else {
        topbar.appendChild(toggleBtn);
      }
    }

    let drawer = document.getElementById('mobile-nav-drawer');
    if (!drawer) {
      const currentPage = window.location.pathname.split('/').pop() || 'dashboard.html';
      const navItems = [
        { href: 'dashboard.html', icon: '⊞', label: 'Dashboard' },
        { href: 'profile.html', icon: '👤', label: 'Profile' },
        { href: 'assessments.html', icon: '▣', label: 'Assessments' },
        { href: 'learning-path.html', icon: '◇', label: 'Learning Path' },
        { href: 'courses.html', icon: '▤', label: 'Courses' },
        { href: 'recommendations.html', icon: '✦', label: 'Recommendations' },
        { href: 'progress.html', icon: '▥', label: 'Progress' },
        { href: 'ai-assistant.html', icon: '◉', label: 'AI Assistant' },
        { href: 'leaderboard.html', icon: '🏆', label: 'Leaderboard' },
        { href: 'career.html', icon: '💼', label: 'Career' },
        { href: 'community.html', icon: '🌐', label: 'Community' },
        { href: 'certifications.html', icon: '🎓', label: 'Certifications' },
        { href: 'settings.html', icon: '⚙', label: 'Settings' },
      ];

      drawer = document.createElement('div');
      drawer.id = 'mobile-nav-drawer';
      drawer.className = 'mobile-nav-drawer';
      drawer.innerHTML = `
        <div class="drawer-header">
          <a href="dashboard.html" style="display:flex;align-items:center;gap:8px;text-decoration:none;">
            <img src="commit_conquer_neon.png" alt="Commit & Conquer" style="height:26px;filter:drop-shadow(0 0 8px rgba(56,189,248,0.4))">
          </a>
          <button class="drawer-close" id="drawer-close-btn" aria-label="Close navigation">✕</button>
        </div>
        <nav style="flex:1;">
          ${navItems.map(item => `
            <a href="${item.href}" class="${item.href === currentPage ? 'active' : ''}">
              <span style="font-size:16px;width:24px;text-align:center;">${item.icon}</span>
              ${item.label}
            </a>
          `).join('')}
        </nav>
        <div style="margin-top:20px;padding-top:16px;border-top:1px solid rgba(139,92,246,0.2);">
          <a href="ai-assistant.html" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;display:flex;align-items:center;gap:8px;padding:13px 16px;border-radius:12px;font-weight:700;font-size:14px;margin-bottom:8px;">
            <span>✦</span> AI Copilot
          </a>
        </div>
      `;
      document.body.appendChild(drawer);
    }

    toggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      drawer.classList.add('open');
      document.body.style.overflow = 'hidden';
    });

    const closeBtn = drawer.querySelector('#drawer-close-btn');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        drawer.classList.remove('open');
        document.body.style.overflow = '';
      });
    }

    drawer.addEventListener('click', (e) => {
      if (e.target === drawer) {
        drawer.classList.remove('open');
        document.body.style.overflow = '';
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && drawer.classList.contains('open')) {
        drawer.classList.remove('open');
        document.body.style.overflow = '';
      }
    });
  }

  function initAuroraOrbs() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    let gridOverlay = document.getElementById('grid-bg-overlay');
    if (!gridOverlay) {
      gridOverlay = document.createElement('div');
      gridOverlay.id = 'grid-bg-overlay';
      gridOverlay.className = 'grid-bg-overlay';
      document.body.insertBefore(gridOverlay, document.body.firstChild);
    }

    let canvas = document.getElementById('bg-canvas');
    if (!canvas) {
      canvas = document.createElement('canvas');
      canvas.id = 'bg-canvas';
      canvas.setAttribute('aria-hidden', 'true');
      document.body.insertBefore(canvas, gridOverlay.nextSibling);
    }
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let s = 0, l = 0;
    let d = Math.min(window.devicePixelRatio || 1, 2);
    let c = [];
    const m = '37, 99, 235';
    const u = '10, 10, 10';
    const mouse = { x: -9999, y: -9999 };
    let rafId = 0;

    const density = 60;

    const resize = () => {
      s = window.innerWidth;
      l = window.innerHeight;
      d = Math.min(window.devicePixelRatio || 1, 2);
      canvas.width = s * d;
      canvas.height = l * d;
      canvas.style.width = `${s}px`;
      canvas.style.height = `${l}px`;
      ctx.setTransform(d, 0, 0, d, 0, 0);

      const count = Math.max(28, Math.floor((s * l) / (18000 - 120 * density)));
      c = Array.from({ length: count }, () => ({
        x: Math.random() * s,
        y: Math.random() * l,
        vx: (Math.random() - 0.5) * 0.25,
        vy: (Math.random() - 0.5) * 0.25,
        r: 1.6 * Math.random() + 1,
        glow: Math.random() > 0.82,
      }));
    };

    const render = () => {
      ctx.clearRect(0, 0, s, l);

      for (let e = 0; e < c.length; e++) {
        const t = c[e];
        t.x += t.vx;
        t.y += t.vy;

        if (mouse.x > 0 && mouse.y > 0) {
          const dx = t.x - mouse.x;
          const dy = t.y - mouse.y;
          const dist = Math.hypot(dx, dy);
          if (dist < 140 && dist > 0) {
            const repel = (140 - dist) / 140;
            t.x += (dx / dist) * repel * 0.8;
            t.y += (dy / dist) * repel * 0.8;
          }
        }

        if (t.x < 0 || t.x > s) t.vx *= -1;
        if (t.y < 0 || t.y > l) t.vy *= -1;
        t.x = Math.max(0, Math.min(s, t.x));
        t.y = Math.max(0, Math.min(l, t.y));
      }

      for (let e = 0; e < c.length; e++) {
        for (let t = e + 1; t < c.length; t++) {
          const a = c[e], b = c[t];
          const dist = Math.hypot(a.x - b.x, a.y - b.y);
          if (dist < 130) {
            const alpha = (1 - dist / 130) * 0.14;
            ctx.strokeStyle = `rgba(${u}, ${alpha})`;
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(a.x, a.y);
            ctx.lineTo(b.x, b.y);
            ctx.stroke();
          }
        }
      }

      if (mouse.x > 0 && mouse.y > 0) {
        for (let e of c) {
          const dist = Math.hypot(e.x - mouse.x, e.y - mouse.y);
          if (dist < 180) {
            const alpha = (1 - dist / 180) * 0.4;
            ctx.strokeStyle = `rgba(${m}, ${alpha})`;
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(e.x, e.y);
            ctx.lineTo(mouse.x, mouse.y);
            ctx.stroke();
          }
        }
      }

      for (let e of c) {
        if (e.glow) {
          ctx.beginPath();
          ctx.arc(e.x, e.y, e.r + 3, 0, 2 * Math.PI);
          ctx.fillStyle = `rgba(${m}, 0.12)`;
          ctx.fill();
        }

        ctx.beginPath();
        ctx.arc(e.x, e.y, e.r, 0, 2 * Math.PI);
        ctx.fillStyle = e.glow ? `rgba(${m}, 0.9)` : `rgba(${u}, 0.35)`;
        ctx.fill();
      }

      rafId = requestAnimationFrame(render);
    };

    resize();
    render();

    window.addEventListener('resize', resize);
    window.addEventListener('mousemove', e => {
      mouse.x = e.clientX;
      mouse.y = e.clientY;
    });
    window.addEventListener('mouseleave', () => {
      mouse.x = -9999;
      mouse.y = -9999;
    });

    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        cancelAnimationFrame(rafId);
      } else {
        rafId = requestAnimationFrame(render);
      }
    });
  }

  function highlightActiveNav() {
    const currentPage = window.location.pathname.split('/').pop() || 'dashboard.html';
    document.querySelectorAll('.top-nav-link, .sidebar-nav .nav-link').forEach(link => {
      const href = link.getAttribute('href');
      if (href === currentPage || (currentPage === '' && href === 'dashboard.html') || (currentPage === 'index.html' && href === 'dashboard.html')) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  }

  function initNavOverflow() {
    const nav = document.querySelector('.top-nav-links');
    if (!nav) return;

    const topbar = nav.closest('.topbar');
    if (!topbar) return;

    let moreWrapper = topbar.querySelector('.nav-more-wrapper');
    if (!moreWrapper) {
      moreWrapper = document.createElement('div');
      moreWrapper.className = 'nav-more-wrapper';

      moreWrapper.style.cssText = 'position:relative; flex-shrink:0; display:none; align-items:center;';
      nav.after(moreWrapper);
    }

    let moreBtn = moreWrapper.querySelector('.nav-more-btn');
    if (!moreBtn) {
      moreBtn = document.createElement('button');
      moreBtn.className = 'nav-more-btn visible';
      moreBtn.setAttribute('aria-haspopup', 'true');
      moreBtn.setAttribute('aria-expanded', 'false');
      moreBtn.innerHTML = 'More <span class="nav-more-chevron">▾</span>';
      moreWrapper.appendChild(moreBtn);
    }

    let moreDropdown = moreWrapper.querySelector('.nav-more-dropdown');
    if (!moreDropdown) {
      moreDropdown = document.createElement('div');
      moreDropdown.className = 'nav-more-dropdown';
      moreDropdown.style.display = 'none';
      moreWrapper.appendChild(moreDropdown);
    }

    moreBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = moreDropdown.style.display === 'block';

      document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
      if (isOpen) {
        moreDropdown.style.display = 'none';
        moreBtn.classList.remove('open');
        moreBtn.setAttribute('aria-expanded', 'false');
      } else {
        moreDropdown.style.display = 'block';
        moreBtn.classList.add('open');
        moreBtn.setAttribute('aria-expanded', 'true');
      }
    });

    document.addEventListener('click', (e) => {
      if (!moreWrapper.contains(e.target)) {
        moreDropdown.style.display = 'none';
        moreBtn.classList.remove('open');
        moreBtn.setAttribute('aria-expanded', 'false');
      }
    });

    function getNavLinks() {
      return Array.from(nav.querySelectorAll('.top-nav-link'));
    }

    function recalcOverflow() {
      const links = getNavLinks();

      links.forEach(l => l.classList.remove('nav-hidden'));
      moreDropdown.innerHTML = '';
      moreDropdown.style.display = 'none';
      moreBtn.classList.remove('open');
      moreBtn.setAttribute('aria-expanded', 'false');
      moreBtn.style.cssText = '';
      moreWrapper.style.display = 'none';

      void nav.offsetWidth;

      const navWidth = nav.offsetWidth;
      const MORE_BTN_RESERVE = 90;
      let usedWidth = 0;
      const overflowLinks = [];

      links.forEach(link => {
        const lw = link.offsetWidth + 2;
        if (usedWidth + lw + MORE_BTN_RESERVE > navWidth && overflowLinks.length === 0) {
          overflowLinks.push(link);
        } else if (overflowLinks.length > 0) {
          overflowLinks.push(link);
        } else {
          usedWidth += lw;
        }
      });

      if (overflowLinks.length > 0) {
        moreWrapper.style.display = 'flex';
        overflowLinks.forEach(link => {
          link.classList.add('nav-hidden');
          const clone = link.cloneNode(true);
          clone.classList.remove('nav-hidden');
          moreDropdown.appendChild(clone);
        });

        if (overflowLinks.some(l => l.classList.contains('active'))) {
          moreBtn.style.color = '#ffffff';
          moreBtn.style.background = 'linear-gradient(135deg, rgba(79,70,229,0.5), rgba(139,92,246,0.35))';
          moreBtn.style.border = '1px solid rgba(168,85,247,0.5)';
          moreBtn.style.boxShadow = '0 0 15px rgba(99,102,241,0.3)';
        }
      }

    }

    setTimeout(recalcOverflow, 60);

    if (window.ResizeObserver) {
      new ResizeObserver(() => recalcOverflow()).observe(topbar);
    }
    window.addEventListener('resize', recalcOverflow);
  }

  document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSearchModal();
    initPopovers();
    initMobileDrawer();
    initMobileNav();
    initAuroraOrbs();
    highlightActiveNav();
    initNavOverflow();

    const searchTrigger = document.querySelector('.search-trigger');
    if (searchTrigger) {
      searchTrigger.addEventListener('click', window.openSearchModal);
      const searchInput = searchTrigger.querySelector('input');
      if (searchInput) {
        searchInput.addEventListener('focus', (e) => {
          e.preventDefault();
          searchInput.blur();
          window.openSearchModal();
        });
      }
    }
  });

})();
