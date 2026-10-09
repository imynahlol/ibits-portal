/* iBITS Portal — small progressive enhancements. Pages work without this. */
(function () {
  'use strict';
  var RM = matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Mobile sidebar */
  document.querySelectorAll('[data-toggle="side"]').forEach(function (b) {
    b.addEventListener('click', function () { document.getElementById('side').classList.toggle('open'); });
  });
  document.addEventListener('click', function (e) {
    var s = document.getElementById('side');
    if (s && s.classList.contains('open') && !s.contains(e.target) && !e.target.closest('[data-toggle="side"]')) s.classList.remove('open');
  });

  /* Show / hide password */
  document.querySelectorAll('[data-pw-toggle]').forEach(function (b) {
    b.addEventListener('click', function () {
      var i = document.getElementById(b.dataset.pwToggle);
      i.type = i.type === 'password' ? 'text' : 'password';
      b.setAttribute('aria-label', i.type === 'password' ? 'Show password' : 'Hide password');
    });
  });

  /* Register: year levels depend on the program */
  var prog = document.getElementById('program'), yr = document.getElementById('year_level');
  if (prog && yr) {
    var levels = JSON.parse(prog.dataset.levels || '{}');
    var ords = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
    var fill = function () {
      var n = levels[prog.value] || 0, keep = yr.dataset.selected || yr.value;
      yr.innerHTML = '<option value="">' + (n ? 'Select year level' : 'Select program first') + '</option>';
      for (var i = 0; i < n; i++) {
        var o = document.createElement('option');
        o.value = o.textContent = ords[i];
        if (o.value === keep) o.selected = true;
        yr.appendChild(o);
      }
      yr.disabled = !n;
    };
    prog.addEventListener('change', function () { yr.dataset.selected = ''; fill(); });
    fill();
  }

  /* ---------- Population counters (scroll-triggered) ---------- */
  function animateCounters() {
    document.querySelectorAll('.pop-num[data-target]').forEach(function (el) {
      if (el.dataset.animated) return;
      el.dataset.animated = '1';

      var target   = parseInt(el.dataset.target, 10) || 0;
      var duration = 1500;
      var startTime = performance.now();

      (function tick(now) {
        var progress = Math.min((now - startTime) / duration, 1);
        var eased    = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.round(target * eased).toLocaleString();
        if (progress < 1) requestAnimationFrame(tick);
        else el.textContent = target.toLocaleString();
      })(startTime);
    });
  }

  function setupPopObserver() {
    var section = document.querySelector('.pop-section');
    if (!section) return;

    if (!('IntersectionObserver' in window)) { animateCounters(); return; }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { animateCounters(); io.disconnect(); }
      });
    }, { threshold: 0.3 });
    io.observe(section);

    /* Safety net: if already visible on load */
    setTimeout(function () {
      var r = section.getBoundingClientRect();
      if (r.top < innerHeight && r.bottom > 0) { animateCounters(); io.disconnect(); }
    }, 400);
  }
  setupPopObserver();

  /* Binary rain */
  var canvases = [].slice.call(document.querySelectorAll('canvas.rain'));
  if (canvases.length) {
    var F = 18;
    var draw = function () {
      canvases.forEach(function (c) {
        var r = c.getBoundingClientRect();
        if (r.bottom < 0 || r.top > innerHeight) return;
        var w = c.width = c.clientWidth, h = c.height = c.clientHeight;
        if (!w || !h) return;
        var g = c.getContext('2d'), light = c.dataset.rain === 'light';
        if (!c._h || c._w !== w) {
          c._w = w;
          c._h = Array.from({ length: Math.ceil(w / F) }, function () { return Math.random() * h / F; });
        }
        g.clearRect(0, 0, w, h);
        g.font = (F - 4) + 'px monospace';
        c._h.forEach(function (y, i) {
          for (var k = 0; k < 7; k++) {
            g.fillStyle = (k === 0 && i % 9 === 0) ? 'rgba(250,213,23,' + (light ? .5 : .5) + ')'
              : light ? 'rgba(6,33,92,' + (.12 - k * .015) + ')' : 'rgba(190,205,235,' + (.3 - k * .04) + ')';
            g.fillText(((i * 7 + k + Math.floor(y)) % 3) ? 1 : 0, i * F, (y - k) * F);
          }
          if (!RM) c._h[i] = (y * F > h + 120) ? -Math.random() * 20 : y + .35;
        });
      });
    };
    draw();
    if (!RM) setInterval(draw, 90);
  }
})();