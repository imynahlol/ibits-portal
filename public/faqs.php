<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

html_head('FAQs', 'faqs');
?>

<style>
  .faq-sec{padding-top:150px;padding-bottom:90px}
  .faq-wrap{max-width:900px;margin:0 auto;position:relative;z-index:2}
  .faq-badge{display:flex;width:max-content;margin:0 auto 22px;align-items:center;gap:10px;padding:9px 22px;border-radius:999px;border:1px solid rgba(255,255,255,.18);background:rgba(8,26,69,.55);font-size:13px;font-weight:700;letter-spacing:.2em;color:#fad517}
  .faq-badge i{width:9px;height:9px;border-radius:50%;background:#fad517;box-shadow:0 0 10px #fad517}
  .faq-title{text-align:center;font-weight:800;line-height:1.1;margin-bottom:14px;font-size:clamp(34px,6vw,64px);color:#fff}
  .faq-title span{color:#fad517}
  .faq-sub{text-align:center;color:#a9b8d6;max-width:560px;margin:0 auto 46px;font-size:15px;line-height:1.7}

  .faq{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:24px;margin-bottom:16px;overflow:hidden;
       box-shadow:0 24px 50px -18px rgba(0,0,0,.7),0 3px 0 rgba(250,213,23,.25);transition:transform .25s,border-color .25s,box-shadow .25s}
  .faq:hover{transform:translateY(-3px);border-color:rgba(250,213,23,.35)}
  .faq[open]{border-color:rgba(250,213,23,.5);box-shadow:0 24px 50px -18px rgba(0,0,0,.7),0 4px 0 rgba(250,213,23,.55)}
  .faq summary{list-style:none;display:flex;align-items:center;gap:16px;cursor:pointer;padding:22px 26px;font-size:20px;font-weight:600;color:#fff}
  .faq summary::-webkit-details-marker{display:none}
  .faq .num{flex:none;width:38px;height:38px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(250,213,23,.16);color:#fad517;font-size:14px;font-weight:700}
  .faq .q{flex:1}
  .faq .plus{flex:none;width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;transition:transform .3s,background .3s}
  .faq .plus svg{width:14px;height:14px}
  .faq[open] .plus{transform:rotate(45deg);background:#fad517;color:#06215c}
  .faq .a{padding:0 26px 24px 80px;color:#c3d0ec;font-size:16px;line-height:1.75;margin:0}

  .faq-cta{text-align:center;margin-top:56px}
  .faq-cta p{color:#a9b8d6;margin-bottom:18px}

  @media(max-width:860px){
    .faq summary{font-size:17px;padding:18px}
    .faq .a{padding:0 18px 20px}
  }
</style>

<main>
  <section class="sec sec-dark faq-sec" id="faqs">
    <canvas class="rain" data-rain="dark" aria-hidden="true"></canvas>
    <div class="container faq-wrap">

      <?= back_button('index.php', 'Back to home') ?>
      <span class="faq-badge"><i></i>HELP CENTER</span>
      <h1 class="faq-title">Frequently Asked <span>Questions</span></h1>
      <p class="faq-sub">Quick answers about joining iBITS, checking your fines, and getting your forms. Can't find it? Reach out to the Help Desk.</p>

      <?php
      $faqs = [
        ['Who can join iBITS?',             'Any registered BSIT or DIT student. Create an account using your student number.'],
        ['How do I check my fines?',        'Log in and open <b>My Fines</b>. You can only see your own.'],
        ['Where is the excuse letter?',     'Open <b>Forms Hub</b> after logging in. Fill the template, then save or print.'],
        ['I forgot my password. What now?', 'Go to <b>Member Login</b> and use the "Forgot password" link to reset it via your student number. Still locked out? Message the Help Desk.'],
        ['How do I contact the Help Desk?', 'Open the <b>Help Desk</b> page from the navbar or the button below. Submit your concern there and an officer will reply during office hours.'],
      ];
      foreach ($faqs as $i => [$q, $a]): ?>
        <details class="faq" id="faq-<?= $i + 1 ?>"<?= $i === 0 ? ' open' : '' ?>>
          <summary>
            <span class="num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
            <span class="q"><?= e($q) ?></span>
            <span class="plus"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></span>
          </summary>
          <p class="a"><?= $a ?></p>
        </details>
      <?php endforeach; ?>

      <div class="faq-cta">
        <p>Still have questions?</p>
        <a class="btn cta-register" href="<?= e(url('helpdesk.php')) ?>">Contact Help Desk</a>
        <a class="btn btn-ghost" href="<?= e(url('login.php')) ?>">Member Login</a>
      </div>

    </div>
  </section>
</main>

<script>
  (function () {
    var f = [].slice.call(document.querySelectorAll('details.faq'));
    f.forEach(function (d) {
      d.addEventListener('toggle', function () {
        if (d.open) f.forEach(function (o) { if (o !== d) o.open = false; });
      });
    });
  })();
</script>

<?php public_footer(); html_foot(); ?>