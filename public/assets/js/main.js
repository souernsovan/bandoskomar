// ---------- Mobile drawer & dropdowns ----------
const burger = document.querySelector('.burger');
const setNav = open => {
  document.body.classList.toggle('nav-open', open);
  if (burger) burger.setAttribute('aria-expanded', open);
};
if (burger) burger.addEventListener('click', () => setNav(!document.body.classList.contains('nav-open')));
document.querySelectorAll('.drawer-close, .overlay').forEach(el => el.addEventListener('click', () => setNav(false)));
document.addEventListener('keydown', e => { if (e.key === 'Escape') setNav(false); });
addEventListener('resize', () => { if (innerWidth > 1180) setNav(false); });
document.querySelectorAll('.has-sub > button').forEach(btn => {
  btn.addEventListener('click', () => btn.parentElement.classList.toggle('show'));
});

// ---------- Hero slider ----------
const slides = document.querySelectorAll('.slide');
if (slides.length > 1) {
  const dotsBox = document.querySelector('.dots');
  let current = 0, timer;
  slides.forEach((_, i) => {
    const d = document.createElement('button');
    d.setAttribute('aria-label', 'Slide ' + (i + 1));
    d.onclick = () => { go(i); restart(); };
    dotsBox.appendChild(d);
  });
  const dots = dotsBox.querySelectorAll('button');
  dots[0].classList.add('active');
  function go(i) {
    slides[current].classList.remove('active'); dots[current].classList.remove('active');
    current = (i + slides.length) % slides.length;
    slides[current].classList.add('active'); dots[current].classList.add('active');
  }
  function restart() {
    clearInterval(timer);
    if (!matchMedia('(prefers-reduced-motion: reduce)').matches)
      timer = setInterval(() => go(current + 1), 5000);
  }
  document.querySelector('.arrow.prev').onclick = () => { go(current - 1); restart(); };
  document.querySelector('.arrow.next').onclick = () => { go(current + 1); restart(); };
  restart();
} else {
  document.querySelectorAll('.hero .arrow').forEach(a => a.remove());
}

// ---------- Lightbox ----------
const lb = document.querySelector('.lightbox:not(.video-modal)');
if (lb) {
  const lbImg = lb.querySelector('img');
  document.querySelectorAll('.gallery button').forEach(b => {
    b.addEventListener('click', () => {
      const img = b.querySelector('img');
      lbImg.src = b.dataset.full || img.src.replace(/-\d+x\d+(?=\.\w+$)/, '');
      lbImg.alt = img.alt;
      lb.classList.add('open');
    });
  });
  lb.addEventListener('click', e => { if (e.target !== lbImg) lb.classList.remove('open'); });
}

// ---------- Gallery filters ----------
document.querySelectorAll('.filters button[data-filter]').forEach(f => {
  f.addEventListener('click', () => {
    document.querySelectorAll('.filters button[data-filter]').forEach(x => x.classList.remove('active'));
    f.classList.add('active');
    const cat = f.dataset.filter;
    document.querySelectorAll('.gallery button').forEach(item => {
      item.hidden = !(cat === 'all' || item.dataset.cat === cat);
    });
  });
});

// ---------- Video modal ----------
const vm = document.querySelector('.video-modal');
if (vm) {
  const box = vm.querySelector('.modal-video');
  document.querySelectorAll('.video').forEach(v => {
    v.addEventListener('click', () => {
      const src = v.dataset.src;
      box.innerHTML = '';
      if (src) {
        const frame = document.createElement('iframe');
        frame.src = src + (src.includes('?') ? '&' : '?') + 'autoplay=1';
        frame.style.cssText = 'width:100%;height:100%;border:0';
        frame.allow = 'autoplay; encrypted-media; picture-in-picture';
        frame.allowFullscreen = true;
        box.appendChild(frame);
      } else {
        const wrap = document.createElement('div');
        const h = document.createElement('h3');
        h.style.color = '#fff';
        h.textContent = v.dataset.title || '';
        const p = document.createElement('p');
        p.textContent = v.dataset.empty || '';
        wrap.append(h, p);
        box.appendChild(wrap);
      }
      vm.classList.add('open');
    });
  });
  vm.addEventListener('click', e => {
    if (e.target === vm || e.target.classList.contains('close')) { vm.classList.remove('open'); box.innerHTML = ''; }
  });
}

document.addEventListener('keydown', e => {
  if (e.key !== 'Escape') return;
  document.querySelectorAll('.lightbox.open').forEach(x => x.classList.remove('open'));
  if (vm) vm.querySelector('.modal-video').innerHTML = '';
});

// ---------- Forms (sent to the server) ----------
document.querySelectorAll('form[data-ajax]').forEach(form => {
  const notice = document.getElementById(form.dataset.notice);
  const submit = form.querySelector('[type=submit]');
  const submitHtml = submit ? submit.innerHTML : '';
  const show = (msg, ok) => {
    notice.textContent = msg;
    notice.classList.toggle('error', !ok);
    notice.classList.add('show');
    notice.scrollIntoView({ behavior: 'smooth', block: 'center' });
  };

  form.addEventListener('submit', async e => {
    e.preventDefault();
    if (form.dataset.validate && window[form.dataset.validate] && !window[form.dataset.validate]()) return;
    if (submit) { submit.disabled = true; submit.textContent = form.dataset.sending || 'Sending...'; }
    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form),
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data.ok) throw new Error(data.message || form.dataset.error);
      show(data.message, true);
      form.reset();
      form.dispatchEvent(new Event('bk:sent'));
    } catch (err) {
      show(err.message || form.dataset.error, false);
    }
    if (submit) { submit.disabled = false; submit.innerHTML = submitHtml; }
  });
});

// ---------- Donate page ----------
const donate = document.getElementById('donate-form');
if (donate) {
  const out = document.getElementById('total-amount');
  const field = document.getElementById('amount-field');
  const custom = document.getElementById('custom-amount');
  const buttons = donate.querySelectorAll('.amounts button');
  const setAmount = v => { field.value = v; out.textContent = v ? '$' + v : '—'; };
  const pick = b => {
    buttons.forEach(x => x.classList.remove('active'));
    b.classList.add('active'); custom.value = ''; setAmount(b.dataset.amount);
  };
  buttons.forEach(b => b.addEventListener('click', () => pick(b)));
  custom.addEventListener('input', () => {
    buttons.forEach(x => x.classList.remove('active'));
    setAmount(custom.value > 0 ? Math.round(custom.value) : '');
  });
  window.bkDonateValid = () => {
    if (field.value) return true;
    const notice = document.getElementById('donate-notice');
    notice.textContent = donate.dataset.noAmount;
    notice.classList.add('show', 'error');
    return false;
  };
  const reset = () => {
    const def = donate.querySelector(`.amounts button[data-amount="${donate.dataset.default}"]`);
    if (def) pick(def); else { custom.value = donate.dataset.default; setAmount(donate.dataset.default); }
  };
  donate.addEventListener('bk:sent', reset);

  // Preselect amount from ?amount=
  const amt = new URLSearchParams(location.search).get('amount');
  if (amt) {
    const btn = donate.querySelector(`.amounts button[data-amount="${CSS.escape(amt)}"]`);
    if (btn) pick(btn);
    else if (+amt > 0) { custom.value = Math.round(+amt); custom.dispatchEvent(new Event('input')); }
  }
}

// ---------- Count-up stats ----------
const counters = document.querySelectorAll('[data-count]');
if (counters.length && 'IntersectionObserver' in window) {
  const io = new IntersectionObserver(entries => {
    entries.forEach(en => {
      if (!en.isIntersecting) return;
      const el = en.target, end = +el.dataset.count, suffix = el.dataset.suffix || '';
      const t0 = performance.now();
      const step = now => {
        const p = Math.min((now - t0) / 1400, 1);
        const v = Math.round(end * (1 - Math.pow(1 - p, 3)));
        el.textContent = (el.dataset.plain ? v : v.toLocaleString()) + suffix;
        if (p < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
      io.unobserve(el);
    });
  }, { threshold: .5 });
  counters.forEach(c => io.observe(c));
}

// ---------- Campaign progress bars ----------
document.querySelectorAll('.bar i').forEach(b => {
  const io = new IntersectionObserver(([en]) => {
    if (en.isIntersecting) { b.style.width = b.dataset.pct + '%'; io.disconnect(); }
  });
  io.observe(b);
});

// ---------- Floating donate after scrolling ----------
const fd = document.querySelector('.float-donate');
if (fd) {
  addEventListener('scroll', () => fd.classList.toggle('show', scrollY > 600), { passive: true });
}

// ---------- Donate popup: once per visit, after a delay or when leaving the page (desktop) ----------
const popup = document.querySelector('.popup');
if (popup) {
  let shown = false;
  try { shown = sessionStorage.getItem('bk-popup') === '1'; } catch (e) {}
  const open = () => {
    if (shown) return;
    shown = true;
    try { sessionStorage.setItem('bk-popup', '1'); } catch (e) {}
    popup.classList.add('open');
  };
  setTimeout(open, (+popup.dataset.delay || 20) * 1000);
  document.addEventListener('mouseout', e => { if (!e.relatedTarget && e.clientY < 10) open(); });
  popup.addEventListener('click', e => {
    if (e.target === popup || e.target.closest('.x') || e.target.closest('.later')) popup.classList.remove('open');
  });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') popup.classList.remove('open'); });
}

// ---------- Volunteers: filter + read more ----------
document.querySelectorAll('[data-vfilter]').forEach(f => f.addEventListener('click', () => {
  document.querySelectorAll('[data-vfilter]').forEach(x => x.classList.remove('active'));
  f.classList.add('active');
  document.querySelectorAll('.vol').forEach(v => v.hidden = !(f.dataset.vfilter === 'all' || v.dataset.role === f.dataset.vfilter));
}));
document.querySelectorAll('.read-more').forEach(b => b.addEventListener('click', () => {
  const card = b.closest('.vol');
  b.textContent = card.classList.toggle('open') ? b.dataset.less : b.dataset.more;
}));

// ---------- Form popups (job application) ----------
document.querySelectorAll('.modal').forEach(modal => {
  const open = trigger => {
    const position = trigger && trigger.dataset.position;
    const select = modal.querySelector('select[name=position]');
    if (position && select) select.value = position;
    modal.classList.add('open');
    document.body.classList.add('modal-lock');
    const first = modal.querySelector('input:not([type=hidden]):not([tabindex="-1"]), select, textarea');
    if (first) setTimeout(() => first.focus(), 50);
  };
  const close = () => {
    modal.classList.remove('open');
    document.body.classList.remove('modal-lock');
    if (location.hash === '#' + modal.id) history.replaceState(null, '', location.pathname + location.search);
  };
  document.querySelectorAll(`[data-modal-open="${modal.id}"]`).forEach(btn => btn.addEventListener('click', e => { e.preventDefault(); open(btn); }));
  modal.querySelectorAll('[data-modal-close]').forEach(btn => btn.addEventListener('click', e => { e.preventDefault(); close(); }));
  modal.addEventListener('click', e => { if (e.target === modal) close(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && modal.classList.contains('open')) close(); });
  if (modal.classList.contains('open') || location.hash === '#' + modal.id) open();
});
