

(function () {
  'use strict';

  const API_BASE = (function () {
    const loc = window.location.pathname;
    if (loc.includes('/sih/skill-intelligence-ui/')) {
      return '../api/';
    } else if (loc.includes('/sih/sih/')) {
      return 'api/';
    } else if (loc.includes('/sih/')) {
      return 'sih/api/';
    } else {
      return 'api/';
    }
  })();

  async function apiFetch(endpoint, opts = {}) {
    try {
      const headers = { 'Content-Type': 'application/json', ...(opts.headers || {}) };
      const localU = getLocalUser();
      if (localU && localU.id) {
        headers['X-User-Id'] = String(localU.id);
        if (localU.email) headers['X-User-Email'] = localU.email;
      }
      const res = await fetch(API_BASE + endpoint, {
        credentials: 'include',
        headers: headers,
        ...opts
      });
      const data = await res.json();
      return data;
    } catch (err) {
      console.warn('[API] fetch error:', err);
      return { status: 'error', message: err.message };
    }
  }

  function toast(title, msg, icon) {
    if (window.showToast) window.showToast(title, msg, icon);
  }

  function getLocalUser() {
    try {
      const stored = sessionStorage.getItem('si_auth_user') || localStorage.getItem('si_auth_user');
      if (stored) {
        const u = JSON.parse(stored);
        if (u && (u.email === 'alex.morgan@enterprise.ai' || u.full_name === 'Alex Morgan' || u.full_name === 'Alex Morgan (Lead)')) {
          sessionStorage.removeItem('si_auth_user');
          localStorage.removeItem('si_auth_user');
          sessionStorage.removeItem('si_user');
          return null;
        }
        return u;
      }
    } catch (_) {}
    return null;
  }

  function isUserAuthenticated() {
    const u = getLocalUser();
    return !!(u && (u.email || u.id));
  }

  function saveLocalUser(u) {
    if (!u) return;
    if (u.email === 'alex.morgan@enterprise.ai' || u.full_name === 'Alex Morgan' || u.full_name === 'Alex Morgan (Lead)') {
      return;
    }
    try {
      sessionStorage.setItem('si_auth_user', JSON.stringify(u));
      localStorage.setItem('si_auth_user', JSON.stringify(u));
      const db = JSON.parse(localStorage.getItem('si_platform_db_v2') || '{}');
      if (!db.user) db.user = {};
      Object.assign(db.user, u);
      localStorage.setItem('si_platform_db_v2', JSON.stringify(db));
    } catch (_) {}
  }

  const Auth = {
    isAuthenticated() {
      return isUserAuthenticated();
    },

    async me() {
      try {
        const res = await apiFetch('auth.php?action=me');
        if (res && res.status === 'success' && res.data) {
          saveLocalUser(res.data);
          return { status: 'success', authenticated: true, data: res.data };
        } else if (res && (res.status === 'unauthenticated' || res.authenticated === false)) {
          sessionStorage.removeItem('si_auth_user');
          localStorage.removeItem('si_auth_user');
          sessionStorage.removeItem('si_user');
          return { status: 'unauthenticated', authenticated: false, data: null };
        }
      } catch (_) {}

      const localU = getLocalUser();
      if (localU && localU.id) {
        return {
          status: 'success',
          authenticated: true,
          data: localU
        };
      }

      return {
        status: 'unauthenticated',
        authenticated: false,
        data: null
      };
    },

    async login(email, password) {
      const cleanEmail = (email || '').trim().toLowerCase();
      try {
        const res = await apiFetch('auth.php?action=login', {
          method: 'POST',
          body: JSON.stringify({ email: cleanEmail, password })
        });
        if (res && res.status === 'success') {
          if (res.data) saveLocalUser(res.data);
          return res;
        } else if (res && res.status === 'error') {
          return res;
        }
      } catch (_) {}

      // Fallback for offline environments using local registry
      let registeredUsers = [];
      try {
        registeredUsers = JSON.parse(localStorage.getItem('si_registered_users') || '[]');
      } catch (_) {}

      const found = registeredUsers.find(u => u.email.toLowerCase() === cleanEmail);
      if (found) {
        if (found.password && found.password !== password) {
          return { status: 'error', message: 'Incorrect password for this account.' };
        }
        const userObj = { ...found };
        delete userObj.password;
        saveLocalUser(userObj);
        return {
          status: 'success',
          message: 'Welcome back, ' + userObj.full_name + '!',
          redirect: 'dashboard.html',
          data: userObj
        };
      }

      return {
        status: 'error',
        message: 'Account not found for this email. Please register first.'
      };
    },

    async register(data) {
      const cleanEmail = (data.email || '').trim().toLowerCase();
      const cleanName = (data.full_name || '').trim();
      const password = data.password || '';
      const roleTitle = data.role_title || 'ML Engineer';

      try {
        const res = await apiFetch('auth.php?action=register', {
          method: 'POST',
          body: JSON.stringify({
            full_name: cleanName,
            email: cleanEmail,
            password: password,
            role_title: roleTitle
          })
        });

        // Store user in local registry for offline fallback
        const initials = cleanName
          .split(' ')
          .filter(Boolean)
          .map(n => n[0])
          .join('')
          .slice(0, 2)
          .toUpperCase() || 'U';

        const record = {
          id: (res && res.data && res.data.id) ? res.data.id : Date.now(),
          full_name: cleanName,
          email: cleanEmail,
          password: password,
          role_title: roleTitle,
          target_role: 'Lead AI Architect',
          department: 'Enterprise Cognitive Systems Division',
          location: 'Bengaluru, India',
          avatar_initials: initials,
          karma_xp: 100,
          rank_percentile: 'Unranked'
        };

        try {
          const registeredUsers = JSON.parse(localStorage.getItem('si_registered_users') || '[]');
          const idx = registeredUsers.findIndex(u => u.email === cleanEmail);
          if (idx >= 0) registeredUsers[idx] = record;
          else registeredUsers.push(record);
          localStorage.setItem('si_registered_users', JSON.stringify(registeredUsers));
        } catch (_) {}

        if (res && res.status === 'success') {
          return res;
        } else if (res && res.status === 'error') {
          return res;
        }
      } catch (_) {}

      // Offline fallback registration
      const initials = cleanName
        .split(' ')
        .filter(Boolean)
        .map(n => n[0])
        .join('')
        .slice(0, 2)
        .toUpperCase() || 'U';

      const newUser = {
        id: Date.now(),
        full_name: cleanName,
        email: cleanEmail,
        password: password,
        role_title: roleTitle,
        target_role: 'Lead AI Architect',
        department: 'Enterprise Cognitive Systems Division',
        location: 'Bengaluru, India',
        avatar_initials: initials,
        karma_xp: 100,
        rank_percentile: 'Unranked'
      };

      try {
        const registeredUsers = JSON.parse(localStorage.getItem('si_registered_users') || '[]');
        if (registeredUsers.some(u => u.email === cleanEmail)) {
          return { status: 'error', message: 'An account with this email already exists.' };
        }
        registeredUsers.push(newUser);
        localStorage.setItem('si_registered_users', JSON.stringify(registeredUsers));
      } catch (_) {}

      return {
        status: 'success',
        message: 'Account registered successfully! Please sign in with your password.',
        data: { id: newUser.id, full_name: newUser.full_name, email: newUser.email }
      };
    },

    async sendOTP(email, purpose = 'login') {
      try {
        const res = await apiFetch('auth.php?action=send_otp', {
          method: 'POST',
          body: JSON.stringify({ email, purpose })
        });
        if (res && res.status === 'success') return res;
      } catch (_) {}

      sessionStorage.setItem('si_active_email', email);
      return {
        status: 'success',
        message: 'Verification code generated for ' + email,
        dev_otp: '482910',
        mock: true,
        expires_in: 600
      };
    },

    async verifyOTP(email, otp) {
      try {
        const res = await apiFetch('auth.php?action=verify_otp', {
          method: 'POST',
          body: JSON.stringify({ email, otp })
        });
        if (res && res.status === 'success') {
          if (res.data) saveLocalUser(res.data);
          return res;
        }
      } catch (_) {}

      if (otp === '482910' || (otp && otp.length === 6)) {
        const em = (email || sessionStorage.getItem('si_active_email') || '').trim().toLowerCase();
        if (!em) {
          return { status: 'error', message: 'Email address missing for OTP verification.' };
        }
        let u = getLocalUser();
        if (!u) {
          const namePart = em.split('@')[0].replace(/[._-]/g, ' ');
          const cleanName = namePart.replace(/\b\w/g, l => l.toUpperCase()) || 'Engineer';
          const initials = cleanName.split(' ').filter(Boolean).map(n => n[0]).join('').slice(0, 2).toUpperCase() || 'E';
          u = {
            id: Date.now(),
            full_name: cleanName,
            email: em,
            role_title: 'ML Engineer',
            target_role: 'Lead AI Architect',
            department: 'Enterprise Cognitive Systems Division',
            location: 'Bengaluru, India',
            avatar_initials: initials,
            karma_xp: 150,
            rank_percentile: 'Top 10%'
          };
        }
        u.email = em;
        saveLocalUser(u);
        return {
          status: 'success',
          message: 'OTP verified successfully!',
          redirect: 'dashboard.html',
          data: u
        };
      }
      return { status: 'error', message: 'Invalid verification code.' };
    },

    async logout() {
      try { await apiFetch('auth.php?action=logout'); } catch (_) {}
      sessionStorage.clear();
      localStorage.removeItem('si_auth_user');
      localStorage.removeItem('si_platform_db_v2');
      sessionStorage.removeItem('si_user');
      window.location.href = 'login.html?logout=1';
    },

    async guard(redirectUrl = 'login.html') {
      const res = await this.me();
      if (res.status !== 'success') {
        window.location.href = redirectUrl;
        return null;
      }
      return res.data;
    }
  };

  const Leaderboard = {
    async getList(period = 'weekly') {
      return apiFetch('leaderboard.php?period=' + period);
    },
    async getMyRank() {
      return apiFetch('leaderboard.php?action=my_rank');
    }
  };

  const Career = {
    async getJobs(opts = {}) {
      const params = new URLSearchParams({ action: 'list', ...opts }).toString();
      return apiFetch('career.php?' + params);
    },
    async apply(jobId) {
      const res = await apiFetch('career.php?action=apply', {
        method: 'POST',
        body: JSON.stringify({ job_id: jobId })
      });
      if (res.status === 'success') toast('Application Sent!', 'Your verified profile was submitted.', '🚀');
      else toast('Error', res.message, '❌');
      return res;
    },
    async save(jobId) {
      const res = await apiFetch('career.php?action=save', {
        method: 'POST',
        body: JSON.stringify({ job_id: jobId })
      });
      if (res.status === 'success') toast(res.saved ? 'Job Saved' : 'Job Unsaved', res.message, res.saved ? '🔖' : '✓');
      return res;
    },
    async getStats() {
      return apiFetch('career.php?action=stats');
    },
    async getSaved() {
      return apiFetch('career.php?action=saved');
    },
    async getApplied() {
      return apiFetch('career.php?action=applied');
    }
  };

  const Community = {
    async getPosts(filter = 'hot', tag = '', page = 1, search = '') {
      let url = 'community.php?action=posts&filter=' + encodeURIComponent(filter) + '&page=' + page;
      if (tag) url += '&tag=' + encodeURIComponent(tag);
      if (search) url += '&search=' + encodeURIComponent(search);
      return apiFetch(url);
    },
    async createPost(data) {
      const res = await apiFetch('community.php?action=create', {
        method: 'POST',
        body: JSON.stringify(data)
      });
      if (res.status === 'success') toast('Post Published! +10 XP', 'Your discussion is now live.', '✅');
      else toast('Error', res.message || 'Could not publish post.', '❌');
      return res;
    },
    async deletePost(postId) {
      const res = await apiFetch('community.php?action=delete', {
        method: 'POST',
        body: JSON.stringify({ post_id: postId })
      });
      if (res.status === 'success') toast('Post Deleted', 'Your post was removed.', '🗑️');
      else toast('Error', res.message || 'Failed to delete post.', '❌');
      return res;
    },
    async react(postId, type = 'like') {
      return apiFetch('community.php?action=react', {
        method: 'POST',
        body: JSON.stringify({ post_id: postId, type })
      });
    },
    async getComments(postId) {
      return apiFetch('community.php?action=comments&post_id=' + postId);
    },
    async addComment(postId, body) {
      const res = await apiFetch('community.php?action=comment', {
        method: 'POST',
        body: JSON.stringify({ post_id: postId, body })
      });
      if (res.status === 'success') toast('Reply Posted! +5 XP', 'Your comment is now visible.', '💬');
      else toast('Error', res.message || 'Failed to post reply.', '❌');
      return res;
    },
    async deleteComment(commentId) {
      return apiFetch('community.php?action=delete_comment', {
        method: 'POST',
        body: JSON.stringify({ comment_id: commentId })
      });
    },
    async getTrending() {
      return apiFetch('community.php?action=trending');
    },
    async getMembers() {
      return apiFetch('community.php?action=members');
    },
    async getEvents() {
      return apiFetch('community.php?action=events');
    },
    async rsvpEvent(eventId) {
      const res = await apiFetch('community.php?action=rsvp', {
        method: 'POST',
        body: JSON.stringify({ event_id: eventId })
      });
      if (res.status === 'success') {
        if (res.registered) toast('RSVP Confirmed! +25 XP', res.message || 'You are registered for this event.', '🎉');
        else toast('RSVP Cancelled', res.message || 'Registration has been cancelled.', 'ℹ️');
      } else {
        toast('Error', res.message || 'Failed to update RSVP.', '❌');
      }
      return res;
    },
    async getStats() {
      return apiFetch('community.php?action=stats');
    }
  };

  const Certifications = {
    async getList(filter = 'all') {
      return apiFetch('certifications.php?action=list&filter=' + filter);
    },
    async getCatalog() {
      return apiFetch('certifications.php?action=catalog');
    },
    async startPath(certId) {
      const res = await apiFetch('certifications.php?action=start', {
        method: 'POST',
        body: JSON.stringify({ cert_id: certId })
      });
      if (res.status === 'success') toast('Path Started! +50 XP', res.message, '🚀');
      else toast('Error', res.message, '❌');
      return res;
    },
    async share(certId) {
      const res = await apiFetch('certifications.php?action=share', {
        method: 'POST',
        body: JSON.stringify({ cert_id: certId })
      });
      if (res.status === 'success') {
        try { await navigator.clipboard.writeText(res.share_url); } catch (_) {}
        toast('Link Copied!', 'Verification link for "' + res.cert_name + '" copied.', '🔗');
      }
      return res;
    },
    async getStats() {
      return apiFetch('certifications.php?action=stats');
    }
  };

  window.SIAuth          = Auth;
  window.SILeaderboard   = Leaderboard;
  window.SICareer        = Career;
  window.SICommunity     = Community;
  window.SICertifications = Certifications;

  async function syncNavbar() {
    const rawPath = window.location.pathname.toLowerCase();
    const page = rawPath.split('/').pop() || 'index.html';
    const isLogin = page === 'login.html' || page === 'login.php';
    const isLanding = page === 'landing.html' || page === 'landing.php' || page === 'index.html' || page === '' || page === 'index.php';

    // Landing / index is public; do not enforce login guard
    if (isLanding) {
      return;
    }

    // On login page, NEVER auto-redirect to dashboard! The user is here to login or register.
    if (isLogin) {
      return;
    }

    const res = await Auth.me();
    const isAuthed = res && res.status === 'success' && res.authenticated;

    if (!isAuthed) {
      window.location.href = 'landing.html';
      return;
    }

    const u = res.data;
    if (!u) return;

    document.querySelectorAll('.user-avatar').forEach(el => { el.textContent = u.avatar_initials || 'U'; });
    document.querySelectorAll('.nav-user-name').forEach(el => {
      const parts = (u.full_name || 'User').split(' ');
      el.textContent = parts[0] + (parts[1] ? ' ' + parts[1][0] + '.' : '');
    });

    document.querySelectorAll('.dropdown-header').forEach(el => {
      const strong = el.querySelector('strong');
      if (strong) strong.textContent = u.full_name || 'User';
      const emailDiv = el.querySelector('div[style*="color:var(--text-muted)"]') || el.querySelector('div[style*="text-muted"]');
      if (emailDiv) emailDiv.textContent = u.email || '';
    });

    document.querySelectorAll('[data-xp-badge]').forEach(el => { el.textContent = (u.karma_xp || 0).toLocaleString() + ' XP'; });

    sessionStorage.setItem('si_user', JSON.stringify(u));
  }

  document.addEventListener('DOMContentLoaded', () => { syncNavbar(); });

})();
