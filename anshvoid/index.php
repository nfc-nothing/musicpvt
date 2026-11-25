<?php
require_once __DIR__ . '/auth.php';
anshvoid_require_auth();

$siteData = anshvoid_get_site_data();
$assets = anshvoid_list_assets();
$credentials = anshvoid_get_credentials();
$csrfToken = anshvoid_csrf_token();

$siteName = $siteData['site']['name'] ?? 'VoidAntara';
$siteLogo = $siteData['site']['logo'] ?? 'public/assets/logo.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AnshVoid Site Control — <?= htmlspecialchars($siteName) ?></title>
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
      --accent-slate: #3E5C7E;
    }
    body {
      background-color: var(--bg-paper);
      color: var(--ink);
      font-family: 'JetBrains Mono', monospace;
      background-image:
        radial-gradient(ellipse 75% 55% at 16% 10%, rgba(252, 248, 238, 0.72) 0%, transparent 60%),
        radial-gradient(ellipse 65% 50% at 85% 24%, rgba(206, 195, 175, 0.34) 0%, transparent 62%),
        linear-gradient(to right, rgba(20, 18, 16, 0.02) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(20, 18, 16, 0.02) 1px, transparent 1px);
      background-size: 100% 100%, 100% 100%, 48px 48px, 48px 48px;
    }
    .custom-scrollbar::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
      background: rgba(20, 18, 16, 0.05);
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: rgba(20, 18, 16, 0.25);
      border-radius: 3px;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <!-- TOP BAR -->
  <header class="border-b border-[#141210]/20 bg-[#EDE7DA]/90 backdrop-blur-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      
      <!-- Brand & Admin Badge -->
      <div class="flex items-center gap-3">
        <?php if (!empty($siteLogo) && file_exists(SITE_ROOT . '/' . $siteLogo)): ?>
          <img src="../<?= htmlspecialchars($siteLogo) ?>" alt="Logo" class="w-8 h-8 object-contain rounded" />
        <?php else: ?>
          <div class="w-8 h-8 border border-[#141210] flex items-center justify-center text-xs font-bold bg-[#E6DFD0]">
            AV
          </div>
        <?php endif; ?>
        <div>
          <span class="font-cormorant text-2xl font-semibold tracking-tight text-[#141210]">AnshVoid</span>
          <span class="text-[10px] ml-2 tracking-widest uppercase bg-[#141210] text-[#EDE7DA] px-2 py-0.5 rounded-full">
            CMS Suite
          </span>
        </div>
      </div>

      <!-- Action Nav -->
      <div class="flex items-center gap-3">
        <a
          href="../index.php"
          target="_blank"
          class="px-3.5 py-1.5 rounded-lg border border-[#141210]/30 hover:border-[#141210] text-xs font-medium text-[#141210] flex items-center gap-1.5 transition-all bg-[#EDE7DA] hover:bg-[#E6DFD0]"
        >
          <span>View Live Site</span>
          <span class="text-[10px]">↗</span>
        </a>

        <div class="hidden sm:flex items-center gap-2 border-l border-[#141210]/20 pl-3">
          <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
          <span class="text-xs text-[#141210]/80">Admin: <strong><?= htmlspecialchars($credentials['username']) ?></strong></span>
        </div>

        <a
          href="logout.php"
          class="px-3 py-1.5 rounded-lg border border-[#9A332A]/30 text-[#9A332A] hover:bg-[#9A332A]/10 text-xs font-medium transition-colors"
        >
          Logout
        </a>
      </div>

    </div>
  </header>

  <!-- MAIN WRAPPER -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- SIDEBAR TABS -->
    <aside class="lg:col-span-3 space-y-1">
      <div class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-3 shadow-sm mb-4">
        <div class="text-[11px] font-semibold uppercase tracking-wider text-[#141210]/60 px-3 py-2">
          Website Modules
        </div>
        <nav class="space-y-1" id="tabNav">
          <button data-tab="overview" class="tab-btn w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg text-left transition-all bg-[#141210] text-[#EDE7DA]">
            <span>📊</span>
            <span>Dashboard Overview</span>
          </button>
          <button data-tab="site" class="tab-btn w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg text-left transition-all text-[#141210]/80 hover:bg-[#141210]/10 hover:text-[#141210]">
            <span>🏷️</span>
            <span>Site Identity & Logo</span>
          </button>
          <button data-tab="hero" class="tab-btn w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg text-left transition-all text-[#141210]/80 hover:bg-[#141210]/10 hover:text-[#141210]">
            <span>⚡</span>
            <span>Hero Section Content</span>
          </button>
          <button data-tab="assets" class="tab-btn w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg text-left transition-all text-[#141210]/80 hover:bg-[#141210]/10 hover:text-[#141210]">
            <span>📁</span>
            <span>Asset Manager (Media)</span>
          </button>
          <button data-tab="tracks" class="tab-btn w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg text-left transition-all text-[#141210]/80 hover:bg-[#141210]/10 hover:text-[#141210]">
            <span>🎵</span>
            <span>Trending Tracks</span>
          </button>
          <button data-tab="templates" class="tab-btn w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg text-left transition-all text-[#141210]/80 hover:bg-[#141210]/10 hover:text-[#141210]">
            <span>🎨</span>
            <span>Visual Templates</span>
          </button>
          <button data-tab="cta" class="tab-btn w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg text-left transition-all text-[#141210]/80 hover:bg-[#141210]/10 hover:text-[#141210]">
            <span>📣</span>
            <span>CTA & Studio Section</span>
          </button>
          <button data-tab="footer" class="tab-btn w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg text-left transition-all text-[#141210]/80 hover:bg-[#141210]/10 hover:text-[#141210]">
            <span>📜</span>
            <span>Footer & Social Links</span>
          </button>
          <button data-tab="rawjson" class="tab-btn w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg text-left transition-all text-[#141210]/80 hover:bg-[#141210]/10 hover:text-[#141210]">
            <span>🛠️</span>
            <span>Raw JSON & Backup</span>
          </button>
          <button data-tab="security" class="tab-btn w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg text-left transition-all text-[#141210]/80 hover:bg-[#141210]/10 hover:text-[#141210]">
            <span>🔒</span>
            <span>Security & Passkey</span>
          </button>
        </nav>
      </div>

      <!-- Quick Tips Card -->
      <div class="bg-[#EDE7DA]/70 border border-[#141210]/15 rounded-2xl p-4 text-[11px] text-[#141210]/70 space-y-2">
        <div class="font-semibold uppercase tracking-wider text-[#141210] flex items-center gap-1.5">
          <span>✦</span>
          <span>Instant Live Updates</span>
        </div>
        <p>Every change saved here updates the live site instantly without rebuilding code or restarting servers.</p>
      </div>
    </aside>

    <!-- CONTENT PANELS CONTAINER -->
    <main class="lg:col-span-9 space-y-6">

      <!-- ================= 1. DASHBOARD OVERVIEW TAB ================= -->
      <section id="tab-overview" class="tab-content block space-y-6">
        
        <div class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-6 shadow-sm">
          <h1 class="font-cormorant text-3xl font-semibold text-[#141210] mb-1">
            Site Control Center
          </h1>
          <p class="text-xs text-[#141210]/70">
            Control branding, typography, music tracks, visual style templates, and assets for <strong><?= htmlspecialchars($siteName) ?></strong>.
          </p>

          <!-- Quick Stats Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
            <div class="bg-[#E6DFD0] border border-[#141210]/15 rounded-xl p-4">
              <span class="text-[10px] uppercase font-semibold text-[#141210]/60 block">Total Tracks</span>
              <span class="font-cormorant text-3xl font-bold text-[#141210]" id="statTracksCount"><?= count($siteData['tracks'] ?? []) ?></span>
            </div>
            <div class="bg-[#E6DFD0] border border-[#141210]/15 rounded-xl p-4">
              <span class="text-[10px] uppercase font-semibold text-[#141210]/60 block">Templates</span>
              <span class="font-cormorant text-3xl font-bold text-[#141210]" id="statTemplatesCount"><?= count($siteData['templates'] ?? []) ?></span>
            </div>
            <div class="bg-[#E6DFD0] border border-[#141210]/15 rounded-xl p-4">
              <span class="text-[10px] uppercase font-semibold text-[#141210]/60 block">Asset Files</span>
              <span class="font-cormorant text-3xl font-bold text-[#141210]" id="statAssetsCount"><?= count($assets) ?></span>
            </div>
            <div class="bg-[#E6DFD0] border border-[#141210]/15 rounded-xl p-4">
              <span class="text-[10px] uppercase font-semibold text-[#141210]/60 block">System Mode</span>
              <span class="font-cormorant text-2xl font-bold text-emerald-800">Active</span>
            </div>
          </div>
        </div>

        <!-- Quick Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-5 hover:border-[#141210] transition-colors cursor-pointer" onclick="switchTab('site')">
            <div class="flex items-center gap-3 mb-2">
              <span class="text-2xl">🏷️</span>
              <h2 class="font-semibold text-sm text-[#141210]">Update Site Name & Logo</h2>
            </div>
            <p class="text-xs text-[#141210]/70">Change website title, browser tab name, meta description, and upload a custom logo.</p>
          </div>

          <div class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-5 hover:border-[#141210] transition-colors cursor-pointer" onclick="switchTab('assets')">
            <div class="flex items-center gap-3 mb-2">
              <span class="text-2xl">📁</span>
              <h2 class="font-semibold text-sm text-[#141210]">Manage Media Assets</h2>
            </div>
            <p class="text-xs text-[#141210]/70">Upload new PNG, JPG, or WebP images and view currently stored assets in /public/assets.</p>
          </div>

          <div class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-5 hover:border-[#141210] transition-colors cursor-pointer" onclick="switchTab('tracks')">
            <div class="flex items-center gap-3 mb-2">
              <span class="text-2xl">🎵</span>
              <h2 class="font-semibold text-sm text-[#141210]">Edit Music Tracks</h2>
            </div>
            <p class="text-xs text-[#141210]/70">Add songs, edit lyric samples, update BPM, and link cover artwork for the trending section.</p>
          </div>

          <div class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-5 hover:border-[#141210] transition-colors cursor-pointer" onclick="switchTab('templates')">
            <div class="flex items-center gap-3 mb-2">
              <span class="text-2xl">🎨</span>
              <h2 class="font-semibold text-sm text-[#141210]">Configure Visual Styles</h2>
            </div>
            <p class="text-xs text-[#141210]/70">Tune aesthetic templates (Noir Comic, Sumi Ink, Astral, Botanical) and studio defaults.</p>
          </div>
        </div>

      </section>

      <!-- ================= 2. SITE IDENTITY & LOGO TAB ================= -->
      <section id="tab-site" class="tab-content hidden space-y-6">
        <form id="form-site" class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-6 shadow-sm space-y-5">
          <div class="border-b border-[#141210]/15 pb-4">
            <h2 class="font-cormorant text-2xl font-semibold text-[#141210]">Site Identity & Branding</h2>
            <p class="text-xs text-[#141210]/65">Configure global website naming, search engine meta details, and branding logo.</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Site Name</label>
              <input
                type="text"
                id="site_name"
                class="w-full px-3.5 py-2.5 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210] focus:ring-1 focus:ring-[#141210]"
                value="<?= htmlspecialchars($siteData['site']['name'] ?? '') ?>"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Browser Tab Title</label>
              <input
                type="text"
                id="site_title"
                class="w-full px-3.5 py-2.5 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210] focus:ring-1 focus:ring-[#141210]"
                value="<?= htmlspecialchars($siteData['site']['title'] ?? '') ?>"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Meta Description (SEO)</label>
            <textarea
              id="site_description"
              rows="3"
              class="w-full px-3.5 py-2.5 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210] focus:ring-1 focus:ring-[#141210]"
            ><?= htmlspecialchars($siteData['site']['description'] ?? '') ?></textarea>
          </div>

          <!-- Logo Management -->
          <div class="border-t border-[#141210]/15 pt-5 space-y-4">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-[#141210]">Navbar Logo</h3>
            
            <div class="flex items-center gap-6">
              <div class="w-20 h-20 bg-[#E6DFD0] border border-[#141210]/30 rounded-xl p-2 flex items-center justify-center overflow-hidden shadow-inner">
                <img
                  id="site_logo_preview"
                  src="../<?= htmlspecialchars($siteLogo) ?>"
                  alt="Site Logo Preview"
                  class="max-w-full max-h-full object-contain"
                  onerror="this.src='../public/assets/logo.png';"
                />
              </div>

              <div class="space-y-2 flex-1">
                <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210]/80">Select Logo Asset</label>
                <select
                  id="site_logo_select"
                  class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                  onchange="updateLogoPreview(this.value)"
                >
                  <?php foreach ($assets as $asset): ?>
                    <option value="<?= htmlspecialchars($asset['path']) ?>" <?= ($siteLogo === $asset['path']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($asset['name']) ?> (<?= $asset['size'] ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
                <p class="text-[11px] text-[#141210]/60">Or upload a new logo in the Asset Manager tab, then select it here.</p>
              </div>
            </div>
          </div>

          <!-- Japanese Hanko Seal -->
          <div class="border-t border-[#141210]/15 pt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Hanko Seal Characters (Top / Bottom)</label>
              <div class="flex items-center gap-3">
                <input
                  type="text"
                  id="hanko_char1"
                  maxlength="2"
                  class="w-16 text-center py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-sm font-bold text-[#9A332A]"
                  value="<?= htmlspecialchars($siteData['site']['hankoSeal'][0] ?? '音') ?>"
                />
                <input
                  type="text"
                  id="hanko_char2"
                  maxlength="2"
                  class="w-16 text-center py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-sm font-bold text-[#9A332A]"
                  value="<?= htmlspecialchars($siteData['site']['hankoSeal'][1] ?? '響') ?>"
                />
                <span class="text-[11px] text-[#141210]/60">Shown in the red archival stamp on the hero section.</span>
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Hanko Seal Tooltip</label>
              <input
                type="text"
                id="hanko_title"
                class="w-full px-3.5 py-2.5 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                value="<?= htmlspecialchars($siteData['site']['hankoTitle'] ?? 'VoidAntara Archival Seal') ?>"
              />
            </div>
          </div>

          <div class="pt-4 flex justify-end">
            <button
              type="button"
              onclick="saveSiteIdentity()"
              class="px-6 py-2.5 bg-[#141210] hover:bg-[#23201D] text-[#EDE7DA] font-medium text-xs uppercase tracking-widest rounded-lg shadow-sm transition-all"
            >
              Save Site Identity
            </button>
          </div>
        </form>
      </section>

      <!-- ================= 3. HERO SECTION CONTENT TAB ================= -->
      <section id="tab-hero" class="tab-content hidden space-y-6">
        <form id="form-hero" class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-6 shadow-sm space-y-5">
          <div class="border-b border-[#141210]/15 pb-4">
            <h2 class="font-cormorant text-2xl font-semibold text-[#141210]">Hero Section Content & Layout</h2>
            <p class="text-xs text-[#141210]/65">Customize the high-impact landing section, headlines, buttons, and visual layer mappings.</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Top Tagline</label>
              <input
                type="text"
                id="hero_tagline"
                class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                value="<?= htmlspecialchars($siteData['hero']['tagline'] ?? 'LYRICS • VISUALS • VIBES') ?>"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Wordmark Part 1 (Pixel Scramble)</label>
              <input
                type="text"
                id="hero_wordmark_void"
                class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                value="<?= htmlspecialchars($siteData['hero']['wordmarkVoid'] ?? 'Void') ?>"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Wordmark Part 2 (Serif)</label>
              <input
                type="text"
                id="hero_wordmark_luma"
                class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                value="<?= htmlspecialchars($siteData['hero']['wordmarkLuma'] ?? 'Antara') ?>"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Hero Pitch Paragraph</label>
              <textarea
                id="hero_pitch"
                rows="3"
                class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
              ><?= htmlspecialchars($siteData['hero']['pitch'] ?? '') ?></textarea>
            </div>

            <div class="space-y-4">
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Primary CTA Button Text</label>
                <input
                  type="text"
                  id="hero_button_text"
                  class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                  value="<?= htmlspecialchars($siteData['hero']['buttonText'] ?? 'Explore Now') ?>"
                />
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Scroll Indicator Text</label>
                  <input
                    type="text"
                    id="hero_scroll_text"
                    class="w-full px-3 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                    value="<?= htmlspecialchars($siteData['hero']['scrollText'] ?? 'Scroll Down') ?>"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Vertical Japanese Text</label>
                  <input
                    type="text"
                    id="hero_jp_text"
                    class="w-full px-3 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                    value="<?= htmlspecialchars($siteData['hero']['jpText'] ?? '音楽は、永遠に') ?>"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Hero Visual Asset Slots -->
          <div class="border-t border-[#141210]/15 pt-5 space-y-4">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-[#141210]">Hero Visual Collage Elements</h3>
            <p class="text-[11px] text-[#141210]/65">Assign which media asset is placed in each collage position.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              <?php
              $heroSlots = [
                'heroStatue' => 'Marble Statue Bust',
                'heroVinyl' => 'Spinning Vinyl Record',
                'heroSkyRect' => 'Arched Sky Backdrop',
                'bird' => 'Soaring Dove Cutout',
                'flowerStamp' => 'Botanical Postage Stamp',
                'flowersHeroMain' => 'Cherry Blossoms (Main)',
                'musicCord' => 'Sketched Treble Clef',
                'musicStar' => 'Spinning Five-Note Star'
              ];
              $heroAssets = $siteData['hero']['assets'] ?? [];
              foreach ($heroSlots as $slotKey => $slotLabel):
                $currentVal = $heroAssets[$slotKey] ?? ($siteData['assets'][$slotKey] ?? '');
              ?>
                <div class="bg-[#E6DFD0] border border-[#141210]/15 rounded-xl p-3 space-y-2">
                  <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-[#141210]"><?= htmlspecialchars($slotLabel) ?></span>
                    <span class="text-[9px] text-[#141210]/50 font-mono"><?= $slotKey ?></span>
                  </div>
                  <select
                    id="hero_slot_<?= $slotKey ?>"
                    class="w-full px-2.5 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-[11px] font-medium text-[#141210]"
                  >
                    <?php foreach ($assets as $asset): ?>
                      <option value="<?= htmlspecialchars($asset['path']) ?>" <?= ($currentVal === $asset['path']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($asset['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="pt-4 flex justify-end">
            <button
              type="button"
              onclick="saveHeroSection()"
              class="px-6 py-2.5 bg-[#141210] hover:bg-[#23201D] text-[#EDE7DA] font-medium text-xs uppercase tracking-widest rounded-lg shadow-sm transition-all"
            >
              Save Hero Section
            </button>
          </div>
        </form>
      </section>

      <!-- ================= 4. ASSET MANAGER (MEDIA) TAB ================= -->
      <section id="tab-assets" class="tab-content hidden space-y-6">
        
        <!-- Upload Card -->
        <div class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-6 shadow-sm space-y-4">
          <div class="flex items-center justify-between border-b border-[#141210]/15 pb-4">
            <div>
              <h2 class="font-cormorant text-2xl font-semibold text-[#141210]">Upload New Asset</h2>
              <p class="text-xs text-[#141210]/65">Upload PNG, JPG, WebP, SVG, or GIF files directly to /public/assets/.</p>
            </div>
            <button
              type="button"
              onclick="refreshAssetsList()"
              class="px-3 py-1.5 text-xs border border-[#141210]/25 rounded-lg hover:bg-[#141210]/10 transition-colors"
            >
              🔄 Refresh List
            </button>
          </div>

          <form id="form-upload-asset" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
            <div class="sm:col-span-6">
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Select File</label>
              <input
                type="file"
                id="asset_file_input"
                accept=".png,.jpg,.jpeg,.webp,.svg,.gif"
                required
                class="w-full text-xs file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border file:border-[#141210]/20 file:bg-[#141210] file:text-[#EDE7DA] file:text-xs file:font-mono file:cursor-pointer hover:file:bg-[#23201D] bg-[#E6DFD0]/60 p-1.5 rounded-lg border border-[#141210]/25"
              />
            </div>

            <div class="sm:col-span-4">
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Custom Filename (Optional)</label>
              <input
                type="text"
                id="asset_custom_name"
                placeholder="e.g. new-vinyl-cover"
                class="w-full px-3 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
              />
            </div>

            <div class="sm:col-span-2">
              <button
                type="submit"
                class="w-full py-2.5 px-4 bg-[#141210] hover:bg-[#23201D] text-[#EDE7DA] font-medium text-xs uppercase tracking-widest rounded-lg shadow-sm transition-all"
              >
                Upload
              </button>
            </div>
          </form>
        </div>

        <!-- Assets Library Grid -->
        <div class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-6 shadow-sm space-y-4">
          <div class="flex items-center justify-between border-b border-[#141210]/15 pb-4">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-[#141210]">
              Media Library (<?= count($assets) ?> items)
            </h3>
            <span class="text-[11px] text-[#141210]/60">Stored in /public/assets/</span>
          </div>

          <div id="assetsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4">
            <?php foreach ($assets as $asset): ?>
              <div class="bg-[#E6DFD0] border border-[#141210]/20 rounded-xl p-3 flex flex-col justify-between group relative overflow-hidden shadow-sm hover:shadow transition-all">
                <div class="aspect-square w-full bg-[#EDE7DA] border border-[#141210]/10 rounded-lg mb-2.5 flex items-center justify-center p-2 overflow-hidden">
                  <img
                    src="../<?= htmlspecialchars($asset['path']) ?>"
                    alt="<?= htmlspecialchars($asset['name']) ?>"
                    class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300"
                    loading="lazy"
                  />
                </div>

                <div class="space-y-1">
                  <div class="text-[11px] font-semibold text-[#141210] truncate" title="<?= htmlspecialchars($asset['name']) ?>">
                    <?= htmlspecialchars($asset['name']) ?>
                  </div>
                  <div class="flex items-center justify-between text-[10px] text-[#141210]/60">
                    <span><?= $asset['size'] ?></span>
                    <span class="uppercase"><?= $asset['ext'] ?></span>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-3 pt-2 border-t border-[#141210]/10 flex items-center justify-between gap-1">
                  <button
                    type="button"
                    onclick="copyToClipboard('<?= htmlspecialchars($asset['path']) ?>')"
                    class="text-[10px] px-2 py-1 rounded bg-[#EDE7DA] hover:bg-[#141210] hover:text-[#EDE7DA] text-[#141210] border border-[#141210]/20 transition-colors"
                    title="Copy path"
                  >
                    Copy Path
                  </button>

                  <button
                    type="button"
                    onclick="deleteAsset('<?= htmlspecialchars($asset['name']) ?>')"
                    class="text-[10px] px-2 py-1 rounded text-[#9A332A] hover:bg-[#9A332A] hover:text-[#EDE7DA] transition-colors"
                    title="Delete asset"
                  >
                    Delete
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      </section>

      <!-- ================= 5. TRENDING TRACKS TAB ================= -->
      <section id="tab-tracks" class="tab-content hidden space-y-6">
        <div class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-6 shadow-sm space-y-5">
          <div class="flex items-center justify-between border-b border-[#141210]/15 pb-4">
            <div>
              <h2 class="font-cormorant text-2xl font-semibold text-[#141210]">Trending Music Tracks</h2>
              <p class="text-xs text-[#141210]/65">Manage track cards, metadata, and choose which 4 tracks appear on the Homepage.</p>
            </div>
            <button
              type="button"
              onclick="addNewTrack()"
              class="px-4 py-2 bg-[#141210] hover:bg-[#23201D] text-[#EDE7DA] font-medium text-xs uppercase tracking-wider rounded-lg shadow-sm transition-all"
            >
              + Add New Track
            </button>
          </div>

          <!-- Featured on Homepage Selector Banner (Select 4) -->
          <div class="bg-[#141210] text-[#EDE7DA] rounded-xl p-4 space-y-3 shadow-md border border-[#EDE7DA]/15">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="text-amber-400 text-sm">★</span>
                <span class="text-xs font-bold uppercase tracking-wider">Homepage Featured Tracks (Select 4)</span>
              </div>
              <span id="featuredCountBadge" class="text-[10px] font-mono px-2.5 py-0.5 rounded bg-[#EDE7DA]/20 text-[#EDE7DA] border border-[#EDE7DA]/30">
                4 of 4 selected
              </span>
            </div>
            <p class="text-[11px] text-[#EDE7DA]/75 leading-relaxed">
              Click the tracks below to toggle which 4 appear on the main website homepage. All tracks will appear in the full dedicated <strong>Trending</strong> page catalogue.
            </p>
            <div id="featuredPillsContainer" class="flex flex-wrap gap-2 pt-1">
              <!-- Dynamically populated pills -->
            </div>
          </div>

          <div id="tracksListContainer" class="space-y-4">
            <!-- Dynamically populated or rendered via JS -->
          </div>

          <div class="pt-4 flex justify-end">
            <button
              type="button"
              onclick="saveTracksData()"
              class="px-6 py-2.5 bg-[#141210] hover:bg-[#23201D] text-[#EDE7DA] font-medium text-xs uppercase tracking-widest rounded-lg shadow-sm transition-all"
            >
              Save All Tracks
            </button>
          </div>
        </div>
      </section>

      <!-- ================= 6. VISUAL TEMPLATES TAB ================= -->
      <section id="tab-templates" class="tab-content hidden space-y-6">
        <div class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-6 shadow-sm space-y-5">
          <div class="flex items-center justify-between border-b border-[#141210]/15 pb-4">
            <div>
              <h2 class="font-cormorant text-2xl font-semibold text-[#141210]">Visual Aesthetic Templates</h2>
              <p class="text-xs text-[#141210]/65">Configure visual layout cards (Noir Comic, Sumi Ink, Astral, Botanical).</p>
            </div>
            <button
              type="button"
              onclick="addNewTemplate()"
              class="px-4 py-2 bg-[#141210] hover:bg-[#23201D] text-[#EDE7DA] font-medium text-xs uppercase tracking-wider rounded-lg shadow-sm transition-all"
            >
              + Add Template
            </button>
          </div>

          <div id="templatesListContainer" class="space-y-4">
            <!-- Dynamically populated via JS -->
          </div>

          <div class="pt-4 flex justify-end">
            <button
              type="button"
              onclick="saveTemplatesData()"
              class="px-6 py-2.5 bg-[#141210] hover:bg-[#23201D] text-[#EDE7DA] font-medium text-xs uppercase tracking-widest rounded-lg shadow-sm transition-all"
            >
              Save All Templates
            </button>
          </div>
        </div>
      </section>

      <!-- ================= 7. CTA & STUDIO TAB ================= -->
      <section id="tab-cta" class="tab-content hidden space-y-6">
        <form id="form-cta" class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-6 shadow-sm space-y-5">
          <div class="border-b border-[#141210]/15 pb-4">
            <h2 class="font-cormorant text-2xl font-semibold text-[#141210]">Call To Action & Studio Modal</h2>
            <p class="text-xs text-[#141210]/65">Configure the bottom high-contrast banner and the interactive lyric studio modal.</p>
          </div>

          <!-- CTA Banner -->
          <div class="space-y-4">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-[#141210]">Bottom Banner Section</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Heading</label>
                <input
                  type="text"
                  id="cta_heading"
                  class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                  value="<?= htmlspecialchars($siteData['cta']['heading'] ?? 'Create. Visualize.') ?>"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Italic Highlight Word</label>
                <input
                  type="text"
                  id="cta_heading_italic"
                  class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                  value="<?= htmlspecialchars($siteData['cta']['headingItalic'] ?? 'Feel.') ?>"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Subtitle</label>
                <input
                  type="text"
                  id="cta_subtitle"
                  class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                  value="<?= htmlspecialchars($siteData['cta']['subtitle'] ?? 'Your music, your style — in one place.') ?>"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Button Text</label>
                <input
                  type="text"
                  id="cta_button_text"
                  class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                  value="<?= htmlspecialchars($siteData['cta']['buttonText'] ?? 'Start Creating') ?>"
                />
              </div>
            </div>
          </div>

          <!-- Studio Modal Defaults -->
          <div class="border-t border-[#141210]/15 pt-5 space-y-4">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-[#141210]">Studio Modal Defaults</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Modal Window Title</label>
                <input
                  type="text"
                  id="studio_modal_title"
                  class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                  value="<?= htmlspecialchars($siteData['studio']['modalTitle'] ?? 'VoidAntara Lyric Studio') ?>"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Default Lyric Line</label>
                <input
                  type="text"
                  id="studio_default_lyric"
                  class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                  value="<?= htmlspecialchars($siteData['studio']['defaultLyric'] ?? 'Midnight echoes in ink & analog grain') ?>"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Default Artist Signature</label>
                <input
                  type="text"
                  id="studio_default_artist"
                  class="w-full px-3.5 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                  value="<?= htmlspecialchars($siteData['studio']['defaultArtist'] ?? 'VOIDANTARA STUDIO • 33 RPM') ?>"
                />
              </div>
            </div>
          </div>

          <div class="pt-4 flex justify-end">
            <button
              type="button"
              onclick="saveCtaAndStudio()"
              class="px-6 py-2.5 bg-[#141210] hover:bg-[#23201D] text-[#EDE7DA] font-medium text-xs uppercase tracking-widest rounded-lg shadow-sm transition-all"
            >
              Save CTA & Studio
            </button>
          </div>
        </form>
      </section>

      <!-- ================= 8. FOOTER & SOCIALS TAB ================= -->
      <section id="tab-footer" class="tab-content hidden space-y-6">
        <form id="form-footer" class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-6 shadow-sm space-y-5">
          <div class="border-b border-[#141210]/15 pb-4">
            <h2 class="font-cormorant text-2xl font-semibold text-[#141210]">Footer & Social Links</h2>
            <p class="text-xs text-[#141210]/65">Configure the footer copyright notice and community links.</p>
          </div>

          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Copyright Notice</label>
            <input
              type="text"
              id="footer_rights"
              class="w-full px-3.5 py-2.5 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
              value="<?= htmlspecialchars($siteData['footer']['rights'] ?? 'VOIDANTARA ARCHIVE • ALL RIGHTS RESERVED.') ?>"
            />
          </div>

          <div class="border-t border-[#141210]/15 pt-5 space-y-4">
            <div class="flex items-center justify-between">
              <h3 class="text-xs font-semibold uppercase tracking-wider text-[#141210]">Social Links</h3>
              <button
                type="button"
                onclick="addSocialLink()"
                class="text-xs px-3 py-1 bg-[#141210] text-[#EDE7DA] rounded-md hover:bg-[#23201D] transition-colors"
              >
                + Add Link
              </button>
            </div>

            <div id="socialsContainer" class="space-y-3">
              <!-- Dynamically populated via JS -->
            </div>
          </div>

          <div class="pt-4 flex justify-end">
            <button
              type="button"
              onclick="saveFooterSettings()"
              class="px-6 py-2.5 bg-[#141210] hover:bg-[#23201D] text-[#EDE7DA] font-medium text-xs uppercase tracking-widest rounded-lg shadow-sm transition-all"
            >
              Save Footer & Socials
            </button>
          </div>
        </form>
      </section>

      <!-- ================= 9. RAW JSON & BACKUP TAB ================= -->
      <section id="tab-rawjson" class="tab-content hidden space-y-6">
        <div class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-6 shadow-sm space-y-4">
          <div class="flex items-center justify-between border-b border-[#141210]/15 pb-4">
            <div>
              <h2 class="font-cormorant text-2xl font-semibold text-[#141210]">Direct Configuration (Raw JSON)</h2>
              <p class="text-xs text-[#141210]/65">View or edit the full authoritative site_content.json file directly.</p>
            </div>
            
            <div class="flex items-center gap-2">
              <button
                type="button"
                onclick="downloadBackupJson()"
                class="px-3.5 py-1.5 text-xs border border-[#141210]/30 rounded-lg hover:bg-[#141210] hover:text-[#EDE7DA] transition-all"
              >
                📥 Download Backup
              </button>

              <label class="px-3.5 py-1.5 text-xs border border-[#141210]/30 rounded-lg hover:bg-[#141210] hover:text-[#EDE7DA] transition-all cursor-pointer">
                <span>📤 Restore JSON</span>
                <input type="file" id="restore_json_file" accept=".json" class="hidden" onchange="restoreBackupFile(this)" />
              </label>
            </div>
          </div>

          <textarea
            id="raw_json_textarea"
            rows="20"
            class="w-full p-4 font-mono text-xs leading-relaxed bg-[#181715] text-[#EDE7DA] rounded-xl border border-[#141210]/25 focus:ring-1 focus:ring-[#141210] custom-scrollbar"
            spellcheck="false"
          ><?= htmlspecialchars(json_encode($siteData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) ?></textarea>

          <div class="flex items-center justify-between pt-2">
            <span class="text-[11px] text-[#141210]/60">Valid JSON required. Changes take effect across the entire site immediately upon saving.</span>
            
            <button
              type="button"
              onclick="saveRawJson()"
              class="px-6 py-2.5 bg-[#141210] hover:bg-[#23201D] text-[#EDE7DA] font-medium text-xs uppercase tracking-widest rounded-lg shadow-sm transition-all"
            >
              Save Raw JSON
            </button>
          </div>
        </div>
      </section>

      <!-- ================= 10. SECURITY & PASSKEY TAB ================= -->
      <section id="tab-security" class="tab-content hidden space-y-6">
        <form id="form-security" class="bg-[#EDE7DA] border border-[#141210]/20 rounded-2xl p-6 shadow-sm space-y-5">
          <div class="border-b border-[#141210]/15 pb-4">
            <h2 class="font-cormorant text-2xl font-semibold text-[#141210]">Security & Admin Credentials</h2>
            <p class="text-xs text-[#141210]/65">Update your admin username and secret passkey.</p>
          </div>

          <div class="space-y-4 max-w-md">
            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Current Admin Password (Required)</label>
              <input
                type="password"
                id="sec_current_password"
                required
                class="w-full px-3.5 py-2.5 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                placeholder="Current password"
              />
            </div>

            <div class="border-t border-[#141210]/15 pt-3">
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Admin Username</label>
              <input
                type="text"
                id="sec_username"
                required
                class="w-full px-3.5 py-2.5 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                value="<?= htmlspecialchars($credentials['username']) ?>"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">New Password (Leave blank to keep current)</label>
              <input
                type="password"
                id="sec_new_password"
                class="w-full px-3.5 py-2.5 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                placeholder="New password (min 6 characters)"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-[#141210] mb-1.5">Confirm New Password</label>
              <input
                type="password"
                id="sec_confirm_password"
                class="w-full px-3.5 py-2.5 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]"
                placeholder="Confirm new password"
              />
            </div>

            <div class="pt-2">
              <button
                type="button"
                onclick="updateAdminCredentials()"
                class="w-full py-2.5 px-4 bg-[#141210] hover:bg-[#23201D] text-[#EDE7DA] font-medium text-xs uppercase tracking-widest rounded-lg shadow-sm transition-all"
              >
                Update Credentials
              </button>
            </div>
          </div>
        </form>
      </section>

    </main>

  </div>

  <!-- TOAST NOTIFICATION -->
  <div
    id="toastBox"
    class="fixed bottom-5 right-5 z-[100] transform transition-all duration-300 translate-y-24 opacity-0 pointer-events-none"
  >
    <div
      id="toastInner"
      class="px-5 py-3 rounded-xl bg-[#141210] text-[#EDE7DA] border border-[#EDE7DA]/30 text-xs shadow-2xl flex items-center gap-3 font-mono"
    >
      <span id="toastIcon">✦</span>
      <span id="toastMessage">Notification message</span>
    </div>
  </div>

  <!-- CLIENT-SIDE JAVASCRIPT CONTROLLER -->
  <script>
    const CSRF_TOKEN = <?= json_encode($csrfToken) ?>;
    let SITE_DATA = <?= json_encode($siteData) ?>;
    let ASSETS_LIST = <?= json_encode($assets) ?>;

    // Toast utility
    function showToast(msg, isError = false) {
      const box = document.getElementById('toastBox');
      const inner = document.getElementById('toastInner');
      const text = document.getElementById('toastMessage');
      const icon = document.getElementById('toastIcon');

      text.textContent = msg;
      icon.textContent = isError ? '⚠️' : '✓';
      inner.className = isError
        ? 'px-5 py-3 rounded-xl bg-[#9A332A] text-white border border-white/20 text-xs shadow-2xl flex items-center gap-3 font-mono'
        : 'px-5 py-3 rounded-xl bg-[#141210] text-[#EDE7DA] border border-[#EDE7DA]/30 text-xs shadow-2xl flex items-center gap-3 font-mono';

      box.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
      setTimeout(() => {
        box.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
      }, 3500);
    }

    // Tab Navigation
    function switchTab(tabId) {
      document.querySelectorAll('.tab-btn').forEach(btn => {
        if (btn.getAttribute('data-tab') === tabId) {
          btn.className = 'tab-btn w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg text-left transition-all bg-[#141210] text-[#EDE7DA]';
        } else {
          btn.className = 'tab-btn w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium rounded-lg text-left transition-all text-[#141210]/80 hover:bg-[#141210]/10 hover:text-[#141210]';
        }
      });

      document.querySelectorAll('.tab-content').forEach(section => {
        section.classList.add('hidden');
        section.classList.remove('block');
      });

      const active = document.getElementById('tab-' + tabId);
      if (active) {
        active.classList.remove('hidden');
        active.classList.add('block');
      }

      if (tabId === 'rawjson') {
        document.getElementById('raw_json_textarea').value = JSON.stringify(SITE_DATA, null, 2);
      }
    }

    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        switchTab(btn.getAttribute('data-tab'));
      });
    });

    // Helper: Build an Asset Select Dropdown HTML
    function buildAssetSelectHtml(id, selectedValue, onchangeCallback = '') {
      let html = `<select id="${id}" class="w-full px-3 py-2 bg-[#E6DFD0]/60 border border-[#141210]/25 rounded-lg text-xs font-medium text-[#141210]" ${onchangeCallback ? `onchange="${onchangeCallback}"` : ''}>`;
      ASSETS_LIST.forEach(asset => {
        const isSel = (asset.path === selectedValue || asset.name === selectedValue) ? 'selected' : '';
        html += `<option value="${asset.path}" ${isSel}>${asset.name} (${asset.size})</option>`;
      });
      html += `</select>`;
      return html;
    }

    function updateLogoPreview(path) {
      const img = document.getElementById('site_logo_preview');
      img.src = '../' + path;
    }

    // Save Site Identity
    async function saveSiteIdentity() {
      SITE_DATA.site = SITE_DATA.site || {};
      SITE_DATA.site.name = document.getElementById('site_name').value.trim();
      SITE_DATA.site.title = document.getElementById('site_title').value.trim();
      SITE_DATA.site.description = document.getElementById('site_description').value.trim();
      SITE_DATA.site.logo = document.getElementById('site_logo_select').value;
      SITE_DATA.site.hankoSeal = [
        document.getElementById('hanko_char1').value.trim() || '音',
        document.getElementById('hanko_char2').value.trim() || '響'
      ];
      SITE_DATA.site.hankoTitle = document.getElementById('hanko_title').value.trim();

      // Keep assets.brandLogo in sync
      SITE_DATA.assets = SITE_DATA.assets || {};
      SITE_DATA.assets.brandLogo = SITE_DATA.site.logo;

      const res = await apiPost('save_section', {
        section: 'site',
        data: JSON.stringify(SITE_DATA.site)
      });

      if (res.success) {
        showToast('Site identity updated successfully!');
      } else {
        showToast(res.message || 'Failed to save', true);
      }
    }

    // Save Hero Section
    async function saveHeroSection() {
      SITE_DATA.hero = SITE_DATA.hero || {};
      SITE_DATA.hero.tagline = document.getElementById('hero_tagline').value.trim();
      SITE_DATA.hero.wordmarkVoid = document.getElementById('hero_wordmark_void').value.trim();
      SITE_DATA.hero.wordmarkLuma = document.getElementById('hero_wordmark_luma').value.trim();
      SITE_DATA.hero.pitch = document.getElementById('hero_pitch').value.trim();
      SITE_DATA.hero.buttonText = document.getElementById('hero_button_text').value.trim();
      SITE_DATA.hero.scrollText = document.getElementById('hero_scroll_text').value.trim();
      SITE_DATA.hero.jpText = document.getElementById('hero_jp_text').value.trim();

      SITE_DATA.hero.assets = SITE_DATA.hero.assets || {};
      const slots = ['heroStatue', 'heroVinyl', 'heroSkyRect', 'bird', 'flowerStamp', 'flowersHeroMain', 'musicCord', 'musicStar'];
      slots.forEach(slot => {
        const el = document.getElementById('hero_slot_' + slot);
        if (el) {
          SITE_DATA.hero.assets[slot] = el.value;
          SITE_DATA.assets[slot] = el.value;
        }
      });

      const res = await apiPost('save_section', {
        section: 'hero',
        data: JSON.stringify(SITE_DATA.hero)
      });

      if (res.success) {
        showToast('Hero section updated successfully!');
      } else {
        showToast(res.message || 'Failed to save', true);
      }
    }

    // Render Tracks
    function renderTracksList() {
      const container = document.getElementById('tracksListContainer');
      const tracks = SITE_DATA.tracks || [];
      document.getElementById('statTracksCount').textContent = tracks.length;

      // Ensure featuredTracks array exists
      if (!Array.isArray(SITE_DATA.featuredTracks)) {
        SITE_DATA.featuredTracks = tracks.filter(t => t.featured).map(t => t.id);
        if (SITE_DATA.featuredTracks.length === 0) {
          SITE_DATA.featuredTracks = tracks.slice(0, 4).map(t => t.id);
        }
      }

      // Update Top Featured Pills & Counter
      const pillsContainer = document.getElementById('featuredPillsContainer');
      const countBadge = document.getElementById('featuredCountBadge');
      const featuredCount = SITE_DATA.featuredTracks.length;

      if (countBadge) {
        countBadge.textContent = `${featuredCount} of 4 selected`;
        countBadge.className = featuredCount === 4
          ? 'text-[10px] font-mono px-2.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 font-bold'
          : 'text-[10px] font-mono px-2.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 font-bold';
      }

      if (pillsContainer) {
        pillsContainer.innerHTML = tracks.map(track => {
          const isFeatured = SITE_DATA.featuredTracks.includes(track.id);
          return `
            <button
              type="button"
              onclick="toggleTrackFeatured('${track.id}')"
              class="px-3 py-1.5 rounded-lg text-xs font-mono transition-all flex items-center gap-1.5 ${
                isFeatured
                  ? 'bg-[#EDE7DA] text-[#141210] font-bold shadow-sm border border-[#EDE7DA]'
                  : 'bg-[#181715] text-[#EDE7DA]/50 hover:text-[#EDE7DA] border border-[#EDE7DA]/15'
              }"
            >
              <span>${isFeatured ? '★' : '☆'}</span>
              <span>${escapeHtml(track.title || 'Untitled')}</span>
            </button>
          `;
        }).join('');
      }

      container.innerHTML = tracks.map((track, i) => {
        const isFeatured = SITE_DATA.featuredTracks.includes(track.id);
        return `
        <div class="bg-[#E6DFD0] border ${isFeatured ? 'border-[#141210] shadow-md ring-1 ring-[#141210]/20' : 'border-[#141210]/20'} rounded-xl p-5 space-y-4 relative group" data-track-index="${i}">
          <div class="flex items-center justify-between border-b border-[#141210]/10 pb-3">
            <div class="flex items-center gap-3">
              <span class="w-6 h-6 rounded-full bg-[#141210] text-[#EDE7DA] text-xs font-bold flex items-center justify-center">
                ${i + 1}
              </span>
              <span class="font-cormorant text-xl font-bold text-[#141210]">${escapeHtml(track.title || 'Untitled Track')}</span>
              <span class="text-xs text-[#141210]/60">— ${escapeHtml(track.artist || 'Unknown Artist')}</span>
            </div>

            <div class="flex items-center gap-2">
              <button
                type="button"
                onclick="toggleTrackFeatured('${track.id}')"
                class="text-xs px-3 py-1 rounded-lg border font-mono transition-all flex items-center gap-1.5 ${
                  isFeatured
                    ? 'bg-[#141210] text-[#EDE7DA] border-[#141210] font-semibold'
                    : 'bg-[#EDE7DA] text-[#141210]/70 border-[#141210]/20 hover:border-[#141210]'
                }"
              >
                <span>${isFeatured ? '★ Featured on Home' : '☆ Feature on Home'}</span>
              </button>

              <button type="button" onclick="removeTrack(${i})" class="text-xs text-[#9A332A] hover:bg-[#9A332A] hover:text-white px-2.5 py-1 rounded transition-colors">
                ✕ Delete
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Track Title</label>
              <input type="text" class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs" value="${escapeHtml(track.title || '')}" onchange="updateTrackField(${i}, 'title', this.value)" />
            </div>
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Artist Name</label>
              <input type="text" class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs" value="${escapeHtml(track.artist || '')}" onchange="updateTrackField(${i}, 'artist', this.value)" />
            </div>
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Serial / Edition</label>
              <input type="text" class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs" value="${escapeHtml(track.serial || '')}" onchange="updateTrackField(${i}, 'serial', this.value)" />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Duration</label>
              <input type="text" class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs" value="${escapeHtml(track.duration || '03:30')}" onchange="updateTrackField(${i}, 'duration', this.value)" />
            </div>
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Vibe Tag</label>
              <input type="text" class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs" value="${escapeHtml(track.vibeTag || 'HI-FI')}" onchange="updateTrackField(${i}, 'vibeTag', this.value)" />
            </div>
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Tempo (BPM)</label>
              <input type="number" class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs" value="${track.bpm || 120}" onchange="updateTrackField(${i}, 'bpm', parseInt(this.value, 10))" />
            </div>
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Cover Artwork Image</label>
              ${buildAssetSelectHtml('track_img_' + i, track.image || '', `updateTrackField(${i}, 'image', this.value)`)}
            </div>
          </div>

          <div>
            <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Lyric Snippet / Hook</label>
            <input type="text" class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs" value="${escapeHtml(track.lyricSample || '')}" onchange="updateTrackField(${i}, 'lyricSample', this.value)" />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Audio MP3 File / URL (Curated Music)</label>
              <input type="text" placeholder="e.g. public/audio/snooze.mp3 or https://..." class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs font-mono" value="${escapeHtml(track.audioUrl || '')}" onchange="updateTrackField(${i}, 'audioUrl', this.value)" />
            </div>
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Recommended Snippet (Min 10s, Max 45s)</label>
              <div class="flex items-center gap-2">
                <input type="number" min="0" max="600" placeholder="Start (sec)" class="w-1/2 px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs font-mono" value="${track.snippetStart || 15}" onchange="updateTrackField(${i}, 'snippetStart', parseInt(this.value)||0)" />
                <span class="text-xs text-[#141210]/50">to</span>
                <input type="number" min="10" max="600" placeholder="End (sec)" class="w-1/2 px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs font-mono" value="${track.snippetEnd || 45}" onchange="updateTrackField(${i}, 'snippetEnd', parseInt(this.value)||45)" />
              </div>
            </div>
          </div>

          <div>
            <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Synced Lyrics / LRC Timestamps (Optional)</label>
            <textarea rows="2" placeholder="[00:15.00] Lyric line 1&#10;[00:22.00] Lyric line 2..." class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs font-mono" onchange="updateTrackField(${i}, 'lyrics', this.value)">${escapeHtml(track.lyrics || '')}</textarea>
          </div>

          <div>
            <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Ticket Stub Note</label>
            <input type="text" class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs" value="${escapeHtml(track.stubNote || '')}" onchange="updateTrackField(${i}, 'stubNote', this.value)" />
          </div>
        </div>
      `;
      }).join('');
    }

    function toggleTrackFeatured(trackId) {
      SITE_DATA.featuredTracks = SITE_DATA.featuredTracks || [];
      const idx = SITE_DATA.featuredTracks.indexOf(trackId);
      if (idx >= 0) {
        SITE_DATA.featuredTracks.splice(idx, 1);
      } else {
        if (SITE_DATA.featuredTracks.length >= 4) {
          showToast('Only 4 tracks can be shown on the Homepage. Please deselect one first.', true);
          return;
        }
        SITE_DATA.featuredTracks.push(trackId);
      }

      // Sync track.featured boolean
      if (SITE_DATA.tracks) {
        SITE_DATA.tracks.forEach(t => {
          t.featured = SITE_DATA.featuredTracks.includes(t.id);
        });
      }

      renderTracksList();
      showToast(`Homepage tracks updated (${SITE_DATA.featuredTracks.length} of 4 selected). Click Save All Tracks when ready.`);
    }

    function updateTrackField(index, field, value) {
      if (SITE_DATA.tracks && SITE_DATA.tracks[index]) {
        SITE_DATA.tracks[index][field] = value;
        // Keep assetKey in sync if image changed
        if (field === 'image') {
          const key = SITE_DATA.tracks[index].assetKey;
          if (key && SITE_DATA.assets) {
            SITE_DATA.assets[key] = value;
          }
        }
      }
    }

    function addNewTrack() {
      SITE_DATA.tracks = SITE_DATA.tracks || [];
      const newId = 'track-' + Date.now();
      SITE_DATA.tracks.push({
        id: newId,
        serial: `NO. 0${SITE_DATA.tracks.length + 1}`,
        duration: '03:45',
        edition: 'STUDIO EDITION',
        vibeTag: 'STEREO',
        stubNote: 'Kinetic lyrical canvas tuned to analog warmth.',
        title: 'New Track',
        artist: 'Featured Artist',
        image: ASSETS_LIST[0] ? ASSETS_LIST[0].path : 'public/assets/download.jpg',
        assetKey: newId,
        pngKey: 'heroVinyl',
        lyricSample: 'Type the lyric snippet here...',
        bpm: 128,
        notes: [261.63, 329.63, 392.0, 493.88],
        bars: [30, 48, 66, 82, 54, 72, 94, 80, 46, 62, 86, 98, 70, 52, 68, 88, 60, 42]
      });
      renderTracksList();
      showToast('New track added. Click Save All Tracks when done.');
    }

    function removeTrack(index) {
      if (confirm('Are you sure you want to delete this track?')) {
        SITE_DATA.tracks.splice(index, 1);
        renderTracksList();
      }
    }

    async function saveTracksData() {
      const res = await apiPost('save_section', {
        section: 'tracks',
        data: JSON.stringify(SITE_DATA.tracks)
      });
      if (res.success) {
        showToast('All tracks saved successfully!');
      } else {
        showToast(res.message || 'Failed to save tracks', true);
      }
    }

    // Render Templates
    function renderTemplatesList() {
      const container = document.getElementById('templatesListContainer');
      const templates = SITE_DATA.templates || [];
      document.getElementById('statTemplatesCount').textContent = templates.length;

      container.innerHTML = templates.map((tpl, i) => `
        <div class="bg-[#E6DFD0] border border-[#141210]/20 rounded-xl p-5 space-y-4 relative group" data-tpl-index="${i}">
          <div class="flex items-center justify-between border-b border-[#141210]/10 pb-3">
            <div class="flex items-center gap-3">
              <span class="w-6 h-6 rounded-full bg-[#141210] text-[#EDE7DA] text-xs font-bold flex items-center justify-center">
                ${i + 1}
              </span>
              <span class="font-cormorant text-xl font-bold text-[#141210]">${escapeHtml(tpl.name || 'Untitled Template')}</span>
              <span class="text-xs text-[#141210]/60">ID: ${escapeHtml(tpl.id || '')}</span>
            </div>
            <button type="button" onclick="removeTemplate(${i})" class="text-xs text-[#9A332A] hover:bg-[#9A332A] hover:text-white px-2.5 py-1 rounded transition-colors">
              ✕ Delete
            </button>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Template Name</label>
              <input type="text" class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs" value="${escapeHtml(tpl.name || '')}" onchange="updateTemplateField(${i}, 'name', this.value)" />
            </div>
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Detail Attachment Style</label>
              <select class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs" onchange="updateTemplateField(${i}, 'detail', this.value)">
                <option value="none" ${tpl.detail === 'none' ? 'selected' : ''}>None</option>
                <option value="tape-top" ${tpl.detail === 'tape-top' ? 'selected' : ''}>Washi Tape Top</option>
                <option value="paperclip" ${tpl.detail === 'paperclip' ? 'selected' : ''}>Silver Paperclip</option>
                <option value="backing-strip" ${tpl.detail === 'backing-strip' ? 'selected' : ''}>Dark Backing Strip</option>
              </select>
            </div>
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Cover Artwork Image</label>
              ${buildAssetSelectHtml('tpl_img_' + i, tpl.image || '', `updateTemplateField(${i}, 'image', this.value)`)}
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Description</label>
              <input type="text" class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs" value="${escapeHtml(tpl.desc || '')}" onchange="updateTemplateField(${i}, 'desc', this.value)" />
            </div>
            <div>
              <label class="block text-[10px] font-semibold uppercase tracking-wider text-[#141210]/70 mb-1">Tags (Comma Separated)</label>
              <input type="text" class="w-full px-3 py-1.5 bg-[#EDE7DA] border border-[#141210]/20 rounded text-xs" value="${escapeHtml(Array.isArray(tpl.tags) ? tpl.tags.join(', ') : (tpl.tags || ''))}" onchange="updateTemplateTags(${i}, this.value)" />
            </div>
          </div>

          <div class="pt-2 border-t border-[#141210]/10 flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-semibold ${tpl.approved !== false ? 'text-[#141210]' : 'text-[#141210]/50'}">
              <input type="checkbox" ${tpl.approved !== false ? 'checked' : ''} onchange="updateTemplateField(${i}, 'approved', this.checked); renderTemplatesList();" class="rounded w-4 h-4 text-[#141210] accent-[#141210]" />
              <span>${tpl.approved !== false ? '✅ Approved for Live Users (Visible on Site & Studio)' : '❌ Hidden / Locked (Not Visible to Users)'}</span>
            </label>
            <span class="text-[11px] font-mono text-[#141210]/60">ID: ${escapeHtml(tpl.id || '')}</span>
          </div>
        </div>
      `).join('');
    }

    function updateTemplateField(index, field, value) {
      if (SITE_DATA.templates && SITE_DATA.templates[index]) {
        SITE_DATA.templates[index][field] = value;
        if (field === 'image') {
          const key = SITE_DATA.templates[index].assetKey;
          if (key && SITE_DATA.assets) {
            SITE_DATA.assets[key] = value;
          }
        }
      }
    }

    function updateTemplateTags(index, raw) {
      if (SITE_DATA.templates && SITE_DATA.templates[index]) {
        SITE_DATA.templates[index].tags = raw.split(',').map(s => s.trim()).filter(Boolean);
      }
    }

    function addNewTemplate() {
      SITE_DATA.templates = SITE_DATA.templates || [];
      const newId = 'style-' + Date.now();
      SITE_DATA.templates.push({
        id: newId,
        name: 'New Visual Style',
        image: ASSETS_LIST[0] ? ASSETS_LIST[0].path : 'public/assets/flower stamp.png',
        assetKey: newId,
        pngKey: 'flowerStamp',
        detail: 'tape-top',
        desc: 'New aesthetic blueprint crafted for lyric typography.',
        tags: ['Minimal', 'Editorial', 'Print']
      });
      renderTemplatesList();
      showToast('New template added. Click Save All Templates when done.');
    }

    function removeTemplate(index) {
      if (confirm('Are you sure you want to delete this template?')) {
        SITE_DATA.templates.splice(index, 1);
        renderTemplatesList();
      }
    }

    async function saveTemplatesData() {
      const res = await apiPost('save_section', {
        section: 'templates',
        data: JSON.stringify(SITE_DATA.templates)
      });
      if (res.success) {
        showToast('All templates saved successfully!');
      } else {
        showToast(res.message || 'Failed to save templates', true);
      }
    }

    // Save CTA & Studio
    async function saveCtaAndStudio() {
      SITE_DATA.cta = SITE_DATA.cta || {};
      SITE_DATA.cta.heading = document.getElementById('cta_heading').value.trim();
      SITE_DATA.cta.headingItalic = document.getElementById('cta_heading_italic').value.trim();
      SITE_DATA.cta.subtitle = document.getElementById('cta_subtitle').value.trim();
      SITE_DATA.cta.buttonText = document.getElementById('cta_button_text').value.trim();

      SITE_DATA.studio = SITE_DATA.studio || {};
      SITE_DATA.studio.modalTitle = document.getElementById('studio_modal_title').value.trim();
      SITE_DATA.studio.defaultLyric = document.getElementById('studio_default_lyric').value.trim();
      SITE_DATA.studio.defaultArtist = document.getElementById('studio_default_artist').value.trim();

      const res1 = await apiPost('save_section', { section: 'cta', data: JSON.stringify(SITE_DATA.cta) });
      const res2 = await apiPost('save_section', { section: 'studio', data: JSON.stringify(SITE_DATA.studio) });

      if (res1.success && res2.success) {
        showToast('CTA banner and Studio modal settings saved!');
      } else {
        showToast('Failed to save settings', true);
      }
    }

    // Render Socials
    function renderSocials() {
      const container = document.getElementById('socialsContainer');
      const socials = (SITE_DATA.footer && SITE_DATA.footer.socials) ? SITE_DATA.footer.socials : [];

      container.innerHTML = socials.map((item, i) => `
        <div class="flex items-center gap-3">
          <input type="text" placeholder="Platform (e.g. Discord)" class="w-1/3 px-3 py-1.5 bg-[#E6DFD0]/60 border border-[#141210]/20 rounded text-xs" value="${escapeHtml(item.name || '')}" onchange="updateSocialField(${i}, 'name', this.value)" />
          <input type="text" placeholder="Icon (e.g. ◈)" class="w-16 text-center py-1.5 bg-[#E6DFD0]/60 border border-[#141210]/20 rounded text-xs" value="${escapeHtml(item.icon || '◈')}" onchange="updateSocialField(${i}, 'icon', this.value)" />
          <input type="text" placeholder="URL (e.g. https://...)" class="flex-1 px-3 py-1.5 bg-[#E6DFD0]/60 border border-[#141210]/20 rounded text-xs" value="${escapeHtml(item.url || '#')}" onchange="updateSocialField(${i}, 'url', this.value)" />
          <button type="button" onclick="removeSocial(${i})" class="text-xs text-[#9A332A] px-2 py-1 rounded hover:bg-[#9A332A] hover:text-white transition-colors">✕</button>
        </div>
      `).join('');
    }

    function updateSocialField(index, field, value) {
      if (SITE_DATA.footer && SITE_DATA.footer.socials && SITE_DATA.footer.socials[index]) {
        SITE_DATA.footer.socials[index][field] = value;
      }
    }

    function addSocialLink() {
      SITE_DATA.footer = SITE_DATA.footer || {};
      SITE_DATA.footer.socials = SITE_DATA.footer.socials || [];
      SITE_DATA.footer.socials.push({ name: 'New Link', icon: '↗', url: '#' });
      renderSocials();
    }

    function removeSocial(index) {
      SITE_DATA.footer.socials.splice(index, 1);
      renderSocials();
    }

    async function saveFooterSettings() {
      SITE_DATA.footer = SITE_DATA.footer || {};
      SITE_DATA.footer.rights = document.getElementById('footer_rights').value.trim();

      const res = await apiPost('save_section', {
        section: 'footer',
        data: JSON.stringify(SITE_DATA.footer)
      });
      if (res.success) {
        showToast('Footer settings and social links saved!');
      } else {
        showToast(res.message || 'Failed to save', true);
      }
    }

    // Save Raw JSON
    async function saveRawJson() {
      const raw = document.getElementById('raw_json_textarea').value;
      try {
        const parsed = JSON.parse(raw);
        SITE_DATA = parsed;
      } catch (e) {
        showToast('Invalid JSON syntax: ' + e.message, true);
        return;
      }

      const res = await apiPost('save_raw_json', { raw_json: raw });
      if (res.success) {
        showToast('Complete site configuration updated!');
        renderTracksList();
        renderTemplatesList();
        renderSocials();
      } else {
        showToast(res.message || 'Failed to save raw JSON', true);
      }
    }

    function downloadBackupJson() {
      const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(SITE_DATA, null, 2));
      const downloadAnchor = document.createElement('a');
      downloadAnchor.setAttribute("href", dataStr);
      downloadAnchor.setAttribute("download", `voidantara_backup_${new Date().toISOString().slice(0,10)}.json`);
      document.body.appendChild(downloadAnchor);
      downloadAnchor.click();
      downloadAnchor.remove();
    }

    function restoreBackupFile(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = async (e) => {
          try {
            const parsed = JSON.parse(e.target.result);
            document.getElementById('raw_json_textarea').value = JSON.stringify(parsed, null, 2);
            SITE_DATA = parsed;
            const res = await apiPost('save_all', { data: JSON.stringify(parsed) });
            if (res.success) {
              showToast('Backup restored successfully!');
              renderTracksList();
              renderTemplatesList();
              renderSocials();
            } else {
              showToast(res.message || 'Failed to restore', true);
            }
          } catch (err) {
            showToast('Invalid JSON backup file: ' + err.message, true);
          }
        };
        reader.readAsText(input.files[0]);
      }
    }

    // Upload Asset
    document.getElementById('form-upload-asset').addEventListener('submit', async (e) => {
      e.preventDefault();
      const fileInput = document.getElementById('asset_file_input');
      const customName = document.getElementById('asset_custom_name').value.trim();

      if (!fileInput.files || !fileInput.files[0]) {
        showToast('Please select a file to upload', true);
        return;
      }

      const formData = new FormData();
      formData.append('action', 'upload_asset');
      formData.append('csrf_token', CSRF_TOKEN);
      formData.append('asset_file', fileInput.files[0]);
      if (customName) formData.append('custom_name', customName);

      try {
        const resp = await fetch('api.php', { method: 'POST', body: formData });
        const res = await resp.json();
        if (res.success) {
          showToast(res.message || 'Asset uploaded!');
          fileInput.value = '';
          document.getElementById('asset_custom_name').value = '';
          await refreshAssetsList();
        } else {
          showToast(res.message || 'Upload failed', true);
        }
      } catch (err) {
        showToast('Network error during upload', true);
      }
    });

    // Delete Asset
    async function deleteAsset(name) {
      if (!confirm(`Are you sure you want to delete '${name}'? This cannot be undone.`)) {
        return;
      }

      const res = await apiPost('delete_asset', { asset_name: name });
      if (res.success) {
        showToast(res.message || 'Asset removed');
        await refreshAssetsList();
      } else {
        showToast(res.message || 'Delete failed', true);
      }
    }

    // Refresh Assets List
    async function refreshAssetsList() {
      try {
        const resp = await fetch('api.php?action=list_assets');
        const res = await resp.json();
        if (res.success && Array.isArray(res.assets)) {
          ASSETS_LIST = res.assets;
          document.getElementById('statAssetsCount').textContent = ASSETS_LIST.length;

          // Rebuild grid
          const grid = document.getElementById('assetsGrid');
          grid.innerHTML = ASSETS_LIST.map(asset => `
            <div class="bg-[#E6DFD0] border border-[#141210]/20 rounded-xl p-3 flex flex-col justify-between group relative overflow-hidden shadow-sm hover:shadow transition-all">
              <div class="aspect-square w-full bg-[#EDE7DA] border border-[#141210]/10 rounded-lg mb-2.5 flex items-center justify-center p-2 overflow-hidden">
                <img src="../${asset.path}" alt="${escapeHtml(asset.name)}" class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy" />
              </div>
              <div class="space-y-1">
                <div class="text-[11px] font-semibold text-[#141210] truncate" title="${escapeHtml(asset.name)}">${escapeHtml(asset.name)}</div>
                <div class="flex items-center justify-between text-[10px] text-[#141210]/60">
                  <span>${asset.size}</span>
                  <span class="uppercase">${asset.ext}</span>
                </div>
              </div>
              <div class="mt-3 pt-2 border-t border-[#141210]/10 flex items-center justify-between gap-1">
                <button type="button" onclick="copyToClipboard('${asset.path}')" class="text-[10px] px-2 py-1 rounded bg-[#EDE7DA] hover:bg-[#141210] hover:text-[#EDE7DA] text-[#141210] border border-[#141210]/20 transition-colors">Copy Path</button>
                <button type="button" onclick="deleteAsset('${escapeHtml(asset.name)}')" class="text-[10px] px-2 py-1 rounded text-[#9A332A] hover:bg-[#9A332A] hover:text-[#EDE7DA] transition-colors">Delete</button>
              </div>
            </div>
          `).join('');

          // Re-render tracks and templates to refresh dropdown options
          renderTracksList();
          renderTemplatesList();
        }
      } catch (e) {
        console.error('Failed to refresh assets', e);
      }
    }

    // Update Credentials
    async function updateAdminCredentials() {
      const cur = document.getElementById('sec_current_password').value;
      const user = document.getElementById('sec_username').value.trim();
      const newP = document.getElementById('sec_new_password').value;
      const conf = document.getElementById('sec_confirm_password').value;

      if (!cur) {
        showToast('Please enter your current password', true);
        return;
      }
      if (newP && newP !== conf) {
        showToast('New passwords do not match', true);
        return;
      }

      const res = await apiPost('update_credentials', {
        current_password: cur,
        new_username: user,
        new_password: newP,
        confirm_password: conf
      });

      if (res.success) {
        showToast('Admin credentials updated successfully!');
        document.getElementById('sec_current_password').value = '';
        document.getElementById('sec_new_password').value = '';
        document.getElementById('sec_confirm_password').value = '';
      } else {
        showToast(res.message || 'Update failed', true);
      }
    }

    // Generic API POST Helper
    async function apiPost(action, bodyObj = {}) {
      const form = new URLSearchParams();
      form.append('action', action);
      form.append('csrf_token', CSRF_TOKEN);
      for (const [k, v] of Object.entries(bodyObj)) {
        form.append(k, v);
      }

      try {
        const resp = await fetch('api.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: form.toString()
        });
        return await resp.json();
      } catch (err) {
        return { success: false, message: 'Network request error' };
      }
    }

    function copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard: ' + text);
      }).catch(() => {
        showToast('Failed to copy', true);
      });
    }

    function escapeHtml(str) {
      if (typeof str !== 'string') return '';
      return str.replace(/[&<>"']/g, m => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
      })[m]);
    }

    // Initialize View
    document.addEventListener('DOMContentLoaded', () => {
      renderTracksList();
      renderTemplatesList();
      renderSocials();
    });
  </script>
</body>
</html>
