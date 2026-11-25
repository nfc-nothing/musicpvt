<?php
$siteDataFile = __DIR__ . '/data/site_content.json';
$siteData = file_exists($siteDataFile) ? json_decode(file_get_contents($siteDataFile), true) : [];
$siteName = htmlspecialchars($siteData['site']['name'] ?? 'VoidAntara', ENT_QUOTES, 'UTF-8');
$siteTitle = 'Trending Music Tracks — ' . $siteName;
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

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,400;1,500;1,600;1,700&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:ital,wght@0,300;0,400;0,500;0,600;1,400&family=Noto+Serif+JP:wght@400;500&family=Oswald:wght@500;600;700&family=Pixelify+Sans:wght@500;600;700&display=swap"
    rel="stylesheet"
  />

  <!-- Tailwind CSS CDN -->
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

  <!-- React Bits CSS -->
  <link rel="stylesheet" href="public/css/react-bits.css" />

  <!-- Core Libraries: React 18, ReactDOM 18, Babel, GSAP, Tone.js -->
  <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
  <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tone/14.8.49/Tone.min.js"></script>

  <style>
    :root {
      --bg-paper: #E6DFD0;
      --bg-paper-light: #EDE7DA;
      --ink: #141210;
      --ink-deep: #12110F;
      --accent-slate: #3E5C7E;
      --seal-red: #9A332A;
    }
    *, *::before, *::after { box-sizing: border-box; }
    html, body {
      margin: 0; padding: 0;
      background-color: var(--bg-paper);
      color: var(--ink);
      font-family: 'JetBrains Mono', monospace;
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
    }
    .paper-surface {
      background-color: var(--bg-paper);
      background-image:
        radial-gradient(ellipse 75% 55% at 16% 10%, rgba(252, 248, 238, 0.72) 0%, transparent 60%),
        radial-gradient(ellipse 65% 50% at 85% 24%, rgba(206, 195, 175, 0.34) 0%, transparent 62%),
        radial-gradient(ellipse 70% 55% at 22% 76%, rgba(196, 184, 162, 0.28) 0%, transparent 65%),
        linear-gradient(to right, rgba(20, 18, 16, 0.022) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(20, 18, 16, 0.022) 1px, transparent 1px);
      background-size: 100% 100%, 100% 100%, 100% 100%, 64px 64px, 64px 64px;
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
  </style>
</head>
<body class="paper-surface min-h-screen flex flex-col">
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

    const RAW_SITE_DATA = window.__SITE_DATA__ || {};
    const SITE_CONFIG = RAW_SITE_DATA.site || {
      name: 'VoidAntara',
      title: 'VoidAntara — Lyrics • Visuals • Vibes',
      logo: 'public/assets/logo.png',
    };

        const DEFAULT_TRACKS_FALLBACK = [
      {
        id: 'snooze',
        serial: 'NO. 01',
        duration: '03:21',
        edition: 'VOIDANTARA PASS',
        vibeTag: 'HI-FI',
        featured: true,
        stubNote: 'Velvet R&B lyric cards synced to warm analog tape saturation & subtle paper grain.',
        title: 'Snooze',
        artist: 'SZA',
        image: 'public/assets/download.jpg',
        lyricSample: "I can't lose when I'm with you",
        bpm: 143,
        notes: [261.63, 329.63, 392, 493.88]
      },
      {
        id: 'die-for-you',
        serial: 'NO. 02',
        duration: '04:20',
        edition: 'MIDNIGHT REEL',
        vibeTag: 'LO-FI',
        featured: true,
        stubNote: 'Starlit horizon glow, nocturnal synth pulses, and luminous serif captions in motion.',
        title: 'Die For You',
        artist: 'The Weeknd',
        image: 'public/assets/download (1).jpg',
        lyricSample: "Even though we're going through it",
        bpm: 134,
        notes: [220, 261.63, 329.63, 392]
      },
      {
        id: '505',
        serial: 'NO. 03',
        duration: '04:13',
        edition: 'NOIR ARCHIVE',
        vibeTag: '33 RPM',
        featured: true,
        stubNote: 'High-contrast silver moonlit silhouette framed in kinetic ink typography and mono grain.',
        title: '505',
        artist: 'Arctic Monkeys',
        image: 'public/assets/download.jpg',
        lyricSample: "I'm going back to 505",
        bpm: 140,
        notes: [293.66, 349.23, 440, 523.25]
      },
      {
        id: 'casual',
        serial: 'NO. 04',
        duration: '03:52',
        edition: 'SURREAL CUT',
        vibeTag: '60 FPS',
        featured: true,
        stubNote: 'Halftone cobalt clouds, dreamy surreal portal framing, and handwritten cadence.',
        title: 'Casual',
        artist: 'Chappell Roan',
        image: 'public/assets/download (1).jpg',
        lyricSample: 'Knee deep in the passenger seat',
        bpm: 130,
        notes: [246.94, 311.13, 369.99, 466.16]
      },
      {
        id: 'starboy',
        serial: 'NO. 05',
        duration: '03:50',
        edition: 'CHROME CUT',
        vibeTag: 'HI-FI',
        featured: false,
        stubNote: 'Electric neon pulses through velvet shadows and crushed paper grain.',
        title: 'Starboy',
        artist: 'The Weeknd ft. Daft Punk',
        image: 'public/assets/headphone.png',
        lyricSample: "Look what you've done, I'm a starboy",
        bpm: 186,
        notes: [261.63, 329.63, 392, 523.25]
      },
      {
        id: 'kill-bill',
        serial: 'NO. 06',
        duration: '02:33',
        edition: 'BLOODLINE',
        vibeTag: 'LO-FI',
        featured: false,
        stubNote: 'Cinematic revenge chords with melancholic vinyl pop and nostalgic cadence.',
        title: 'Kill Bill',
        artist: 'SZA',
        image: 'public/assets/flower lotus.png',
        lyricSample: "I might kill my ex, not the best idea",
        bpm: 118,
        notes: [220, 293.66, 349.23, 440]
      }
    ];

    const ALL_TRACKS = (RAW_SITE_DATA.tracks && RAW_SITE_DATA.tracks.length)
      ? RAW_SITE_DATA.tracks
      : DEFAULT_TRACKS_FALLBACK;

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

    /* Ambient Synth Preview */
    const ambientSynth = (() => {
      let timerId = null;
      let synth = null;
      let step = 0;

      const stop = () => {
        if (timerId) { clearInterval(timerId); timerId = null; }
        if (synth) { try { synth.releaseAll(); } catch (e) {} }
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
            try { synth.triggerAttackRelease(chord, '4n'); } catch (e) {}
            step += 1;
          };
          playStep();
          timerId = setInterval(playStep, beatMs);
        } catch (e) {}
      };

      return { start, stop };
    })();

    /* =========================================================================
       REACTIVE MUSIC NOTE CURATOR CURSOR (Zero React Re-renders for 60fps)
       ========================================================================= */
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
              target && target.closest && target.closest('a, button, input, [role="button"], .cursor-interactive, .cursor-target')
            );
            const dark = Boolean(target && target.closest && target.closest('[data-dark-section="true"], .bg-\\[\\#141210\\], .dark-paper-surface'));
            if (interactive !== lastInteractive || dark !== lastDark) {
              lastInteractive = interactive;
              lastDark = dark;
              syncAppearance();
            }
          }

          const now = performance.now();
          if (now - lastTrailAtRef.current > (isPlayingRef.current ? 160 : 300)) {
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
          <div ref={trailLayerRef} className="fixed inset-0 pointer-events-none z-[9995] overflow-hidden" aria-hidden="true" />

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
       MAIN TRENDING PAGE APPLICATION
       ========================================================================= */
    function TrendingPage() {
      const [playingTrackId, setPlayingTrackId] = useState(null);
      const [tracksList, setTracksList] = useState(ALL_TRACKS);
      const [menuOpen, setMenuOpen] = useState(false);
      const [scrolled, setScrolled] = useState(false);
      const [snippetModalTrack, setSnippetModalTrack] = useState(null);
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

      const showToast = useCallback((msg) => {
        setToastMsg(msg);
      }, []);

      useEffect(() => {
        if (!toastMsg) return undefined;
        const t = setTimeout(() => setToastMsg(''), 3000);
        return () => clearTimeout(t);
      }, [toastMsg]);

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

      const handleClearMusic = useCallback(() => {
        setActiveMusic(null);
        try { localStorage.removeItem('void_project_music'); } catch(e) {}
        showToast('Removed audio snippet from Project');
      }, [showToast]);

      const handleClearTemplate = useCallback(() => {
        setActiveTemplate(null);
        try { localStorage.removeItem('void_project_template'); } catch(e) {}
        showToast('Removed template from Project');
      }, [showToast]);

      useEffect(() => {
        const onScroll = () => {
          const next = window.scrollY > 30;
          setScrolled((prev) => (prev === next ? prev : next));
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
      }, []);

      useEffect(() => {
        if (!window.__SITE_DATA__ || !window.__SITE_DATA__.tracks) {
          fetch('data/site_content.json')
            .then(r => r.json())
            .then(data => {
              if (data && data.tracks && data.tracks.length) {
                window.__SITE_DATA__ = data;
                setTracksList(data.tracks);
              }
            })
            .catch(() => {});
        }
      }, []);

      // Order: Home, Trending, Templates, About
      const navItems = [
        { id: 'home', label: 'Home', href: 'index.php#home' },
        { id: 'trending', label: 'Trending', href: 'trending.php', active: true },
        { id: 'templates', label: 'Templates', href: 'templates.php' },
        { id: 'about', label: 'About', href: 'index.php#about' },
      ];

      const handleToggleTrack = (track) => {
        if (playingTrackId === track.id) {
          ambientSynth.stop();
          setPlayingTrackId(null);
        } else {
          ambientSynth.start(track);
          setPlayingTrackId(track.id);
        }
      };

      return (
        <div className="relative min-h-screen flex flex-col justify-between">
          
          {/* Cursors matching Homepage */}
          <TargetCursor
            targetSelector=".cursor-target, .vintage-ticket-card, a, button"
            spinDuration={2}
            parallaxOn={true}
            hoverDuration={0.2}
            cursorColor="#EDE7DA"
            cursorColorOnTarget="#FFFFFF"
            showLabel={true}
          />
          <MusicNoteCursor isPlaying={Boolean(playingTrackId)} />

          {/* Navigation Bar (Identical Responsive Behavior as Homepage) */}
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
                    : 'bg-[#E6DFD0]/80 backdrop-blur-sm border border-[#141210]/15 px-4 sm:px-6 py-2'
                }`}
              >
                {/* Left Brand Mark */}
                <a
                  href="index.php"
                  className="flex items-center gap-2.5 group min-h-[44px] pr-2 cursor-target"
                  data-cursor-label="HOME"
                  aria-label={`${SITE_CONFIG.name || 'VoidAntara'} home`}
                >
                  <img
                    src={resolveAssetUrl(SITE_CONFIG.logo || 'public/assets/logo.png')}
                    alt={SITE_CONFIG.name || 'VoidAntara'}
                    className="w-7 h-7 sm:w-8 sm:h-8 object-contain rounded transition-transform duration-300 group-hover:scale-105"
                    onError={(e) => { e.currentTarget.style.display = 'none'; }}
                  />
                  <span className="font-mono font-semibold text-base sm:text-[17px] tracking-[-0.04em] text-[#141210]">
                    {SITE_CONFIG.name || 'VoidAntara'}
                  </span>
                </a>

                {/* Center Pill Navigation (Desktop Only) */}
                <nav
                  aria-label="Primary navigation"
                  className="hidden md:flex items-center gap-1 p-1 rounded-full border border-[#141210]/28 bg-[#E6DFD0]/75 backdrop-blur-sm shadow-[0_2px_10px_rgba(20,18,16,0.04)]"
                >
                  {navItems.map((item) => (
                    <a
                      key={item.id}
                      href={item.href}
                      className={`px-5 py-1.5 rounded-full font-mono text-xs transition-all duration-300 min-h-[36px] flex items-center cursor-target ${
                        item.active
                          ? 'bg-[#141210] text-[#EDE7DA] shadow-sm font-semibold'
                          : 'text-[#141210]/80 hover:text-[#141210] hover:bg-[#141210]/8'
                      }`}
                      data-cursor-label={item.label}
                    >
                      {item.label}
                    </a>
                  ))}
                </nav>

                {/* Right Side: Mobile Hamburger Button */}
                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={() => setMenuOpen((v) => !v)}
                    aria-label={menuOpen ? 'Close navigation menu' : 'Open navigation menu'}
                    aria-expanded={menuOpen}
                    className="md:hidden w-11 h-11 rounded-full border border-[#141210]/25 flex flex-col items-center justify-center gap-1.5 text-[#141210] cursor-target"
                    data-cursor-label="MENU"
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

          {/* Fullscreen Mobile Drawer (Same as Homepage) */}
          {menuOpen && (
            <div
              className="fixed inset-0 z-[120] md:hidden bg-[#141210] text-[#EDE7DA] flex flex-col justify-between p-6 pt-24"
            >
              <button
                type="button"
                onClick={() => setMenuOpen(false)}
                aria-label="Close menu"
                className="absolute top-5 right-5 w-11 h-11 rounded-full border border-[#EDE7DA]/30 flex items-center justify-center font-mono text-lg text-[#EDE7DA] cursor-target"
                data-cursor-label="CLOSE"
              >
                ×
              </button>
              <div className="space-y-4">
                {navItems.map((item, i) => (
                  <a
                    key={item.id}
                    href={item.href}
                    onClick={() => setMenuOpen(false)}
                    className={`text-left flex items-baseline gap-4 py-3 border-b border-[#EDE7DA]/15 text-2xl font-serif ${
                      item.active ? 'text-[#EDE7DA] font-bold' : 'text-[#EDE7DA]/75 hover:text-[#EDE7DA]'
                    }`}
                  >
                    <span className="font-mono text-xs opacity-50">0{i + 1}</span>
                    <span>{item.label}</span>
                  </a>
                ))}
              </div>
              <div className="text-center font-mono text-[11px] text-[#EDE7DA]/40 uppercase tracking-widest pt-8">
                {SITE_CONFIG.name || 'VoidAntara'} Archive
              </div>
            </div>
          )}

          {/* MAIN HERO & GRID */}
          <main className="max-w-[1380px] mx-auto px-5 sm:px-8 lg:px-12 pt-28 pb-16 flex-1 w-full space-y-10">
            
            {/* Top Heading Section with SplitFlapText (Stable container to eliminate layout shift) */}
            <div className="text-center space-y-3 max-w-3xl mx-auto pt-2">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#141210]/8 border border-[#141210]/15 text-[11px] font-mono tracking-widest uppercase text-[#141210]/75">
                <span>02 // 04</span>
                <span>•</span>
                <span>COMMUNITY REEL</span>
              </div>

              {/* SplitFlapText Departure Board (Fixed min-height avoids jumping) */}
              <div className="min-h-[84px] sm:min-h-[96px] flex items-center justify-center py-2">
                <SplitFlapText
                  words={['TRENDING TRACKS', 'CURATED ARCHIVE', 'ANALOG SESSIONS']}
                  fontSize="clamp(18px, 4vw, 32px)"
                  tileColor="#141210"
                  textColor="#EDE7DA"
                  align="center"
                />
              </div>
            </div>

            {/* Tracks Grid with TiltedCard */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 pb-12">
              {tracksList.map(track => {
                const isPlaying = playingTrackId === track.id;
                return (
                  <div key={track.id} className="flex justify-center">
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
                      showTooltip={true}
                      className="cursor-target w-full"
                      data-cursor-label="FLIP CARD"
                      flipOnClick={true}
                      backContent={
                        <div className="w-full h-full p-6 flex flex-col justify-between bg-[#141210] text-[#EDE7DA] border border-[#EDE7DA]/25 rounded-2xl select-none">
                          <div>
                            <div className="flex items-center justify-between border-b border-[#EDE7DA]/15 pb-3">
                              <span className="font-mono text-[10px] tracking-widest text-[#EDE7DA]/60 uppercase">{track.serial || 'NO. 01'}</span>
                              <span className="text-[9px] font-mono tracking-widest px-2.5 py-0.5 rounded bg-[#EDE7DA]/10 text-[#EDE7DA] uppercase font-bold border border-[#EDE7DA]/20">
                                {track.vibeTag || 'HI-FI'}
                              </span>
                            </div>

                            <p className="font-cormorant italic text-2xl leading-snug mt-5 text-[#EDE7DA]">
                              "{track.lyricSample || 'Turn your favorite music into stunning lyric visuals.'}"
                            </p>

                            <p className="font-mono text-xs text-[#EDE7DA]/65 mt-4 leading-relaxed">
                              {track.stubNote || 'Analog tape saturation & subtle paper grain.'}
                            </p>
                          </div>

                          <div className="border-t border-[#EDE7DA]/15 pt-3.5 flex items-center justify-between font-mono text-xs text-[#EDE7DA]/75">
                            <span>BPM: <strong>{track.bpm || 120}</strong></span>
                            <span>{track.duration || '03:30'}</span>
                            <span className="text-[10px] text-[#9A332A] bg-[#9A332A]/20 px-2 py-0.5 rounded border border-[#9A332A]/40 font-mono">
                              Click to flip ↺
                            </span>
                          </div>

                          <button
                            type="button"
                            onClick={(e) => {
                              e.stopPropagation();
                              handleSelectTrack(e, track);
                            }}
                            className="w-full mt-3 py-2.5 rounded-xl bg-[#EDE7DA] text-[#141210] font-mono text-xs font-bold hover:bg-white transition-colors flex items-center justify-center gap-1.5 cursor-pointer cursor-target"
                            data-cursor-label="SELECT DISC"
                          >
                            <span>Select Music Disc 🎵</span>
                          </button>
                        </div>
                      }
                    >
                      {/* Dark Vignette Overlay */}
                      <div className="absolute inset-0 bg-gradient-to-t from-[#141210]/95 via-[#141210]/35 to-transparent pointer-events-none" />

                      {/* Header Badge Layer */}
                      <TiltedCardLayer depth={24}>
                        <div className="absolute top-4 left-4 right-4 flex items-center justify-between pointer-events-none">
                          <span className="font-mono text-[10px] tracking-widest uppercase bg-[#141210]/85 backdrop-blur-md px-2.5 py-1 rounded text-[#EDE7DA] border border-[#EDE7DA]/20">
                            {track.serial || 'NO. 01'}
                          </span>
                          <span className="font-mono text-[10px] tracking-widest uppercase bg-[#EDE7DA]/95 text-[#141210] px-2.5 py-1 rounded font-bold shadow-sm">
                            {track.vibeTag || 'HI-FI'}
                          </span>
                        </div>
                      </TiltedCardLayer>

                      {/* Bottom Track Info & Play Button Layer */}
                      <TiltedCardLayer depth={36}>
                        <div className="absolute bottom-4 left-4 right-4 flex items-end justify-between">
                          <div className="text-left text-[#EDE7DA] pr-3">
                            <h3 className="font-cormorant text-2xl font-bold leading-tight drop-shadow-md">
                              {track.title}
                            </h3>
                            <p className="font-mono text-xs text-[#EDE7DA]/80 tracking-wide mt-0.5">
                              {track.artist}
                            </p>
                          </div>

                          <button
                            type="button"
                            onClick={(e) => {
                              e.stopPropagation();
                              handleSelectTrack(e, track);
                            }}
                            className="w-12 h-12 rounded-full bg-[#EDE7DA] text-[#141210] hover:scale-105 active:scale-95 flex items-center justify-center shadow-xl transition-transform cursor-target flex-shrink-0 cursor-pointer"
                            data-cursor-label="SELECT DISC"
                            aria-label={`Select ${track.title} for project`}
                          >
                            <span className="text-xs ml-0.5">▶</span>
                          </button>
                        </div>
                      </TiltedCardLayer>
                    </TiltedCard>
                  </div>
                );
              })}
            </div>

          </main>

          {/* FOOTER */}
          <footer className="border-t border-[#141210]/15 py-6 bg-[#EDE7DA]/75">
            <div className="max-w-[1380px] mx-auto px-5 sm:px-8 lg:px-12 flex flex-col sm:flex-row items-center justify-between gap-4 font-mono text-xs text-[#141210]/75">
              <div className="flex items-center gap-2.5">
                <img
                  src={resolveAssetUrl(SITE_CONFIG.logo || 'public/assets/logo.png')}
                  alt={SITE_CONFIG.name || 'VoidAntara'}
                  className="w-6 h-5 object-contain rounded"
                />
                <span className="font-semibold text-xs tracking-tight text-[#141210]">{SITE_CONFIG.name || 'VoidAntara'}</span>
              </div>
              <div className="text-[11px] tracking-wider uppercase text-[#141210]/65 text-center">
                VOIDANTARA ARCHIVE • ALL RIGHTS RESERVED.
              </div>
              <div className="flex items-center gap-4 text-xs">
                <a href="index.php" className="hover:underline cursor-target" data-cursor-label="HOME">Home</a>
                <a href="templates.php" className="hover:underline cursor-target" data-cursor-label="TEMPLATES">Templates</a>
              </div>
            </div>
          </footer>

          <ProjectFolderDock
            activeMusic={activeMusic}
            activeTemplate={activeTemplate}
            onOpenStudio={() => { window.location.href = 'template/Liquid_glass_center.html'; }}
            onClearMusic={handleClearMusic}
            onClearTemplate={handleClearTemplate}
            onExploreMusic={() => { window.scrollTo({ top: 0, behavior: 'smooth' }); }}
            onExploreTemplates={() => { window.location.href = 'templates.php'; }}
          />

          {toastMsg && (
            <div
              role="status"
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
        console.warn("Recovered from interface error:", err, info);
      }
      render() {
        if (this.state.hasError) {
          return (
            <div className="min-h-screen bg-[#141210] text-[#EDE7DA] flex flex-col items-center justify-center p-6 text-center font-mono">
              <div className="w-16 h-16 rounded-full bg-[#9A332A]/20 border border-[#9A332A] flex items-center justify-center text-2xl mb-4">
                🎵
              </div>
              <h2 className="text-xl font-bold font-cormorant text-white mb-2">VoidAntara Archive</h2>
              <p className="text-xs text-[#EDE7DA]/70 max-w-md mb-6">
                Your selected music disc is saved. Click below to continue smoothly.
              </p>
              <button
                type="button"
                onClick={() => {
                  this.setState({ hasError: false });
                  window.location.reload();
                }}
                className="px-6 py-2.5 rounded-full bg-[#EDE7DA] text-[#141210] font-bold text-xs hover:bg-white transition-all shadow-lg cursor-pointer"
              >
                Reload Archive
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
          <TrendingPage />
        </ErrorBoundary>
      );
    }
  </script>
</body>
</html>
