<?php
$siteDataFile = __DIR__ . '/data/site_content.json';
$siteData = file_exists($siteDataFile) ? json_decode(file_get_contents($siteDataFile), true) : [];
$siteName = htmlspecialchars($siteData['site']['name'] ?? 'VoidAntara', ENT_QUOTES, 'UTF-8');
$siteTitle = htmlspecialchars($siteData['site']['title'] ?? 'VoidAntara — Lyrics • Visuals • Vibes', ENT_QUOTES, 'UTF-8');
$siteDesc = htmlspecialchars($siteData['site']['description'] ?? 'Turn your favorite music into stunning lyric visuals.', ENT_QUOTES, 'UTF-8');
$siteLogo = htmlspecialchars($siteData['site']['logo'] ?? 'public/assets/logo.png', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $siteTitle; ?></title>
  <meta name="description" content="<?php echo $siteDesc; ?>" />
  <link rel="icon" type="image/png" href="<?php echo $siteLogo; ?>" />
  <script>
    window.__SITE_DATA__ = <?php echo json_encode($siteData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
  </script>

  <!-- Google Fonts: Pixelify Sans, Instrument Serif, Cormorant Garamond, Oswald, JetBrains Mono, Noto Serif JP -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,400;1,500;1,600;1,700&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:ital,wght@0,300;0,400;0,500;0,600;1,400&family=Noto+Serif+JP:wght@400;500&family=Oswald:wght@500;600;700&family=Pixelify+Sans:wght@500;600;700&display=swap"
    rel="stylesheet"
  />

  <!-- Tailwind CSS CDN & Custom Design Tokens -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            paper: '#E6DFD0',
            paperLight: '#EDE7DA',
            ink: '#141210',
            inkDark: '#12110F',
            slateAccent: '#3E5C7E',
            sealRed: '#9A332A',
          },
          fontFamily: {
            pixel: ['"Pixelify Sans"', 'monospace'],
            serifed: ['"Instrument Serif"', '"Cormorant Garamond"', 'Georgia', 'serif'],
            cormorant: ['"Cormorant Garamond"', 'Georgia', 'serif'],
            condensed: ['"Oswald"', 'Impact', 'sans-serif'],
            mono: ['"JetBrains Mono"', 'monospace'],
            jp: ['"Noto Serif JP"', 'serif'],
          },
        },
      },
    };
  </script>

  <!-- Core Libraries: React 18, ReactDOM 18, Babel, GSAP + ScrollTrigger + CustomEase, Lenis, Anime.js, Tone.js -->
  <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
  <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/CustomEase.min.js"></script>
  <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tone/14.8.49/Tone.min.js"></script>
  <!-- React Bits CSS -->
  <link rel="stylesheet" href="public/css/react-bits.css" />

  <style>
    :root {
      --bg-paper: #E6DFD0;
      --bg-paper-light: #EDE7DA;
      --ink: #141210;
      --ink-deep: #12110F;
      --accent-slate: #3E5C7E;
      --seal-red: #9A332A;
    }

    *, *::before, *::after {
      box-sizing: border-box;
    }

    html, body {
      margin: 0;
      padding: 0;
      background-color: var(--bg-paper);
      color: var(--ink);
      font-family: 'JetBrains Mono', monospace;
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
    }

    /* Hide native pointer on fine-pointer desktops when music cursor is active */
    @media (pointer: fine) {
      body.music-cursor-active,
      body.music-cursor-active a,
      body.music-cursor-active button,
      body.music-cursor-active input,
      body.music-cursor-active .cursor-interactive {
        cursor: none !important;
      }
    }

    /* Warm aged parchment paper surface with soft tonal mottling & ultra-subtle drafting lines */
    .paper-surface {
      background-color: var(--bg-paper);
      background-image:
        radial-gradient(ellipse 75% 55% at 16% 10%, rgba(252, 248, 238, 0.72) 0%, transparent 60%),
        radial-gradient(ellipse 65% 50% at 85% 24%, rgba(206, 195, 175, 0.34) 0%, transparent 62%),
        radial-gradient(ellipse 70% 55% at 22% 76%, rgba(196, 184, 162, 0.28) 0%, transparent 65%),
        radial-gradient(ellipse 60% 45% at 82% 88%, rgba(250, 245, 234, 0.5) 0%, transparent 60%),
        linear-gradient(to right, rgba(20, 18, 16, 0.022) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(20, 18, 16, 0.022) 1px, transparent 1px);
      background-size: 100% 100%, 100% 100%, 100% 100%, 100% 100%, 64px 64px, 64px 64px;
    }

    /* Deep matte charcoal torn-paper surface */
    .dark-paper-surface {
      background-color: var(--ink-deep);
      background-image:
        radial-gradient(ellipse 65% 55% at 20% 22%, rgba(237, 231, 218, 0.055) 0%, transparent 58%),
        radial-gradient(ellipse 60% 50% at 82% 78%, rgba(62, 92, 126, 0.09) 0%, transparent 60%),
        linear-gradient(180deg, rgba(20, 18, 16, 0.96) 0%, rgba(16, 15, 13, 1) 50%, rgba(18, 17, 15, 0.98) 100%);
    }

    /* Fine-grain archival paper & film noise overlay (vector turbulence, never pixelated) */
    .film-grain-overlay {
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: 9000;
      opacity: 0.11;
      mix-blend-mode: multiply;
    }

    /* Wordmark styling */
    .wordmark-void {
      font-family: 'Pixelify Sans', monospace;
      font-weight: 700;
      letter-spacing: -0.04em;
      color: var(--ink);
    }

    .wordmark-luma {
      font-family: 'Instrument Serif', 'Cormorant Garamond', Georgia, serif;
      font-style: italic;
      font-weight: 400;
      letter-spacing: -0.025em;
      color: var(--ink);
    }

    .reveal-mask {
      display: inline-block;
      overflow: hidden;
      vertical-align: bottom;
      padding-right: 0.08em;
    }

    /* Interactive hover glitch pulse on "Void" pixel wordmark */
    @keyframes voidGlitchShift {
      0% { transform: translate(0, 0) skewX(0deg); text-shadow: none; }
      20% { transform: translate(-2px, 1px) skewX(-4deg); text-shadow: 2px 0 rgba(62, 92, 126, 0.78), -2px 0 rgba(154, 51, 42, 0.68); }
      45% { transform: translate(2px, -1px) skewX(3deg); text-shadow: -2px 0 rgba(62, 92, 126, 0.7), 2px 0 rgba(20, 18, 16, 0.5); }
      75% { transform: translate(-1px, 0px) skewX(-1deg); text-shadow: 1px 0 rgba(62, 92, 126, 0.4); }
      100% { transform: translate(0, 0) skewX(0deg); text-shadow: none; }
    }
    .void-hover-interactive {
      display: inline-block;
      transition: transform 0.25s ease;
    }
    .void-hover-interactive:hover {
      animation: voidGlitchShift 0.42s steps(3, end);
    }

    /* Arrow-shift button micro-interaction */
    .arrow-shift-icon {
      transition: transform 0.32s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .arrow-shift-trigger:hover .arrow-shift-icon {
      transform: translateX(4px);
    }

    /* Ink-fill hover button for CTA */
    .ink-fill-btn {
      position: relative;
      overflow: hidden;
      z-index: 1;
      transition: color 0.36s ease, border-color 0.36s ease, transform 0.3s ease;
    }
    .ink-fill-btn::before {
      content: '';
      position: absolute;
      inset: 0;
      background-color: #141210;
      transform: scaleX(0);
      transform-origin: left center;
      transition: transform 0.42s cubic-bezier(0.22, 1, 0.36, 1);
      z-index: -1;
    }
    .ink-fill-btn:hover {
      color: #EDE7DA;
      border-color: rgba(237, 231, 218, 0.65);
    }
    .ink-fill-btn:hover::before {
      transform: scaleX(1);
    }

    /* Polaroid hover pill wiggle */
    @keyframes pillWiggle {
      0%, 100% { transform: translateX(-50%) rotate(0deg); }
      25% { transform: translateX(-50%) rotate(-4deg); }
      65% { transform: translateX(-50%) rotate(3.5deg); }
    }
    .polaroid-group:hover .polaroid-pill {
      animation: pillWiggle 0.45s ease-in-out;
    }

    /* Vintage Collector Ticket Card (Trending Now) */
    .vintage-ticket-card {
      position: relative;
      background-color: #E7DEC9;
      background-image:
        radial-gradient(circle at 20% 15%, rgba(255, 252, 244, 0.58) 0%, transparent 55%),
        radial-gradient(circle at 80% 85%, rgba(164, 146, 118, 0.24) 0%, transparent 55%);
      color: #141210;
      clip-path: polygon(
        14px 0%, calc(50% - 10px) 0%, calc(50% - 8px) 7px, calc(50% + 8px) 7px, calc(50% + 10px) 0%,
        calc(100% - 14px) 0%, 100% 14px,
        100% calc(69% - 11px), calc(100% - 11px) 69%, 100% calc(69% + 11px),
        100% calc(100% - 14px), calc(100% - 14px) 100%,
        14px 100%, 0% calc(100% - 14px),
        0% calc(69% + 11px), 11px 69%, 0% calc(69% - 11px),
        0% 14px
      );
      transition: transform 0.48s cubic-bezier(0.22, 1, 0.36, 1), filter 0.4s ease;
    }
    .vintage-ticket-wrap {
      filter: drop-shadow(0 14px 26px rgba(0, 0, 0, 0.48));
      transition: transform 0.48s cubic-bezier(0.22, 1, 0.36, 1), filter 0.45s ease;
    }
    .vintage-ticket-wrap:hover {
      transform: translateY(-10px) rotate(0deg) !important;
      filter: drop-shadow(0 24px 36px rgba(0, 0, 0, 0.68));
    }
    .vintage-ticket-vertical {
      writing-mode: vertical-rl;
      transform: rotate(180deg);
      letter-spacing: 0.14em;
    }

    /* Ambient organic idle motion loops */
    @keyframes flowerSway {
      0%, 100% { transform: rotate(0deg) translateY(0px); }
      50% { transform: rotate(2.1deg) translateY(-4px); }
    }
    .flower-sway {
      animation: flowerSway 7.2s ease-in-out infinite;
      transform-origin: bottom center;
    }
    .flower-sway-slow {
      animation: flowerSway 9.4s ease-in-out infinite reverse;
      transform-origin: bottom center;
    }

    @keyframes birdGlide {
      0%, 100% { transform: translate(0px, 0px) rotate(-6deg); }
      50% { transform: translate(-8px, -10px) rotate(-2deg); }
    }
    .bird-glide {
      animation: birdGlide 7.8s ease-in-out infinite;
    }

    @keyframes butterflyFlutter {
      0%, 100% { transform: translate(0px, 0px) scale(1) rotate(0deg); }
      50% { transform: translate(6px, -8px) scale(1.03) rotate(3deg); }
    }
    .butterfly-flutter {
      animation: butterflyFlutter 6.2s ease-in-out infinite;
    }

    @keyframes bookFloat {
      0%, 100% { transform: translateY(0px) rotate(-4deg); }
      50% { transform: translateY(-7px) rotate(-1.5deg); }
    }
    .book-float {
      animation: bookFloat 6.8s ease-in-out infinite;
    }

    @keyframes cassetteBob {
      0%, 100% { transform: translateY(0px) rotate(-11deg); }
      50% { transform: translateY(-8px) rotate(-7deg); }
    }
    .cassette-float {
      animation: cassetteBob 6.6s ease-in-out infinite;
    }

    @keyframes starSlowSpin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    .music-star-spin {
      animation: starSlowSpin 22s linear infinite;
    }

    @keyframes pixelFlicker {
      0%, 72%, 100% { opacity: 1; transform: translate(0, 0); }
      76% { opacity: 0.25; transform: translate(1px, -1px); }
      82% { opacity: 0.9; transform: translate(-1px, 1px); }
      88% { opacity: 0.45; transform: translate(0, 0); }
    }
    .pixel-glitch-loop {
      animation: pixelFlicker 4.4s steps(2, end) infinite;
    }

    @keyframes headphonesBass {
      0%, 100% { transform: scale(1); }
      14% { transform: scale(1.028); }
      28% { transform: scale(1); }
      42% { transform: scale(1.018); }
      56% { transform: scale(1); }
    }
    .headphones-bass-loop {
      animation: headphonesBass 2.4s ease-in-out infinite;
    }

    @keyframes scrollWheelDot {
      0% { transform: translateY(0px); opacity: 1; }
      70% { transform: translateY(8px); opacity: 0.25; }
      100% { transform: translateY(0px); opacity: 1; }
    }
    .scroll-wheel-dot {
      animation: scrollWheelDot 1.9s ease-in-out infinite;
    }

    @keyframes cursorRingSpin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    .music-cursor-ring-spin {
      animation: cursorRingSpin 10s linear infinite;
    }
    .music-cursor-ring-fast {
      animation: cursorRingSpin 3.2s linear infinite;
    }

    @keyframes noteFloatUp {
      0% { transform: translate(-50%, -50%) scale(0.75) rotate(0deg); opacity: 0.95; }
      100% { transform: translate(calc(-50% + var(--dx, 14px)), calc(-50% - 34px)) scale(1.15) rotate(var(--rot, 14deg)); opacity: 0; }
    }
    .cursor-note-particle {
      animation: noteFloatUp 0.9s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    /* Hide scrollbars cleanly for horizontal mobile rails */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* Vertical Japanese typography */
    .writing-vertical {
      writing-mode: vertical-rl;
      text-orientation: upright;
    }

    /* Accessible keyboard focus outlines */
    :focus-visible {
      outline: 2px solid #3E5C7E;
      outline-offset: 3px;
    }

    /* Respect prefers-reduced-motion */
    @media (prefers-reduced-motion: reduce) {
      .flower-sway,
      .flower-sway-slow,
      .bird-glide,
      .butterfly-flutter,
      .book-float,
      .cassette-float,
      .music-star-spin,
      .pixel-glitch-loop,
      .headphones-bass-loop,
      .scroll-wheel-dot,
      .music-cursor-ring-spin,
      .music-cursor-ring-fast {
        animation: none !important;
      }
      .void-hover-interactive:hover,
      .polaroid-group:hover .polaroid-pill {
        animation: none !important;
      }
    }
  </style>
</head>
<body class="paper-surface text-[#141210] selection:bg-[#141210] selection:text-[#EDE7DA]">
  <div id="root"></div>

  <script type="text/babel">
    <?php include __DIR__ . '/public/js/folder-studio.js'; ?>
    const { useState, useEffect, useRef, useCallback, useMemo } = React;
    const Folder = window.VoidFolder;
    const AudioSnippetModal = window.VoidAudioSnippetModal;
    const ProjectFolderDock = window.VoidProjectFolderDock;

    function resolveAssetUrl(url) {
      if (!url) return '';
      if (/^(https?:|\/\/|data:)/i.test(url)) return url;
      if (url.startsWith('/')) {
        url = url.replace(/^\/+/, '');
      }
      return url;
    }

    /* =========================================================================
       REACT BITS: TargetCursor Component
       ========================================================================= */
    const CORNER_ANGLES = [0, 90, 180, 270];
    const QUARTER = Math.PI / 2;

    const spring = (state, goal, stiffness, damping, dt) => {
      const steps = 2;
      const h = dt / steps;
      for (let i = 0; i < steps; i++) {
        state.v += (stiffness * (goal - state.x) - damping * state.v) * h;
        state.x += state.v * h;
      }
    };

    const resolveElement = value => (value && 'current' in value ? value.current : value) || null;

    function TargetCursor({
      targetSelector = '.cursor-target',
      cursorColor = '#EDE7DA',
      cursorColorOnTarget,
      spinDuration = 2,
      hoverDuration = 0.2,
      parallaxOn = true,
      hideDefaultCursor = true,
      size = 36,
      cornerSize = 12,
      thickness = 2.5,
      padding = 6,
      matchRadius = true,
      dotSize = 4,
      blendMode = 'difference',
      showLabel = true,
      clickEffect = true,
      container
    }) {
      const [host, setHost] = useState(null);
      const rootRef = useRef(null);
      const cornerRefs = useRef([]);
      const dotRef = useRef(null);
      const labelRef = useRef(null);
      const wakeRef = useRef(null);
      const settings = {
        targetSelector,
        cursorColor,
        cursorColorOnTarget,
        spinDuration,
        hoverDuration: Math.max(0.05, hoverDuration),
        parallaxOn,
        hideDefaultCursor,
        size: Math.max(4, size),
        cornerSize: Math.max(2, cornerSize),
        thickness: Math.max(0.5, thickness),
        padding,
        matchRadius,
        dotSize: Math.max(0, dotSize),
        showLabel,
        clickEffect,
        container
      };
      const settingsRef = useRef(settings);
      settingsRef.current = settings;

      useEffect(() => {
        const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
        const update = () => setHost(finePointer.matches ? document.body : null);
        update();
        finePointer.addEventListener('change', update);
        return () => finePointer.removeEventListener('change', update);
      }, []);

      useEffect(() => {
        const root = rootRef.current;
        if (!host || !root) return undefined;

        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const style = document.createElement('style');
        document.head.appendChild(style);

        const pointer = { x: 0, y: 0, inside: false, seen: false, pressed: false };
        const centerX = { x: 0, v: 0 };
        const centerY = { x: 0, v: 0 };
        const halfW = { x: 0, v: 0 };
        const halfH = { x: 0, v: 0 };
        const radius = { x: 0, v: 0 };
        const turn = { x: 0, v: 0 };
        const lock = { x: 0, v: 0 };
        const press = { x: 0, v: 0 };
        const presence = { x: 0, v: 0 };
        let target = null;
        let targetRadius = 0;
        let quarter = 0;
        let spinSpeed = 0;
        let raf = 0;
        let scrollRaf = 0;
        let last = 0;
        let lastLabel = '';
        let lastColor = '';
        let lastScope = null;
        let lastMoveTarget = null;
        let lastCornerSize = -1;
        let lastThickness = -1;

        const scopeOf = () => resolveElement(settingsRef.current.container);

        const hideCursor = () => {
          const s = settingsRef.current;
          const scope = scopeOf();
          if (scope !== lastScope) {
            lastScope?.removeAttribute('data-target-cursor-scope');
            scope?.setAttribute('data-target-cursor-scope', '');
            lastScope = scope;
          }
          const selector = scope ? '[data-target-cursor-scope]' : 'html';
          const rule =
            s.hideDefaultCursor && pointer.inside ? `${selector}, ${selector} * { cursor: none !important; }` : '';
          if (style.textContent !== rule) style.textContent = rule;
        };

        const findTarget = (x, y, element) => {
          const s = settingsRef.current;
          const node = element || document.elementFromPoint(x, y);
          const found = node?.closest?.(s.targetSelector) || null;
          const scope = scopeOf();
          if (found && scope && !scope.contains(found)) return null;
          return found;
        };

        const lockOn = next => {
          if (next === target) return;
          target = next;
          if (target) {
            const angle = turn.x;
            quarter = Math.round(angle / QUARTER);
            const corner = parseFloat(getComputedStyle(target).borderTopLeftRadius) || 0;
            targetRadius = corner;
            spinSpeed = 0;
          }
        };

        const frame = now => {
          raf = 0;
          const s = settingsRef.current;
          const dt = last ? Math.min(0.05, (now - last) / 1000) : 1 / 60;
          last = now;

          if (target && !target.isConnected) lockOn(null);
          const rect = target ? target.getBoundingClientRect() : null;
          const locked = !!rect && pointer.inside;
          const snapW = 4.6 / s.hoverDuration;
          const snapK = snapW * snapW;
          const snapC = 2 * 0.62 * snapW;

          spring(presence, pointer.inside ? 1 : 0, 260, 32, dt);
          spring(lock, locked ? 1 : 0, snapK, 2 * snapW, dt);
          spring(press, pointer.pressed && s.clickEffect ? 1 : 0, 900, 40, dt);

          let goalX = pointer.x;
          let goalY = pointer.y;
          let goalW = s.size / 2;
          let goalH = s.size / 2;
          let goalR = Math.min(2, s.cornerSize / 4);
          if (locked) {
            const cx = rect.left + rect.width / 2;
            const cy = rect.top + rect.height / 2;
            const lean = s.parallaxOn ? 0.08 : 0;
            goalX = cx + (pointer.x - cx) * lean;
            goalY = cy + (pointer.y - cy) * lean;
            const odd = Math.abs(quarter) % 2 === 1;
            const w = rect.width / 2 + s.padding;
            const h = rect.height / 2 + s.padding;
            goalW = odd ? h : w;
            goalH = odd ? w : h;
            goalR = s.matchRadius ? Math.min(targetRadius + Math.max(0, s.padding), s.cornerSize) : 0;
          }

          if (!pointer.inside && presence.x < 0.01) pointer.seen = false;
          if (!pointer.seen) {
            centerX.x = goalX;
            centerY.x = goalY;
            halfW.x = goalW;
            halfH.x = goalH;
            pointer.seen = pointer.inside;
          }

          const follow = locked ? snapK : 2200;
          const followC = locked ? snapC : 2 * Math.sqrt(2200);
          spring(centerX, goalX, follow, followC, dt);
          spring(centerY, goalY, follow, followC, dt);
          spring(halfW, goalW, snapK, snapC, dt);
          spring(halfH, goalH, snapK, snapC, dt);
          spring(radius, goalR, snapK, 2 * snapW, dt);

          if (locked) {
            spring(turn, quarter * QUARTER, snapK, snapC, dt);
          } else {
            const speed = s.spinDuration > 0 && !reduce ? (Math.PI * 2) / s.spinDuration : 0;
            spinSpeed += (speed - spinSpeed) * (1 - Math.exp(-dt * 3));
            turn.v = spinSpeed;
            turn.x += spinSpeed * dt;
          }

          const squeeze = 1 - press.x * 0.14;
          const cos = Math.cos(turn.x);
          const sin = Math.sin(turn.x);
          const w = Math.max(0, halfW.x) * squeeze;
          const h = Math.max(0, halfH.x) * squeeze;
          const local = [
            [-w, -h],
            [w, -h],
            [w, h],
            [-w, h]
          ];
          const r = Math.max(0, Math.min(radius.x, s.cornerSize));
          const degrees = (turn.x * 180) / Math.PI;
          let left = Infinity;
          let top = Infinity;
          const sizeChanged = s.cornerSize !== lastCornerSize || s.thickness !== lastThickness;
          if (sizeChanged) {
            lastCornerSize = s.cornerSize;
            lastThickness = s.thickness;
          }
          cornerRefs.current.forEach((corner, i) => {
            if (!corner) return;
            const [lx, ly] = local[i];
            const x = centerX.x + lx * cos - ly * sin;
            const y = centerY.x + lx * sin + ly * cos;
            left = Math.min(left, x);
            top = Math.min(top, y);
            corner.style.transform = `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, 0) rotate(${(CORNER_ANGLES[i] + degrees).toFixed(2)}deg)`;
            if (sizeChanged) {
              corner.style.width = `${s.cornerSize}px`;
              corner.style.height = `${s.cornerSize}px`;
              corner.style.borderWidth = `${s.thickness}px 0 0 ${s.thickness}px`;
            }
            corner.style.borderTopLeftRadius = `${r.toFixed(2)}px`;
          });

          const dot = dotRef.current;
          if (dot) {
            const dotScale = 1 - press.x * 0.35;
            dot.style.transform = `translate3d(${pointer.x}px, ${pointer.y}px, 0) translate(-50%, -50%) scale(${dotScale.toFixed(3)})`;
            dot.style.width = `${s.dotSize}px`;
            dot.style.height = `${s.dotSize}px`;
          }

          const label = labelRef.current;
          if (label) {
            const text = s.showLabel && target ? target.getAttribute('data-cursor-label') || '' : '';
            if (text && text !== lastLabel) label.textContent = text;
            if (text) lastLabel = text;
            const shown = text ? Math.max(0, Math.min(1, lock.x)) : 0;
            label.style.transform = `translate3d(${left.toFixed(2)}px, ${(top - 8).toFixed(2)}px, 0) translateY(-100%) translateY(${((1 - shown) * 4).toFixed(2)}px)`;
            label.style.opacity = shown.toFixed(3);
          }

          const mix = s.cursorColorOnTarget ? Math.round(Math.max(0, Math.min(1, lock.x)) * 100) : 0;
          const color = mix ? `color-mix(in oklab, ${s.cursorColorOnTarget} ${mix}%, ${s.cursorColor})` : s.cursorColor;
          if (color !== lastColor) {
            root.style.setProperty('--target-cursor-color', color);
            lastColor = color;
          }
          root.style.opacity = Math.max(0, Math.min(1, presence.x)).toFixed(3);

          const resting =
            !pointer.inside &&
            presence.x < 0.002 &&
            Math.abs(presence.v) < 0.002 &&
            Math.abs(lock.x) < 0.002 &&
            Math.abs(press.x) < 0.002;
          const spinning = pointer.inside && !locked && spinSpeed > 0.0001;
          const moving =
            Math.abs(centerX.v) + Math.abs(centerY.v) + Math.abs(halfW.v) + Math.abs(halfH.v) + Math.abs(turn.v) > 0.01 ||
            Math.abs(goalX - centerX.x) + Math.abs(goalY - centerY.x) > 0.05 ||
            Math.abs(lock.v) + Math.abs(press.v) + Math.abs(presence.v) > 0.001;
          if (!resting && (spinning || moving || locked)) raf = requestAnimationFrame(frame);
          else last = 0;
        };

        const wake = () => {
          if (!raf) raf = requestAnimationFrame(frame);
        };
        wakeRef.current = () => {
          hideCursor();
          wake();
        };

        const insideScope = (x, y, element) => {
          const scope = scopeOf();
          if (!scope) return true;
          if (element && scope.contains(element)) return true;
          const box = scope.getBoundingClientRect();
          return x >= box.left && x <= box.right && y >= box.top && y <= box.bottom;
        };

        const onPointerMove = event => {
          if (event.pointerType && event.pointerType !== 'mouse') return;
          pointer.x = event.clientX;
          pointer.y = event.clientY;
          const evTarget = event.target;
          const inside = insideScope(pointer.x, pointer.y, evTarget);
          if (inside !== pointer.inside) {
            pointer.inside = inside;
            hideCursor();
            lastMoveTarget = null;
          }
          if (!inside) {
            lockOn(null);
            lastMoveTarget = null;
          } else if (evTarget !== lastMoveTarget) {
            lastMoveTarget = evTarget;
            lockOn(findTarget(pointer.x, pointer.y, evTarget));
          }
          wake();
        };

        const onPointerDown = event => {
          if (event.pointerType && event.pointerType !== 'mouse') return;
          pointer.pressed = true;
          wake();
        };

        const onPointerUp = () => {
          pointer.pressed = false;
          wake();
        };

        const onLeave = () => {
          pointer.inside = false;
          pointer.pressed = false;
          lastMoveTarget = null;
          lockOn(null);
          hideCursor();
          wake();
        };

        const onScroll = () => {
          if (!pointer.inside || scrollRaf) return;
          scrollRaf = requestAnimationFrame(() => {
            scrollRaf = 0;
            if (!pointer.inside) return;
            lastMoveTarget = null;
            lockOn(findTarget(pointer.x, pointer.y));
            wake();
          });
        };

        window.addEventListener('pointermove', onPointerMove, { passive: true });
        window.addEventListener('pointerdown', onPointerDown, { passive: true });
        window.addEventListener('pointerup', onPointerUp, { passive: true });
        window.addEventListener('scroll', onScroll, { passive: true, capture: true });
        document.documentElement.addEventListener('pointerleave', onLeave);
        window.addEventListener('blur', onLeave);

        return () => {
          cancelAnimationFrame(raf);
          if (scrollRaf) cancelAnimationFrame(scrollRaf);
          wakeRef.current = null;
          window.removeEventListener('pointermove', onPointerMove);
          window.removeEventListener('pointerdown', onPointerDown);
          window.removeEventListener('pointerup', onPointerUp);
          window.removeEventListener('scroll', onScroll, { capture: true });
          document.documentElement.removeEventListener('pointerleave', onLeave);
          window.removeEventListener('blur', onLeave);
          lastScope?.removeAttribute('data-target-cursor-scope');
          style.remove();
        };
      }, [host]);

      useEffect(() => {
        wakeRef.current?.();
      });

      if (!host) return null;

      return ReactDOM.createPortal(
        <div ref={rootRef} className="target-cursor" style={{ mixBlendMode: blendMode, opacity: 0 }} aria-hidden="true">
          {CORNER_ANGLES.map((angle, i) => (
            <span
              key={angle}
              ref={element => {
                cornerRefs.current[i] = element;
              }}
              className="target-cursor__corner"
            />
          ))}
          <span ref={dotRef} className="target-cursor__dot" />
          <span ref={labelRef} className="target-cursor__label" />
        </div>,
        host
      );
    }

    /* =========================================================================
       REACT BITS: TiltedCard Component
       ========================================================================= */
    const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

    const parseColor = value => {
      const text = String(value || '').trim();
      const hex = /^#?([0-9a-f]{3}|[0-9a-f]{6})$/i.exec(text);
      if (hex) {
        const full = hex[1].length === 3 ? hex[1].replace(/./g, digit => digit + digit) : hex[1];
        return [0, 2, 4].map(index => parseInt(full.slice(index, index + 2), 16));
      }
      const rgb = /^rgba?\(\s*([\d.]+)[\s,]+([\d.]+)[\s,]+([\d.]+)/i.exec(text);
      if (rgb) return [rgb[1], rgb[2], rgb[3]].map(channel => clamp(Math.round(parseFloat(channel)), 0, 255));
      return null;
    };

    const toLength = value => (typeof value === 'number' ? `${value}px` : value);

    const smoother = t => {
      const x = clamp(t, 0, 1);
      return x * x * x * (x * (x * 6 - 15) + 10);
    };

    const stepPhysics = (state, key, target, omega, zeta, dt) => {
      const velocity = `v${key}`;
      const steps = Math.max(1, Math.ceil(dt / (1 / 240)));
      const h = dt / steps;
      for (let index = 0; index < steps; index++) {
        state[velocity] += (omega * omega * (target - state[key]) - 2 * zeta * omega * state[velocity]) * h;
        state[key] += state[velocity] * h;
      }
      const settled = Math.abs(target - state[key]) < 0.001 && Math.abs(state[velocity]) < 0.01;
      if (settled) {
        state[key] = target;
        state[velocity] = 0;
      }
      return !settled;
    };

    function TiltedCardLayer({ depth = 20, className = '', style, children }) {
      return (
        <div className={`tilted-card__layer ${className}`.trim()} style={{ '--tc-depth': depth, ...style }}>
          {children}
        </div>
      );
    }

    function TiltedCard({
      imageSrc,
      altText = 'Tilted card image',
      captionText = '',
      containerHeight = '300px',
      containerWidth = '100%',
      imageHeight = '300px',
      imageWidth = '100%',
      scaleOnHover = 1.04,
      rotateAmplitude = 12,
      showTooltip = true,
      overlayContent = null,
      displayOverlayContent = false,
      overlayDepth = 30,
      children,
      backContent = null,
      flipOnClick = true,
      flipped,
      onFlip,
      radius = 16,
      perspective = 800,
      smoothness = 0.6,
      glare = 0.45,
      glareColor = '#ffffff',
      shadow = 0.5,
      parallax = 1,
      axis = 'both',
      reverse = false,
      trackWindow = false,
      floating = false,
      gyroscope = true,
      intro = true,
      introDelay = 0,
      onClick,
      className = '',
      style
    }) {
      const figureRef = useRef(null);
      const bodyRef = useRef(null);
      const shadowRef = useRef(null);
      const captionRef = useRef(null);
      const wakeRef = useRef(null);
      const flipRef = useRef(null);
      const [innerFlipped, setInnerFlipped] = useState(false);
      const controlled = typeof flipped === 'boolean';
      const isFlipped = Boolean(backContent) && (controlled ? flipped : innerFlipped);

      const settings = {
        amplitude: clamp(rotateAmplitude, 0, 60),
        scale: Math.max(0.5, scaleOnHover),
        smoothness: clamp(smoothness, 0, 1),
        glare: clamp(glare, 0, 1),
        shadow: clamp(shadow, 0, 1),
        parallax: Math.max(0, parallax),
        axis,
        reverse,
        trackWindow,
        floating,
        gyroscope,
        intro,
        introDelay: Math.max(0, introDelay),
        flippable: Boolean(backContent) && flipOnClick,
        interactive: (Boolean(backContent) && flipOnClick) || Boolean(onClick),
        flipped: isFlipped,
        tooltip: showTooltip && Boolean(captionText)
      };
      const settingsRef = useRef(settings);
      settingsRef.current = settings;

      useEffect(() => {
        wakeRef.current?.();
      });

      useEffect(() => {
        const figure = figureRef.current;
        const body = bodyRef.current;
        if (!figure || !body) return;

        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const coarse = window.matchMedia('(hover: none) and (pointer: coarse)').matches;
        const state = {
          rx: 0, vrx: 0, ry: 0, vry: 0, lift: 0, vlift: 0,
          ty: 0, vty: 0, flip: 0, vflip: 0, press: 0, vpress: 0,
          tip: 0, vtip: 0, shown: 0, vshown: 0, tx: 0, tyy: 0
        };
        const pointer = { nx: 0, ny: 0, x: 0, y: 0, inside: false, mouse: false, seen: false, vx: 0, last: 0 };
        const press = { active: false, x: 0, y: 0, moved: false };
        const tilt = { gamma: 0, beta: 0, baseGamma: 0, baseBeta: 0, calibrated: false, live: false, asked: false };
        let opacity = 1;
        let entered = true;
        let introAt = -1;
        let time = Math.random() * 10;
        let focused = false;
        let raf = 0;
        let last = 0;
        let visible = true;

        if (settingsRef.current.intro && !reduced) {
          entered = false;
          opacity = 0;
          state.rx = 16;
          state.ty = 22;
          body.style.opacity = '0';
        }

        const frame = now => {
          raf = 0;
          const s = settingsRef.current;
          const dt = clamp((now - last) / 1000 || 0, 0, 0.05);
          last = Math.max(last, now);
          time += dt;
          let busy = false;

          const amplitude = s.amplitude * (reduced ? 0.3 : 1);
          const hovering = pointer.inside && pointer.mouse;
          const touching = press.active && !pointer.mouse;
          const following = hovering || touching || (s.trackWindow && pointer.seen && !coarse);
          let inputX = 0;
          let inputY = 0;
          if (following) {
            inputX = pointer.nx;
            inputY = pointer.ny;
          } else if (tilt.live && s.gyroscope) {
            inputX = clamp((tilt.gamma - tilt.baseGamma) / 22, -1, 1);
            inputY = clamp((tilt.beta - tilt.baseBeta) / 22, -1, 1);
            tilt.baseGamma += (tilt.gamma - tilt.baseGamma) * (1 - Math.exp(-dt / 4));
            tilt.baseBeta += (tilt.beta - tilt.baseBeta) * (1 - Math.exp(-dt / 4));
            busy = true;
          } else if (s.floating && !reduced && visible) {
            inputX = 0.32 * Math.sin(time * 0.62) + 0.12 * Math.sin(time * 1.31 + 0.7);
            inputY = 0.26 * Math.sin(time * 0.83 + 1.9) + 0.1 * Math.sin(time * 1.57);
            busy = true;
          }
          if (s.axis === 'horizontal') inputY = 0;
          if (s.axis === 'vertical') inputX = 0;
          const sign = s.reverse ? -1 : 1;

          const omega = 20 * Math.pow(0.2, s.smoothness);
          const zeta = 1 - 0.3 * s.smoothness;
          const lifted = hovering || touching || focused ? 1 : 0;

          if (!entered && introAt >= 0 && now >= introAt) entered = true;
          const settle = entered || !s.intro || reduced;
          busy = stepPhysics(state, 'rx', settle ? -inputY * amplitude * sign : state.rx, omega, zeta, dt) || busy;
          busy = stepPhysics(state, 'ry', settle ? inputX * amplitude * sign : state.ry, omega, zeta, dt) || busy;
          busy = stepPhysics(
            state, 'ty',
            settle ? (s.floating && !following && !reduced ? -4 * Math.sin(time * 0.9) : 0) : state.ty,
            omega * 0.8, 0.9, dt
          ) || busy;
          busy = stepPhysics(state, 'lift', lifted, omega, Math.max(zeta, 0.85), dt) || busy;
          busy = stepPhysics(state, 'flip', s.flipped ? 180 : 0, reduced ? 18 : 9.5, 0.72, dt) || busy;
          busy = stepPhysics(state, 'press', press.active && !press.moved && s.interactive ? 1 : 0, 26, 0.8, dt) || busy;
          if (settle && opacity < 1) {
            opacity = Math.min(1, opacity + dt / 0.45);
            busy = true;
          }

          const active = lifted > 0 || state.lift > 0.01;
          if (active !== figure.hasAttribute('data-active')) figure.toggleAttribute('data-active', active);
          const scale = 1 + (s.scale - 1) * state.lift - 0.02 * state.press;
          const rx = state.rx;
          const ry = state.ry;
          const radiansX = (rx * Math.PI) / 180;
          const radiansY = (ry * Math.PI) / 180;
          body.style.transform = `translate3d(0, ${state.ty.toFixed(2)}px, 0) rotateX(${rx.toFixed(3)}deg) rotateY(${(ry + state.flip).toFixed(3)}deg) scale(${scale.toFixed(4)})`;
          body.style.opacity = opacity < 1 ? smoother(opacity).toFixed(3) : '';
          body.style.setProperty('--tc-px', (Math.sin(radiansY) * s.parallax).toFixed(4));
          body.style.setProperty('--tc-py', (-Math.sin(radiansX) * s.parallax).toFixed(4));
          const reach = Math.max(amplitude, 1);
          body.style.setProperty('--tc-gx', `${(50 + (ry / reach) * 46).toFixed(2)}%`);
          body.style.setProperty('--tc-gy', `${(50 - (rx / reach) * 46).toFixed(2)}%`);
          const energy = Math.min(1, Math.hypot(rx, ry) / reach);
          body.style.setProperty(
            '--tc-glare',
            (s.glare * Math.max(state.lift, energy) * (opacity < 1 ? smoother(opacity) : 1)).toFixed(3)
          );

          const shade = shadowRef.current;
          if (shade) {
            const strength = s.shadow * (opacity < 1 ? smoother(opacity) : 1);
            const offsetX = -Math.sin(radiansY) * 22;
            const offsetY = 12 + 16 * state.lift + Math.sin(radiansX) * 18 - state.ty * 0.6;
            const turn = Math.abs(Math.cos((state.flip * Math.PI) / 180));
            const spread = scale * (0.86 + 0.04 * state.lift);
            shade.style.transform = `translate3d(${offsetX.toFixed(2)}px, ${offsetY.toFixed(2)}px, 0) scale(${(spread * (0.2 + 0.8 * turn)).toFixed(4)}, ${spread.toFixed(4)})`;
            shade.style.opacity = (strength * (0.6 + 0.4 * state.lift)).toFixed(3);
          }

          const caption = captionRef.current;
          if (caption) {
            const show = s.tooltip && hovering ? 1 : 0;
            busy = stepPhysics(state, 'shown', show, 16, 1, dt) || busy;
            const follow = 1 - Math.exp(-dt / 0.035);
            state.tx += (pointer.x - state.tx) * follow;
            state.tyy += (pointer.y - state.tyy) * follow;
            if (Math.abs(pointer.x - state.tx) + Math.abs(pointer.y - state.tyy) > 0.2) busy = true;
            pointer.vx *= Math.exp(-dt / 0.08);
            busy = stepPhysics(state, 'tip', reduced ? 0 : clamp(-pointer.vx * 0.012, -14, 14), 18, 0.62, dt) || busy;
            caption.style.transform = `translate3d(${state.tx.toFixed(1)}px, ${state.tyy.toFixed(1)}px, 0) rotate(${state.tip.toFixed(2)}deg)`;
            caption.style.opacity = Math.max(0, state.shown).toFixed(3);
          }

          if ((busy || (!settle && s.intro)) && visible) raf = requestAnimationFrame(frame);
        };

        const wake = () => {
          if (raf || !visible) return;
          last = performance.now();
          raf = requestAnimationFrame(frame);
        };
        wakeRef.current = wake;

        const locate = (event, area) => {
          const rect = area.getBoundingClientRect();
          return {
            x: event.clientX - rect.left,
            y: event.clientY - rect.top,
            width: Math.max(1, rect.width),
            height: Math.max(1, rect.height)
          };
        };

        const track = event => {
          const spot = locate(event, figure);
          const now = performance.now();
          const elapsed = Math.max(8, now - (pointer.last || now - 16)) / 1000;
          const dx = spot.x - pointer.x;
          pointer.vx += (dx / elapsed - pointer.vx) * 0.5;
          pointer.last = now;
          pointer.x = spot.x;
          pointer.y = spot.y;
          pointer.nx = clamp((spot.x - spot.width / 2) / (spot.width / 2), -1, 1);
          pointer.ny = clamp((spot.y - spot.height / 2) / (spot.height / 2), -1, 1);
        };

        const onEnter = event => {
          pointer.inside = true;
          pointer.mouse = event.pointerType === 'mouse' || event.pointerType === 'pen';
          const spot = locate(event, figure);
          pointer.x = spot.x;
          pointer.y = spot.y;
          state.tx = spot.x;
          state.tyy = spot.y;
          pointer.vx = 0;
          track(event);
          wake();
        };

        const onMove = event => {
          pointer.mouse = event.pointerType === 'mouse' || event.pointerType === 'pen';
          if (!pointer.inside && pointer.mouse) {
            pointer.inside = true;
            const spot = locate(event, figure);
            pointer.x = spot.x;
            pointer.y = spot.y;
            state.tx = spot.x;
            state.tyy = spot.y;
            pointer.vx = 0;
          }
          track(event);
          wake();
        };

        const onLeave = () => {
          pointer.inside = false;
          pointer.vx = 0;
          wake();
        };

        figure.addEventListener('pointerenter', onEnter);
        figure.addEventListener('pointermove', onMove);
        figure.addEventListener('pointerleave', onLeave);

        const observer = new IntersectionObserver(entries => {
          visible = entries.some(entry => entry.isIntersecting);
          if (visible && !entered && introAt < 0) introAt = performance.now() + settingsRef.current.introDelay;
          wake();
        });
        observer.observe(figure);
        wake();

        return () => {
          cancelAnimationFrame(raf);
          observer.disconnect();
          figure.removeEventListener('pointerenter', onEnter);
          figure.removeEventListener('pointermove', onMove);
          figure.removeEventListener('pointerleave', onLeave);
        };
      }, []);

      const flippable = Boolean(backContent) && flipOnClick;
      const interactive = flippable || Boolean(onClick);

      const toggle = (e) => {
        if (e && e.target && e.target.closest && e.target.closest('button, a, input, [role="button"]')) return;
        const next = !isFlipped;
        if (!controlled) setInnerFlipped(next);
        onFlip?.(next);
      };

      const corner = toLength(radius);
      const glareRgb = (parseColor(glareColor) || [255, 255, 255]).join(', ');
      const bleed = Math.ceil(Math.sin((clamp(rotateAmplitude, 0, 60) * Math.PI) / 180) * 24 * Math.max(0, parallax)) + 1;

      return (
        <figure
          ref={figureRef}
          className={`tilted-card ${className}`.trim()}
          style={{
            width: toLength(containerWidth),
            height: toLength(containerHeight),
            perspective: toLength(perspective),
            ...style
          }}
          data-interactive={interactive ? '' : undefined}
          data-flipped={isFlipped ? '' : undefined}
          tabIndex={interactive ? 0 : undefined}
          role={interactive ? 'button' : undefined}
          onClick={interactive ? (e) => toggle(e) : undefined}
        >
          <div
            ref={shadowRef}
            className="tilted-card__shadow"
            aria-hidden="true"
            style={{ width: toLength(imageWidth), height: toLength(imageHeight), borderRadius: corner }}
          />
          <div
            ref={bodyRef}
            className="tilted-card__body"
            style={{
              width: toLength(imageWidth),
              height: toLength(imageHeight),
              '--tc-radius': corner,
              '--tc-glare-rgb': glareRgb
            }}
          >
            <div className="tilted-card__face tilted-card__front">
              {imageSrc && (
                <img
                  className="tilted-card__img"
                  src={imageSrc}
                  alt={altText}
                  draggable={false}
                  style={{
                    '--tc-depth': -24,
                    top: `-${bleed}px`,
                    left: `-${bleed}px`,
                    width: `calc(100% + ${bleed * 2}px)`,
                    height: `calc(100% + ${bleed * 2}px)`
                  }}
                />
              )}
              {children}
              <span className="tilted-card__glare" aria-hidden="true" />
              <span className="tilted-card__rim" aria-hidden="true" />
              <span className="tilted-card__edge" aria-hidden="true" />
            </div>
            {backContent && (
              <div className="tilted-card__face tilted-card__back" aria-hidden={!isFlipped}>
                {backContent}
                <span className="tilted-card__glare" aria-hidden="true" />
                <span className="tilted-card__rim" aria-hidden="true" />
                <span className="tilted-card__edge" aria-hidden="true" />
              </div>
            )}
            {displayOverlayContent && overlayContent && (
              <div className="tilted-card__overlay" style={{ '--tc-lift': `${overlayDepth}px` }}>
                {overlayContent}
              </div>
            )}
          </div>
          {showTooltip && captionText && (
            <figcaption ref={captionRef} className="tilted-card__caption">
              {captionText}
            </figcaption>
          )}
        </figure>
      );
    }

    /* =========================================================================
       REACT BITS: SplitFlapText Component (Departure Board Animation)
       ========================================================================= */
    /* =========================================================================
       REACT BITS: SplitFlapText Component (Mechanical Departure Board)
       ========================================================================= */
    /* =========================================================================
       REACT BITS: SplitFlapText Component (Mechanical Departure Board)
       ========================================================================= */
    function SplitFlapText({
      text,
      words = [],
      fontSize = 28,
      tileColor = '#141210',
      textColor = '#EDE7DA',
      cycleDelay = 3200,
      stagger = 0.035,
      flipDuration = 0.075,
      loop = true,
      align = 'center',
      className = ''
    }) {
      const wordsList = words.length ? words : [text || 'TRENDING TRACKS'];
      const [wordIndex, setWordIndex] = useState(0);
      const currentTarget = wordsList[wordIndex] || '';

      const CHARSET = ' ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-/';
      const [displayedChars, setDisplayedChars] = useState(() => currentTarget.split(''));
      const [flippingIndices, setFlippingIndices] = useState(new Set());

      useEffect(() => {
        if (wordsList.length <= 1 && !loop) return undefined;
        const interval = setInterval(() => {
          setWordIndex((prev) => (prev + 1) % wordsList.length);
        }, cycleDelay);
        return () => clearInterval(interval);
      }, [wordsList.length, cycleDelay, loop]);

      useEffect(() => {
        const targetWord = wordsList[wordIndex] || '';
        const targetChars = targetWord.split('');
        const targetLen = targetChars.length;

        setDisplayedChars((prev) => {
          const next = [...prev];
          while (next.length < targetLen) next.push(' ');
          if (next.length > targetLen) next.length = targetLen;
          return next;
        });

        const activeTimeouts = [];

        for (let i = 0; i < targetLen; i++) {
          const targetChar = targetChars[i] || ' ';
          const delay = i * (stagger * 1000);
          const flips = 3 + Math.floor(Math.random() * 3);

          for (let f = 0; f <= flips; f++) {
            const flipDelay = delay + f * (flipDuration * 1000);
            const t = setTimeout(() => {
              setFlippingIndices((prev) => {
                const next = new Set(prev);
                next.add(i);
                return next;
              });

              setDisplayedChars((prev) => {
                const arr = [...prev];
                if (f === flips) {
                  arr[i] = targetChar;
                } else {
                  arr[i] = CHARSET[Math.floor(Math.random() * CHARSET.length)];
                }
                return arr;
              });

              setTimeout(() => {
                setFlippingIndices((prev) => {
                  const next = new Set(prev);
                  next.delete(i);
                  return next;
                });
              }, flipDuration * 1000);
            }, flipDelay);

            activeTimeouts.push(t);
          }
        }

        return () => activeTimeouts.forEach(clearTimeout);
      }, [wordIndex, wordsList]);

      const wordsArray = [];
      let currentWordChars = [];
      displayedChars.forEach((char, idx) => {
        if (char === ' ') {
          if (currentWordChars.length) {
            wordsArray.push(currentWordChars);
            currentWordChars = [];
          }
          wordsArray.push([{ char: ' ', index: idx, isSpace: true }]);
        } else {
          currentWordChars.push({ char, index: idx });
        }
      });
      if (currentWordChars.length) wordsArray.push(currentWordChars);

      return (
        <div
          className={`split-flap-container ${className}`.trim()}
          style={{
            fontSize: typeof fontSize === 'number' ? `${fontSize}px` : fontSize,
            '--sf-tile-color': tileColor,
            '--sf-text-color': textColor,
            justifyContent: align === 'center' ? 'center' : align === 'right' ? 'flex-end' : 'flex-start'
          }}
          aria-label={currentTarget}
        >
          {wordsArray.map((wordGroup, wIdx) => {
            if (wordGroup.length === 1 && wordGroup[0].isSpace) {
              return <span key={`space-${wIdx}`} className="split-flap-space" style={{ width: '0.42em' }} />;
            }
            return (
              <div key={`word-${wIdx}`} className="split-flap-word">
                {wordGroup.map(({ char, index }) => {
                  const isFlipping = flippingIndices.has(index);
                  return (
                    <div key={index} className="split-flap-tile">
                      <div className={`split-flap-half split-flap-top ${isFlipping ? 'split-flap-anim-down' : ''}`}>
                        <div className="split-flap-inner">
                          <span>{char}</span>
                        </div>
                      </div>
                      <div className={`split-flap-half split-flap-bottom ${isFlipping ? 'split-flap-anim-up' : ''}`}>
                        <div className="split-flap-inner">
                          <span>{char}</span>
                        </div>
                      </div>
                      <div className="split-flap-hinge" />
                    </div>
                  );
                })}
              </div>
            );
          })}
        </div>
      );
    }



    /* =========================================================================
       1. CENTRALIZED ANIMATION CONFIGURATION (Timings, Easings, Polaroids)
       ========================================================================= */
    const ANIM_CONFIG = {
      preloaderDurationMs: 1350,
      easings: {
        paperOut: 'power3.out',
        expoOut: 'expo.out',
        customPaper: 'paperEase',
      },
      vinylBaseDegPerSec: 16,
      vinylVelocityFactor: 0.45,
      polaroids: [
        { rotate: -6.5, y: 10 },
        { rotate: 2.2, y: -6 },
        { rotate: -3.2, y: 8 },
        { rotate: 5.5, y: -2 },
      ],
    };

    if (window.gsap) {
      if (window.ScrollTrigger) window.gsap.registerPlugin(window.ScrollTrigger);
      if (window.CustomEase) {
        window.gsap.registerPlugin(window.CustomEase);
        try {
          window.CustomEase.create('paperEase', 'M0,0 C0.18,0.88 0.32,1 1,1');
        } catch (e) {
          /* Fallback to power3.out */
        }
      }
    }

    function getPrefersReducedMotion() {
      return Boolean(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
    }

    function runAnime(targets, params) {
      if (!window.anime) return null;
      try {
        if (typeof window.anime === 'function') {
          return window.anime({ targets, ...params });
        }
        if (window.anime.animate) {
          return window.anime.animate(targets, params);
        }
      } catch (e) {
        return null;
      }
      return null;
    }

    /* =========================================================================
       2. DROP-IN ASSET MANIFEST (/public/assets/)
       Drop your transparent PNG/WebP files into /public/assets/ with these
       exact names and SmartAsset automatically swaps them in with zero edits.
       ========================================================================= */
    const DEFAULT_ASSET_MANIFEST = {
      brandLogo: 'public/assets/logo.png',
      bird: 'public/assets/bird.png',
      book: 'public/assets/book.png',
      butterfly: 'public/assets/butterfly.png',
      glowingBeach: 'public/assets/download (1).jpg',
      skyDoor: 'public/assets/download.jpg',
      flowerLotus: 'public/assets/flower lotus.png',
      flowerStamp: 'public/assets/flower stamp.png',
      flowerHalftone: 'public/assets/flower.png',
      guitar: 'public/assets/guiter.png',
      cassette: 'public/assets/old cacet.png',
      washiTape: 'public/assets/tape.png',
      musicStar: 'public/assets/music star.png',
      musicCord: 'public/assets/music cord.png',
      heroSkyRect: 'public/assets/download.jpg',
      heroVinyl: 'public/assets/musicdise.png',
      heroStatue: 'public/assets/marblestatue.png',
      flowersHeroMain: 'public/assets/flower.png',
      flowersHeroLeft: 'public/assets/flower.png',
      flowersCta: 'public/assets/flower lotus.png',
      trendingCD: 'public/assets/musicdise.png',
      trackSnooze: 'public/assets/download.jpg',
      trackDieForYou: 'public/assets/download (1).jpg',
      track505: 'public/assets/download.jpg',
      trackCasual: 'public/assets/download (1).jpg',
      templateNoirComic: 'public/assets/download (1).jpg',
      templateSumiInk: 'public/assets/flower.png',
      templateAstral: 'public/assets/download.jpg',
      templateBotanical: 'public/assets/flower stamp.png',
      ctaSkyline: 'public/assets/download.jpg',
      ctaHeadphones: 'public/assets/headphone.png',
    };

    const RAW_SITE_DATA = window.__SITE_DATA__ || {};
    const ASSET_MANIFEST = { ...DEFAULT_ASSET_MANIFEST };
    if (RAW_SITE_DATA.assets) {
      for (const [k, v] of Object.entries(RAW_SITE_DATA.assets)) {
        ASSET_MANIFEST[k] = resolveAssetUrl(v);
      }
    } else {
      for (const [k, v] of Object.entries(ASSET_MANIFEST)) {
        ASSET_MANIFEST[k] = resolveAssetUrl(v);
      }
    }

    const SITE_CONFIG = RAW_SITE_DATA.site || {
      name: 'VoidAntara',
      title: 'VoidAntara — Lyrics • Visuals • Vibes',
      description: 'Turn your favorite music into stunning lyric visuals. Velvet R&B cards, ambient tape saturation, and paper grain aesthetics.',
      logo: 'public/assets/logo.png',
      hankoSeal: ['音', '響'],
      hankoTitle: 'VoidAntara Archival Seal'
    };

    const HERO_CONFIG = RAW_SITE_DATA.hero || {
      tagline: 'LYRICS • VISUALS • VIBES',
      wordmarkVoid: 'Void',
      wordmarkLuma: 'Antara',
      pitch: 'Turn your favorite music into stunning lyric visuals.',
      buttonText: 'Explore Now',
      scrollText: 'Scroll Down',
      jpText: '音楽は、永遠に'
    };

    const CTA_CONFIG = RAW_SITE_DATA.cta || {
      heading: 'Create. Visualize.',
      headingItalic: 'Feel.',
      subtitle: 'Your music, your style — in one place.',
      buttonText: 'Start Creating'
    };

    const STUDIO_CONFIG = RAW_SITE_DATA.studio || {
      modalTitle: 'VoidAntara Lyric Studio',
      defaultLyric: 'Midnight echoes in ink & analog grain',
      defaultArtist: 'VOIDANTARA STUDIO • 33 RPM'
    };

    const FOOTER_CONFIG = RAW_SITE_DATA.footer || {
      rights: 'VOIDANTARA ARCHIVE • ALL RIGHTS RESERVED.',
      socials: [
        { name: 'Discord', icon: '◈', url: '#' },
        { name: 'Twitter / X', icon: '↗', url: '#' },
        { name: 'Instagram', icon: '◎', url: '#' },
        { name: 'GitHub', icon: '⌂', url: '#' }
      ]
    };

    /* =========================================================================
       3. TRACKS & TEMPLATES DATA
       ========================================================================= */
    const DEFAULT_TRACKS_DATA = [
      {
        id: 'snooze',
        serial: 'NO. 01',
        duration: '03:21',
        edition: 'VOIDANTARA PASS',
        vibeTag: 'HI-FI',
        stubNote: 'Velvet R&B lyric cards synced to warm analog tape saturation & subtle paper grain.',
        title: 'Snooze',
        artist: 'SZA',
        assetKey: 'trackSnooze',
        pngKey: 'trackSnooze',
        lyricSample: "I can't lose when I'm with you",
        bpm: 143,
        notes: [261.63, 329.63, 392.0, 493.88],
        bars: [34, 58, 42, 78, 92, 60, 48, 84, 70, 38, 66, 95, 54, 44, 76, 88, 52, 36, 68, 82, 46, 56, 32],
      },
      {
        id: 'die-for-you',
        serial: 'NO. 02',
        duration: '04:20',
        edition: 'MIDNIGHT REEL',
        vibeTag: 'LO-FI',
        stubNote: 'Starlit horizon glow, nocturnal synth pulses, and luminous serif captions in motion.',
        title: 'Die For You',
        artist: 'The Weeknd',
        assetKey: 'trackDieForYou',
        pngKey: 'glowingBeach',
        lyricSample: "Even though we're going through it",
        bpm: 134,
        notes: [220.0, 261.63, 329.63, 392.0],
        bars: [46, 72, 60, 38, 84, 96, 68, 52, 74, 88, 42, 58, 90, 76, 48, 64, 82, 50, 36, 70, 62, 44, 30],
      },
      {
        id: '505',
        serial: 'NO. 03',
        duration: '04:13',
        edition: 'NOIR ARCHIVE',
        vibeTag: '33 RPM',
        stubNote: 'High-contrast silver moonlit silhouette framed in kinetic ink typography and mono grain.',
        title: '505',
        artist: 'Arctic Monkeys',
        assetKey: 'track505',
        pngKey: 'track505',
        lyricSample: "I'm going back to 505",
        bpm: 140,
        notes: [293.66, 349.23, 440.0, 523.25],
        bars: [30, 48, 66, 82, 54, 72, 94, 80, 46, 62, 86, 98, 70, 52, 68, 88, 60, 42, 74, 58, 40, 50, 28],
      },
      {
        id: 'casual',
        serial: 'NO. 04',
        duration: '03:52',
        edition: 'SURREAL CUT',
        vibeTag: '60 FPS',
        stubNote: 'Halftone cobalt clouds, dreamy surreal portal framing, and handwritten cadence.',
        title: 'Casual',
        artist: 'Chappell Roan',
        assetKey: 'trackCasual',
        pngKey: 'skyDoor',
        lyricSample: 'Knee deep in the passenger seat',
        bpm: 130,
        notes: [246.94, 311.13, 369.99, 466.16],
        bars: [40, 64, 52, 86, 74, 44, 60, 90, 78, 50, 36, 72, 92, 66, 48, 80, 58, 42, 68, 84, 54, 38, 32],
      },
    ];

    const TRACKS_DATA = (RAW_SITE_DATA.tracks && RAW_SITE_DATA.tracks.length)
      ? RAW_SITE_DATA.tracks.map(t => ({
          ...t,
          image: resolveAssetUrl(t.image || (ASSET_MANIFEST[t.assetKey] || ''))
        }))
      : DEFAULT_TRACKS_DATA;

    const DEFAULT_TEMPLATES_DATA = [
      {
        id: 'liquid-glass-center',
        name: 'Liquid Glass Center',
        assetKey: 'glowingBeach',
        pngKey: 'glowingBeach',
        detail: 'glass-rim',
        desc: 'Cinematic glassmorphic synced lyric player with dynamic reactive orbs, 3D card tilt & kinetic typography.',
        tags: ['Glassmorphic', 'Kinetic 3D', 'LRC Synced', 'Studio Visualizer'],
        templateFile: 'template/Liquid_glass_center.html',
        approved: true,
      },
      {
        id: 'noir-comic',
        name: 'Noir Comic',
        assetKey: 'templateNoirComic',
        pngKey: 'templateNoirComic',
        detail: 'tape-top',
        desc: 'Manga-inspired ink shadows, stark grain, and expressive silhouette framing.',
        tags: ['Halftone', 'Monochrome', 'High Contrast'],
      },
      {
        id: 'sumi-ink',
        name: 'Sumi Ink',
        assetKey: 'templateSumiInk',
        pngKey: 'templateSumiInk',
        detail: 'none',
        desc: 'Delicate brush calligraphy blooms on warm handmade mulberry paper.',
        tags: ['Botanical', 'Calligraphy', 'Parchment'],
      },
      {
        id: 'astral',
        name: 'Astral',
        assetKey: 'templateAstral',
        pngKey: 'glowingBeach',
        detail: 'paperclip',
        desc: 'Luminous starlit figures, nocturnal tides, and celestial lyric trails.',
        tags: ['Celestial', 'Nocturnal', 'Luminous'],
      },
      {
        id: 'botanical',
        name: 'Botanical',
        assetKey: 'templateBotanical',
        pngKey: 'flowerStamp',
        detail: 'backing-strip',
        desc: 'Pressed monochrome magnolia postage prints with archival ink borders.',
        tags: ['Archival', 'Vintage Stamp', 'Etched'],
      },
    ];

    const TEMPLATES_DATA = ((RAW_SITE_DATA.templates && RAW_SITE_DATA.templates.length)
      ? RAW_SITE_DATA.templates.filter(t => t.approved !== false).map(t => ({
          ...t,
          image: resolveAssetUrl(t.image || (ASSET_MANIFEST[t.assetKey] || ''))
        }))
      : DEFAULT_TEMPLATES_DATA).filter(t => t.approved !== false);

    /* =========================================================================
       4. WEB AUDIO / TONE.JS AMBIENT TRACK PREVIEW SYNTH (No External URLs)
       ========================================================================= */
    const ambientSynth = (() => {
      let timerId = null;
      let synth = null;
      let step = 0;

      const stop = () => {
        if (timerId) {
          clearInterval(timerId);
          timerId = null;
        }
        if (synth) {
          try {
            synth.releaseAll();
          } catch (e) {}
        }
      };

      const start = async (track) => {
        stop();
        if (!window.Tone) return;
        try {
          await window.Tone.start();
          if (!synth) {
            const reverb = new window.Tone.Reverb({ decay: 2.8, wet: 0.4 }).toDestination();
            synth = new window.Tone.PolySynth(window.Tone.Synth, {
              oscillator: { type: 'triangle' },
              envelope: { attack: 0.08, decay: 0.4, sustain: 0.25, release: 1.2 },
            }).connect(reverb);
            synth.volume.value = -16;
          }
          const notes = track.notes || [261.63, 329.63, 392.0, 493.88];
          const beatMs = Math.max(260, Math.round((60 / (track.bpm || 120)) * 1000));
          step = 0;
          const playStep = () => {
            if (!synth) return;
            const n = notes[step % notes.length];
            const chord = step % 4 === 0 ? [n, notes[(step + 2) % notes.length]] : [n];
            try {
              synth.triggerAttackRelease(chord, '4n');
            } catch (e) {}
            step += 1;
          };
          playStep();
          timerId = setInterval(playStep, beatMs);
        } catch (e) {
          /* Ignore audio context restriction errors gracefully */
        }
      };

      return { start, stop };
    })();

    /* =========================================================================
       5. LENIS SMOOTH SCROLL HOOK (Synced to GSAP Ticker & Scroll Velocity)
       ========================================================================= */
    function useLenis(onVelocity) {
      const lenisRef = useRef(null);

      useEffect(() => {
        if (getPrefersReducedMotion() || !window.Lenis) return undefined;

        const lenis = new window.Lenis({
          duration: 0.9,
          easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
          smoothWheel: true,
          wheelMultiplier: 1.05,
          touchMultiplier: 1.5,
        });
        lenisRef.current = lenis;

        lenis.on('scroll', (e) => {
          if (window.ScrollTrigger) window.ScrollTrigger.update();
          if (onVelocity) onVelocity(e.velocity || 0);
        });

        let rafId = null;
        const gsapTick = (time) => lenis.raf(time * 1000);

        if (window.gsap) {
          window.gsap.ticker.add(gsapTick);
          window.gsap.ticker.lagSmoothing(0);
        } else {
          const loop = (t) => {
            lenis.raf(t);
            rafId = requestAnimationFrame(loop);
          };
          rafId = requestAnimationFrame(loop);
        }

        return () => {
          if (window.gsap) window.gsap.ticker.remove(gsapTick);
          if (rafId) cancelAnimationFrame(rafId);
          lenis.destroy();
          lenisRef.current = null;
        };
      }, [onVelocity]);

      return lenisRef;
    }

    /* =========================================================================
       6. PROCEDURAL HIGH-DPI RASTER PNG ENGINE (image/png Blob Generator)
       Renders richly shaded raster PNG stand-ins matched to your uploaded
       assets until local /public/assets/ files are placed on disk.
       ========================================================================= */
    function createRng(seed) {
      let s = seed >>> 0;
      return () => {
        s = (s * 1664525 + 1013904223) >>> 0;
        return s / 4294967296;
      };
    }

    function drawRoundRect(ctx, x, y, w, h, r) {
      const radius = Math.min(r, w / 2, h / 2);
      ctx.beginPath();
      ctx.moveTo(x + radius, y);
      ctx.arcTo(x + w, y, x + w, y + h, radius);
      ctx.arcTo(x + w, y + h, x, y + h, radius);
      ctx.arcTo(x, y + h, x, y, radius);
      ctx.arcTo(x, y, x + w, y, radius);
      ctx.closePath();
    }

    function applyCanvasGrain(ctx, w, h, strength = 10, seed = 42, preserveAlpha = false) {
      try {
        const img = ctx.getImageData(0, 0, w, h);
        const d = img.data;
        const rand = createRng(seed);
        for (let i = 0; i < d.length; i += 4) {
          if (preserveAlpha && d[i + 3] < 14) continue;
          const n = (rand() - 0.5) * strength * 2;
          d[i] = Math.max(0, Math.min(255, d[i] + n));
          d[i + 1] = Math.max(0, Math.min(255, d[i + 1] + n));
          d[i + 2] = Math.max(0, Math.min(255, d[i + 2] + n));
        }
        ctx.putImageData(img, 0, 0);
      } catch (e) {}
    }

    function drawPainterlyBlossom(ctx, cx, cy, scale = 1, rot = 0, ink = '#191715', petalLight = '#F2EBE0') {
      ctx.save();
      ctx.translate(cx, cy);
      ctx.rotate(rot);
      ctx.scale(scale, scale);

      for (let i = 0; i < 5; i++) {
        ctx.save();
        ctx.rotate((i * Math.PI * 2) / 5);
        const g = ctx.createRadialGradient(0, -14, 3, 0, -24, 34);
        g.addColorStop(0, '#443E37');
        g.addColorStop(0.32, petalLight);
        g.addColorStop(1, '#CFC4B4');
        ctx.fillStyle = g;
        ctx.beginPath();
        ctx.moveTo(0, 0);
        ctx.bezierCurveTo(-18, -14, -22, -40, 0, -46);
        ctx.bezierCurveTo(22, -40, 18, -14, 0, 0);
        ctx.closePath();
        ctx.fill();
        ctx.strokeStyle = ink;
        ctx.lineWidth = 1.4;
        ctx.stroke();

        /* Halftone stipple near petal base */
        ctx.fillStyle = 'rgba(20,18,16,0.32)';
        for (let p = 0; p < 7; p++) {
          ctx.beginPath();
          ctx.arc((p - 3) * 3.4, -12 - (p % 3) * 4.5, 1.3, 0, Math.PI * 2);
          ctx.fill();
        }
        ctx.restore();
      }

      /* Stamens & center ink pistil */
      ctx.fillStyle = ink;
      ctx.beginPath();
      ctx.arc(0, 0, 6.5, 0, Math.PI * 2);
      ctx.fill();
      for (let s = 0; s < 10; s++) {
        const a = (s * Math.PI * 2) / 10;
        ctx.strokeStyle = ink;
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(0, 0);
        ctx.lineTo(Math.cos(a) * 16, Math.sin(a) * 16);
        ctx.stroke();
        ctx.beginPath();
        ctx.arc(Math.cos(a) * 17, Math.sin(a) * 17, 1.8, 0, Math.PI * 2);
        ctx.fill();
      }
      ctx.restore();
    }

    const PNG_PAINTERS = {
      heroVinyl: {
        w: 560,
        h: 560,
        paint(ctx, w, h) {
          const cx = w / 2;
          const cy = h / 2;
          const r = w * 0.48;

          /* Deep glossy black LP base (musicdise.png) */
          ctx.fillStyle = '#070707';
          ctx.beginPath();
          ctx.arc(cx, cy, r, 0, Math.PI * 2);
          ctx.fill();

          ctx.save();
          ctx.beginPath();
          ctx.arc(cx, cy, r, 0, Math.PI * 2);
          ctx.clip();

          const drawSheenWedge = (a0, a1, opacity) => {
            const g = ctx.createRadialGradient(cx, cy, r * 0.34, cx, cy, r);
            g.addColorStop(0, `rgba(220,220,220,${opacity * 0.65})`);
            g.addColorStop(0.55, `rgba(248,248,248,${opacity})`);
            g.addColorStop(1, `rgba(180,180,180,${opacity * 0.5})`);
            ctx.fillStyle = g;
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, r, a0, a1);
            ctx.closePath();
            ctx.fill();
          };
          drawSheenWedge(Math.PI * 0.88, Math.PI * 1.4, 0.52);
          drawSheenWedge(Math.PI * 1.06, Math.PI * 1.24, 0.42);
          drawSheenWedge(-Math.PI * 0.12, Math.PI * 0.38, 0.48);
          drawSheenWedge(Math.PI * 0.06, Math.PI * 0.22, 0.38);
          ctx.restore();

          for (let gr = r * 0.35; gr < r * 0.985; gr += 2.6) {
            ctx.strokeStyle = gr % 7.8 < 2.6 ? 'rgba(255,255,255,0.22)' : 'rgba(12,12,12,0.58)';
            ctx.lineWidth = 0.85;
            ctx.beginPath();
            ctx.arc(cx, cy, gr, 0, Math.PI * 2);
            ctx.stroke();
          }

          /* Matte silver-grey center label */
          const lblR = r * 0.34;
          const lblG = ctx.createRadialGradient(cx - 10, cy - 10, 8, cx, cy, lblR);
          lblG.addColorStop(0, '#B8B8B8');
          lblG.addColorStop(1, '#9A9A9A');
          ctx.fillStyle = lblG;
          ctx.beginPath();
          ctx.arc(cx, cy, lblR, 0, Math.PI * 2);
          ctx.fill();
          ctx.strokeStyle = 'rgba(20,20,20,0.45)';
          ctx.lineWidth = 2;
          ctx.stroke();

          ctx.fillStyle = '#070707';
          ctx.beginPath();
          ctx.arc(cx, cy, 8, 0, Math.PI * 2);
          ctx.fill();

          applyCanvasGrain(ctx, w, h, 9, 101, true);
        },
      },

      heroStatue: {
        w: 640,
        h: 720,
        paint(ctx, w, h) {
          ctx.save();
          ctx.scale(w / 460, h / 540);

          /* Background botanical branch sprouting behind head (marblestatue.png) */
          ctx.strokeStyle = '#2B2824';
          ctx.lineWidth = 4;
          ctx.beginPath();
          ctx.moveTo(300, 320);
          ctx.bezierCurveTo(355, 240, 385, 160, 420, 62);
          ctx.stroke();
          drawPainterlyBlossom(ctx, 378, 226, 0.96, 0.22, '#221F1C', '#F2EBE0');
          drawPainterlyBlossom(ctx, 408, 126, 0.64, -0.35, '#221F1C', '#EAE2D5');

          /* Draped classical shoulders & torso */
          const torsoG = ctx.createLinearGradient(75, 290, 415, 530);
          torsoG.addColorStop(0, '#EFEAE0');
          torsoG.addColorStop(0.48, '#C9C0B2');
          torsoG.addColorStop(0.85, '#6F685F');
          torsoG.addColorStop(1, '#2D2A26');
          ctx.fillStyle = torsoG;
          ctx.beginPath();
          ctx.moveTo(70, 536);
          ctx.bezierCurveTo(76, 428, 114, 372, 182, 350);
          ctx.lineTo(192, 292);
          ctx.lineTo(278, 296);
          ctx.lineTo(292, 356);
          ctx.bezierCurveTo(366, 378, 412, 434, 420, 536);
          ctx.closePath();
          ctx.fill();
          ctx.strokeStyle = '#1B1917';
          ctx.lineWidth = 1.8;
          ctx.stroke();

          ctx.strokeStyle = 'rgba(20, 18, 16, 0.48)';
          ctx.lineWidth = 2.4;
          [
            [140, 372, 188, 448, 260, 526],
            [176, 356, 232, 436, 324, 526],
            [268, 362, 318, 432, 372, 518],
            [112, 412, 144, 476, 174, 528],
          ].forEach(([x0, y0, cx1, cy1, x1, y1]) => {
            ctx.beginPath();
            ctx.moveTo(x0, y0);
            ctx.quadraticCurveTo(cx1, cy1, x1, y1);
            ctx.stroke();
          });

          const neckG = ctx.createLinearGradient(178, 260, 286, 330);
          neckG.addColorStop(0, '#E3DDD1');
          neckG.addColorStop(0.55, '#9F978A');
          neckG.addColorStop(1, '#2B2825');
          ctx.fillStyle = neckG;
          ctx.beginPath();
          ctx.moveTo(186, 266);
          ctx.quadraticCurveTo(194, 314, 174, 360);
          ctx.quadraticCurveTo(234, 384, 292, 356);
          ctx.quadraticCurveTo(270, 310, 276, 264);
          ctx.closePath();
          ctx.fill();

          const headG = ctx.createRadialGradient(204, 178, 18, 232, 206, 126);
          headG.addColorStop(0, '#F6F2EA');
          headG.addColorStop(0.5, '#D1C8B9');
          headG.addColorStop(0.84, '#797167');
          headG.addColorStop(1, '#23201D');
          ctx.fillStyle = headG;
          ctx.beginPath();
          ctx.moveTo(154, 148);
          ctx.bezierCurveTo(148, 212, 162, 274, 208, 296);
          ctx.bezierCurveTo(248, 308, 290, 276, 306, 224);
          ctx.bezierCurveTo(320, 176, 308, 122, 262, 102);
          ctx.bezierCurveTo(208, 86, 162, 108, 154, 148);
          ctx.closePath();
          ctx.fill();
          ctx.strokeStyle = '#1B1917';
          ctx.lineWidth = 1.6;
          ctx.stroke();

          ctx.strokeStyle = 'rgba(20,18,16,0.68)';
          ctx.lineWidth = 2.1;
          ctx.beginPath();
          ctx.moveTo(204, 182);
          ctx.lineTo(192, 224);
          ctx.quadraticCurveTo(204, 232, 214, 226);
          ctx.stroke();

          ctx.fillStyle = 'rgba(32,29,26,0.68)';
          ctx.beginPath();
          ctx.moveTo(188, 248);
          ctx.quadraticCurveTo(208, 242, 228, 250);
          ctx.quadraticCurveTo(208, 258, 188, 248);
          ctx.fill();

          [
            [168, 118, 28], [198, 94, 32], [236, 84, 34], [274, 96, 32],
            [304, 124, 30], [318, 162, 28], [312, 200, 25], [296, 230, 22],
            [148, 146, 22], [186, 120, 24], [228, 112, 26], [268, 124, 26],
            [292, 154, 24], [286, 186, 20],
          ].forEach(([cx, cy, cr], idx) => {
            const cg = ctx.createRadialGradient(cx - cr * 0.3, cy - cr * 0.3, 2, cx, cy, cr);
            cg.addColorStop(0, '#F5F0E6');
            cg.addColorStop(0.58, idx % 2 === 0 ? '#BDB4A5' : '#A9A091');
            cg.addColorStop(1, '#2D2A26');
            ctx.fillStyle = cg;
            ctx.beginPath();
            ctx.arc(cx, cy, cr, 0, Math.PI * 2);
            ctx.fill();
            ctx.strokeStyle = 'rgba(20,18,16,0.55)';
            ctx.lineWidth = 1.4;
            ctx.stroke();
          });

          /* Distressed black cracked tape censor bar across eyes */
          ctx.save();
          ctx.translate(212, 184);
          ctx.rotate(-0.4);
          ctx.fillStyle = '#121110';
          ctx.fillRect(-78, -20, 156, 38);
          ctx.strokeStyle = 'rgba(240,235,224,0.48)';
          ctx.lineWidth = 1.1;
          ctx.beginPath();
          ctx.moveTo(-74, 10);
          ctx.lineTo(-14, 2);
          ctx.lineTo(42, -5);
          ctx.lineTo(74, -12);
          ctx.moveTo(-18, -18);
          ctx.lineTo(-10, 16);
          ctx.stroke();
          ctx.restore();

          drawPainterlyBlossom(ctx, 112, 342, 0.92, -0.3, '#221F1C', '#F2EBE0');
          drawPainterlyBlossom(ctx, 166, 392, 0.85, 0.38, '#221F1C', '#EAE2D5');
          drawPainterlyBlossom(ctx, 92, 424, 0.72, -0.55, '#221F1C', '#E4DBCD');

          ctx.restore();
          applyCanvasGrain(ctx, w, h, 14, 102, true);
        },
      },

      skyDoor: {
        w: 440,
        h: 620,
        paint(ctx, w, h) {
          /* Surreal halftone blue clouds + arched stone doorway on grassy hill (download.jpg) */
          const skyG = ctx.createLinearGradient(0, 0, 0, h * 0.82);
          skyG.addColorStop(0, '#2A6386');
          skyG.addColorStop(0.55, '#3E7A9C');
          skyG.addColorStop(1, '#4C89AA');
          ctx.fillStyle = skyG;
          ctx.fillRect(0, 0, w, h);

          /* Fluffy warm cream halftone clouds */
          const clouds = [
            [240, 82, 92], [178, 128, 72], [312, 158, 86], [108, 224, 78],
            [274, 244, 96], [358, 272, 74], [148, 330, 82], [298, 378, 88],
            [96, 434, 72], [356, 446, 68],
          ];
          ctx.fillStyle = '#E6DEC9';
          clouds.forEach(([cx, cy, cr]) => {
            ctx.beginPath();
            ctx.arc(cx, cy, cr, 0, Math.PI * 2);
            ctx.fill();
          });

          /* Halftone screen overlay on sky */
          ctx.fillStyle = 'rgba(20, 48, 68, 0.22)';
          for (let y = 4; y < h * 0.8; y += 6) {
            for (let x = 4; x < w; x += 6) {
              if ((x + y) % 12 === 0) ctx.fillRect(x, y, 2, 2);
            }
          }

          /* Rolling olive-green hill */
          const hillG = ctx.createLinearGradient(0, h * 0.72, 0, h);
          hillG.addColorStop(0, '#6B773E');
          hillG.addColorStop(1, '#3B4421');
          ctx.fillStyle = hillG;
          ctx.beginPath();
          ctx.moveTo(0, h * 0.76);
          ctx.quadraticCurveTo(w * 0.34, h * 0.84, w * 0.56, h * 0.82);
          ctx.quadraticCurveTo(w * 0.82, h * 0.79, w, h * 0.77);
          ctx.lineTo(w, h);
          ctx.lineTo(0, h);
          ctx.closePath();
          ctx.fill();

          /* Arched stone portal with vintage blue double doors */
          const dx = w * 0.5;
          const dy = h * 0.84;
          ctx.fillStyle = '#DDD5C4';
          ctx.beginPath();
          ctx.arc(dx, dy - 94, 48, Math.PI, 0);
          ctx.lineTo(dx + 48, dy);
          ctx.lineTo(dx - 48, dy);
          ctx.closePath();
          ctx.fill();
          ctx.strokeStyle = '#6B6459';
          ctx.lineWidth = 2;
          ctx.stroke();

          ctx.fillStyle = '#4B829E';
          ctx.beginPath();
          ctx.arc(dx, dy - 92, 34, Math.PI, 0);
          ctx.lineTo(dx + 34, dy);
          ctx.lineTo(dx - 34, dy);
          ctx.closePath();
          ctx.fill();

          ctx.strokeStyle = '#1E3C4C';
          ctx.lineWidth = 2;
          ctx.beginPath();
          ctx.moveTo(dx, dy - 94);
          ctx.lineTo(dx, dy);
          ctx.stroke();

          applyCanvasGrain(ctx, w, h, 14, 201, false);
        },
      },

      glowingBeach: {
        w: 440,
        h: 560,
        paint(ctx, w, h) {
          /* Starlit nocturnal beach with silhouette facing glowing figure (download (1).jpg) */
          const sky = ctx.createLinearGradient(0, 0, 0, h);
          sky.addColorStop(0, '#090D18');
          sky.addColorStop(0.45, '#1D2944');
          sky.addColorStop(0.58, '#41404C');
          sky.addColorStop(0.66, '#141722');
          sky.addColorStop(1, '#09090D');
          ctx.fillStyle = sky;
          ctx.fillRect(0, 0, w, h);

          /* Starfield */
          const rand = createRng(202);
          for (let i = 0; i < 160; i++) {
            ctx.fillStyle = `rgba(240,246,255,${0.25 + rand() * 0.7})`;
            ctx.beginPath();
            ctx.arc(rand() * w, rand() * h * 0.54, 0.6 + rand() * 1.3, 0, Math.PI * 2);
            ctx.fill();
          }

          /* Ethereal blue-white bloom around luminous figure */
          const aura = ctx.createRadialGradient(w * 0.58, h * 0.53, 8, w * 0.58, h * 0.55, 165);
          aura.addColorStop(0, 'rgba(255, 255, 255, 0.96)');
          aura.addColorStop(0.26, 'rgba(182, 224, 255, 0.72)');
          aura.addColorStop(0.62, 'rgba(72, 128, 208, 0.25)');
          aura.addColorStop(1, 'rgba(10, 14, 26, 0)');
          ctx.fillStyle = aura;
          ctx.fillRect(0, 0, w, h);

          /* Ground reflection glow */
          const pool = ctx.createRadialGradient(w * 0.58, h * 0.72, 10, w * 0.58, h * 0.72, 110);
          pool.addColorStop(0, 'rgba(230, 245, 255, 0.85)');
          pool.addColorStop(0.5, 'rgba(120, 172, 232, 0.35)');
          pool.addColorStop(1, 'rgba(12, 15, 22, 0)');
          ctx.fillStyle = pool;
          ctx.beginPath();
          ctx.ellipse(w * 0.58, h * 0.73, 120, 32, 0, 0, Math.PI * 2);
          ctx.fill();

          /* Glowing ethereal silhouette (right) */
          ctx.fillStyle = '#FFFFFF';
          ctx.beginPath();
          ctx.arc(w * 0.58, h * 0.41, 13, 0, Math.PI * 2);
          ctx.fill();
          ctx.beginPath();
          ctx.moveTo(w * 0.54, h * 0.44);
          ctx.lineTo(w * 0.62, h * 0.44);
          ctx.lineTo(w * 0.63, h * 0.60);
          ctx.lineTo(w * 0.60, h * 0.71);
          ctx.lineTo(w * 0.56, h * 0.71);
          ctx.lineTo(w * 0.53, h * 0.60);
          ctx.closePath();
          ctx.fill();

          /* Dark standing silhouette facing glow (left) */
          ctx.fillStyle = '#0A0B0F';
          ctx.beginPath();
          ctx.arc(w * 0.34, h * 0.41, 13, 0, Math.PI * 2);
          ctx.fill();
          ctx.beginPath();
          ctx.moveTo(w * 0.29, h * 0.44);
          ctx.lineTo(w * 0.38, h * 0.44);
          ctx.lineTo(w * 0.39, h * 0.61);
          ctx.lineTo(w * 0.37, h * 0.72);
          ctx.lineTo(w * 0.31, h * 0.72);
          ctx.lineTo(w * 0.29, h * 0.61);
          ctx.closePath();
          ctx.fill();

          applyCanvasGrain(ctx, w, h, 16, 203, false);
        },
      },

      trackSnooze: {
        w: 440,
        h: 480,
        paint(ctx, w, h) {
          const g = ctx.createLinearGradient(0, 0, 0, h);
          g.addColorStop(0, '#B9B1A5');
          g.addColorStop(0.52, '#6E6860');
          g.addColorStop(1, '#151311');
          ctx.fillStyle = g;
          ctx.fillRect(0, 0, w, h);

          /* Misty clouds */
          ctx.fillStyle = 'rgba(242, 236, 224, 0.45)';
          [[90, 90, 80], [210, 65, 110], [340, 115, 90], [280, 185, 76]].forEach(([x, y, r]) => {
            ctx.beginPath();
            ctx.arc(x, y, r, 0, Math.PI * 2);
            ctx.fill();
          });

          /* Dramatic tilted-back silhouette looking up at sky */
          ctx.fillStyle = '#12100E';
          ctx.beginPath();
          ctx.moveTo(95, h);
          ctx.quadraticCurveTo(135, 300, 185, 252);
          ctx.quadraticCurveTo(215, 212, 198, 165);
          ctx.quadraticCurveTo(174, 145, 204, 124);
          ctx.quadraticCurveTo(254, 110, 272, 152);
          ctx.quadraticCurveTo(288, 196, 260, 242);
          ctx.quadraticCurveTo(325, 286, 355, h);
          ctx.closePath();
          ctx.fill();

          applyCanvasGrain(ctx, w, h, 18, 204, false);
        },
      },

      track505: {
        w: 440,
        h: 480,
        paint(ctx, w, h) {
          const sky = ctx.createLinearGradient(0, 0, 0, h);
          sky.addColorStop(0, '#2B2926');
          sky.addColorStop(0.6, '#1B1917');
          sky.addColorStop(1, '#0C0B0A');
          ctx.fillStyle = sky;
          ctx.fillRect(0, 0, w, h);

          /* Giant luminous full moon */
          const moon = ctx.createRadialGradient(w * 0.52, h * 0.38, 16, w * 0.52, h * 0.38, 148);
          moon.addColorStop(0, '#F5EFE2');
          moon.addColorStop(0.62, '#D7CEBE');
          moon.addColorStop(0.84, 'rgba(215, 206, 190, 0.25)');
          moon.addColorStop(1, 'rgba(215, 206, 190, 0)');
          ctx.fillStyle = moon;
          ctx.beginPath();
          ctx.arc(w * 0.52, h * 0.38, 148, 0, Math.PI * 2);
          ctx.fill();

          /* Centered moody silhouette standing before moon */
          ctx.fillStyle = '#0E0D0C';
          ctx.beginPath();
          ctx.arc(w * 0.52, h * 0.44, 34, 0, Math.PI * 2);
          ctx.fill();
          ctx.beginPath();
          ctx.moveTo(w * 0.32, h);
          ctx.quadraticCurveTo(w * 0.36, h * 0.62, w * 0.52, h * 0.54);
          ctx.quadraticCurveTo(w * 0.68, h * 0.62, w * 0.72, h);
          ctx.closePath();
          ctx.fill();

          applyCanvasGrain(ctx, w, h, 18, 205, false);
        },
      },

      templateNoirComic: {
        w: 400,
        h: 520,
        paint(ctx, w, h) {
          const bg = ctx.createLinearGradient(0, 0, w, h);
          bg.addColorStop(0, '#262422');
          bg.addColorStop(0.6, '#141210');
          bg.addColorStop(1, '#090807');
          ctx.fillStyle = bg;
          ctx.fillRect(0, 0, w, h);

          /* Manga speed lines & urban halftone */
          ctx.strokeStyle = 'rgba(237, 231, 218, 0.16)';
          ctx.lineWidth = 1.4;
          for (let i = 0; i < 24; i++) {
            const a = (i / 24) * Math.PI * 2;
            ctx.beginPath();
            ctx.moveTo(w * 0.52 + Math.cos(a) * 65, h * 0.42 + Math.sin(a) * 65);
            ctx.lineTo(w * 0.52 + Math.cos(a) * 320, h * 0.42 + Math.sin(a) * 320);
            ctx.stroke();
          }

          /* Stylized anime silhouette wearing white headphones */
          ctx.fillStyle = '#070606';
          ctx.beginPath();
          ctx.arc(w * 0.48, h * 0.42, 62, 0, Math.PI * 2);
          ctx.fill();
          ctx.beginPath();
          ctx.moveTo(w * 0.2, h);
          ctx.quadraticCurveTo(w * 0.28, h * 0.64, w * 0.48, h * 0.56);
          ctx.quadraticCurveTo(w * 0.7, h * 0.64, w * 0.78, h);
          ctx.closePath();
          ctx.fill();

          ctx.strokeStyle = '#EDE7DA';
          ctx.lineWidth = 8;
          ctx.beginPath();
          ctx.arc(w * 0.48, h * 0.40, 68, Math.PI * 1.12, Math.PI * 1.88);
          ctx.stroke();
          ctx.fillStyle = '#EDE7DA';
          ctx.beginPath();
          ctx.ellipse(w * 0.63, h * 0.42, 16, 26, 0.15, 0, Math.PI * 2);
          ctx.fill();

          applyCanvasGrain(ctx, w, h, 18, 206, false);
        },
      },

      templateSumiInk: {
        w: 400,
        h: 520,
        paint(ctx, w, h) {
          ctx.fillStyle = '#DFD6C5';
          ctx.fillRect(0, 0, w, h);

          /* Soft ink wash stains */
          const wash = ctx.createRadialGradient(130, 190, 12, 130, 190, 160);
          wash.addColorStop(0, 'rgba(30, 26, 22, 0.26)');
          wash.addColorStop(1, 'rgba(30, 26, 22, 0)');
          ctx.fillStyle = wash;
          ctx.fillRect(0, 0, w, h);

          /* Sumi-e ink branch sweeping across parchment */
          ctx.strokeStyle = '#1E1B18';
          ctx.lineWidth = 6;
          ctx.lineCap = 'round';
          ctx.beginPath();
          ctx.moveTo(395, 50);
          ctx.bezierCurveTo(270, 140, 205, 250, 56, 455);
          ctx.stroke();

          drawPainterlyBlossom(ctx, 265, 160, 0.92, 0.25, '#1B1917', '#F2EBE0');
          drawPainterlyBlossom(ctx, 185, 280, 0.82, -0.35, '#1B1917', '#EAE2D3');
          drawPainterlyBlossom(ctx, 118, 375, 0.66, 0.48, '#1B1917', '#E6DEC9');

          applyCanvasGrain(ctx, w, h, 14, 207, false);
        },
      },

      flowerStamp: {
        w: 420,
        h: 420,
        paint(ctx, w, h) {
          /* Black perforated postage stamp with white magnolia blossoms (flower stamp.png) */
          const pad = 26;
          ctx.fillStyle = '#141210';
          ctx.fillRect(pad, pad, w - pad * 2, h - pad * 2);

          /* Perforated stamp teeth along edges */
          ctx.globalCompositeOperation = 'destination-out';
          const step = 22;
          for (let x = pad + 11; x < w - pad; x += step) {
            ctx.beginPath();
            ctx.arc(x, pad, 6, 0, Math.PI * 2);
            ctx.arc(x, h - pad, 6, 0, Math.PI * 2);
            ctx.fill();
          }
          for (let y = pad + 11; y < h - pad; y += step) {
            ctx.beginPath();
            ctx.arc(pad, y, 6, 0, Math.PI * 2);
            ctx.arc(w - pad, y, 6, 0, Math.PI * 2);
            ctx.fill();
          }
          ctx.globalCompositeOperation = 'source-over';

          /* Inner vintage postage border & typography */
          ctx.strokeStyle = 'rgba(237, 231, 218, 0.65)';
          ctx.lineWidth = 1.4;
          ctx.strokeRect(pad + 20, pad + 24, w - (pad + 20) * 2, h - (pad + 24) * 2);

          ctx.fillStyle = 'rgba(237, 231, 218, 0.85)';
          ctx.font = '13px "JetBrains Mono", monospace';
          ctx.fillText('botanical', pad + 22, pad + 18);
          ctx.fillText('postage stamp', w - pad - 128, h - pad - 10);

          /* Crisp white etched magnolia blossoms */
          drawPainterlyBlossom(ctx, w * 0.58, h * 0.56, 1.38, 0.25, '#141210', '#FFFFFF');
          drawPainterlyBlossom(ctx, w * 0.39, h * 0.42, 1.05, -0.45, '#141210', '#F6F3EC');

          applyCanvasGrain(ctx, w, h, 12, 208, true);
        },
      },

      flowerHalftone: {
        w: 440,
        h: 440,
        paint(ctx, w, h) {
          /* Three halftone cherry blossoms cluster (flower.png) */
          drawPainterlyBlossom(ctx, 162, 146, 1.55, -0.22, '#141210', '#F3EFE6');
          drawPainterlyBlossom(ctx, 294, 216, 1.45, 0.35, '#141210', '#EFE9DC');
          drawPainterlyBlossom(ctx, 178, 304, 1.48, 0.14, '#141210', '#E8E1D3');

          /* Coarse halftone dots */
          ctx.save();
          ctx.globalCompositeOperation = 'source-atop';
          ctx.fillStyle = 'rgba(20, 18, 16, 0.22)';
          for (let y = 0; y < h; y += 7) {
            for (let x = 0; x < w; x += 7) {
              ctx.beginPath();
              ctx.arc(x, y, 1.8, 0, Math.PI * 2);
              ctx.fill();
            }
          }
          ctx.restore();
          applyCanvasGrain(ctx, w, h, 10, 209, true);
        },
      },

      flowerLotus: {
        w: 460,
        h: 400,
        paint(ctx, w, h) {
          /* Crosshatched black ink lotus sticker (flower lotus.png) */
          ctx.save();
          ctx.translate(w / 2, h * 0.62);

          const petals = [
            [-1.08, 120, 42], [1.08, 120, 42],
            [-0.65, 146, 46], [0.65, 146, 46],
            [-0.32, 158, 44], [0.32, 158, 44],
            [0, 168, 50],
          ];

          /* White sticker halo */
          ctx.fillStyle = 'rgba(244, 240, 232, 0.9)';
          petals.forEach(([ang, len, wid]) => {
            ctx.save();
            ctx.rotate(ang);
            ctx.beginPath();
            ctx.moveTo(0, 18);
            ctx.bezierCurveTo(-wid - 10, -len * 0.35, -wid * 0.7 - 8, -len * 0.85, 0, -len - 10);
            ctx.bezierCurveTo(wid * 0.7 + 8, -len * 0.85, wid + 10, -len * 0.35, 0, 18);
            ctx.fill();
            ctx.restore();
          });

          /* Deep engraved lotus petals */
          petals.forEach(([ang, len, wid], idx) => {
            ctx.save();
            ctx.rotate(ang);
            const pg = ctx.createRadialGradient(0, -len * 0.45, 6, 0, -len * 0.4, len * 0.75);
            pg.addColorStop(0, idx === 6 ? '#A6A096' : '#6A655D');
            pg.addColorStop(0.55, '#191715');
            pg.addColorStop(1, '#090908');
            ctx.fillStyle = pg;
            ctx.beginPath();
            ctx.moveTo(0, 8);
            ctx.bezierCurveTo(-wid, -len * 0.35, -wid * 0.68, -len * 0.85, 0, -len);
            ctx.bezierCurveTo(wid * 0.68, -len * 0.85, wid, -len * 0.35, 0, 8);
            ctx.closePath();
            ctx.fill();
            ctx.strokeStyle = '#EDE7DA';
            ctx.lineWidth = 1.4;
            ctx.stroke();
            ctx.restore();
          });

          ctx.restore();
          applyCanvasGrain(ctx, w, h, 14, 210, true);
        },
      },

      bird: {
        w: 440,
        h: 440,
        paint(ctx, w, h) {
          /* Soaring halftone dove silhouette (bird.png) */
          ctx.save();
          ctx.translate(w * 0.48, h * 0.56);

          const drawSilhouette = (pad, fill) => {
            ctx.fillStyle = fill;
            ctx.beginPath();
            ctx.moveTo(-148 - pad, -22);
            ctx.quadraticCurveTo(-110, -56, -78, -28);
            /* Left raised wing */
            ctx.bezierCurveTo(-115, -90, -145, -160, -108, -196 - pad);
            ctx.bezierCurveTo(-60, -172, -32, -96, -24, -46);
            /* Right outstretched wing */
            ctx.bezierCurveTo(22, -108, 92, -175, 162 + pad, -198 - pad);
            ctx.bezierCurveTo(176 + pad, -118, 128, -22, 46, 32);
            /* Tail feathers */
            ctx.bezierCurveTo(106, 56, 134, 96, 102, 146 + pad);
            ctx.bezierCurveTo(54, 154 + pad, 6, 118, -32, 74);
            ctx.bezierCurveTo(-96, 52, -118, 6, -148 - pad, -22);
            ctx.closePath();
            ctx.fill();
          };

          drawSilhouette(10, 'rgba(246, 242, 234, 0.88)');
          const bg = ctx.createLinearGradient(-120, -160, 120, 120);
          bg.addColorStop(0, '#5A554E');
          bg.addColorStop(0.42, '#161412');
          bg.addColorStop(1, '#090908');
          drawSilhouette(0, bg);

          /* Halftone dot screening */
          ctx.save();
          ctx.globalCompositeOperation = 'source-atop';
          ctx.fillStyle = 'rgba(237, 231, 218, 0.24)';
          for (let y = -200; y < 180; y += 7) {
            for (let x = -170; x < 180; x += 7) {
              if ((x + y) % 14 === 0) ctx.fillRect(x, y, 2.5, 2.5);
            }
          }
          ctx.restore();
          ctx.restore();
          applyCanvasGrain(ctx, w, h, 10, 211, true);
        },
      },

      book: {
        w: 440,
        h: 360,
        paint(ctx, w, h) {
          /* Open halftone poetry book sticker (book.png) */
          ctx.save();
          ctx.translate(w / 2, h / 2);
          ctx.rotate(0.14);

          /* Outer black cover base */
          ctx.fillStyle = '#121110';
          ctx.beginPath();
          ctx.moveTo(-175, 45);
          ctx.lineTo(-105, -108);
          ctx.quadraticCurveTo(-20, -124, 12, -88);
          ctx.quadraticCurveTo(68, -112, 152, -96);
          ctx.lineTo(175, 58);
          ctx.quadraticCurveTo(88, 92, 0, 72);
          ctx.quadraticCurveTo(-92, 92, -175, 45);
          ctx.closePath();
          ctx.fill();

          /* Left & right paper pages */
          const drawPage = (pts) => {
            ctx.fillStyle = '#EFE9DC';
            ctx.beginPath();
            ctx.moveTo(pts[0][0], pts[0][1]);
            pts.slice(1).forEach(([px, py]) => ctx.lineTo(px, py));
            ctx.closePath();
            ctx.fill();
            ctx.strokeStyle = '#141210';
            ctx.lineWidth = 2;
            ctx.stroke();
          };
          drawPage([[-145, 24], [-94, -96], [-4, -74], [-22, 46]]);
          drawPage([[14, -74], [126, -86], [112, 42], [-6, 46]]);

          /* Printed poetry lines */
          ctx.strokeStyle = 'rgba(20, 18, 16, 0.55)';
          ctx.lineWidth = 2;
          for (let i = 0; i < 11; i++) {
            const y = -68 + i * 9.5;
            ctx.beginPath();
            ctx.moveTo(-88 - i * 3.5, y);
            ctx.lineTo(-22 - i * 1.4, y + 10);
            ctx.moveTo(22 - i * 1.4, y + 6);
            ctx.lineTo(106 - i * 1.2, y - 2);
            ctx.stroke();
          }

          ctx.restore();
          applyCanvasGrain(ctx, w, h, 14, 212, true);
        },
      },

      butterfly: {
        w: 440,
        h: 420,
        paint(ctx, w, h) {
          /* Three sumi-e ink brush butterflies (butterfly.png) */
          const drawWinged = (cx, cy, sc, rot) => {
            ctx.save();
            ctx.translate(cx, cy);
            ctx.rotate(rot);
            ctx.scale(sc, sc);

            ctx.fillStyle = '#141210';
            /* Upper & lower ink wings */
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.bezierCurveTo(-48, -68, -110, -48, -82, 8);
            ctx.bezierCurveTo(-58, 32, -14, 18, 0, 0);
            ctx.moveTo(0, 0);
            ctx.bezierCurveTo(46, -74, 108, -42, 74, 12);
            ctx.bezierCurveTo(48, 34, 14, 16, 0, 0);
            ctx.fill();

            /* Inner negative space brush streaks */
            ctx.fillStyle = 'rgba(230, 223, 208, 0.72)';
            ctx.beginPath();
            ctx.ellipse(-38, -22, 22, 12, -0.45, 0, Math.PI * 2);
            ctx.ellipse(34, -24, 20, 11, 0.45, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
          };

          drawWinged(126, 148, 0.96, -0.18);
          drawWinged(326, 166, 0.88, 0.28);
          drawWinged(208, 294, 0.92, -0.52);

          applyCanvasGrain(ctx, w, h, 8, 213, true);
        },
      },

      guitar: {
        w: 480,
        h: 480,
        paint(ctx, w, h) {
          /* Stippled black electric guitar (guiter.png) */
          ctx.save();
          ctx.translate(w * 0.5, h * 0.52);
          ctx.rotate(-0.74);

          /* Neck & headstock */
          ctx.fillStyle = '#0D0C0B';
          ctx.fillRect(-14, -228, 28, 178);
          ctx.beginPath();
          ctx.moveTo(-18, -228);
          ctx.lineTo(-24, -276);
          ctx.lineTo(18, -284);
          ctx.lineTo(16, -228);
          ctx.closePath();
          ctx.fill();

          /* Double-cutaway electric guitar body */
          const bodyG = ctx.createRadialGradient(-28, 36, 12, 0, 56, 124);
          bodyG.addColorStop(0, '#44403A');
          bodyG.addColorStop(0.55, '#121110');
          bodyG.addColorStop(1, '#070706');
          ctx.fillStyle = bodyG;
          ctx.beginPath();
          ctx.moveTo(-22, -52);
          ctx.bezierCurveTo(-86, -64, -92, 6, -68, 32);
          ctx.bezierCurveTo(-116, 78, -100, 162, 0, 168);
          ctx.bezierCurveTo(100, 162, 116, 78, 68, 32);
          ctx.bezierCurveTo(92, 6, 78, -56, 22, -48);
          ctx.closePath();
          ctx.fill();
          ctx.strokeStyle = 'rgba(237, 231, 218, 0.78)';
          ctx.lineWidth = 2.4;
          ctx.stroke();

          /* Dual chrome humbuckers & bridge */
          ctx.fillStyle = '#D5CFC3';
          ctx.fillRect(-30, -14, 60, 22);
          ctx.fillRect(-30, 34, 60, 22);
          ctx.fillRect(-26, 76, 52, 10);

          /* 6 guitar strings */
          ctx.strokeStyle = 'rgba(237, 231, 218, 0.65)';
          ctx.lineWidth = 0.9;
          for (let s = -10; s <= 10; s += 4) {
            ctx.beginPath();
            ctx.moveTo(s * 0.85, -245);
            ctx.lineTo(s, 84);
            ctx.stroke();
          }

          /* Volume/Tone knobs */
          ctx.strokeStyle = 'rgba(237, 231, 218, 0.6)';
          ctx.lineWidth = 1.6;
          [[40, 88], [60, 106], [34, 118], [52, 134]].forEach(([kx, ky]) => {
            ctx.beginPath();
            ctx.arc(kx, ky, 7.5, 0, Math.PI * 2);
            ctx.stroke();
          });

          ctx.restore();
          applyCanvasGrain(ctx, w, h, 14, 214, true);
        },
      },

      trendingCD: {
        w: 440,
        h: 440,
        paint(ctx, w, h) {
          const cx = w / 2;
          const cy = h / 2;
          const r = w * 0.47;

          const base = ctx.createRadialGradient(cx, cy, r * 0.25, cx, cy, r);
          base.addColorStop(0, '#D4D6DC');
          base.addColorStop(0.65, '#B2B9C4');
          base.addColorStop(1, '#757D89');
          ctx.fillStyle = base;
          ctx.beginPath();
          ctx.arc(cx, cy, r, 0, Math.PI * 2);
          ctx.fill();

          /* Subtle iridescent holographic wedges */
          ctx.save();
          ctx.beginPath();
          ctx.arc(cx, cy, r, 0, Math.PI * 2);
          ctx.clip();
          [
            [0.2, 0.9, 'rgba(130, 196, 245, 0.55)'],
            [0.85, 1.35, 'rgba(245, 224, 152, 0.45)'],
            [3.35, 4.1, 'rgba(148, 174, 240, 0.55)'],
            [4.05, 4.6, 'rgba(240, 198, 184, 0.42)'],
          ].forEach(([a0, a1, col]) => {
            ctx.fillStyle = col;
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, r, a0, a1);
            ctx.closePath();
            ctx.fill();
          });
          ctx.restore();

          /* Inner polycarbonate ring & cutout hole */
          ctx.strokeStyle = 'rgba(255,255,255,0.65)';
          ctx.lineWidth = 2;
          ctx.beginPath();
          ctx.arc(cx, cy, r * 0.34, 0, Math.PI * 2);
          ctx.stroke();

          ctx.globalCompositeOperation = 'destination-out';
          ctx.beginPath();
          ctx.arc(cx, cy, r * 0.16, 0, Math.PI * 2);
          ctx.fill();
          ctx.globalCompositeOperation = 'source-over';

          applyCanvasGrain(ctx, w, h, 8, 301, true);
        },
      },

      ctaSkyline: {
        w: 1440,
        h: 440,
        paint(ctx, w, h) {
          const rand = createRng(401);

          /* Subtle atmospheric moonlight glow & night haze */
          const haze = ctx.createRadialGradient(w * 0.56, h * 0.25, 20, w * 0.56, h * 0.55, w * 0.55);
          haze.addColorStop(0, 'rgba(237, 231, 218, 0.14)');
          haze.addColorStop(0.55, 'rgba(62, 92, 126, 0.09)');
          haze.addColorStop(1, 'rgba(18, 17, 15, 0)');
          ctx.fillStyle = haze;
          ctx.fillRect(0, 0, w, h);

          /* Distant architectural skyline layer */
          let dx = -10;
          while (dx < w + 20) {
            const bw = 34 + Math.floor(rand() * 68);
            const bh = 95 + Math.floor(rand() * 190);
            ctx.fillStyle = 'rgba(237, 231, 218, 0.08)';
            ctx.fillRect(dx, h - bh, bw, bh);
            if (rand() > 0.65) {
              ctx.fillRect(dx + bw * 0.2, h - bh - 26, bw * 0.6, 26);
              ctx.fillRect(dx + bw * 0.48, h - bh - 56, 2.5, 32);
            }
            dx += bw + 4 + Math.floor(rand() * 12);
          }

          /* Midground iconic Art Deco stepped towers */
          const drawArtDecoTower = (cx, baseW, height, spireH, tone, litRatio) => {
            const left = cx - baseW / 2;
            const top = h - height;
            ctx.fillStyle = tone;
            ctx.fillRect(left, top + 46, baseW, height - 46);
            ctx.fillRect(left + baseW * 0.12, top + 20, baseW * 0.76, 30);
            ctx.fillRect(left + baseW * 0.26, top, baseW * 0.48, 24);
            ctx.beginPath();
            ctx.moveTo(cx - baseW * 0.16, top);
            ctx.lineTo(cx, top - spireH);
            ctx.lineTo(cx + baseW * 0.16, top);
            ctx.closePath();
            ctx.fill();
            ctx.fillRect(cx - 1.2, top - spireH - 36, 2.4, 38);

            ctx.strokeStyle = 'rgba(237, 231, 218, 0.26)';
            ctx.lineWidth = 1;
            ctx.strokeRect(left + 0.5, top + 46.5, baseW - 1, height - 46);

            for (let wy = top + 56; wy < h - 18; wy += 11) {
              for (let wx = left + 7; wx < left + baseW - 9; wx += 9) {
                const r = rand();
                if (r < litRatio) {
                  ctx.fillStyle = r < litRatio * 0.35
                    ? 'rgba(244, 237, 218, 0.58)'
                    : 'rgba(237, 231, 218, 0.26)';
                  ctx.fillRect(wx, wy, 4.5, 5.5);
                }
              }
            }
          };

          drawArtDecoTower(w * 0.18, 86, 265, 42, '#1A1917', 0.42);
          drawArtDecoTower(w * 0.43, 108, 330, 58, '#1D1B19', 0.48);
          drawArtDecoTower(w * 0.62, 92, 295, 66, '#191816', 0.44);
          drawArtDecoTower(w * 0.81, 102, 250, 38, '#1A1917', 0.36);

          /* Foreground detailed city blocks with lit skyscraper window strips */
          let fx = 0;
          while (fx < w) {
            const bw = 38 + Math.floor(rand() * 72);
            const bh = 72 + Math.floor(rand() * 168);
            const top = h - bh;

            const bgG = ctx.createLinearGradient(fx, top, fx + bw, h);
            bgG.addColorStop(0, '#22201D');
            bgG.addColorStop(1, '#11100E');
            ctx.fillStyle = bgG;
            ctx.fillRect(fx, top, bw, bh);

            ctx.strokeStyle = 'rgba(237, 231, 218, 0.22)';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(fx, top);
            ctx.lineTo(fx + bw, top);
            ctx.stroke();

            if (rand() > 0.55) {
              ctx.fillStyle = '#1E1C19';
              ctx.fillRect(fx + 8, top - 12, bw * 0.34, 12);
            }

            for (let wy = top + 12; wy < h - 12; wy += 12) {
              for (let wx = fx + 7; wx < fx + bw - 8; wx += 10) {
                const v = rand();
                if (v > 0.58) {
                  ctx.fillStyle = v > 0.88
                    ? 'rgba(246, 240, 224, 0.62)'
                    : 'rgba(237, 231, 218, 0.24)';
                  ctx.fillRect(wx, wy, 4.5, 6);
                }
              }
            }
            fx += bw + 4 + Math.floor(rand() * 14);
          }

          /* Ground mist fade into footer */
          const mist = ctx.createLinearGradient(0, h * 0.72, 0, h);
          mist.addColorStop(0, 'rgba(18, 17, 15, 0)');
          mist.addColorStop(1, 'rgba(18, 17, 15, 0.88)');
          ctx.fillStyle = mist;
          ctx.fillRect(0, h * 0.72, w, h * 0.28);

          applyCanvasGrain(ctx, w, h, 11, 402, true);
        },
      },

      ctaHeadphones: {
        w: 580,
        h: 580,
        paint(ctx, w, h) {
          ctx.save();
          ctx.scale(w / 420, h / 420);
          ctx.rotate(-0.08);

          /* White die-cut sticker halo (headphone.png) */
          ctx.strokeStyle = 'rgba(244, 240, 232, 0.86)';
          ctx.lineWidth = 42;
          ctx.lineCap = 'round';
          ctx.beginPath();
          ctx.arc(204, 210, 134, Math.PI * 1.02, Math.PI * 1.98);
          ctx.stroke();

          ctx.strokeStyle = '#0E0D0C';
          ctx.lineWidth = 30;
          ctx.beginPath();
          ctx.arc(204, 210, 134, Math.PI * 1.02, Math.PI * 1.98);
          ctx.stroke();

          ctx.strokeStyle = 'rgba(237, 231, 218, 0.52)';
          ctx.lineWidth = 2;
          ctx.setLineDash([4, 5]);
          ctx.beginPath();
          ctx.arc(204, 210, 144, Math.PI * 1.08, Math.PI * 1.92);
          ctx.stroke();
          ctx.setLineDash([]);

          const drawCup = (cx, cy, deg, sc = 1) => {
            ctx.save();
            ctx.translate(cx, cy);
            ctx.rotate((deg * Math.PI) / 180);
            ctx.scale(sc, sc);

            ctx.fillStyle = 'rgba(244, 240, 232, 0.88)';
            ctx.beginPath();
            ctx.ellipse(0, 0, 68, 92, 0, 0, Math.PI * 2);
            ctx.fill();

            const cushionG = ctx.createRadialGradient(-14, -14, 10, 0, 0, 84);
            cushionG.addColorStop(0, '#5A554E');
            cushionG.addColorStop(0.45, '#191715');
            cushionG.addColorStop(1, '#080807');
            ctx.fillStyle = cushionG;
            ctx.beginPath();
            ctx.ellipse(0, 0, 60, 84, 0, 0, Math.PI * 2);
            ctx.fill();

            ctx.strokeStyle = 'rgba(237, 231, 218, 0.55)';
            ctx.lineWidth = 1.6;
            ctx.beginPath();
            ctx.ellipse(6, 2, 40, 62, 0, 0, Math.PI * 2);
            ctx.stroke();

            ctx.save();
            ctx.beginPath();
            ctx.ellipse(0, 0, 58, 82, 0, 0, Math.PI * 2);
            ctx.clip();
            ctx.strokeStyle = 'rgba(237, 231, 218, 0.16)';
            ctx.lineWidth = 1;
            for (let i = -90; i <= 90; i += 6) {
              ctx.beginPath();
              ctx.moveTo(i, -90);
              ctx.lineTo(i + 40, 90);
              ctx.stroke();
            }
            ctx.restore();
            ctx.restore();
          };

          drawCup(132, 294, -22, 0.96);
          drawCup(268, 286, 18, 1.02);
          ctx.restore();
          applyCanvasGrain(ctx, w, h, 14, 403, true);
        },
      },

      cassette: {
        w: 560,
        h: 440,
        paint(ctx, w, h) {
          ctx.save();
          ctx.translate(w / 2, h / 2);
          ctx.rotate(0.11);

          const cw = 390;
          const ch = 248;

          ctx.fillStyle = 'rgba(244, 240, 232, 0.9)';
          drawRoundRect(ctx, -cw / 2 - 12, -ch / 2 - 12, cw + 24, ch + 24, 18);
          ctx.fill();

          const bodyG = ctx.createLinearGradient(-cw / 2, -ch / 2, cw / 2, ch / 2);
          bodyG.addColorStop(0, '#24221F');
          bodyG.addColorStop(0.5, '#11100E');
          bodyG.addColorStop(1, '#080807');
          ctx.fillStyle = bodyG;
          drawRoundRect(ctx, -cw / 2, -ch / 2, cw, ch, 12);
          ctx.fill();

          ctx.fillStyle = '#E6DFD0';
          drawRoundRect(ctx, -cw / 2 + 26, -ch / 2 + 22, cw - 52, 54, 6);
          ctx.fill();
          ctx.fillStyle = '#141210';
          ctx.font = '600 13px "JetBrains Mono", monospace';
          ctx.fillText('VOIDANTARA • SIDE A   C-90', -cw / 2 + 44, -ch / 2 + 54);

          ctx.strokeStyle = 'rgba(237, 231, 218, 0.55)';
          ctx.lineWidth = 2;
          ctx.strokeRect(-56, -24, 112, 48);

          [-96, 96].forEach((rx) => {
            ctx.fillStyle = '#090908';
            ctx.beginPath();
            ctx.arc(rx, 0, 32, 0, Math.PI * 2);
            ctx.fill();
            ctx.strokeStyle = '#EDE7DA';
            ctx.lineWidth = 2.4;
            ctx.stroke();

            for (let t = 0; t < 6; t++) {
              const a = (t * Math.PI) / 3;
              ctx.beginPath();
              ctx.moveTo(rx + Math.cos(a) * 10, Math.sin(a) * 10);
              ctx.lineTo(rx + Math.cos(a) * 20, Math.sin(a) * 20);
              ctx.stroke();
            }
          });

          ctx.strokeStyle = 'rgba(237, 231, 218, 0.45)';
          ctx.lineWidth = 2;
          ctx.beginPath();
          ctx.moveTo(-132, ch / 2);
          ctx.lineTo(-108, 62);
          ctx.lineTo(108, 62);
          ctx.lineTo(132, ch / 2);
          ctx.closePath();
          ctx.stroke();

          ctx.fillStyle = 'rgba(237, 231, 218, 0.14)';
          for (let y = -ch / 2 + 6; y < ch / 2 - 6; y += 7) {
            for (let x = -cw / 2 + 6; x < cw / 2 - 6; x += 7) {
              if ((Math.round(x + y) & 1) === 0) ctx.fillRect(x, y, 2, 2);
            }
          }

          ctx.restore();
          applyCanvasGrain(ctx, w, h, 14, 512, true);
        },
      },

      washiTape: {
        w: 520,
        h: 150,
        paint(ctx, w, h) {
          ctx.save();
          ctx.beginPath();
          ctx.moveTo(18, 34);
          ctx.lineTo(96, 26);
          ctx.lineTo(184, 34);
          ctx.lineTo(290, 22);
          ctx.lineTo(412, 16);
          ctx.lineTo(498, 28);
          ctx.lineTo(506, 108);
          ctx.lineTo(416, 118);
          ctx.lineTo(298, 112);
          ctx.lineTo(176, 126);
          ctx.lineTo(74, 122);
          ctx.lineTo(14, 112);
          ctx.closePath();

          ctx.save();
          ctx.strokeStyle = 'rgba(245, 242, 234, 0.82)';
          ctx.lineWidth = 10;
          ctx.stroke();
          ctx.restore();

          ctx.clip();

          const sky = ctx.createLinearGradient(0, 0, w, h);
          sky.addColorStop(0, '#1D438A');
          sky.addColorStop(0.5, '#2E67B8');
          sky.addColorStop(1, '#3C7CC9');
          ctx.fillStyle = sky;
          ctx.fillRect(0, 0, w, h);

          [
            [210, 76, 46, '#84B4E8'],
            [248, 72, 30, '#D9E8F6'],
            [140, 74, 38, '#538ED6'],
            [340, 62, 28, '#DCE8A6'],
          ].forEach(([sx, sy, sr, col]) => {
            ctx.strokeStyle = col;
            ctx.lineWidth = 5;
            ctx.setLineDash([12, 6]);
            ctx.beginPath();
            ctx.arc(sx, sy, sr, 0, Math.PI * 1.85);
            ctx.stroke();
          });
          ctx.setLineDash([]);

          const moon = ctx.createRadialGradient(456, 48, 6, 456, 48, 42);
          moon.addColorStop(0, '#FFF176');
          moon.addColorStop(0.55, '#F5CE42');
          moon.addColorStop(1, 'rgba(245,206,66,0)');
          ctx.fillStyle = moon;
          ctx.beginPath();
          ctx.arc(456, 48, 42, 0, Math.PI * 2);
          ctx.fill();

          [[92, 42, 16], [148, 92, 14], [344, 58, 20]].forEach(([sx, sy, sr]) => {
            const g = ctx.createRadialGradient(sx, sy, 2, sx, sy, sr);
            g.addColorStop(0, '#FFF7AD');
            g.addColorStop(1, 'rgba(255,247,173,0)');
            ctx.fillStyle = g;
            ctx.beginPath();
            ctx.arc(sx, sy, sr, 0, Math.PI * 2);
            ctx.fill();
          });

          ctx.fillStyle = '#131A1D';
          ctx.beginPath();
          ctx.moveTo(58, 130);
          ctx.quadraticCurveTo(64, 58, 78, 22);
          ctx.quadraticCurveTo(88, 66, 98, 130);
          ctx.closePath();
          ctx.fill();

          ctx.restore();
          applyCanvasGrain(ctx, w, h, 10, 513, true);
        },
      },

      musicStar: {
        w: 360,
        h: 360,
        paint(ctx, w, h) {
          ctx.save();
          ctx.translate(w / 2, h / 2);
          ctx.fillStyle = '#141210';
          ctx.strokeStyle = '#141210';

          for (let i = 0; i < 5; i++) {
            ctx.save();
            ctx.rotate((i * Math.PI * 2) / 5 - Math.PI / 2);

            ctx.beginPath();
            ctx.ellipse(26, -34, 18, 13, 0.45, 0, Math.PI * 2);
            ctx.fill();

            ctx.lineWidth = 6.5;
            ctx.lineCap = 'round';
            ctx.beginPath();
            ctx.moveTo(38, -28);
            ctx.lineTo(118, -2);
            ctx.stroke();

            ctx.beginPath();
            ctx.moveTo(126, -2);
            ctx.lineTo(86, 22);
            ctx.lineTo(94, 0);
            ctx.closePath();
            ctx.fill();

            ctx.restore();
          }
          ctx.restore();
          applyCanvasGrain(ctx, w, h, 6, 514, true);
        },
      },

      musicCord: {
        w: 320,
        h: 480,
        paint(ctx, w, h) {
          ctx.save();
          ctx.scale(w / 240, h / 360);
          ctx.strokeStyle = '#141210';
          ctx.lineCap = 'round';
          ctx.lineJoin = 'round';

          const drawClefPath = () => {
            ctx.beginPath();
            ctx.moveTo(114, 294);
            ctx.bezierCurveTo(152, 314, 162, 266, 146, 212);
            ctx.lineTo(118, 66);
            ctx.bezierCurveTo(112, 24, 144, 22, 148, 64);
            ctx.bezierCurveTo(150, 114, 72, 162, 72, 212);
            ctx.bezierCurveTo(72, 262, 168, 268, 174, 212);
            ctx.bezierCurveTo(178, 166, 114, 152, 108, 198);
            ctx.bezierCurveTo(104, 222, 128, 232, 138, 218);
          };

          for (let j = 0; j < 4; j++) {
            ctx.save();
            ctx.translate((j - 1.5) * 2.2, j % 2 === 0 ? -1.8 : 1.8);
            ctx.lineWidth = 1.6;
            ctx.strokeStyle = 'rgba(20, 18, 16, 0.34)';
            drawClefPath();
            ctx.stroke();
            ctx.restore();
          }

          ctx.lineWidth = 12;
          ctx.strokeStyle = '#141210';
          drawClefPath();
          ctx.stroke();

          ctx.fillStyle = '#141210';
          ctx.beginPath();
          ctx.arc(104, 286, 16, 0, Math.PI * 2);
          ctx.fill();

          ctx.restore();
          applyCanvasGrain(ctx, w, h, 8, 515, true);
        },
      },
    };

    const PNG_CACHE = new Map();
    function getGeneratedPngUrl(key) {
      if (!key) return Promise.resolve('');
      if (PNG_CACHE.has(key)) return PNG_CACHE.get(key);
      const spec = PNG_PAINTERS[key];
      if (!spec) return Promise.resolve('');

      const promise = new Promise((resolve) => {
        try {
          const canvas = document.createElement('canvas');
          canvas.width = spec.w;
          canvas.height = spec.h;
          const ctx = canvas.getContext('2d');
          spec.paint(ctx, spec.w, spec.h);
          if (canvas.toBlob) {
            canvas.toBlob((blob) => {
              if (blob) resolve(URL.createObjectURL(blob));
              else resolve(canvas.toDataURL('image/png'));
            }, 'image/png');
          } else {
            resolve(canvas.toDataURL('image/png'));
          }
        } catch (e) {
          resolve('');
        }
      });

      PNG_CACHE.set(key, promise);
      return promise;
    }

    /* SmartAsset: Attempts asset file first; falls back seamlessly to procedural PNG */
    function SmartAsset({ src, pngKey, alt = '', className = '', imgClassName = 'object-cover', lazy = true }) {
      const normalizedSrc = resolveAssetUrl(src);
      const [activeSrc, setActiveSrc] = useState(normalizedSrc);
      const [usedFallback, setUsedFallback] = useState(false);

      useEffect(() => {
        const nextSrc = resolveAssetUrl(src);
        setActiveSrc(nextSrc);
        setUsedFallback(false);
      }, [src]);

      const handleError = useCallback(() => {
        if (usedFallback) return;
        setUsedFallback(true);
        getGeneratedPngUrl(pngKey).then((url) => {
          if (url) setActiveSrc(url);
        });
      }, [usedFallback, pngKey]);

      return (
        <div className={`w-full h-full relative ${className}`}>
          {activeSrc ? (
            <img
              src={activeSrc}
              alt={alt}
              loading={lazy ? 'lazy' : 'eager'}
              decoding="async"
              onError={handleError}
              className={`w-full h-full block ${imgClassName}`}
            />
          ) : null}
        </div>
      );
    }

    /* =========================================================================
       7. BRAND EMBLEMS, SPARKLES, TORN EDGES & TACTILE UI PRIMITIVES
       ========================================================================= */
    function VoidAntaraLogo({ className = 'w-8 h-7' }) {
      /* Interlocking serif VA orbit monogram + sparkle from logo.jpg */
      return (
        <svg viewBox="0 0 120 96" fill="none" className={className} aria-hidden="true">
          <path
            d="M12 16 H29 L45 64 L64 16 H70 L94 82 H102 V86 H76 V82 H83 L63 29 L43 78 H39 L19 20 H12 V16 Z"
            fill="currentColor"
          />
          <ellipse
            cx="57"
            cy="56"
            rx="44"
            ry="15"
            transform="rotate(-22 57 56)"
            stroke="currentColor"
            strokeWidth="3.6"
          />
          <path
            d="M106 51 C106 58 109 61 116 61 C109 61 106 64 106 71 C106 64 103 61 96 61 C103 61 106 58 106 51 Z"
            fill="currentColor"
          />
        </svg>
      );
    }

    function SparkleIcon({ className = 'w-6 h-6', hollow = false }) {
      return (
        <svg viewBox="0 0 40 40" fill="none" className={className} aria-hidden="true">
          <path
            d="M20 2 C21.2 13.5 26.5 18.8 38 20 C26.5 21.2 21.2 26.5 20 38 C18.8 26.5 13.5 21.2 2 20 C13.5 18.8 18.8 13.5 20 2 Z"
            fill={hollow ? 'none' : 'currentColor'}
            stroke="currentColor"
            strokeWidth={hollow ? '2.2' : '1.2'}
            strokeLinejoin="round"
          />
          {hollow && <circle cx="20" cy="20" r="2.6" fill="currentColor" />}
        </svg>
      );
    }

    function PixelGlitchCluster({ variant = 'a', className = 'w-7 h-7' }) {
      return (
        <svg viewBox="0 0 36 36" fill="currentColor" className={`pixel-glitch-loop ${className}`} aria-hidden="true">
          {variant === 'a' ? (
            <>
              <rect x="0" y="0" width="9" height="9" />
              <rect x="9" y="9" width="9" height="9" />
              <rect x="18" y="0" width="9" height="9" opacity="0.55" />
              <rect x="18" y="18" width="9" height="9" />
              <rect x="27" y="27" width="9" height="9" />
            </>
          ) : (
            <>
              <rect x="4" y="2" width="8" height="8" />
              <rect x="12" y="10" width="8" height="8" />
              <rect x="4" y="18" width="8" height="8" />
              <rect x="20" y="18" width="8" height="8" opacity="0.65" />
              <rect x="12" y="26" width="8" height="8" />
            </>
          )}
        </svg>
      );
    }

    function TornEdge({ position = 'top', fill = '#12110F' }) {
      const isTop = position === 'top';
      return (
        <div
          aria-hidden="true"
          className={`torn-edge-parallax w-[106%] -ml-[3%] overflow-hidden leading-none pointer-events-none select-none ${
            isTop ? '-mb-[2px]' : '-mt-[2px]'
          }`}
        >
          <svg
            viewBox="0 0 1440 84"
            preserveAspectRatio="none"
            className="w-full h-10 sm:h-14 md:h-18 block"
          >
            {isTop ? (
              <>
                {/* Pale exposed paper-fiber tear rim behind the torn charcoal layer */}
                <path
                  fill="#CFC5B2"
                  opacity="0.72"
                  d="M0,45 L28,41 L54,46 L86,33 L118,38 L152,24 L188,31 L226,28 L262,17 L304,26 L342,20 L382,32 L424,25 L466,14 L512,24 L554,19 L598,30 L642,15 L688,26 L734,21 L778,12 L824,27 L868,20 L914,32 L960,16 L1008,24 L1052,18 L1098,31 L1144,22 L1190,14 L1236,28 L1284,21 L1332,33 L1378,24 L1412,30 L1440,22 L1440,84 L0,84 Z"
                />
                {/* Primary organic hand-torn dark ink paper shape */}
                <path
                  fill={fill}
                  d="M0,51 L26,46 L52,52 L84,39 L116,44 L150,30 L186,37 L224,33 L260,23 L302,32 L340,25 L380,38 L422,30 L464,20 L510,30 L552,24 L596,36 L640,21 L686,32 L732,27 L776,18 L822,33 L866,26 L912,38 L958,22 L1006,30 L1050,24 L1096,37 L1142,28 L1188,20 L1234,34 L1282,27 L1330,39 L1376,30 L1410,35 L1440,28 L1440,84 L0,84 Z"
                />
              </>
            ) : (
              <>
                {/* Pale exposed paper-fiber tear rim beneath the torn charcoal edge */}
                <path
                  fill="#CFC5B2"
                  opacity="0.72"
                  d="M0,0 L1440,0 L1440,42 L1404,50 L1362,40 L1318,56 L1274,44 L1228,60 L1182,42 L1136,54 L1090,38 L1044,58 L998,46 L952,62 L906,44 L860,56 L814,38 L768,54 L722,42 L676,60 L630,44 L584,56 L538,38 L492,52 L446,42 L400,58 L354,40 L308,52 L262,36 L216,54 L170,42 L124,58 L78,44 L36,52 L0,46 Z"
                />
                {/* Primary organic hand-torn dark ink paper shape */}
                <path
                  fill={fill}
                  d="M0,0 L1440,0 L1440,36 L1404,44 L1362,34 L1318,50 L1274,38 L1228,54 L1182,36 L1136,48 L1090,32 L1044,52 L998,40 L952,56 L906,38 L860,50 L814,32 L768,48 L722,36 L676,54 L630,38 L584,50 L538,32 L492,46 L446,36 L400,52 L354,34 L308,46 L262,30 L216,48 L170,36 L124,52 L78,38 L36,46 L0,40 Z"
                />
              </>
            )}
          </svg>
        </div>
      );
    }

    /* ReactBits-inspired Magnet Component (Restyled for VoidAntara) */
    function Magnet({ children, strength = 0.24, className = '' }) {
      const ref = useRef(null);

      const handleMove = (e) => {
        if (getPrefersReducedMotion() || !ref.current) return;
        const rect = ref.current.getBoundingClientRect();
        const dx = (e.clientX - (rect.left + rect.width / 2)) * strength;
        const dy = (e.clientY - (rect.top + rect.height / 2)) * strength;
        ref.current.style.transform = `translate3d(${dx.toFixed(1)}px, ${dy.toFixed(1)}px, 0)`;
      };

      const handleLeave = () => {
        if (ref.current) ref.current.style.transform = 'translate3d(0, 0, 0)';
      };

      return (
        <div
          ref={ref}
          onMouseMove={handleMove}
          onMouseLeave={handleLeave}
          className={`inline-block transition-transform duration-300 ease-out ${className}`}
        >
          {children}
        </div>
      );
    }

    /* ReactBits-inspired Decrypted Pixel Text (Runs on intro + interactive hover) */
    function DecryptedPixelText({ text = 'Void', trigger = true, interactive = false }) {
      const [display, setDisplay] = useState(text);
      const intervalRef = useRef(null);

      const runScramble = useCallback(() => {
        if (getPrefersReducedMotion()) {
          setDisplay(text);
          return;
        }
        if (intervalRef.current) clearInterval(intervalRef.current);

        const glyphs = '▓▒░▪▫■□01V0ID';
        let frame = 0;
        const maxFrames = text.length * 4 + 4;
        intervalRef.current = setInterval(() => {
          frame += 1;
          const resolvedCount = Math.floor(frame / 4);
          const next = text
            .split('')
            .map((ch, idx) => {
              if (idx < resolvedCount) return ch;
              return glyphs[(frame + idx * 3) % glyphs.length];
            })
            .join('');
          setDisplay(next);
          if (frame >= maxFrames) {
            setDisplay(text);
            clearInterval(intervalRef.current);
            intervalRef.current = null;
          }
        }, 38);
      }, [text]);

      useEffect(() => {
        if (trigger) runScramble();
        return () => {
          if (intervalRef.current) clearInterval(intervalRef.current);
        };
      }, [trigger, runScramble]);

      return (
        <span
          onMouseEnter={interactive ? runScramble : undefined}
          className={interactive ? 'void-hover-interactive cursor-interactive' : ''}
        >
          {display}
        </span>
      );
    }

    /* Subtle Archival Parchment Grain + Hairline Paper Fibers Overlay (Pre-baked Tile for 60fps) */
    function FilmGrainOverlay() {
      const grainUrl = useMemo(() => {
        try {
          const size = 144;
          const canvas = document.createElement('canvas');
          canvas.width = size;
          canvas.height = size;
          const ctx = canvas.getContext('2d');
          if (!ctx) return '';
          const img = ctx.createImageData(size, size);
          const data = img.data;
          let seed = 1337;
          for (let i = 0; i < data.length; i += 4) {
            seed = (seed * 1664525 + 1013904223) >>> 0;
            const v = (seed >>> 24) & 255;
            data[i] = v;
            data[i + 1] = v;
            data[i + 2] = v;
            data[i + 3] = 48;
          }
          ctx.putImageData(img, 0, 0);
          return canvas.toDataURL('image/png');
        } catch (e) {
          return '';
        }
      }, []);

      return (
        <div
          className="film-grain-overlay"
          aria-hidden="true"
          style={grainUrl ? {
            backgroundImage: `url(${grainUrl})`,
            backgroundRepeat: 'repeat',
            backgroundSize: '144px 144px',
            transform: 'translateZ(0)'
          } : undefined}
        >
          {/* Delicate archival paper crackle veins & fine editorial creases */}
          <svg
            viewBox="0 0 1440 900"
            preserveAspectRatio="xMidYMid slice"
            fill="none"
            className="absolute inset-0 w-full h-full opacity-45"
          >
            <path
              d="M-20 184 L190 158 L312 192 L488 164 L640 202 L820 176"
              stroke="#141210"
              strokeWidth="0.6"
              strokeOpacity="0.32"
            />
            <path
              d="M920 -10 L964 148 L938 296 L1012 430 L980 612 L1046 910"
              stroke="#141210"
              strokeWidth="0.55"
              strokeOpacity="0.26"
            />
            <path
              d="M140 520 L294 568 L428 542 L592 608 L760 584 L924 646"
              stroke="#141210"
              strokeWidth="0.55"
              strokeOpacity="0.24"
            />
            <path
              d="M1140 160 L1248 214 L1324 196 L1460 254"
              stroke="#141210"
              strokeWidth="0.55"
              strokeOpacity="0.28"
            />
          </svg>
        </div>
      );
    }

    /* ClickSparkOverlay: Spawns tiny monochrome musical/ink sparks on click */
    function ClickSparkOverlay() {
      const [sparks, setSparks] = useState([]);

      useEffect(() => {
        if (getPrefersReducedMotion()) return undefined;
        const onClick = (e) => {
          const isDark = Boolean(e.target && e.target.closest && e.target.closest('[data-dark-section="true"]'));
          const id = `${Date.now()}-${Math.random()}`;
          const glyphs = ['♪', '♫', '✦', '♩'];
          const batch = Array.from({ length: 4 }, (_, i) => ({
            id: `${id}-${i}`,
            x: e.clientX,
            y: e.clientY,
            glyph: glyphs[i % glyphs.length],
            dx: `${(i - 1.5) * 20}px`,
            rot: `${(i % 2 === 0 ? 1 : -1) * (12 + i * 7)}deg`,
            dark: isDark,
          }));
          setSparks((prev) => [...prev.slice(-16), ...batch]);
          setTimeout(() => {
            setSparks((prev) => prev.filter((s) => !s.id.startsWith(id)));
          }, 920);
        };
        window.addEventListener('click', onClick);
        return () => window.removeEventListener('click', onClick);
      }, []);

      return (
        <div className="fixed inset-0 pointer-events-none z-[9990] overflow-hidden" aria-hidden="true">
          {sparks.map((s) => (
            <span
              key={s.id}
              className={`cursor-note-particle fixed text-sm font-serif select-none ${
                s.dark ? 'text-[#EDE7DA]' : 'text-[#141210]'
              }`}
              style={{
                left: `${s.x}px`,
                top: `${s.y}px`,
                '--dx': s.dx,
                '--rot': s.rot,
              }}
            >
              {s.glyph}
            </span>
          ))}
        </div>
      );
    }

    /* Reactive Music-Note Cursor (♪ / ♫ + Spinning Vinyl Ring + Note Trails — Zero React Re-renders) */
    function MusicNoteCursor({ isPlaying }) {
      const [enabled, setEnabled] = useState(false);

      const cursorRef = useRef(null);
      const ringRef = useRef(null);
      const glyphRef = useRef(null);
      const trailLayerRef = useRef(null);
      const posRef = useRef({ x: -100, y: -100, tx: -100, ty: -100 });
      const lastTrailAtRef = useRef(0);
      const isPlayingRef = useRef(isPlaying);
      isPlayingRef.current = isPlaying;

      useEffect(() => {
        const isFine = window.matchMedia && window.matchMedia('(pointer: fine)').matches;
        if (!isFine) return undefined;
        setEnabled(true);
        document.body.classList.add('music-cursor-active');

        let rafId = null;
        let lastTarget = null;
        let lastInteractive = false;
        let lastDark = false;
        let visibleState = false;

        const syncAppearance = () => {
          const ring = ringRef.current;
          const glyph = glyphRef.current;
          if (ring) {
            ring.className = `rounded-full border border-dashed transition-all duration-300 ${
              lastDark ? 'border-[#EDE7DA]/65' : 'border-[#141210]/60'
            } ${
              isPlayingRef.current ? 'music-cursor-ring-fast' : 'music-cursor-ring-spin'
            } ${lastInteractive ? 'w-11 h-11 scale-110' : 'w-8 h-8 scale-100'}`;
          }
          if (glyph) {
            const showDouble = lastInteractive || isPlayingRef.current;
            glyph.className = `absolute font-serif leading-none select-none transition-all duration-200 ${
              lastDark ? 'text-[#EDE7DA]' : 'text-[#141210]'
            } ${showDouble ? 'text-[17px] scale-110' : 'text-[15px] scale-100'}`;
            const nextChar = showDouble ? '♫' : '♪';
            if (glyph.textContent !== nextChar) glyph.textContent = nextChar;
          }
        };

        const animate = () => {
          rafId = null;
          const dx = posRef.current.tx - posRef.current.x;
          const dy = posRef.current.ty - posRef.current.y;
          posRef.current.x += dx * 0.28;
          posRef.current.y += dy * 0.28;
          if (cursorRef.current) {
            cursorRef.current.style.transform = `translate3d(${posRef.current.x.toFixed(2)}px, ${posRef.current.y.toFixed(2)}px, 0)`;
          }
          if (Math.abs(dx) > 0.12 || Math.abs(dy) > 0.12) {
            rafId = requestAnimationFrame(animate);
          }
        };

        const wake = () => {
          if (!rafId) rafId = requestAnimationFrame(animate);
        };

        const spawnTrail = (x, y, dark) => {
          const layer = trailLayerRef.current;
          if (!layer) return;
          while (layer.childElementCount >= 6) {
            layer.removeChild(layer.firstChild);
          }
          const glyphs = ['♪', '♫', '♩'];
          const el = document.createElement('span');
          el.className = `cursor-note-particle fixed text-[11px] select-none ${
            dark ? 'text-[#EDE7DA]/75' : 'text-[#141210]/65'
          }`;
          el.style.left = `${x}px`;
          el.style.top = `${y}px`;
          el.style.setProperty('--dx', `${Math.round((Math.random() - 0.5) * 28)}px`);
          el.style.setProperty('--rot', `${Math.round((Math.random() - 0.5) * 32)}deg`);
          el.textContent = glyphs[Math.floor(Math.random() * glyphs.length)];
          el.addEventListener('animationend', () => el.remove(), { once: true });
          layer.appendChild(el);
        };

        const onMove = (e) => {
          if (posRef.current.tx === -100) {
            posRef.current.x = e.clientX;
            posRef.current.y = e.clientY;
          }
          posRef.current.tx = e.clientX;
          posRef.current.ty = e.clientY;
          wake();

          if (!visibleState && cursorRef.current) {
            visibleState = true;
            cursorRef.current.style.opacity = '1';
          }

          const target = e.target;
          if (target !== lastTarget) {
            lastTarget = target;
            const interactive = Boolean(
              target && target.closest && target.closest('a, button, input, [role="button"], .cursor-interactive')
            );
            const dark = Boolean(target && target.closest && target.closest('[data-dark-section="true"]'));
            if (interactive !== lastInteractive || dark !== lastDark) {
              lastInteractive = interactive;
              lastDark = dark;
              syncAppearance();
            }
          }

          const now = performance.now();
          if (!getPrefersReducedMotion() && now - lastTrailAtRef.current > (isPlayingRef.current ? 160 : 300)) {
            lastTrailAtRef.current = now;
            spawnTrail(e.clientX, e.clientY, lastDark);
          }
        };

        const onLeave = () => {
          visibleState = false;
          lastTarget = null;
          if (cursorRef.current) cursorRef.current.style.opacity = '0';
        };

        window.addEventListener('mousemove', onMove, { passive: true });
        document.addEventListener('mouseleave', onLeave);

        return () => {
          document.body.classList.remove('music-cursor-active');
          if (rafId) cancelAnimationFrame(rafId);
          window.removeEventListener('mousemove', onMove);
          document.removeEventListener('mouseleave', onLeave);
        };
      }, []);

      if (!enabled) return null;

      return (
        <>
          {/* Subtle floating music note trail */}
          <div ref={trailLayerRef} className="fixed inset-0 pointer-events-none z-[9995] overflow-hidden" aria-hidden="true" />

          {/* Primary Music Note Pointer + Spinning Dashed Vinyl Ring */}
          <div
            ref={cursorRef}
            aria-hidden="true"
            className="fixed top-0 left-0 pointer-events-none z-[9999] transition-opacity duration-200"
            style={{ willChange: 'transform', opacity: 0 }}
          >
            <div className="relative -translate-x-1/2 -translate-y-1/2 flex items-center justify-center">
              <div
                ref={ringRef}
                className={`rounded-full border border-dashed transition-all duration-300 border-[#141210]/60 ${
                  isPlaying ? 'music-cursor-ring-fast' : 'music-cursor-ring-spin'
                } w-8 h-8 scale-100`}
              />
              <span
                ref={glyphRef}
                className="absolute font-serif leading-none select-none transition-all duration-200 text-[#141210] text-[15px] scale-100"
              >
                {isPlaying ? '♫' : '♪'}
              </span>
            </div>
          </div>
        </>
      );
    }

    /* =========================================================================
       8. PIXEL-BLOCK PRELOADER (Skipped on repeat visits)
       ========================================================================= */
    function Preloader({ onDone }) {
      const [phase, setPhase] = useState('assembling');

      useEffect(() => {
        const seen = sessionStorage.getItem('voidantara_preloader_seen_v1');
        if (seen || getPrefersReducedMotion()) {
          onDone();
          return undefined;
        }
        const t1 = setTimeout(() => setPhase('dissolving'), ANIM_CONFIG.preloaderDurationMs - 380);
        const t2 = setTimeout(() => {
          sessionStorage.setItem('voidantara_preloader_seen_v1', '1');
          onDone();
        }, ANIM_CONFIG.preloaderDurationMs);

        return () => {
          clearTimeout(t1);
          clearTimeout(t2);
        };
      }, [onDone]);

      return (
        <div
          data-dark-section="true"
          className={`fixed inset-0 z-[500] bg-[#12110F] text-[#EDE7DA] flex flex-col items-center justify-center transition-opacity duration-500 ${
            phase === 'dissolving' ? 'opacity-0 pointer-events-none' : 'opacity-100'
          }`}
        >
          <div className="flex flex-col items-center gap-4">
            <img
              src={resolveAssetUrl(SITE_CONFIG.logo || 'public/assets/logo.png')}
              alt={SITE_CONFIG.name || 'VoidAntara'}
              className="w-14 h-12 object-contain"
              onError={(e) => { e.currentTarget.style.display = 'none'; }}
            />
            <div className="flex items-baseline gap-0.5 text-4xl sm:text-5xl">
              <span className="font-pixel font-bold tracking-tight">
                <DecryptedPixelText text={HERO_CONFIG.wordmarkVoid || "Void"} trigger />
              </span>
              <span className="font-serifed italic text-[#EDE7DA]/90">{HERO_CONFIG.wordmarkLuma || "Antara"}</span>
            </div>
            <div className="w-36 h-[2px] bg-[#EDE7DA]/20 overflow-hidden rounded-full mt-2">
              <div
                className="h-full bg-[#EDE7DA] transition-all duration-1000 ease-out"
                style={{ width: phase === 'dissolving' ? '100%' : '76%' }}
              />
            </div>
          </div>
        </div>
      );
    }

    /* =========================================================================
       9. RESTORED EDITORIAL NAVBAR (Original Hairlines, Centered Pill, Mobile Torn Menu)
       ========================================================================= */
    function Navbar({ activeSection, onNavigate, onOpenSearch, onOpenStudio }) {
      const [scrolled, setScrolled] = useState(false);
      const [menuOpen, setMenuOpen] = useState(false);

      useEffect(() => {
        const onScroll = () => {
          const next = window.scrollY > 36;
          setScrolled((prev) => (prev === next ? prev : next));
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
      }, []);

      const navItems = [
        { id: 'home', label: 'Home' },
        { id: 'trending', label: 'Trending' },
        { id: 'templates', label: 'Templates' },
        { id: 'about', label: 'About' },
      ];

      const handleNavClick = (id) => {
        setMenuOpen(false);
        onNavigate(id);
      };

      return (
        <>
          <header
            className={`fixed top-0 left-0 right-0 z-[100] transition-all duration-500 ${
              scrolled ? 'py-2.5 sm:py-3' : 'py-4 sm:py-5'
            }`}
          >
            <div className="max-w-[1380px] mx-auto px-5 sm:px-8 lg:px-12">
              <div
                className={`relative flex items-center justify-between transition-all duration-500 rounded-full ${
                  scrolled
                    ? 'bg-[#E6DFD0]/86 backdrop-blur-md border border-[#141210]/22 shadow-[0_10px_30px_rgba(20,18,16,0.09)] px-4 sm:px-6 py-1.5'
                    : 'bg-transparent border border-transparent px-0 py-0'
                }`}
              >
                {/* Original Subtle Editorial Hairline Rule + Crosshair Ticks (visible at top) */}
                {!scrolled && (
                  <div
                    aria-hidden="true"
                    className="hidden lg:flex items-center justify-between pointer-events-none absolute inset-x-[170px] top-1/2 -translate-y-1/2 -z-10 opacity-45"
                  >
                    <div className="flex items-center gap-6 w-[24%]">
                      <span className="h-[1px] flex-1 bg-[#141210]/35" />
                    </div>
                    <div className="flex items-center gap-6 w-[24%]">
                      <span className="h-[1px] flex-1 bg-[#141210]/35" />
                    </div>
                  </div>
                )}

                {/* Left Brand Mark — User Logo + Site Name (Star SVG removed) */}
                <a
                  href="#home"
                  onClick={(e) => {
                    e.preventDefault();
                    handleNavClick('home');
                  }}
                  className="flex items-center gap-2.5 group min-h-[44px] pr-2"
                  aria-label={`${SITE_CONFIG.name || 'VoidAntara'} home`}
                >
                  <img
                    src={resolveAssetUrl(SITE_CONFIG.logo || 'public/assets/logo.png')}
                    alt={SITE_CONFIG.name || 'VoidAntara'}
                    className="w-7 h-7 sm:w-8 sm:h-8 object-contain rounded transition-transform duration-300 group-hover:scale-105"
                    onError={(e) => {
                      e.currentTarget.style.display = 'none';
                    }}
                  />
                  <span className="font-mono font-semibold text-base sm:text-[17px] tracking-[-0.04em] text-[#141210]">
                    {SITE_CONFIG.name || 'VoidAntara'}
                  </span>
                </a>

                {/* Center Pill Navigation (Desktop — Original Pill Framing) */}
                <nav
                  aria-label="Primary navigation"
                  className="hidden md:flex items-center gap-1 p-1 rounded-full border border-[#141210]/28 bg-[#E6DFD0]/75 backdrop-blur-sm shadow-[0_2px_10px_rgba(20,18,16,0.04)]"
                >
                  {navItems.map((item) => {
                    const isActive = activeSection === item.id;
                    return (
                      <button
                        key={item.id}
                        type="button"
                        onClick={() => handleNavClick(item.id)}
                        className={`px-5 py-1.5 rounded-full font-mono text-xs transition-all duration-300 min-h-[36px] ${
                          isActive
                            ? 'bg-[#141210] text-[#EDE7DA] shadow-sm'
                            : 'text-[#141210]/80 hover:text-[#141210] hover:bg-[#141210]/8'
                        }`}
                      >
                        {item.label}
                      </button>
                    );
                  })}
                </nav>

                {/* Right Utility Icons (Search + Pixel Grid + Mobile Hamburger) */}
                <div className="flex items-center gap-2 sm:gap-3">
                  <button
                    type="button"
                    onClick={onOpenSearch}
                    aria-label="Search tracks and templates"
                    className="w-11 h-11 rounded-full flex items-center justify-center text-[#141210] hover:bg-[#141210]/10 transition-colors"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.9" className="w-5 h-5">
                      <circle cx="11" cy="11" r="7" />
                      <path d="M20 20L16.2 16.2" strokeLinecap="round" />
                    </svg>
                  </button>

                  <button
                    type="button"
                    onClick={onOpenStudio}
                    aria-label="Open VoidAntara visualizer studio"
                    className="hidden sm:flex w-11 h-11 rounded-full items-center justify-center text-[#141210] hover:bg-[#141210]/10 transition-colors"
                  >
                    <svg viewBox="0 0 20 20" fill="currentColor" className="w-5 h-5" aria-hidden="true">
                      <rect x="2" y="2" width="5" height="5" />
                      <rect x="13" y="2" width="5" height="5" />
                      <rect x="7.5" y="7.5" width="5" height="5" />
                      <rect x="2" y="13" width="5" height="5" />
                      <rect x="13" y="13" width="5" height="5" />
                    </svg>
                  </button>

                  <button
                    type="button"
                    onClick={() => setMenuOpen((v) => !v)}
                    aria-label={menuOpen ? 'Close navigation menu' : 'Open navigation menu'}
                    aria-expanded={menuOpen}
                    className="md:hidden w-11 h-11 rounded-full border border-[#141210]/25 flex flex-col items-center justify-center gap-1.5 text-[#141210]"
                  >
                    <span
                      className={`block w-4 h-[1.6px] bg-current transition-transform duration-300 ${
                        menuOpen ? 'translate-y-[3.6px] rotate-45' : ''
                      }`}
                    />
                    <span
                      className={`block w-4 h-[1.6px] bg-current transition-transform duration-300 ${
                        menuOpen ? '-translate-y-[3.6px] -rotate-45' : ''
                      }`}
                    />
                  </button>
                </div>
              </div>
            </div>
          </header>

          {/* Full-screen Torn-Paper Mobile Menu */}
          {menuOpen && (
            <div
              data-dark-section="true"
              className="fixed inset-0 z-[120] md:hidden dark-paper-surface text-[#EDE7DA] flex flex-col justify-between p-6 pt-24"
            >
              <button
                type="button"
                onClick={() => setMenuOpen(false)}
                aria-label="Close menu"
                className="absolute top-5 right-5 w-11 h-11 rounded-full border border-[#EDE7DA]/30 flex items-center justify-center font-mono text-lg"
              >
                ×
              </button>

              <div className="flex flex-col gap-5">
                {navItems.map((item, i) => (
                  <button
                    key={item.id}
                    type="button"
                    onClick={() => handleNavClick(item.id)}
                    className="text-left flex items-baseline gap-4 py-2 border-b border-[#EDE7DA]/15"
                  >
                    <span className="font-mono text-xs text-[#EDE7DA]/50">0{i + 1}</span>
                    <span className="font-cormorant italic text-4xl">{item.label}</span>
                  </button>
                ))}
              </div>

              <div className="flex items-center justify-between pt-6 border-t border-[#EDE7DA]/15 font-mono text-xs text-[#EDE7DA]/65">
                <span>LYRICS • VISUALS • VIBES</span>
                <button
                  type="button"
                  onClick={() => {
                    setMenuOpen(false);
                    onOpenStudio();
                  }}
                  className="px-4 py-2 rounded-full bg-[#EDE7DA] text-[#141210]"
                >
                  Open Studio
                </button>
              </div>
            </div>
          )}
        </>
      );
    }

    /* =========================================================================
       10. HERO SECTION (Wordmark, Orbit Ellipse, Marble Collage, Vinyl Velocity)
       ========================================================================= */
    function HeroSection({ ready, scrollVelocityRef, onExplore }) {
      const heroRef = useRef(null);
      const orbitPathRef = useRef(null);
      const vinylSpinRef = useRef(null);
      const [voidTrigger, setVoidTrigger] = useState(false);

      /* Continuous vinyl rotation that reacts to Lenis scroll velocity (pauses when offscreen) */
      useEffect(() => {
        if (getPrefersReducedMotion()) return undefined;
        let angle = 0;
        let lastTs = performance.now();
        let rafId = null;
        let inView = true;

        const tick = (now) => {
          if (!inView) {
            rafId = null;
            return;
          }
          const dt = Math.min(0.05, (now - lastTs) / 1000);
          lastTs = now;
          const vel = scrollVelocityRef.current || 0;
          const speed = ANIM_CONFIG.vinylBaseDegPerSec + Math.abs(vel) * ANIM_CONFIG.vinylVelocityFactor * 22;
          angle = (angle + speed * dt) % 360;
          if (vinylSpinRef.current) {
            vinylSpinRef.current.style.transform = `rotate(${angle.toFixed(2)}deg)`;
          }
          rafId = requestAnimationFrame(tick);
        };

        const observer = heroRef.current
          ? new IntersectionObserver((entries) => {
              inView = entries.some((e) => e.isIntersecting);
              if (inView && !rafId) {
                lastTs = performance.now();
                rafId = requestAnimationFrame(tick);
              }
            })
          : null;
        if (observer && heroRef.current) observer.observe(heroRef.current);

        rafId = requestAnimationFrame(tick);
        return () => {
          if (rafId) cancelAnimationFrame(rafId);
          if (observer) observer.disconnect();
        };
      }, [scrollVelocityRef]);

      /* Choreographed Hero Intro Timeline + Scroll Parallax */
      useEffect(() => {
        if (!ready) return undefined;
        const reduced = getPrefersReducedMotion();
        setVoidTrigger(true);

        /* Draw orbit ellipse via anime.js */
        if (orbitPathRef.current && !reduced) {
          const len = orbitPathRef.current.getTotalLength ? orbitPathRef.current.getTotalLength() : 960;
          orbitPathRef.current.style.strokeDasharray = `${len}`;
          orbitPathRef.current.style.strokeDashoffset = `${len}`;
          runAnime(orbitPathRef.current, {
            strokeDashoffset: [len, 0],
            duration: 1300,
            delay: 240,
            easing: 'easeOutExpo',
          });
        }

        /* Idle twinkle loop on Hero sparkles via anime.js */
        let twinkleAnim = null;
        if (!reduced && heroRef.current) {
          const sparks = heroRef.current.querySelectorAll('.hero-spark-pop');
          if (sparks.length) {
            twinkleAnim = runAnime(sparks, {
              scale: [1, 1.24],
              opacity: [0.82, 1],
              duration: 1650,
              direction: 'alternate',
              loop: true,
              easing: 'easeInOutSine',
              delay: (el, i) => 900 + i * 380,
            });
          }
        }

        if (!window.gsap || !heroRef.current) return undefined;

        const ctx = window.gsap.context(() => {
          if (!reduced) {
            const tl = window.gsap.timeline({ defaults: { ease: 'power3.out' } });
            tl.fromTo('.hero-tagline', { y: 14, opacity: 0 }, { y: 0, opacity: 1, duration: 0.6 }, 0.05)
              .fromTo('.hero-luma-inner', { yPercent: 110, opacity: 0 }, { yPercent: 0, opacity: 1, duration: 0.9, ease: 'expo.out' }, 0.16)
              .fromTo('.hero-spark-pop', { scale: 0, rotate: -35, opacity: 0 }, { scale: 1, rotate: 0, opacity: 1, duration: 0.7, ease: 'back.out(1.8)', stagger: 0.12 }, 0.36)
              .fromTo('.hero-rect-slide', { y: 40, opacity: 0 }, { y: 0, opacity: 1, duration: 0.85, stagger: 0.1 }, 0.2)
              .fromTo('.hero-vinyl-enter', { scale: 0.72, rotate: -70, opacity: 0 }, { scale: 1, rotate: 0, opacity: 1, duration: 1.05, ease: 'expo.out' }, 0.25)
              .fromTo('.hero-statue-reveal', { clipPath: 'inset(18% 0% 0% 0%)', y: 36, opacity: 0 }, { clipPath: 'inset(0% 0% 0% 0%)', y: 0, opacity: 1, duration: 1.0, ease: 'expo.out' }, 0.28)
              .fromTo('.hero-stagger-last', { y: 18, opacity: 0 }, { y: 0, opacity: 1, duration: 0.65, stagger: 0.09 }, 0.52);

            /* Desktop multi-depth scroll parallax */
            const mm = window.gsap.matchMedia();
            mm.add('(min-width: 1024px)', () => {
              window.gsap.to('.parallax-statue', {
                y: -36,
                ease: 'none',
                scrollTrigger: { trigger: heroRef.current, start: 'top top', end: 'bottom top', scrub: 1 },
              });
              window.gsap.to('.parallax-vinyl', {
                y: 28,
                ease: 'none',
                scrollTrigger: { trigger: heroRef.current, start: 'top top', end: 'bottom top', scrub: 1 },
              });
              window.gsap.to('.parallax-rects', {
                y: -62,
                ease: 'none',
                scrollTrigger: { trigger: heroRef.current, start: 'top top', end: 'bottom top', scrub: 1 },
              });
              window.gsap.to('.parallax-flowers', {
                y: 44,
                ease: 'none',
                scrollTrigger: { trigger: heroRef.current, start: 'top top', end: 'bottom top', scrub: 1 },
              });
            });
          }
        }, heroRef);

        return () => {
          ctx.revert();
          if (twinkleAnim && typeof twinkleAnim.pause === 'function') twinkleAnim.pause();
        };
      }, [ready]);

      return (
        <section
          id="home"
          ref={heroRef}
          className="relative pt-24 sm:pt-28 lg:pt-32 pb-16 sm:pb-24 overflow-hidden"
        >
          {/* Top-Left Red Hanko Seal Stamp + Vintage Botanical Stamp (flower stamp.png) */}
          <div className="hero-stagger-last hidden sm:flex flex-col items-center gap-2.5 absolute left-5 lg:left-8 top-28 z-20 select-none">
            <div
              title={SITE_CONFIG.hankoTitle || "VoidAntara Archival Seal"}
              className="w-7 h-10 border-[1.5px] border-[#9A332A] text-[#9A332A] bg-[#E6DFD0]/70 rounded-[3px] flex flex-col items-center justify-center font-jp text-[10px] leading-tight font-medium shadow-sm"
            >
              <span>{(SITE_CONFIG.hankoSeal && SITE_CONFIG.hankoSeal[0]) || '音'}</span>
              <span>{(SITE_CONFIG.hankoSeal && SITE_CONFIG.hankoSeal[1]) || '響'}</span>
            </div>
            <div className="w-14 h-14 -rotate-6 opacity-90 shadow-md pointer-events-none">
              <SmartAsset
                src={ASSET_MANIFEST.flowerStamp}
                pngKey="flowerStamp"
                imgClassName="object-contain"
                alt="Botanical magnolia postage stamp"
                lazy={false}
              />
            </div>
          </div>

          {/* Left Bottom Decorative Halftone Cherry Blossoms */}
          <div className="parallax-flowers pointer-events-none select-none hidden md:block absolute -left-8 bottom-2 w-48 lg:w-60 aspect-square z-10 mix-blend-multiply opacity-90">
            <div className="w-full h-full flower-sway-slow">
              <SmartAsset
                src={ASSET_MANIFEST.flowersHeroLeft}
                pngKey="flowerHalftone"
                imgClassName="object-contain"
                alt="Halftone cherry blossoms decoration"
                lazy={false}
              />
            </div>
          </div>

          <div className="max-w-[1380px] mx-auto px-5 sm:px-8 lg:px-14 relative z-20">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-6 items-center">
              {/* LEFT COLUMN: Tagline, Wordmark + Orbit, Copy, CTA, Scroll Mouse */}
              <div className="lg:col-span-6 relative flex flex-col items-center lg:items-start text-center lg:text-left">
                <p className="hero-tagline font-mono text-[11px] sm:text-xs tracking-[0.26em] uppercase text-[#141210]/85 mb-3">
                  {HERO_CONFIG.tagline || 'LYRICS • VISUALS • VIBES'}
                </p>

                {/* Main Wordmark + Hand-Drawn Orbit Ellipse + Sparkles */}
                <div className="relative inline-flex items-baseline justify-center lg:justify-start py-4 px-2">
                  <svg
                    viewBox="0 0 540 190"
                    fill="none"
                    aria-hidden="true"
                    className="pointer-events-none absolute -inset-x-6 sm:-inset-x-10 -inset-y-5 w-[calc(100%+3rem)] sm:w-[calc(100%+5rem)] h-[calc(100%+2.5rem)]"
                  >
                    <path
                      ref={orbitPathRef}
                      d="M62 128 C28 162, 108 178, 286 122 C448 70, 514 26, 464 16 C406 4, 246 42, 162 84"
                      stroke="#141210"
                      strokeWidth="2.1"
                      strokeLinecap="round"
                    />
                  </svg>

                  <SparkleIcon className="hero-spark-pop w-8 h-8 sm:w-10 sm:h-10 text-[#141210] absolute -top-3 -right-2 sm:-right-7" />
                  <SparkleIcon className="hero-spark-pop w-4 h-4 text-[#141210] absolute bottom-2 left-1 sm:left-0" />

                  <h1 className="flex items-baseline leading-none">
                    <span className="hero-void-wrap wordmark-void text-[clamp(2.9rem,7.2vw,5.4rem)]">
                      <DecryptedPixelText text={HERO_CONFIG.wordmarkVoid || "Void"} trigger={voidTrigger} interactive />
                    </span>
                    <span className="reveal-mask -ml-0.5 sm:-ml-1">
                      <span className="hero-luma-inner inline-block wordmark-luma text-[clamp(3.3rem,8vw,6.1rem)]">
                        {HERO_CONFIG.wordmarkLuma || "Antara"}
                      </span>
                    </span>
                  </h1>
                </div>

                {/* Sketched Treble Clef (music cord.png) */}
                <div className="hero-stagger-last pointer-events-none select-none hidden sm:flex items-center gap-3 absolute left-[44%] top-[24%] z-[4] opacity-85 mix-blend-multiply">
                  <div className="w-10 h-14 -rotate-6">
                    <SmartAsset
                      src={ASSET_MANIFEST.musicCord}
                      pngKey="musicCord"
                      imgClassName="object-contain"
                      alt="Hand-sketched treble clef accent"
                      lazy={false}
                    />
                  </div>
                </div>

                <p className="hero-stagger-last mt-4 max-w-md font-mono text-xs sm:text-[13.5px] leading-relaxed text-[#141210]/80">
                  {HERO_CONFIG.pitch || 'Turn your favorite music into stunning lyric visuals.'}
                </p>

                {/* Explore Now Dark Pill CTA */}
                <div className="hero-stagger-last mt-8">
                  <Magnet strength={0.22}>
                    <button
                      type="button"
                      onClick={onExplore}
                      className="arrow-shift-trigger px-7 py-3.5 min-h-[50px] rounded-full bg-[#141210] text-[#EDE7DA] border border-[#141210] font-mono text-xs sm:text-[13px] inline-flex items-center gap-3.5 shadow-[0_12px_28px_rgba(20,18,16,0.22)] hover:bg-[#23201D] transition-colors"
                    >
                      <span className="text-[11px]">▶</span>
                      <span>{HERO_CONFIG.buttonText || 'Explore Now'}</span>
                      <span className="arrow-shift-icon inline-block">→</span>
                    </button>
                  </Magnet>
                </div>

                {/* Scroll Down Mouse Indicator */}
                <div className="hero-stagger-last mt-10 flex items-center gap-3 text-[#141210]/75">
                  <div className="w-5 h-8 rounded-full border border-[#141210]/65 flex justify-center pt-1.5">
                    <span className="scroll-wheel-dot block w-1 h-2 rounded-full bg-[#141210]" />
                  </div>
                  <span className="font-mono text-[11px] tracking-wide">{HERO_CONFIG.scrollText || 'Scroll Down'}</span>
                </div>
              </div>

              {/* RIGHT COLUMN: Layered Editorial Collage (Statue, Vinyl, Sky Door, Bird, Blossoms, JP Text) */}
              <div className="lg:col-span-6 relative flex justify-center lg:justify-end">
                <div className="relative w-full max-w-[460px] sm:max-w-[540px] aspect-[1/1.05]">
                  {/* Muted Blue Cloud & Arched Doorway Backdrop Rectangle (download.jpg) */}
                  <div className="parallax-rects hero-rect-slide absolute right-[13%] top-[0%] w-[28%] h-[58%] overflow-hidden border border-[#141210]/20 bg-[#3E5C7E] shadow-md z-[1]">
                    <SmartAsset
                      src={ASSET_MANIFEST.heroSkyRect}
                      pngKey="skyDoor"
                      alt="Halftone blue clouds and surreal arched doorway backdrop"
                      lazy={false}
                    />
                  </div>

                  {/* Ink Black Architectural Backdrop Rectangles */}
                  <div className="parallax-rects hero-rect-slide absolute right-[35%] top-[9%] w-[26%] h-[42%] bg-[#141210] z-[1]" />
                  <div className="parallax-rects hero-rect-slide absolute left-[6%] bottom-[12%] w-[11%] h-[22%] bg-[#3E5C7E]/90 z-[1]" />
                  <div className="parallax-rects hero-rect-slide absolute right-[4%] bottom-[26%] w-[14%] h-[26%] bg-[#141210] z-[1]" />

                  {/* Soaring Halftone Dove (bird.png) */}
                  <div className="hero-stagger-last pointer-events-none select-none absolute -left-[2%] top-[2%] w-[30%] aspect-square z-[6] mix-blend-multiply">
                    <div className="w-full h-full bird-glide">
                      <SmartAsset
                        src={ASSET_MANIFEST.bird}
                        pngKey="bird"
                        imgClassName="object-contain"
                        alt="Halftone soaring dove cutout"
                        lazy={false}
                      />
                    </div>
                  </div>

                  {/* Spinning Glossy Vinyl LP Record (musicdise.png) */}
                  <div className="parallax-vinyl hero-vinyl-enter absolute left-[5%] top-[22%] w-[56%] aspect-square z-[2] drop-shadow-[0_18px_28px_rgba(20,18,16,0.34)]">
                    <div ref={vinylSpinRef} className="w-full h-full">
                      <SmartAsset
                        src={ASSET_MANIFEST.heroVinyl}
                        pngKey="heroVinyl"
                        imgClassName="object-contain"
                        alt="Spinning glossy black vinyl record"
                        lazy={false}
                      />
                    </div>
                  </div>

                  {/* Marble Statue Bust with Black Tape Eye Bar & Blossoms (marblestatue.png) */}
                  <div className="parallax-statue hero-statue-reveal absolute right-[8%] bottom-[4%] w-[78%] h-[88%] z-[3] drop-shadow-[0_22px_34px_rgba(20,18,16,0.28)]">
                    <SmartAsset
                      src={ASSET_MANIFEST.heroStatue}
                      pngKey="heroStatue"
                      imgClassName="object-contain object-bottom"
                      alt="Classical marble statue bust with black tape censor bar and blossoms"
                      lazy={false}
                    />
                  </div>

                  {/* Foreground Halftone Blossoms Accent (flower.png) */}
                  <div className="parallax-flowers pointer-events-none select-none absolute left-[12%] bottom-[0%] w-[36%] aspect-square z-[5] mix-blend-multiply">
                    <div className="w-full h-full flower-sway">
                      <SmartAsset
                        src={ASSET_MANIFEST.flowersHeroMain}
                        pngKey="flowerHalftone"
                        imgClassName="object-contain"
                        alt="Halftone cherry blossoms accent"
                        lazy={false}
                      />
                    </div>
                  </div>

                  {/* Vertical Japanese Editorial Text ("Music is eternal") */}
                  <div
                    aria-label="Japanese Editorial Accent"
                    className="hero-stagger-last writing-vertical font-jp text-xs sm:text-sm tracking-[0.38em] text-[#141210]/85 absolute right-[0%] top-[22%] z-[6] select-none"
                  >
                    {HERO_CONFIG.jpText || '音楽は、永遠に'}
                  </div>

                  {/* Pixel-Glitch Squares + Spinning Music-Note Star (music star.png) */}
                  <PixelGlitchCluster variant="a" className="w-7 h-7 text-[#141210] absolute left-[18%] top-[10%] z-[6]" />
                  <PixelGlitchCluster variant="a" className="w-6 h-6 text-[#141210] absolute right-[2%] bottom-[16%] z-[6]" />
                  <div className="hero-stagger-last pointer-events-none select-none absolute right-[8%] -top-[6%] w-12 sm:w-14 aspect-square z-[6] mix-blend-multiply">
                    <div className="w-full h-full music-star-spin">
                      <SmartAsset
                        src={ASSET_MANIFEST.musicStar}
                        pngKey="musicStar"
                        imgClassName="object-contain"
                        alt="Five-note musical star emblem"
                        lazy={false}
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      );
    }

    /* =========================================================================
       11. TRENDING NOW SECTION (Vintage Collector Ticket Cards + Left Signal + Cassette + CD)
       ========================================================================= */
    function TrendingSection({ playingTrackId, onToggleTrack, onSelectTrack, onViewAll }) {
      const sectionRef = useRef(null);
      const waveBarsRef = useRef([]);
      const [hoveredCardId, setHoveredCardId] = useState(null);

      useEffect(() => {
        if (getPrefersReducedMotion()) return undefined;

        const activeBars = waveBarsRef.current.filter(Boolean);
        let waveAnim = null;
        if (activeBars.length) {
          waveAnim = runAnime(activeBars, {
            scaleY: [0.35, 1.15],
            duration: 680,
            direction: 'alternate',
            loop: true,
            easing: 'easeInOutSine',
            delay: (el, i) => i * 75,
          });
        }

        if (!window.gsap || !sectionRef.current) return undefined;

        const ctx = window.gsap.context(() => {
          window.gsap.fromTo(
            '.trending-card-reveal',
            { y: 44, opacity: 0, rotate: (i) => (i % 2 === 0 ? -2.5 : 2.5) },
            {
              y: 0,
              opacity: 1,
              rotate: 0,
              duration: 0.85,
              stagger: 0.11,
              ease: 'power3.out',
              scrollTrigger: { trigger: sectionRef.current, start: 'top 76%', once: true },
            }
          );
        }, sectionRef);

        return () => {
          ctx.revert();
          if (waveAnim && typeof waveAnim.pause === 'function') waveAnim.pause();
        };
      }, []);

      const leftSignalHeights = [22, 48, 30, 74, 96, 54, 38, 64, 28, 16];

      // Homepage: Filter to show ONLY the 4 featured tracks selected via Admin panel (featuredTracks)
      const featuredIds = (RAW_SITE_DATA && Array.isArray(RAW_SITE_DATA.featuredTracks) && RAW_SITE_DATA.featuredTracks.length > 0)
        ? RAW_SITE_DATA.featuredTracks
        : ['snooze', 'die-for-you', '505', 'casual'];

      let displayTracks = TRACKS_DATA.filter(t => featuredIds.includes(t.id));
      if (displayTracks.length < 4) {
        TRACKS_DATA.forEach(t => {
          if (displayTracks.length < 4 && !displayTracks.some(d => d.id === t.id)) {
            displayTracks.push(t);
          }
        });
      }
      displayTracks = displayTracks.slice(0, 4);

      return (
        <section id="trending" ref={sectionRef} data-dark-section="true" className="relative z-20">
          <TornEdge position="top" fill="#12110F" />

          <div className="dark-paper-surface text-[#EDE7DA] relative py-14 sm:py-20 lg:py-24 overflow-hidden">
            {/* Big Left Edge Audio Signal Waveform */}
            <div
              className="hidden md:flex items-center gap-1.5 absolute left-0 top-1/2 -translate-y-1/2 pl-2 opacity-80 pointer-events-none"
              aria-hidden="true"
            >
              {leftSignalHeights.map((h, idx) => (
                <span
                  key={idx}
                  ref={(el) => {
                    waveBarsRef.current[idx] = el;
                  }}
                  className="block w-[3px] rounded-full bg-[#EDE7DA]/85 origin-center"
                  style={{ height: `${h}px` }}
                />
              ))}
            </div>

            {/* Floating Vintage Halftone Cassette */}
            <div className="hidden lg:block cassette-float pointer-events-none select-none absolute right-24 top-4 w-[132px] aspect-[1.27/1] opacity-90 z-10 drop-shadow-[0_12px_22px_rgba(0,0,0,0.6)]">
              <SmartAsset
                src={ASSET_MANIFEST.cassette}
                pngKey="cassette"
                imgClassName="object-contain"
                alt="Vintage halftone audio cassette tape"
              />
            </div>

            {/* Right Edge Iridescent CD Disc */}
            <div className="hidden lg:block pointer-events-none select-none absolute -right-24 top-[56%] -translate-y-1/2 w-[240px] aspect-square opacity-95">
              <div className="w-full h-full music-cursor-ring-spin" style={{ animationDuration: '26s' }}>
                <SmartAsset
                  src={ASSET_MANIFEST.trendingCD}
                  pngKey="trendingCD"
                  imgClassName="object-contain"
                  alt="Iridescent compact disc graphic"
                />
              </div>
            </div>

            <div className="max-w-[1380px] mx-auto px-5 sm:px-8 lg:px-16 relative z-10">
              {/* Section Heading + SplitFlapText + Explore Full Page */}
              <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-5 mb-10 sm:mb-12">
                <div>
                  <div className="flex items-center gap-2 mb-2">
                    <span className="font-mono text-[10px] tracking-widest uppercase bg-[#EDE7DA]/15 text-[#EDE7DA] px-2.5 py-0.5 rounded border border-[#EDE7DA]/25">
                      02 // 04 &bull; FEATURED 4 SELECTIONS
                    </span>
                  </div>

                  {/* SplitFlapText Mechanical Departure Board Animation */}
                  <div className="py-1 min-h-[44px] sm:min-h-[50px] flex items-center">
                    <SplitFlapText
                      words={['TRENDING NOW', 'CURATED REEL', 'COMMUNITY PICKS', 'ANALOG PASSES']}
                      fontSize="clamp(20px, 2.8vw, 32px)"
                      tileColor="#EDE7DA"
                      textColor="#141210"
                      align="left"
                    />
                  </div>
                  <p className="font-mono text-xs text-[#EDE7DA]/65 mt-2">
                    4 curated tracks from the community vault. 3D tilt, flip for lyrics, and reticle lock on hover.
                  </p>
                </div>

                <div className="flex items-center gap-3">
                  <a
                    href="trending.php"
                    className="arrow-shift-trigger self-start sm:self-auto px-5 py-2.5 min-h-[44px] rounded-full border border-[#EDE7DA]/35 font-mono text-xs text-[#EDE7DA] inline-flex items-center gap-2.5 hover:bg-[#EDE7DA] hover:text-[#141210] transition-colors cursor-target"
                    data-cursor-label="ALL TRACKS"
                  >
                    <span>Explore Full Page ({TRACKS_DATA.length} Tracks)</span>
                    <span className="arrow-shift-icon inline-block">&rarr;</span>
                  </a>
                </div>
              </div>

              {/* 4 Featured Collector Track Cards Upgraded with TiltedCard */}
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-2 pb-4">
                {displayTracks.map((track) => {
                  const isPlaying = playingTrackId === track.id;
                  const isHovered = hoveredCardId === track.id;

                  return (
                    <div
                      key={track.id}
                      onMouseEnter={() => setHoveredCardId(track.id)}
                      onMouseLeave={() => setHoveredCardId(null)}
                      className="trending-card-reveal flex justify-center w-full"
                    >
                      <TiltedCard
                        imageSrc={resolveAssetUrl(track.image)}
                        altText={`${track.title} by ${track.artist}`}
                        captionText={`${track.title} • ${track.artist}`}
                        containerWidth="100%"
                        containerHeight="440px"
                        imageWidth="100%"
                        imageHeight="100%"
                        rotateAmplitude={12}
                        scaleOnHover={1.03}
                        glare={0.4}
                        shadow={0.5}
                        radius={16}
                        showTooltip={false}
                        className="cursor-target w-full"
                        data-cursor-label="FLIP & PLAY"
                        flipOnClick={true}
                        backContent={
                          <div className="w-full h-full p-5 flex flex-col justify-between bg-[#141210] text-[#EDE7DA] border border-[#EDE7DA]/25 rounded-2xl select-none">
                            <div>
                              <div className="flex items-center justify-between border-b border-[#EDE7DA]/15 pb-2.5">
                                <span className="font-mono text-[9px] tracking-widest text-[#EDE7DA]/60 uppercase">{track.serial || 'NO. 01'}</span>
                                <span className="text-[9px] font-mono tracking-widest px-2.5 py-0.5 rounded bg-[#EDE7DA]/10 text-[#EDE7DA] uppercase font-bold border border-[#EDE7DA]/20">
                                  {track.vibeTag || 'HI-FI'}
                                </span>
                              </div>

                              <p className="font-cormorant italic text-xl leading-snug mt-4 text-[#EDE7DA]">
                                "{track.lyricSample || 'Turn your favorite music into stunning lyric visuals.'}"
                              </p>

                              <p className="font-mono text-[11px] text-[#EDE7DA]/65 mt-3 leading-relaxed">
                                {track.stubNote || 'Analog tape saturation & subtle paper grain.'}
                              </p>
                            </div>

                            <div className="border-t border-[#EDE7DA]/15 pt-3 flex items-center justify-between font-mono text-[11px] text-[#EDE7DA]/75">
                              <span>BPM: <strong>{track.bpm || 120}</strong></span>
                              <span>{track.duration || '03:30'}</span>
                              <span className="text-[9.5px] text-[#EDE7DA]/60 font-mono">Click to flip &#8635;</span>
                            </div>

                            <button
                              type="button"
                              onClick={(e) => {
                                e.stopPropagation();
                                if (onSelectTrack) onSelectTrack(e, track);
                              }}
                              className="w-full mt-2.5 py-2 rounded-xl bg-[#EDE7DA] text-[#141210] font-mono text-[11px] font-bold hover:bg-white transition-colors flex items-center justify-center gap-1.5 cursor-pointer cursor-target"
                              data-cursor-label="SELECT DISC"
                            >
                              <span>Select Music Disc 🎵</span>
                            </button>
                          </div>
                        }
                      >
                        {/* Dark Vignette Overlay */}
                        <div className="absolute inset-0 bg-gradient-to-t from-[#141210]/95 via-[#141210]/35 to-transparent pointer-events-none" />

                        {/* Top Badges Parallax Layer */}
                        <TiltedCardLayer depth={22}>
                          <div className="absolute top-3.5 left-3.5 right-3.5 flex items-center justify-between pointer-events-none">
                            <span className="font-mono text-[9px] tracking-widest uppercase bg-[#141210]/85 backdrop-blur-md px-2.5 py-1 rounded text-[#EDE7DA] border border-[#EDE7DA]/20">
                              {track.serial || 'NO. 01'}
                            </span>
                            <span className="font-mono text-[9px] tracking-widest uppercase bg-[#EDE7DA]/95 text-[#141210] px-2.5 py-1 rounded font-bold shadow-sm">
                              {track.vibeTag || 'HI-FI'}
                            </span>
                          </div>
                        </TiltedCardLayer>

                        {/* Bottom Track Information & Play Trigger Layer */}
                        <TiltedCardLayer depth={34}>
                          <div className="absolute bottom-3.5 left-3.5 right-3.5 flex items-end justify-between">
                            <div className="text-left text-[#EDE7DA] pr-2">
                              <h3 className="font-cormorant text-xl font-bold leading-tight drop-shadow-md truncate max-w-[175px]">
                                {track.title}
                              </h3>
                              <p className="font-mono text-[11px] text-[#EDE7DA]/80 tracking-wide mt-0.5 truncate max-w-[175px]">
                                {track.artist}
                              </p>
                            </div>

                            <button
                              type="button"
                              onClick={(e) => {
                                e.stopPropagation();
                                if (onSelectTrack) {
                                  onSelectTrack(e, track);
                                } else {
                                  onToggleTrack(track);
                                }
                              }}
                              className="w-10 h-10 rounded-full border border-[#EDE7DA]/50 bg-[#141210]/85 text-[#EDE7DA] hover:bg-[#EDE7DA] hover:text-[#141210] flex items-center justify-center transition-all duration-300 shrink-0 cursor-target shadow-lg hover:scale-110 active:scale-95 cursor-pointer"
                              data-cursor-label="SELECT DISC"
                              aria-label={`Select ${track.title} music disc`}
                            >
                              <span className="text-xs ml-0.5">&#9654;</span>
                            </button>
                          </div>
                        </TiltedCardLayer>
                      </TiltedCard>
                    </div>
                  );
                })}
              </div>
            </div>
          </div>

          <TornEdge position="bottom" fill="#12110F" />
        </section>
      );
    }

    /* =========================================================================
       12. EXPLORE TEMPLATES SECTION (Fanned Polaroids, Washi Tape, Butterflies, Book)
       ========================================================================= */
    function TemplatesSection({ onSelectTemplate, onBrowseAll }) {
      const sectionRef = useRef(null);
      const [mobileIndex, setMobileIndex] = useState(0);
      const touchStartX = useRef(null);

      useEffect(() => {
        if (getPrefersReducedMotion() || !window.gsap || !sectionRef.current) return undefined;

        const ctx = window.gsap.context(() => {
          const mm = window.gsap.matchMedia();
          mm.add('(min-width: 1024px)', () => {
            window.gsap.fromTo(
              '.polaroid-card-desktop',
              {
                x: (i) => (1.5 - i) * 72,
                y: 26,
                rotate: (i) => (i - 1.5) * 10,
                opacity: 0.72,
              },
              {
                x: 0,
                y: (i) => (ANIM_CONFIG.polaroids[i] ? ANIM_CONFIG.polaroids[i].y : 0),
                rotate: (i) => (ANIM_CONFIG.polaroids[i] ? ANIM_CONFIG.polaroids[i].rotate : 0),
                opacity: 1,
                ease: 'power2.out',
                scrollTrigger: {
                  trigger: sectionRef.current,
                  start: 'top 78%',
                  end: 'center 52%',
                  scrub: 1,
                },
              }
            );
          });
        }, sectionRef);

        return () => ctx.revert();
      }, []);

      const handleTouchStart = (e) => {
        touchStartX.current = e.touches[0].clientX;
      };

      const handleTouchEnd = (e) => {
        if (touchStartX.current === null) return;
        const dx = e.changedTouches[0].clientX - touchStartX.current;
        touchStartX.current = null;
        if (Math.abs(dx) < 35) return;
        setMobileIndex((prev) =>
          dx < 0 ? (prev + 1) % TEMPLATES_DATA.length : (prev - 1 + TEMPLATES_DATA.length) % TEMPLATES_DATA.length
        );
      };

      return (
        <section id="templates" ref={sectionRef} className="relative py-20 sm:py-24 lg:py-28 overflow-hidden">
          {/* Fluttering Ink Butterflies above Polaroid Deck (butterfly.png) */}
          <div className="butterfly-flutter pointer-events-none select-none hidden md:block absolute right-[12%] top-6 w-32 lg:w-40 aspect-square z-10 mix-blend-multiply opacity-85">
            <SmartAsset
              src={ASSET_MANIFEST.butterfly}
              pngKey="butterfly"
              imgClassName="object-contain"
              alt="Ink-brush fluttering butterflies"
            />
          </div>

          <PixelGlitchCluster
            variant="b"
            className="w-7 h-9 text-[#141210] hidden lg:block absolute right-10 top-12 opacity-85"
          />

          <div className="max-w-[1380px] mx-auto px-5 sm:px-8 lg:px-14 relative z-20">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
              {/* Left Column: Heading, Copy, CTA, Floating Book + Treble Clef */}
              <div className="lg:col-span-4">
                <div className="flex items-start gap-3">
                  <SparkleIcon className="w-7 h-7 text-[#141210] mt-1 shrink-0" />
                  <div>
                    <div className="py-1 min-h-[48px] sm:min-h-[56px] flex items-center">
                      <SplitFlapText
                        words={['CANVAS TEMPLATES']}
                        loop={false}
                        fontSize="clamp(20px, 2.8vw, 32px)"
                        tileColor="#141210"
                        textColor="#EDE7DA"
                        align="left"
                      />
                    </div>
                    <p className="mt-4 font-mono text-xs sm:text-[13px] leading-relaxed text-[#141210]/75 max-w-xs">
                      Choose from aesthetic styles &amp; bring your lyrics to life.
                    </p>

                    <div className="mt-6">
                      <Magnet strength={0.2}>
                        <a
                          href="templates.php"
                          className="arrow-shift-trigger px-6 py-3 min-h-[46px] rounded-full bg-[#141210] text-[#EDE7DA] font-mono text-xs inline-flex items-center gap-3 shadow-[0_10px_24px_rgba(20,18,16,0.18)] hover:bg-[#24211E] transition-colors cursor-target"
                          data-cursor-label="TEMPLATES"
                        >
                          <span>Browse All Templates ({TEMPLATES_DATA.length} Total)</span>
                          <span className="arrow-shift-icon inline-block">&rarr;</span>
                        </a>
                      </Magnet>
                    </div>

                    {/* Open Halftone Vintage Lyric Book (book.png) + Sketched Treble Clef (music cord.png) */}
                    <div className="relative mt-7 flex items-end gap-3 pointer-events-none select-none">
                      <div className="book-float w-[138px] sm:w-[162px] aspect-[1.24/1] mix-blend-multiply">
                        <SmartAsset
                          src={ASSET_MANIFEST.book}
                          pngKey="book"
                          imgClassName="object-contain"
                          alt="Halftone open vintage poetry and lyric book"
                        />
                      </div>
                      <div className="w-10 sm:w-12 h-16 -rotate-12 mb-3 opacity-80 mix-blend-multiply">
                        <SmartAsset
                          src={ASSET_MANIFEST.musicCord}
                          pngKey="musicCord"
                          imgClassName="object-contain"
                          alt="Sketched charcoal musical treble clef"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {/* Right Polaroid Deck — Desktop 4-Card Fan */}
              <div className="lg:col-span-8 hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-4 items-center">
                {TEMPLATES_DATA.map((tpl, idx) => {
                  const rest = ANIM_CONFIG.polaroids[idx] || { rotate: 0, y: 0 };
                  return (
                    <button
                      key={tpl.id}
                      type="button"
                      onClick={(e) => onSelectTemplate(e, tpl)}
                      style={{ transform: `rotate(${rest.rotate}deg) translateY(${rest.y}px)` }}
                      className="polaroid-card-desktop polaroid-group cursor-interactive relative text-left bg-[#F4EFE4] p-2.5 pb-8 shadow-[0_14px_30px_rgba(20,18,16,0.18)] border border-[#141210]/15 transition-all duration-500 hover:!rotate-0 hover:!-translate-y-4 hover:z-30 hover:shadow-[0_24px_45px_rgba(20,18,16,0.28)]"
                    >
                      {/* Tactile Starry Night Washi Tape (tape.png) / Paperclip / Backing Strip */}
                      {tpl.detail === 'tape-top' && (
                        <span
                          aria-hidden="true"
                          className="pointer-events-none absolute -top-4 left-1/2 -translate-x-1/2 w-24 h-7 -rotate-3 z-20 drop-shadow-sm"
                        >
                          <SmartAsset
                            src={ASSET_MANIFEST.washiTape}
                            pngKey="washiTape"
                            imgClassName="object-contain"
                            alt=""
                          />
                        </span>
                      )}
                      {tpl.detail === 'none' && (
                        <span
                          aria-hidden="true"
                          className="pointer-events-none absolute -top-3.5 right-1 w-16 h-5 rotate-12 z-20 opacity-90 drop-shadow-sm"
                        >
                          <SmartAsset
                            src={ASSET_MANIFEST.washiTape}
                            pngKey="washiTape"
                            imgClassName="object-contain"
                            alt=""
                          />
                        </span>
                      )}
                      {tpl.detail === 'paperclip' && (
                        <span
                          aria-hidden="true"
                          className="absolute -top-3.5 left-1/2 -translate-x-1/2 w-5 h-9 rounded-full border-2 border-[#141210]/70"
                        />
                      )}
                      {tpl.detail === 'backing-strip' && (
                        <span
                          aria-hidden="true"
                          className="absolute top-6 -right-2 w-2.5 h-24 bg-[#141210]/85"
                        />
                      )}

                      <div className="aspect-[3/4] w-full overflow-hidden bg-[#141210]">
                        <SmartAsset
                          src={ASSET_MANIFEST[tpl.assetKey]}
                          pngKey={tpl.pngKey || tpl.assetKey}
                          alt={`${tpl.name} lyric visual template`}
                        />
                      </div>

                      <span className="polaroid-pill absolute -bottom-3 left-1/2 -translate-x-1/2 whitespace-nowrap px-4 py-1 rounded-full bg-[#EDE7DA] border border-[#141210]/75 font-mono text-[11px] text-[#141210] shadow-sm">
                        {tpl.name}
                      </span>
                    </button>
                  );
                })}
              </div>

              {/* Mobile Swipeable Stacked Polaroid Deck */}
              <div
                className="sm:hidden flex flex-col items-center"
                onTouchStart={handleTouchStart}
                onTouchEnd={handleTouchEnd}
              >
                <div className="relative w-[260px] h-[360px]">
                  {TEMPLATES_DATA.map((tpl, idx) => {
                    const offset = (idx - mobileIndex + TEMPLATES_DATA.length) % TEMPLATES_DATA.length;
                    const isTop = offset === 0;
                    const rot = offset === 0 ? -1.5 : offset === 1 ? 5 : offset === 2 ? -6 : 9;
                    const translateX = offset * 10;
                    const translateY = offset * 6;
                    return (
                      <button
                        key={tpl.id}
                        type="button"
                        onClick={(e) => (isTop ? onSelectTemplate(e, tpl) : setMobileIndex(idx))}
                        style={{
                          transform: `translate(${translateX}px, ${translateY}px) rotate(${rot}deg) scale(${1 - offset * 0.04})`,
                          zIndex: TEMPLATES_DATA.length - offset,
                          opacity: offset > 2 ? 0.5 : 1,
                        }}
                        className="absolute inset-0 text-left bg-[#F4EFE4] p-2.5 pb-9 shadow-[0_14px_30px_rgba(20,18,16,0.22)] border border-[#141210]/20 transition-all duration-500"
                      >
                        <div className="w-full h-full overflow-hidden bg-[#141210]">
                          <SmartAsset
                            src={ASSET_MANIFEST[tpl.assetKey]}
                            pngKey={tpl.pngKey || tpl.assetKey}
                            alt={`${tpl.name} template preview`}
                          />
                        </div>
                        <span className="absolute -bottom-3 left-1/2 -translate-x-1/2 whitespace-nowrap px-4 py-1 rounded-full bg-[#EDE7DA] border border-[#141210]/75 font-mono text-xs text-[#141210]">
                          {tpl.name}
                        </span>
                      </button>
                    );
                  })}
                </div>

                <div className="mt-8 flex items-center gap-3">
                  <button
                    type="button"
                    aria-label="Previous template"
                    onClick={() => setMobileIndex((p) => (p - 1 + TEMPLATES_DATA.length) % TEMPLATES_DATA.length)}
                    className="w-11 h-11 rounded-full border border-[#141210]/35 flex items-center justify-center font-mono text-sm"
                  >
                    ←
                  </button>
                  <span className="font-mono text-xs text-[#141210]/70">
                    {mobileIndex + 1} / {TEMPLATES_DATA.length}
                  </span>
                  <button
                    type="button"
                    aria-label="Next template"
                    onClick={() => setMobileIndex((p) => (p + 1) % TEMPLATES_DATA.length)}
                    className="w-11 h-11 rounded-full border border-[#141210]/35 flex items-center justify-center font-mono text-sm"
                  >
                    →
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
      );
    }

    /* =========================================================================
       13. CTA SECTION (Upgraded Metropolis Skyline + Headphones + Guitar + Lotus)
       ========================================================================= */
    function CtaSection({ onStartCreating }) {
      const sectionRef = useRef(null);

      useEffect(() => {
        if (getPrefersReducedMotion() || !window.gsap || !sectionRef.current) return undefined;

        const ctx = window.gsap.context(() => {
          window.gsap.fromTo(
            '.cta-headphones-slide',
            { x: 64, opacity: 0, rotate: 8 },
            {
              x: 0,
              opacity: 1,
              rotate: 0,
              duration: 1.0,
              ease: 'expo.out',
              scrollTrigger: { trigger: sectionRef.current, start: 'top 76%', once: true },
            }
          );

          const mm = window.gsap.matchMedia();
          mm.add('(min-width: 1024px)', () => {
            window.gsap.to('.cta-skyline-parallax', {
              y: -26,
              ease: 'none',
              scrollTrigger: { trigger: sectionRef.current, start: 'top bottom', end: 'bottom top', scrub: 1 },
            });
          });
        }, sectionRef);

        return () => ctx.revert();
      }, []);

      return (
        <section id="about" ref={sectionRef} data-dark-section="true" className="relative z-20">
          <TornEdge position="top" fill="#12110F" />

          <div className="dark-paper-surface text-[#EDE7DA] relative pt-16 sm:pt-22 pb-20 sm:pb-28 overflow-hidden">
            {/* Upgraded Detailed Architectural Metropolis Skyline Layer */}
            <div className="cta-skyline-parallax pointer-events-none select-none absolute inset-x-0 bottom-0 h-64 sm:h-80 lg:h-[340px] opacity-95">
              <SmartAsset
                src={ASSET_MANIFEST.ctaSkyline}
                pngKey="ctaSkyline"
                imgClassName="object-cover object-bottom"
                alt="Detailed monochrome Art Deco city skyline backdrop"
              />
            </div>

            <div className="max-w-[1380px] mx-auto px-5 sm:px-8 lg:px-14 relative z-10">
              <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 items-end">
                {/* Left Copy + Start Creating Ink-Fill Button */}
                <div className="lg:col-span-6 pb-2">
                  <h2 className="font-cormorant text-[clamp(2.3rem,5vw,3.9rem)] leading-[1.04] font-medium tracking-tight">
                    {CTA_CONFIG.heading || 'Create. Visualize.'}{' '}
                    <span className="italic font-semibold">{CTA_CONFIG.headingItalic || 'Feel.'}</span>
                  </h2>
                  <p className="mt-3 font-mono text-xs sm:text-[13px] text-[#EDE7DA]/75">
                    {CTA_CONFIG.subtitle || 'Your music, your style — in one place.'}
                  </p>

                  <div className="mt-8 flex items-center gap-5">
                    <Magnet strength={0.2}>
                      <button
                        type="button"
                        onClick={onStartCreating}
                        className="ink-fill-btn arrow-shift-trigger px-7 py-3.5 min-h-[50px] rounded-full bg-[#EDE7DA] text-[#141210] border border-[#EDE7DA] font-mono text-xs sm:text-[13px] font-medium inline-flex items-center gap-3 shadow-[0_14px_32px_rgba(0,0,0,0.45)]"
                      >
                        <SparkleIcon className="w-4 h-4" />
                        <span>{CTA_CONFIG.buttonText || 'Start Creating'}</span>
                        <span className="arrow-shift-icon inline-block">→</span>
                      </button>
                    </Magnet>

                    <div className="hidden sm:block pointer-events-none select-none w-11 h-11 opacity-75 invert">
                      <div className="w-full h-full music-star-spin">
                        <SmartAsset
                          src={ASSET_MANIFEST.musicStar}
                          pngKey="musicStar"
                          imgClassName="object-contain"
                          alt="Spinning musical star icon"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                {/* Right Collage: Lotus (flower lotus.png) + Electric Guitar (guiter.png) + Headphones (headphone.png) */}
                <div className="lg:col-span-6 relative flex justify-center lg:justify-end">
                  <div className="relative w-full max-w-[440px] sm:max-w-[500px] h-[250px] sm:h-[300px]">
                    {/* Halftone Lotus Bloom */}
                    <div className="flower-sway pointer-events-none select-none absolute left-0 bottom-0 w-44 sm:w-52 aspect-[1.15/1] z-20 drop-shadow-[0_12px_20px_rgba(0,0,0,0.5)]">
                      <SmartAsset
                        src={ASSET_MANIFEST.flowersCta}
                        pngKey="flowerLotus"
                        imgClassName="object-contain"
                        alt="Halftone ink lotus bloom"
                      />
                    </div>

                    {/* Stippled Electric Guitar */}
                    <div className="pointer-events-none select-none absolute left-[24%] -top-10 w-48 sm:w-56 aspect-square z-10 opacity-90 drop-shadow-[0_14px_24px_rgba(0,0,0,0.65)]">
                      <SmartAsset
                        src={ASSET_MANIFEST.guitar}
                        pngKey="guitar"
                        imgClassName="object-contain"
                        alt="Stippled black electric guitar"
                      />
                    </div>

                    {/* Studio Headphones with Bass-Pulse Loop */}
                    <div className="cta-headphones-slide pointer-events-none select-none absolute right-0 -bottom-6 w-56 sm:w-72 aspect-square z-30 drop-shadow-[0_20px_32px_rgba(0,0,0,0.7)]">
                      <div className="w-full h-full headphones-bass-loop">
                        <SmartAsset
                          src={ASSET_MANIFEST.ctaHeadphones}
                          pngKey="ctaHeadphones"
                          imgClassName="object-contain"
                          alt="Crosshatched studio headphones"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      );
    }

    /* =========================================================================
       14. FOOTER
       ========================================================================= */
    function Footer({ onSocialToast }) {
      const socials = (FOOTER_CONFIG.socials && FOOTER_CONFIG.socials.length)
        ? FOOTER_CONFIG.socials
        : [
            { name: 'Discord', icon: '◈', url: '#' },
            { name: 'Twitter / X', icon: '↗', url: '#' },
            { name: 'Instagram', icon: '◎', url: '#' },
            { name: 'GitHub', icon: '⌂', url: '#' },
          ];

      return (
        <footer className="paper-surface border-t border-[#141210]/15 py-5">
          <div className="max-w-[1380px] mx-auto px-5 sm:px-8 lg:px-14 flex flex-col sm:flex-row items-center justify-between gap-4 font-mono text-[11px] text-[#141210]/75">
            <div className="flex items-center gap-3">
              <img
                src={resolveAssetUrl(SITE_CONFIG.logo || 'public/assets/logo.png')}
                alt={SITE_CONFIG.name || 'VoidAntara'}
                className="w-6 h-5 object-contain rounded"
                onError={(e) => { e.currentTarget.style.display = 'none'; }}
              />
              <span>{FOOTER_CONFIG.rights || '© VoidAntara. All rights reserved.'}</span>
            </div>

            <div className="flex items-center gap-6">
              {socials.map((s) => (
                <a
                  key={s.name}
                  href={s.url || '#'}
                  target="_blank"
                  rel="noopener noreferrer"
                  onClick={(e) => {
                    if (!s.url || s.url === '#') {
                      e.preventDefault();
                      onSocialToast(`Opening ${SITE_CONFIG.name || 'VoidAntara'} on ${s.name}`);
                    }
                  }}
                  className="inline-flex items-center gap-1.5 hover:text-[#141210] transition-colors min-h-[40px]"
                >
                  <span>{s.icon}</span>
                  <span>{s.name}</span>
                </a>
              ))}
            </div>
          </div>
        </footer>
      );
    }

    /* =========================================================================
       15. SEARCH & LIVE VISUALIZER STUDIO MODALS (No alert/confirm)
       ========================================================================= */
    function SearchModal({ open, onClose, onPickTrack, onPickTemplate }) {
      const [query, setQuery] = useState('');
      if (!open) return null;

      const q = query.trim().toLowerCase();
      const tracks = TRACKS_DATA.filter(
        (t) => !q || t.title.toLowerCase().includes(q) || t.artist.toLowerCase().includes(q)
      );
      const templates = TEMPLATES_DATA.filter(
        (t) => !q || t.name.toLowerCase().includes(q) || t.desc.toLowerCase().includes(q)
      );

      return (
        <div
          className="fixed inset-0 z-[300] bg-[#12110F]/75 backdrop-blur-sm flex items-start justify-center pt-20 px-4"
          onClick={onClose}
        >
          <div
            className="w-full max-w-xl rounded-2xl bg-[#E6DFD0] border border-[#141210]/25 shadow-2xl overflow-hidden"
            onClick={(e) => e.stopPropagation()}
          >
            <div className="flex items-center gap-3 px-5 py-4 border-b border-[#141210]/15">
              <span className="font-mono text-sm">⌕</span>
              <input
                type="text"
                autoFocus
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                placeholder="Search tracks, artists, or templates..."
                className="flex-1 bg-transparent font-mono text-sm text-[#141210] placeholder:text-[#141210]/45 focus:outline-none"
              />
              <button
                type="button"
                onClick={onClose}
                className="px-2.5 py-1 rounded-full border border-[#141210]/25 font-mono text-[11px]"
              >
                ESC
              </button>
            </div>

            <div className="p-5 max-h-[65vh] overflow-y-auto space-y-5">
              <div>
                <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#141210]/55 mb-2">
                  Trending Visual Passes
                </p>
                <div className="space-y-1.5">
                  {tracks.map((t) => (
                    <button
                      key={t.id}
                      type="button"
                      onClick={() => {
                        onPickTrack(t);
                        onClose();
                      }}
                      className="w-full text-left px-3 py-2.5 rounded-xl hover:bg-[#141210]/8 flex items-center justify-between transition-colors"
                    >
                      <div>
                        <span className="font-mono text-xs font-semibold">{t.title}</span>
                        <span className="font-mono text-[11px] text-[#141210]/60 ml-2">— {t.artist}</span>
                      </div>
                      <span className="font-mono text-[10px] px-2 py-0.5 rounded-full border border-[#141210]/25">
                        {t.bpm} BPM
                      </span>
                    </button>
                  ))}
                  {tracks.length === 0 && (
                    <p className="font-mono text-xs text-[#141210]/50 px-3 py-2">No matching tracks.</p>
                  )}
                </div>
              </div>

              <div>
                <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#141210]/55 mb-2">
                  Aesthetic Templates
                </p>
                <div className="grid grid-cols-2 gap-2">
                  {templates.map((tpl) => (
                    <button
                      key={tpl.id}
                      type="button"
                      onClick={() => {
                        onPickTemplate(tpl);
                        onClose();
                      }}
                      className="text-left p-3 rounded-xl border border-[#141210]/15 hover:bg-[#141210] hover:text-[#EDE7DA] transition-colors"
                    >
                      <p className="font-cormorant italic text-lg leading-tight">{tpl.name}</p>
                      <p className="font-mono text-[10px] opacity-70 mt-1 line-clamp-2">{tpl.desc}</p>
                    </button>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      );
    }

    function StudioModal({ open, initialTemplate, activeMusic, onClose, onToast }) {
      const musicInfo = (window.normalizeTrackInfo ? window.normalizeTrackInfo(activeMusic) : (activeMusic?.track || activeMusic || {}));
      const [selectedTemplate, setSelectedTemplate] = useState(initialTemplate || TEMPLATES_DATA[0]);
      const [lyricLine, setLyricLine] = useState(
        musicInfo?.lyricSample || STUDIO_CONFIG.defaultLyric || 'Midnight echoes in ink & analog grain'
      );
      const [artistLine, setArtistLine] = useState(
        activeMusic ? `${musicInfo.title || 'Selected Track'} • ${musicInfo.artist || 'VoidAntara'}` : (STUDIO_CONFIG.defaultArtist || 'VOIDANTARA STUDIO • 33 RPM')
      );

      useEffect(() => {
        if (initialTemplate) setSelectedTemplate(initialTemplate);
      }, [initialTemplate]);

      useEffect(() => {
        if (activeMusic) {
          const info = (window.normalizeTrackInfo ? window.normalizeTrackInfo(activeMusic) : (activeMusic?.track || activeMusic || {}));
          if (info.lyricSample) setLyricLine(info.lyricSample);
          setArtistLine(`${info.title || 'Selected Track'} • ${info.artist || 'VoidAntara'}`);
        }
      }, [activeMusic]);

      if (!open) return null;

      return (
        <div
          data-dark-section="true"
          className="fixed inset-0 z-[310] bg-[#12110F]/82 backdrop-blur-md flex items-center justify-center p-4"
          onClick={onClose}
        >
          <div
            className="w-full max-w-3xl rounded-2xl bg-[#181715] text-[#EDE7DA] border border-[#EDE7DA]/20 shadow-2xl overflow-hidden"
            onClick={(e) => e.stopPropagation()}
          >
            <div className="flex items-center justify-between px-6 py-4 border-b border-[#EDE7DA]/12">
              <div className="flex items-center gap-2.5">
                <img
                  src={resolveAssetUrl(SITE_CONFIG.logo || 'public/assets/logo.png')}
                  alt={SITE_CONFIG.name || 'VoidAntara'}
                  className="w-7 h-6 object-contain rounded"
                  onError={(e) => { e.currentTarget.style.display = 'none'; }}
                />
                <span className="font-mono text-xs tracking-widest uppercase">{STUDIO_CONFIG.modalTitle || 'VoidAntara Lyric Studio'}</span>
              </div>
              <button
                type="button"
                onClick={onClose}
                className="w-9 h-9 rounded-full border border-[#EDE7DA]/25 flex items-center justify-center font-mono text-sm hover:bg-[#EDE7DA] hover:text-[#141210] transition-colors"
              >
                ×
              </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-12 gap-6 p-6">
              {/* Live Poster Preview */}
              <div className="md:col-span-6">
                <div className="relative aspect-[4/5] rounded-xl overflow-hidden border border-[#EDE7DA]/20 bg-[#12110F]">
                  <SmartAsset
                    src={ASSET_MANIFEST[selectedTemplate.assetKey]}
                    pngKey={selectedTemplate.pngKey || selectedTemplate.assetKey}
                    alt={`${selectedTemplate.name} live studio canvas`}
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-[#12110F]/92 via-[#12110F]/25 to-transparent flex flex-col justify-end p-5">
                    <span className="font-mono text-[9px] tracking-[0.22em] uppercase text-[#EDE7DA]/70">
                      {selectedTemplate.name} Preset
                    </span>
                    <p className="font-cormorant italic text-2xl sm:text-3xl leading-tight mt-1 text-[#EDE7DA]">
                      “{lyricLine || 'Type your lyric...'}”
                    </p>
                    <p className="font-mono text-[10px] tracking-[0.18em] uppercase text-[#EDE7DA]/65 mt-3">
                      {artistLine}
                    </p>
                  </div>
                </div>
              </div>

              {/* Studio Controls */}
              <div className="md:col-span-6 flex flex-col justify-between gap-5">
                <div className="space-y-4">
                  {activeMusic && (
                    <div className="p-3 rounded-xl bg-[#EDE7DA]/10 border border-[#EDE7DA]/20 flex items-center justify-between gap-2">
                      <div className="min-w-0">
                        <span className="block font-mono text-[9px] uppercase tracking-wider text-[#EDE7DA]/60">
                          Synced Audio Snippet
                        </span>
                        <p className="font-mono text-xs font-bold text-white truncate">
                          {musicInfo.title} <span className="font-normal text-[#EDE7DA]/70">({musicInfo.duration}s)</span>
                        </p>
                      </div>
                      <span className="px-2 py-0.5 rounded bg-[#9A332A] text-white font-mono text-[10px] font-bold shrink-0">
                        LOCKED
                      </span>
                    </div>
                  )}

                  <div>
                    <label className="block font-mono text-[10px] uppercase tracking-[0.2em] text-[#EDE7DA]/60 mb-2">
                      Template Style
                    </label>
                    <div className="grid grid-cols-2 gap-2">
                      {TEMPLATES_DATA.map((tpl) => {
                        const active = selectedTemplate.id === tpl.id;
                        return (
                          <button
                            key={tpl.id}
                            type="button"
                            onClick={() => setSelectedTemplate(tpl)}
                            className={`px-3 py-2 rounded-xl font-mono text-xs border text-left transition-colors ${
                              active
                                ? 'bg-[#EDE7DA] text-[#141210] border-[#EDE7DA]'
                                : 'border-[#EDE7DA]/20 text-[#EDE7DA]/80 hover:border-[#EDE7DA]/50'
                            }`}
                          >
                            {tpl.name}
                          </button>
                        );
                      })}
                    </div>
                  </div>

                  <div>
                    <label className="block font-mono text-[10px] uppercase tracking-[0.2em] text-[#EDE7DA]/60 mb-1.5">
                      Featured Lyric Line
                    </label>
                    <input
                      type="text"
                      value={lyricLine}
                      onChange={(e) => setLyricLine(e.target.value)}
                      className="w-full px-3.5 py-2.5 rounded-xl bg-[#12110F] border border-[#EDE7DA]/20 font-mono text-xs text-[#EDE7DA] focus:outline-none focus:border-[#EDE7DA]/60"
                    />
                  </div>

                  <div>
                    <label className="block font-mono text-[10px] uppercase tracking-[0.2em] text-[#EDE7DA]/60 mb-1.5">
                      Track Caption / Credit
                    </label>
                    <input
                      type="text"
                      value={artistLine}
                      onChange={(e) => setArtistLine(e.target.value)}
                      className="w-full px-3.5 py-2.5 rounded-xl bg-[#12110F] border border-[#EDE7DA]/20 font-mono text-xs text-[#EDE7DA] focus:outline-none focus:border-[#EDE7DA]/60"
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <a
                    href="template/Liquid_glass_center.html"
                    className="w-full py-3 rounded-full bg-[#9A332A] text-white font-mono text-xs font-bold hover:bg-[#b0372d] text-center flex items-center justify-center gap-2 shadow-lg transition-transform hover:scale-102"
                  >
                    <span>🚀 Launch Liquid Glass Studio (Cinematic 3D)</span>
                    <span>→</span>
                  </a>

                  <button
                    type="button"
                    onClick={() => {
                      onToast(`Exported "${selectedTemplate.name}" lyric reel config!`);
                      onClose();
                    }}
                    className="w-full py-2.5 rounded-full bg-[#EDE7DA] text-[#141210] font-mono text-xs font-semibold hover:bg-white transition-colors"
                  >
                    Export Reel Config (1080×1920)
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      );
    }

    /* =========================================================================
       16. ROOT APPLICATION
       ========================================================================= */
    function App() {
      const [preloaderDone, setPreloaderDone] = useState(false);
      const [activeSection, setActiveSection] = useState('home');
      const [playingTrackId, setPlayingTrackId] = useState(null);
      const [searchOpen, setSearchOpen] = useState(false);
      const [studioOpen, setStudioOpen] = useState(false);
      const [studioTemplate, setStudioTemplate] = useState(TEMPLATES_DATA[0]);
      const [toastMsg, setToastMsg] = useState('');
      const [activeMusic, setActiveMusic] = useState(() => {
        try {
          const s = localStorage.getItem('void_project_music');
          return s ? JSON.parse(s) : null;
        } catch(e) { return null; }
      });
      const [activeTemplate, setActiveTemplate] = useState(() => {
        try {
          const s = localStorage.getItem('void_project_template');
          return s ? JSON.parse(s) : null;
        } catch(e) { return null; }
      });
      const [snippetModalTrack, setSnippetModalTrack] = useState(null);

      const scrollVelocityRef = useRef(0);

      useEffect(() => {
        if (!window.__SITE_DATA__) {
          fetch('data/site_content.json')
            .then(r => r.json())
            .then(data => {
              if (data && data.site) {
                window.__SITE_DATA__ = data;
                if (data.site.title) document.title = data.site.title;
              }
            })
            .catch(() => {});
        }
      }, []);
      const handleVelocity = useCallback((v) => {
        scrollVelocityRef.current = v;
      }, []);
      const lenisRef = useLenis(handleVelocity);

      const showToast = useCallback((msg) => {
        setToastMsg(msg);
      }, []);

      useEffect(() => {
        if (!toastMsg) return undefined;
        const t = setTimeout(() => setToastMsg(''), 3000);
        return () => clearTimeout(t);
      }, [toastMsg]);

      /* Stop audio synth on unmount */
      useEffect(() => () => ambientSynth.stop(), []);

      /* Track active section on scroll (rAF-throttled) */
      useEffect(() => {
        const sections = ['home', 'trending', 'templates', 'about'];
        let scrollTick = 0;
        const onScroll = () => {
          if (scrollTick) return;
          scrollTick = requestAnimationFrame(() => {
            scrollTick = 0;
            const y = window.scrollY + 240;
            let current = 'home';
            for (let i = 0; i < sections.length; i++) {
              const el = document.getElementById(sections[i]);
              if (el && el.offsetTop <= y) current = sections[i];
            }
            setActiveSection((prev) => (prev === current ? prev : current));
          });
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => {
          if (scrollTick) cancelAnimationFrame(scrollTick);
          window.removeEventListener('scroll', onScroll);
        };
      }, []);

      /* Subtle scroll-scrub parallax on all torn-paper section dividers */
      useEffect(() => {
        if (getPrefersReducedMotion() || !window.gsap || !window.ScrollTrigger) return undefined;
        const ctx = window.gsap.context(() => {
          const mm = window.gsap.matchMedia();
          mm.add('(min-width: 1024px)', () => {
            window.gsap.utils.toArray('.torn-edge-parallax').forEach((edge, idx) => {
              window.gsap.to(edge, {
                xPercent: idx % 2 === 0 ? -1.4 : 1.4,
                ease: 'none',
                scrollTrigger: {
                  trigger: edge,
                  start: 'top bottom',
                  end: 'bottom top',
                  scrub: 1,
                },
              });
            });
          });
        });
        return () => ctx.revert();
      }, []);

      /* ESC key closes modals */
      useEffect(() => {
        const onKey = (e) => {
          if (e.key === 'Escape') {
            setSearchOpen(false);
            setStudioOpen(false);
          }
        };
        window.addEventListener('keydown', onKey);
        return () => window.removeEventListener('keydown', onKey);
      }, []);

      const scrollToSection = useCallback(
        (id) => {
          const el = document.getElementById(id);
          if (!el) return;
          if (lenisRef.current) {
            lenisRef.current.scrollTo(el, { offset: -20 });
          } else {
            el.scrollIntoView({ behavior: 'smooth' });
          }
        },
        [lenisRef]
      );

      const handleToggleTrack = useCallback(
        (track) => {
          if (playingTrackId === track.id) {
            ambientSynth.stop();
            setPlayingTrackId(null);
            showToast(`Paused "${track.title}"`);
          } else {
            setPlayingTrackId(track.id);
            ambientSynth.start(track);
            showToast(`♪ Now previewing "${track.title}" — ${track.artist}`);
          }
        },
        [playingTrackId, showToast]
      );

      const handleSelectTrack = useCallback((eOrTrack, maybeTrack) => {
        const track = maybeTrack || eOrTrack;
        const targetEl = (eOrTrack && eOrTrack.currentTarget) ? eOrTrack.currentTarget : null;

        if (window.flyItemToFolder && targetEl) {
          window.flyItemToFolder(targetEl, 'disc', track.image);
        }

        const newMusic = {
          track: track,
          startTime: 0,
          endTime: 30,
          duration: 30
        };
        setActiveMusic(newMusic);
        try {
          localStorage.setItem('void_project_music', JSON.stringify(newMusic));
        } catch(e) {}
        showToast(`🎵 Music disc "${track.title}" added to Project Folder!`);
      }, [showToast]);

      const handleSelectTemplate = useCallback((eOrTpl, maybeTpl) => {
        const tpl = maybeTpl || eOrTpl;
        const targetEl = (eOrTpl && eOrTpl.currentTarget) ? eOrTpl.currentTarget : null;

        if (window.flyItemToFolder && targetEl) {
          window.flyItemToFolder(targetEl, 'template', tpl.image);
        }

        setActiveTemplate(tpl);
        setStudioTemplate(tpl);
        try {
          localStorage.setItem('void_project_template', JSON.stringify(tpl));
        } catch(e) {}
        showToast(`🎬 Template Screen "${tpl.name || tpl.title}" added to Project Folder!`);
      }, [showToast]);

      const handleClearMusic = useCallback(() => {
        setActiveMusic(null);
        try { localStorage.removeItem('void_project_music'); } catch(e) {}
        showToast('Removed music disc from Project Folder');
      }, [showToast]);

      const handleClearTemplate = useCallback(() => {
        setActiveTemplate(null);
        try { localStorage.removeItem('void_project_template'); } catch(e) {}
        showToast('Removed template screen from Project Folder');
      }, [showToast]);

      const handleLaunchStudio = useCallback((music, tpl) => {
        const t = tpl || activeTemplate || studioTemplate;
        if (t) setStudioTemplate(t);
        showToast('🚀 Launching Cinematic Glass Studio...');
        setTimeout(() => {
          window.location.href = (t && t.templateFile) ? t.templateFile : 'template/Liquid_glass_center.html';
        }, 300);
      }, [activeTemplate, studioTemplate, showToast]);

      return (
        <div className="min-h-screen relative overflow-x-clip">
          {!preloaderDone && <Preloader onDone={() => setPreloaderDone(true)} />}

          <FilmGrainOverlay />
          <ClickSparkOverlay />
          <TargetCursor targetSelector=".cursor-target, .vintage-ticket-card, a, button" spinDuration={2} parallaxOn={true} hoverDuration={0.2} cursorColor="#EDE7DA" cursorColorOnTarget="#FFFFFF" showLabel={true} />
          <MusicNoteCursor isPlaying={Boolean(playingTrackId)} />

          <Navbar
            activeSection={activeSection}
            onNavigate={scrollToSection}
            onOpenSearch={() => setSearchOpen(true)}
            onOpenStudio={() => setStudioOpen(true)}
          />

          <main>
            <HeroSection
              ready={preloaderDone}
              scrollVelocityRef={scrollVelocityRef}
              onExplore={() => scrollToSection('trending')}
            />

            <TrendingSection
              playingTrackId={playingTrackId}
              onToggleTrack={handleToggleTrack}
              onSelectTrack={handleSelectTrack}
              onViewAll={() => setSearchOpen(true)}
            />

            <TemplatesSection
              onSelectTemplate={handleSelectTemplate}
              onBrowseAll={() => setStudioOpen(true)}
            />

            <CtaSection onStartCreating={() => setStudioOpen(true)} />
          </main>

          <Footer onSocialToast={showToast} />

          <SearchModal
            open={searchOpen}
            onClose={() => setSearchOpen(false)}
            onPickTrack={(track) => {
              scrollToSection('trending');
              handleSelectTrack(track);
            }}
            onPickTemplate={handleSelectTemplate}
          />

          <StudioModal
            open={studioOpen}
            initialTemplate={activeTemplate || studioTemplate}
            activeMusic={activeMusic}
            onClose={() => setStudioOpen(false)}
            onToast={showToast}
          />

          <ProjectFolderDock
            activeMusic={activeMusic}
            activeTemplate={activeTemplate}
            onOpenStudio={handleLaunchStudio}
            onClearMusic={handleClearMusic}
            onClearTemplate={handleClearTemplate}
            onExploreMusic={() => scrollToSection('trending')}
            onExploreTemplates={() => scrollToSection('templates')}
          />

          {/* Non-blocking Editorial Toast Banner */}
          {toastMsg && (
            <div
              role="status"
              aria-live="polite"
              className="fixed bottom-5 left-1/2 -translate-x-1/2 z-[400] px-5 py-2.5 rounded-full bg-[#141210] text-[#EDE7DA] border border-[#EDE7DA]/25 font-mono text-xs shadow-2xl whitespace-nowrap"
            >
              {toastMsg}
            </div>
          )}
        </div>
      );
    }

    class ErrorBoundary extends React.Component {
      constructor(props) {
        super(props);
        this.state = { hasError: false };
      }
      static getDerivedStateFromError() {
        return { hasError: true };
      }
      componentDidCatch(err, info) {
        console.warn("Recovered from interface state:", err, info);
      }
      render() {
        if (this.state.hasError) {
          return (
            <div className="min-h-screen bg-[#141210] text-[#EDE7DA] flex flex-col items-center justify-center p-6 text-center font-mono">
              <div className="w-16 h-16 rounded-full bg-[#9A332A]/20 border border-[#9A332A] flex items-center justify-center text-2xl mb-4">
                🎵
              </div>
              <h2 className="text-xl font-bold font-cormorant text-white mb-2">VoidAntara Studio Ready</h2>
              <p className="text-xs text-[#EDE7DA]/70 max-w-md mb-6">
                Your project selections are active in localStorage. Click below to continue smoothly.
              </p>
              <button
                type="button"
                onClick={() => {
                  this.setState({ hasError: false });
                  window.location.reload();
                }}
                className="px-6 py-2.5 rounded-full bg-[#EDE7DA] text-[#141210] font-bold text-xs hover:bg-white transition-all shadow-lg cursor-pointer"
              >
                Reload Studio
              </button>
            </div>
          );
        }
        return this.props.children;
      }
    }

    const rootEl = document.getElementById('root');
    if (rootEl) {
      ReactDOM.createRoot(rootEl).render(
        <ErrorBoundary>
          <App />
        </ErrorBoundary>
      );
    }
  </script>
</body>
</html>