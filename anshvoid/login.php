<?php
require_once __DIR__ . '/auth.php';

// If already logged in, redirect to admin index
if (anshvoid_is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
$siteData = anshvoid_get_site_data();
$siteName = $siteData['site']['name'] ?? 'VoidAntara';
$logo = $siteData['site']['logo'] ?? 'public/assets/logo.png';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!anshvoid_verify_csrf($token)) {
        $error = 'Security check failed. Please refresh and try again.';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (anshvoid_attempt_login($username, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid credentials. Please verify your username and password.';
        }
    }
}

$csrfToken = anshvoid_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AnshVoid Control — <?= htmlspecialchars($siteName) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,400;1,600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root {
      --bg-paper: #E6DFD0;
      --bg-paper-light: #EDE7DA;
      --ink: #141210;
      --seal-red: #9A332A;
    }
    body {
      background-color: var(--bg-paper);
      color: var(--ink);
      font-family: 'JetBrains Mono', monospace;
      background-image:
        radial-gradient(ellipse 75% 55% at 16% 10%, rgba(252, 248, 238, 0.72) 0%, transparent 60%),
        radial-gradient(ellipse 65% 50% at 85% 24%, rgba(206, 195, 175, 0.34) 0%, transparent 62%),
        linear-gradient(to right, rgba(20, 18, 16, 0.025) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(20, 18, 16, 0.025) 1px, transparent 1px);
      background-size: 100% 100%, 100% 100%, 48px 48px, 48px 48px;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-md">
    
    <!-- Top Branding -->
    <div class="text-center mb-6">
      <div class="inline-flex items-center justify-center gap-3 mb-2">
        <?php if (!empty($logo) && file_exists(SITE_ROOT . '/' . $logo)): ?>
          <img src="../<?= htmlspecialchars($logo) ?>" alt="Logo" class="w-10 h-10 object-contain rounded" />
        <?php else: ?>
          <div class="w-9 h-9 border border-[#141210] flex items-center justify-center text-sm font-bold bg-[#EDE7DA] shadow-sm">
            VA
          </div>
        <?php endif; ?>
        <span class="font-cormorant text-3xl font-semibold tracking-tight text-[#141210]">AnshVoid</span>
      </div>
      <p class="text-xs uppercase tracking-widest text-[#141210]/60">Secure Site Control Panel</p>
    </div>

    <!-- Login Box -->
    <div class="bg-[#EDE7DA] border border-[#141210]/25 rounded-2xl p-7 shadow-[0_12px_36px_rgba(20,18,16,0.08)] relative overflow-hidden">
      <!-- Crosshair Corner Ticks -->
      <div class="absolute top-2 left-2 text-[9px] text-[#141210]/35 select-none">✦</div>
      <div class="absolute top-2 right-2 text-[9px] text-[#141210]/35 select-none">✦</div>
      <div class="absolute bottom-2 left-2 text-[9px] text-[#141210]/35 select-none">✦</div>
      <div class="absolute bottom-2 right-2 text-[9px] text-[#141210]/35 select-none">✦</div>

      <div class="border-b border-[#141210]/15 pb-4 mb-5">
        <h1 class="text-sm font-semibold uppercase tracking-wider text-[#141210] flex items-center justify-between">
          <span>Authentication</span>
          <span class="text-[10px] text-[#9A332A] bg-[#9A332A]/10 px-2 py-0.5 rounded border border-[#9A332A]/30">Restricted</span>
        </h1>
      </div>

      <?php if (!empty($error)): ?>
        <div class="mb-5 p-3 rounded-lg bg-red-100 border border-red-300 text-red-800 text-xs flex items-center gap-2">
          <span>⚠️</span>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>" />

        <div>
          <label for="username" class="block text-xs font-medium uppercase tracking-wider text-[#141210]/80 mb-1.5">
            Admin Identity
          </label>
          <input
            type="text"
            id="username"
            name="username"
            required
            autocomplete="username"
            placeholder="anshvoid"
            class="w-full px-3.5 py-2.5 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-sm text-[#141210] placeholder-[#141210]/35 focus:outline-none focus:border-[#141210] focus:ring-1 focus:ring-[#141210] transition-all"
          />
        </div>

        <div>
          <label for="password" class="block text-xs font-medium uppercase tracking-wider text-[#141210]/80 mb-1.5">
            Passkey
          </label>
          <input
            type="password"
            id="password"
            name="password"
            required
            autocomplete="current-password"
            placeholder="••••••••••••"
            class="w-full px-3.5 py-2.5 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-sm text-[#141210] placeholder-[#141210]/35 focus:outline-none focus:border-[#141210] focus:ring-1 focus:ring-[#141210] transition-all"
          />
        </div>

        <button
          type="submit"
          class="w-full mt-2 py-3 px-4 bg-[#141210] hover:bg-[#23201D] text-[#EDE7DA] font-medium text-xs uppercase tracking-widest rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2"
        >
          <span>Enter Dashboard</span>
          <span>→</span>
        </button>
      </form>

      <!-- Default Access Notice -->
      <div class="mt-6 pt-4 border-t border-[#141210]/15 text-[11px] text-[#141210]/65 space-y-1">
        <div class="font-medium text-[#141210]/80 flex items-center gap-1.5">
          <span>ℹ️</span>
          <span>Initial Access Setup:</span>
        </div>
        <p>User: <code class="bg-[#E6DFD0] px-1 py-0.5 rounded border border-[#141210]/20 font-mono text-[10px]">anshvoid</code> | Pass: <code class="bg-[#E6DFD0] px-1 py-0.5 rounded border border-[#141210]/20 font-mono text-[10px]">anshvoid123</code></p>
        <p class="text-[10px] text-[#9A332A]">You can update credentials anytime inside Settings.</p>
      </div>
    </div>

    <!-- Return to Site link -->
    <div class="text-center mt-5">
      <a href="../index.php" class="text-xs text-[#141210]/70 hover:text-[#141210] underline decoration-[#141210]/30 hover:decoration-[#141210] transition-colors">
        ← Return to <?= htmlspecialchars($siteName) ?> Live Site
      </a>
    </div>

  </div>
</body>
</html>
