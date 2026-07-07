/* ==========================================================================
   MBEA Integrated Psychiatric & Lifestyle Medicine Clinic
   Landing Page Logic
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
  attachNavToggle();
  attachSmoothScroll();
  attachScrollReveal();
  setFooterYear();
});

/* --------------------------------------------------------------------------
   Mobile nav toggle
   -------------------------------------------------------------------------- */

function attachNavToggle() {
  const toggle = document.getElementById('nav-toggle');
  const links = document.getElementById('nav-links');
  if (!toggle || !links) return;

  toggle.addEventListener('click', function () {
    const isOpen = links.classList.toggle('open');
    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  });

  links.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', function () {
      links.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    });
  });
}

/* --------------------------------------------------------------------------
   Smooth scroll for in-page anchor links
   -------------------------------------------------------------------------- */

function attachSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(function (link) {
    link.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href').slice(1);
      const target = document.getElementById(targetId);
      if (!target) return;
      e.preventDefault();
      const navHeight = document.getElementById('site-nav').offsetHeight;
      const top = target.getBoundingClientRect().top + window.scrollY - navHeight - 16;
      window.scrollTo({ top: top, behavior: 'smooth' });
    });
  });
}

/* --------------------------------------------------------------------------
   Scroll reveal for sections
   -------------------------------------------------------------------------- */

function attachScrollReveal() {
  const revealEls = document.querySelectorAll('.reveal');
  if (!revealEls.length) return;

  if (!('IntersectionObserver' in window)) {
    revealEls.forEach(function (el) { el.classList.add('is-visible'); });
    return;
  }

  const observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  revealEls.forEach(function (el) { observer.observe(el); });
}

/* --------------------------------------------------------------------------
   Footer year
   -------------------------------------------------------------------------- */

function setFooterYear() {
  const el = document.getElementById('footer-year');
  if (el) el.textContent = new Date().getFullYear();
}