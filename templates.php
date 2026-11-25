<?php
$siteDataFile = __DIR__ . '/data/site_content.json';
$siteData = file_exists($siteDataFile) ? json_decode(file_get_contents($siteDataFile), true) : [];
$siteName = htmlspecialchars($siteData['site']['name'] ?? 'VoidAntara', ENT_QUOTES, 'UTF-8');
$siteTitle = 'Visual Style Templates — ' . $siteName;
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

  <!-- Core Libraries -->
  <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
  <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

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

    const DEFAULT_TEMPLATES_FALLBACK = [
      {
        id: 'liquid-glass-center',
        name: 'Liquid Glass Center',
        image: 'public/assets/download (1).jpg',
        detail: 'glass-rim',
        desc: 'Cinematic glassmorphic synced lyric player with dynamic reactive orbs, 3D card tilt & kinetic typography.',
        tags: ['Glassmorphic', 'Kinetic 3D', 'LRC Synced', 'Studio Visualizer'],
        templateFile: 'template/Liquid_glass_center.html',
        approved: true,
      },
      {
        id: 'noir-comic',
        name: 'Noir Comic',
        image: 'public/assets/download (1).jpg',
        detail: 'tape-top',
        desc: 'Manga-inspired ink shadows, stark grain, and expressive silhouette framing.',
        tags: ['Halftone', 'Monochrome', 'High Contrast'],
      },
      {
        id: 'sumi-ink',
        name: 'Sumi Ink',
        image: 'public/assets/flower.png',
        detail: 'none',
        desc: 'Delicate brush calligraphy blooms on warm handmade mulberry paper.',
        tags: ['Botanical', 'Calligraphy', 'Parchment'],
      },
      {
        id: 'astral',
        name: 'Astral',
        image: 'public/assets/download.jpg',
        detail: 'paperclip',
        desc: 'Luminous starlit figures, nocturnal tides, and celestial lyric trails.',
        tags: ['Celestial', 'Nocturnal', 'Luminous'],
      },
      {
        id: 'botanical',
        name: 'Botanical',
        image: 'public/assets/flower stamp.png',
        detail: 'backing-strip',
        desc: 'Pressed monochrome magnolia postage prints with archival ink borders.',
        tags: ['Archival', 'Vintage Stamp', 'Etched'],
      },
    ];

    const ALL_TEMPLATES = ((RAW_SITE_DATA.templates && RAW_SITE_DATA.templates.length)
      ? RAW_SITE_DATA.templates
      : DEFAULT_TEMPLATES_FALLBACK).filter(t => t.approved !== false);

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
    const toLength = value => (typeof value === 'number' ? `${value}px` : value);

    function TiltedCardLayer({ depth = 20, className = '', style, children }) {
      return (
        <div className={`tilted-card__layer ${className}`.trim()} style={{ '--tc-depth': depth, ...style }}>
          {children}
        </div>
      );
    }

    function TiltedCard({
      imageSrc,
      altText = 'Template style preview',
      captionText = '',
      containerHeight = '420px',
      containerWidth = '100%',
      imageHeight = '100%',
      imageWidth = '100%',
      rotateAmplitude = 12,
      scaleOnHover = 1.04,
      glare = 0.45,
      radius = 16,
      children,
      backContent = null,
      flipOnClick = true,
      className = '',
      onClick
    }) {
      const figureRef = useRef(null);
      const bodyRef = useRef(null);
      const [flipped, setFlipped] = useState(false);

      useEffect(() => {
        const figure = figureRef.current;
        const body = bodyRef.current;
        if (!figure || !body) return;

        let raf = 0;
        let rx = 0, ry = 0;

        const onMove = (e) => {
          const rect = figure.getBoundingClientRect();
          const cx = rect.left + rect.width / 2;
          const cy = rect.top + rect.height / 2;
          const nx = clamp((e.clientX - cx) / (rect.width / 2), -1, 1);
          const ny = clamp((e.clientY - cy) / (rect.height / 2), -1, 1);
          rx = -ny * rotateAmplitude;
          ry = nx * rotateAmplitude;
          body.style.transform = `rotateX(${rx.toFixed(2)}deg) rotateY(${(ry + (flipped ? 180 : 0)).toFixed(2)}deg) scale(${scaleOnHover})`;
        };

        const onLeave = () => {
          body.style.transform = `rotateX(0deg) rotateY(${flipped ? 180 : 0}deg) scale(1)`;
        };

        figure.addEventListener('pointermove', onMove);
        figure.addEventListener('pointerleave', onLeave);
        return () => {
          figure.removeEventListener('pointermove', onMove);
          figure.removeEventListener('pointerleave', onLeave);
        };
      }, [flipped, rotateAmplitude, scaleOnHover]);

      return (
        <figure
          ref={figureRef}
          className={`tilted-card ${className}`.trim()}
          style={{ width: toLength(containerWidth), height: toLength(containerHeight), perspective: '900px' }}
          onClick={() => {
            if (backContent && flipOnClick) setFlipped(v => !v);
            if (onClick) onClick();
          }}
        >
          <div
            ref={bodyRef}
            className="tilted-card__body w-full h-full transition-transform duration-200 ease-out"
            style={{ borderRadius: `${radius}px`, '--tc-radius': `${radius}px` }}
          >
            <div className="tilted-card__face tilted-card__front bg-[#141210]">
              {imageSrc && (
                <img src={imageSrc} alt={altText} className="tilted-card__img w-full h-full object-cover" />
              )}
              {children}
              <span className="tilted-card__glare" />
              <span className="tilted-card__rim" />
            </div>
            {backContent && (
              <div className="tilted-card__face tilted-card__back">
                {backContent}
                <span className="tilted-card__glare" />
                <span className="tilted-card__rim" />
              </div>
            )}
          </div>
        </figure>
      );
    }

    /* =========================================================================
       REACT BITS: SplitFlapText Component
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
       MAIN TEMPLATES PAGE
       ========================================================================= */
    function TemplatesPage() {
      const [templatesList, setTemplatesList] = useState(ALL_TEMPLATES);
      const [menuOpen, setMenuOpen] = useState(false);
      const [scrolled, setScrolled] = useState(false);
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

      const showToast = useCallback((msg) => {
        setToastMsg(msg);
      }, []);

      useEffect(() => {
        if (!toastMsg) return undefined;
        const t = setTimeout(() => setToastMsg(''), 3000);
        return () => clearTimeout(t);
      }, [toastMsg]);

      const handleSelectTemplate = useCallback((eOrTpl, maybeTpl) => {
        const tpl = maybeTpl || eOrTpl;
        const targetEl = (eOrTpl && eOrTpl.currentTarget) ? eOrTpl.currentTarget : null;

        if (window.flyItemToFolder && targetEl) {
          window.flyItemToFolder(targetEl, 'template', tpl.image);
        }

        setActiveTemplate(tpl);
        try {
          localStorage.setItem('void_project_template', JSON.stringify(tpl));
        } catch(e) {}
        showToast(`🎬 Template "${tpl.name}" added to Project Folder!`);
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

      const handleSaveSnippet = useCallback((snippet) => {
        setActiveMusic(snippet);
        setSnippetModalTrack(null);
        try {
          localStorage.setItem('void_project_music', JSON.stringify(snippet));
        } catch(e) {}
        showToast(`🎵 Snippet "${snippet.track.title}" (${snippet.duration}s) added to Project Folder!`);
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
        if (!window.__SITE_DATA__ || !window.__SITE_DATA__.templates) {
          fetch('data/site_content.json')
            .then(r => r.json())
            .then(data => {
              if (data && data.templates && data.templates.length) {
                window.__SITE_DATA__ = data;
                setTemplatesList(data.templates.filter(t => t.approved !== false));
              }
            })
            .catch(() => {});
        }
      }, []);

      // Order: Home, Trending, Templates, About
      const navItems = [
        { id: 'home', label: 'Home', href: 'index.php#home' },
        { id: 'trending', label: 'Trending', href: 'trending.php' },
        { id: 'templates', label: 'Templates', href: 'templates.php', active: true },
        { id: 'about', label: 'About', href: 'index.php#about' },
      ];

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
          <MusicNoteCursor isPlaying={false} />

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
            
            {/* Top Heading Section with SplitFlapText (Fixed min-height avoids jumping) */}
            <div className="text-center space-y-3 max-w-3xl mx-auto pt-2">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#141210]/8 border border-[#141210]/15 text-[11px] font-mono tracking-widest uppercase text-[#141210]/75">
                <span>03 // 04</span>
                <span>•</span>
                <span>AESTHETIC BLUEPRINTS</span>
              </div>

              {/* SplitFlapText Departure Board */}
              <div className="min-h-[84px] sm:min-h-[96px] flex items-center justify-center py-2">
                <SplitFlapText
                  words={['VISUAL TEMPLATES', 'STYLE BLUEPRINT', 'CANVAS ARCHIVES']}
                  fontSize="clamp(18px, 4vw, 32px)"
                  tileColor="#141210"
                  textColor="#EDE7DA"
                  align="center"
                />
              </div>
            </div>

            {/* Templates Grid with TiltedCard */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 pb-12">
              {templatesList.map(tpl => (
                <div key={tpl.id} className="flex justify-center">
                  <TiltedCard
                    imageSrc={resolveAssetUrl(tpl.image)}
                    altText={tpl.name}
                    captionText={tpl.name}
                    containerWidth="100%"
                    containerHeight="430px"
                    imageWidth="100%"
                    imageHeight="100%"
                    rotateAmplitude={12}
                    scaleOnHover={1.03}
                    glare={0.4}
                    radius={16}
                    className="cursor-target w-full"
                    data-cursor-label="FLIP INFO"
                    backContent={
                      <div className="w-full h-full p-6 flex flex-col justify-between bg-[#141210] text-[#EDE7DA] border border-[#EDE7DA]/25 rounded-2xl select-none">
                        <div>
                          <div className="flex items-center justify-between border-b border-[#EDE7DA]/15 pb-2.5">
                            <span className="font-mono text-[10px] tracking-widest text-[#EDE7DA]/60 uppercase">{tpl.detail || 'Standard'}</span>
                            <span className="text-[10px] font-mono tracking-widest px-2 py-0.5 rounded bg-[#EDE7DA]/10 text-[#EDE7DA]">
                              PRESET
                            </span>
                          </div>

                          <h4 className="font-cormorant text-2xl font-bold mt-4 text-[#EDE7DA]">{tpl.name}</h4>
                          <p className="font-mono text-xs text-[#EDE7DA]/75 mt-2 leading-relaxed">{tpl.desc}</p>
                          
                          <div className="flex flex-wrap gap-1.5 mt-4">
                            {(tpl.tags || []).map(t => (
                              <span key={t} className="text-[10px] font-mono px-2 py-0.5 rounded bg-[#EDE7DA]/15 text-[#EDE7DA]/90">
                                #{t}
                              </span>
                            ))}
                          </div>
                        </div>

                        <div className="border-t border-[#EDE7DA]/15 pt-3 flex items-center justify-between font-mono text-[11px] text-[#EDE7DA]/70">
                          <span>Click to flip ↺</span>
                          <span className="text-[#9A332A] font-bold">VoidAntara Style</span>
                        </div>

                        <button
                          type="button"
                          onClick={(e) => {
                            e.stopPropagation();
                            handleSelectTemplate(e, tpl);
                          }}
                          className="w-full mt-3 py-2.5 rounded-xl bg-[#EDE7DA] text-[#141210] font-mono text-xs font-bold hover:bg-white transition-colors flex items-center justify-center gap-1.5 cursor-pointer cursor-target"
                          data-cursor-label="SELECT STYLE"
                        >
                          <span>Select for Project 📂</span>
                        </button>
                      </div>
                    }
                  >
                    <div className="absolute inset-0 bg-gradient-to-t from-[#141210]/95 via-[#141210]/20 to-transparent pointer-events-none" />

                    <TiltedCardLayer depth={25}>
                      <div className="absolute top-4 left-4 right-4 flex items-center justify-between pointer-events-none">
                        <span className="font-mono text-[10px] tracking-widest uppercase bg-[#141210]/80 px-2.5 py-1 rounded text-[#EDE7DA] border border-[#EDE7DA]/20">
                          {tpl.detail || 'Classic'}
                        </span>
                      </div>
                    </TiltedCardLayer>

                    <TiltedCardLayer depth={35}>
                      <div className="absolute bottom-4 left-4 right-4 flex items-end justify-between">
                        <div className="text-left text-[#EDE7DA] pr-2">
                          <h3 className="font-cormorant text-2xl font-bold drop-shadow-md">{tpl.name}</h3>
                          <p className="font-mono text-xs text-[#EDE7DA]/75 line-clamp-2 mt-1 leading-relaxed">
                            {tpl.desc}
                          </p>
                        </div>
                        <button
                          type="button"
                          onClick={(e) => {
                            e.stopPropagation();
                            handleSelectTemplate(e, tpl);
                          }}
                          className="px-3 py-1.5 rounded-full bg-[#EDE7DA] text-[#141210] hover:bg-white text-[11px] font-mono font-bold shrink-0 cursor-target shadow-md transition-transform hover:scale-105 active:scale-95"
                          data-cursor-label="SELECT"
                        >
                          Select 📂
                        </button>
                      </div>
                    </TiltedCardLayer>
                  </TiltedCard>
                </div>
              ))}
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
                <a href="trending.php" className="hover:underline cursor-target" data-cursor-label="TRENDING">Trending</a>
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
            onExploreMusic={() => { window.location.href = 'trending.php'; }}
            onExploreTemplates={() => { window.scrollTo({ top: 0, behavior: 'smooth' }); }}
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
                🎬
              </div>
              <h2 className="text-xl font-bold font-cormorant text-white mb-2">VoidAntara Archive</h2>
              <p className="text-xs text-[#EDE7DA]/70 max-w-md mb-6">
                Your selected template screen is saved. Click below to continue smoothly.
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
          <TemplatesPage />
        </ErrorBoundary>
      );
    }
  </script>
</body>
</html>
