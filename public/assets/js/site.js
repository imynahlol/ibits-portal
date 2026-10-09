(function () {
  'use strict';

  var RM = matchMedia('(prefers-reduced-motion: reduce)').matches;

  var themeButton = document.querySelector('[data-theme-toggle]');
  var savedTheme  = 'light';

  try {
    savedTheme = localStorage.getItem('ibits-theme') || 'light';
  } catch (e) {}

  function setTheme(theme) {
    theme = theme === 'dark' ? 'dark' : 'light';

    document.body.setAttribute('data-theme', theme);
    document.documentElement.classList.toggle('dark', theme === 'dark');

    if (themeButton) {
      var dark = theme === 'dark';
      themeButton.innerHTML =
        '<span aria-hidden="true">' + (dark ? '☀' : '☾') + '</span>' +
        '<span class="theme-label">' + (dark ? 'Light mode' : 'Dark mode') + '</span>';
      themeButton.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
      themeButton.title = dark ? 'Switch to light mode' : 'Switch to dark mode';
    }
  }

  setTheme(savedTheme);

  if (themeButton) {
    themeButton.addEventListener('click', function () {
      var next = document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';

      setTheme(next);

      try {
        localStorage.setItem('ibits-theme', next);
      } catch (e) {}
    });
  }

  var motionTargets = document.querySelectorAll(
    '.hero .inner > *, .sec h2, .navy-card, .mascot, .pop-card, .mv-card, .ev, .card, .tile, .stat > div'
  );

  motionTargets.forEach(function (el, index) {
    if (el.classList.contains('rain') || el.dataset.motionReady) return;

    el.dataset.motionReady = '1';
    el.classList.add('motion-in');
    el.style.setProperty('--motion-delay', Math.min(index % 6, 5) * 65 + 'ms');
  });

  if ('IntersectionObserver' in window && !RM) {
    var revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('motion-visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });

    motionTargets.forEach(function (el) {
      if (el.dataset.motionReady) revealObserver.observe(el);
    });
  } else {
    motionTargets.forEach(function (el) {
      el.classList.add('motion-visible');
    });
  }

  if (!RM && !matchMedia('(pointer: coarse)').matches) {
    document
      .querySelectorAll('.navy-card, .mv-card, .pop-card, .ev, .tile, .card3d')
      .forEach(function (el) {
        el.classList.add('tilt');

        el.addEventListener('mousemove', function (event) {
          var r = el.getBoundingClientRect();
          var x = (event.clientX - r.left) / r.width - 0.5;
          var y = (event.clientY - r.top) / r.height - 0.5;

          el.style.transform =
            'perspective(800px) rotateX(' + (-y * 4) + 'deg) ' +
            'rotateY(' + (x * 5) + 'deg) translateY(-2px)';
        });

        el.addEventListener('mouseleave', function () {
          el.style.transform = '';
        });
      });
  }

  /* Mobile sidebar */
  document.querySelectorAll('[data-toggle="side"]').forEach(function (b) {
    b.addEventListener('click', function () {
      document.getElementById('side').classList.toggle('open');
    });
  });

  document.addEventListener('click', function (e) {
    var s = document.getElementById('side');

    if (
      s &&
      s.classList.contains('open') &&
      !s.contains(e.target) &&
      !e.target.closest('[data-toggle="side"]')
    ) {
      s.classList.remove('open');
    }
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
  var prog = document.getElementById('program');
  var yr   = document.getElementById('year_level');

  if (prog && yr) {
    var levels = JSON.parse(prog.dataset.levels || '{}');
    var ords   = ['1st Year', '2nd Year', '3rd Year', '4th Year'];

    var fill = function () {
      var n    = levels[prog.value] || 0;
      var keep = yr.dataset.selected || yr.value;

      yr.innerHTML =
        '<option value="">' + (n ? 'Select year level' : 'Select program first') + '</option>';

      for (var i = 0; i < n; i++) {
        var o = document.createElement('option');
        o.value = o.textContent = ords[i];
        if (o.value === keep) o.selected = true;
        yr.appendChild(o);
      }

      yr.disabled = !n;
    };

    prog.addEventListener('change', function () {
      yr.dataset.selected = '';
      fill();
    });

    fill();
  }

  /* Population counters */
  var nums = document.querySelectorAll('[data-count]');

  function run(el) {
    var to = +el.dataset.count;

    if (RM || !to) {
      el.textContent = to;
      return;
    }

    var t0  = performance.now();
    var dur = 1100;

    (function step(t) {
      var p = Math.min(1, (t - t0) / dur);

      el.textContent = Math.round(to * (1 - Math.pow(1 - p, 3)));

      if (p < 1) requestAnimationFrame(step);
    })(t0);
  }

  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (e) {
        if (e.isIntersecting) {
          run(e.target);
          io.unobserve(e.target);
        }
      });
    }, { threshold: .4 });

    nums.forEach(function (n) {
      io.observe(n);
    });
  } else {
    nums.forEach(run);
  }

  /* Binary rain */
  var canvases = [].slice.call(document.querySelectorAll('canvas.rain'));

  if (canvases.length) {
    var F = 18;

    var draw = function () {
      canvases.forEach(function (c) {
        var r = c.getBoundingClientRect();

        if (r.bottom < 0 || r.top > innerHeight) return;

        var w = c.width  = c.clientWidth;
        var h = c.height = c.clientHeight;
        if (!w || !h) return;

        var g     = c.getContext('2d');
        var light = c.dataset.rain === 'light';

        if (!c._h || c._w !== w) {
          c._w = w;
          c._h = Array.from({ length: Math.ceil(w / F) }, function () {
            return Math.random() * h / F;
          });
        }

        g.clearRect(0, 0, w, h);
        g.font = (F - 4) + 'px monospace';

        c._h.forEach(function (y, i) {
          for (var k = 0; k < 7; k++) {
            g.fillStyle =
              (k === 0 && i % 9 === 0)
                ? 'rgba(250,213,23,' + (light ? .5 : .5) + ')'
                : light
                  ? 'rgba(6,33,92,' + (.12 - k * .015) + ')'
                  : 'rgba(190,205,235,' + (.3 - k * .04) + ')';

            g.fillText(
              ((i * 7 + k + Math.floor(y)) % 3) ? 1 : 0,
              i * F,
              (y - k) * F
            );
          }

          if (!RM) c._h[i] = (y * F > h + 120) ? -Math.random() * 20 : y + .35;
        });
      });
    };

    draw();

    if (!RM) setInterval(draw, 90);
  }
})();