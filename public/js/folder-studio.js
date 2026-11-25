/* =========================================================================
   VOIDANTARA - React Bits Folder & Audio Trimmer Studio Components
   ========================================================================= */

(function(window) {
  const { Children, isValidElement, useCallback, useEffect, useLayoutEffect, useMemo, useRef, useState } = React;

  /* -------------------------------------------------------------------------
     Folder Component (React Bits)
     ------------------------------------------------------------------------- */
  const TAU = Math.PI * 2;
  const STEP = 1 / 240;
  const MAX_ITEMS = 6;
  const DEFAULT_ITEMS = [
    { title: 'Brand guidelines' },
    { title: 'Launch plan' },
    { title: 'Moodboard' }
  ];

  const PILE_TURN = [0.5, -1.1, 1.5, -0.7, 1.2, -1.6];
  const BOOK_REST = [
    { x: 0.14, y: -0.004, r: 1.4 },
    { x: 0.115, y: 0.008, r: -1.1 },
    { x: 0.09, y: 0, r: 0.6 }
  ];
  const BOOK_PEEK = [
    { x: 0.27, y: -0.016, r: 4.5 },
    { x: 0.2, y: 0.014, r: 1.2 },
    { x: 0.14, y: 0, r: -1.2 }
  ];
  const FLAP_REST = [
    { x: 0.16, rise: 0, r: 3 },
    { x: -0.17, rise: 0.05, r: -3.5 },
    { x: 0, rise: 0.1, r: 0.5 },
    { x: 0.25, rise: 0.08, r: 5 },
    { x: -0.25, rise: 0.09, r: -5.5 },
    { x: 0.08, rise: 0.12, r: 1.5 }
  ];
  const SPREAD_TURN = [4, -5, 3, -6, 5, -3];
  const SPREAD_DROP = [0.03, -0.04, 0.05, -0.02, 0.04, -0.05];
  const DROP_TURN = [-6, 4, -3, 7, -5, 3];
  const BOOK_ANGLES = { open: -180, receive: -24, peek: -13, rest: 0 };
  const FLAP_ANGLES = { open: -64, receive: -30, peek: -15, rest: 0 };
  const LAYERS = { out: 50, drag: 80, focus: 90 };

  const useIsomorphicLayoutEffect = typeof window === 'undefined' ? useEffect : useLayoutEffect;
  const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

  const parseColor = (() => {
    let context = null;
    const cache = new Map();
    return value => {
      const key = String(value);
      if (cache.has(key)) return cache.get(key);
      let rgb = [20, 18, 16];
      if (typeof document !== 'undefined') {
        context = context || document.createElement('canvas').getContext('2d', { willReadFrequently: true });
        if (context) {
          context.clearRect(0, 0, 1, 1);
          context.fillStyle = '#141210';
          context.fillStyle = key;
          context.fillRect(0, 0, 1, 1);
          rgb = Array.from(context.getImageData(0, 0, 1, 1).data.slice(0, 3));
        }
      }
      cache.set(key, rgb);
      return rgb;
    };
  })();

  const mixRgb = (rgb, target, amount) => rgb.map((channel, i) => Math.round(channel + (target[i] - channel) * amount));
  const rgbString = rgb => `rgb(${rgb[0]}, ${rgb[1]}, ${rgb[2]})`;
  const luma = rgb => (0.2126 * rgb[0] + 0.7152 * rgb[1] + 0.0722 * rgb[2]) / 255;

  const multiply = (a, b) => {
    const out = new Array(16);
    for (let col = 0; col < 4; col++) {
      for (let row = 0; row < 4; row++) {
        out[col * 4 + row] =
          a[row] * b[col * 4] + a[4 + row] * b[col * 4 + 1] + a[8 + row] * b[col * 4 + 2] + a[12 + row] * b[col * 4 + 3];
      }
    }
    return out;
  };

  const chain = (...list) => list.reduce(multiply);
  const move = (x, y, z = 0) => [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, x, y, z, 1];
  const stretch = (x, y) => [x, 0, 0, 0, 0, y, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1];

  const turnX = degrees => {
    const r = (degrees * Math.PI) / 180;
    const c = Math.cos(r);
    const s = Math.sin(r);
    return [1, 0, 0, 0, 0, c, s, 0, 0, -s, c, 0, 0, 0, 0, 1];
  };

  const turnY = degrees => {
    const r = (degrees * Math.PI) / 180;
    const c = Math.cos(r);
    const s = Math.sin(r);
    return [c, 0, -s, 0, 0, 1, 0, 0, s, 0, c, 0, 0, 0, 0, 1];
  };

  const turnZ = degrees => {
    const r = (degrees * Math.PI) / 180;
    const c = Math.cos(r);
    const s = Math.sin(r);
    return [c, s, 0, 0, -s, c, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1];
  };

  const toCss = m => `matrix3d(${m.map(v => (Math.abs(v) < 1e-7 ? 0 : Number(v.toFixed(6)))).join(', ')})`;

  const measureFolder = (book, scale, labelWidth, hasLabel, tabPosition) => {
    const w = (book ? 180 : 224) * scale;
    const h = (book ? 232 : 176) * scale;
    const tabH = 20 * scale;
    const r = 12 * scale;
    const shoulder = tabH * 0.75;
    const frontTop = book ? tabH : tabH + h * 0.3;
    const corners = book ? [0, r, r, 0] : [r, r, r, r];
    const maxTab = w - corners[0] - corners[1] - shoulder * 2 - 10 * scale;
    const tabW = clamp(hasLabel ? labelWidth + 14 * scale + shoulder : w * 0.34, Math.min(w * 0.3, maxTab), maxTab);
    const tabX =
      tabPosition === 'left'
        ? corners[0] + 5 * scale + shoulder
        : tabPosition === 'center'
          ? (w - tabW) / 2
          : w - corners[1] - 5 * scale - shoulder - tabW;
    const docW = book ? w * 0.8 : w * 0.54;
    return {
      book,
      scale,
      w,
      h,
      tabH,
      r,
      shoulder,
      corners,
      tabW,
      tabX,
      frontTop,
      frontH: tabH + h - frontTop,
      docW,
      docH: docW * (book ? 1.28 : 1.22)
    };
  };

  const backPath = g => {
    const { w, h, tabX, tabW, tabH, shoulder } = g;
    const [tl, tr, br, bl] = g.corners;
    const top = tabH;
    const bottom = tabH + h;
    return [
      `M 0 ${top + tl}`,
      `Q 0 ${top} ${tl} ${top}`,
      `L ${tabX - shoulder} ${top}`,
      `C ${tabX} ${top} ${tabX} 0 ${tabX + shoulder} 0`,
      `L ${tabX + tabW - shoulder} 0`,
      `C ${tabX + tabW} 0 ${tabX + tabW} ${top} ${tabX + tabW + shoulder} ${top}`,
      `L ${w - tr} ${top}`,
      `Q ${w} ${top} ${w} ${top + tr}`,
      `L ${w} ${bottom - br}`,
      `Q ${w} ${bottom} ${w - br} ${bottom}`,
      `L ${bl} ${bottom}`,
      `Q 0 ${bottom} 0 ${bottom - bl}`,
      'Z'
    ].join(' ');
  };

  const DocumentCard = ({ title, description }) => (
    <div className="folder-doc">
      {title ? <span className="folder-doc-title">{title}</span> : <span className="folder-doc-heading" />}
      {description && <span className="folder-doc-text">{description}</span>}
      <span className="folder-doc-line" style={{ width: '92%' }} />
      <span className="folder-doc-line" style={{ width: '84%' }} />
      <span className="folder-doc-line" style={{ width: '88%' }} />
      <span className="folder-doc-block" />
      <span className="folder-doc-line" style={{ width: '80%' }} />
    </div>
  );

  const renderItem = item => {
    if (isValidElement(item)) return item;
    if (typeof item === 'string' || typeof item === 'number') return <DocumentCard title={String(item)} />;
    if (item && typeof item === 'object') {
      if (item.image) {
        return (
          <div className="folder-photo">
            <img src={item.image} alt={item.alt ?? item.title ?? ''} draggable={false} />
          </div>
        );
      }
      if (item.content) return item.content;
      return <DocumentCard title={item.title} description={item.description} />;
    }
    return <DocumentCard />;
  };

  const placeBook = (rank, count, phase, layout, g) => {
    const pileX = g.w / 2;
    const pileY = g.frontTop + g.frontH / 2;
    if (phase === 'open') {
      if (layout === 'row') {
        const gap = 10 * g.scale;
        const fit = Math.min(0.8, (g.w * 1.86 - gap * (count - 1)) / (count * g.docW));
        const step = g.docW * fit + gap;
        return { x: g.w / 2 + (rank - (count - 1) / 2) * step, y: pileY, z: 0, r: 0, ry: 0, s: fit };
      }
      if (layout === 'stack') {
        const fit = 0.74;
        const strip = Math.min(26 * g.scale, (g.h * 0.36) / Math.max(1, count - 1));
        const top = g.frontTop + 14 * g.scale + (count - 1) * strip;
        return { x: g.w, y: top + (g.docH * fit) / 2 - rank * strip, z: 0, r: 0, ry: 0, s: fit };
      }
      const fit = count > 4 ? 0.58 : count > 3 ? 0.64 : 0.72;
      const span = count === 2 ? g.w : Math.min(g.w * 1.3, g.w * 0.6 * (count - 1));
      const x = count > 1 ? span / 2 - (rank * span) / (count - 1) : g.w * 0.5;
      return { x: g.w / 2 + x, y: pileY + SPREAD_DROP[rank] * g.h, z: 0, r: SPREAD_TURN[rank], ry: 0, s: fit };
    }
    const offsets = phase === 'rest' ? BOOK_REST : BOOK_PEEK;
    const offset = offsets[rank] ?? { x: 0.06, y: 0, r: PILE_TURN[rank] ?? 0 };
    return {
      x: pileX + offset.x * g.w,
      y: pileY + offset.y * g.h + rank * 0.8 * g.scale,
      z: 0,
      r: offset.r,
      ry: 0,
      s: 1
    };
  };

  const placeFlap = (rank, count, phase, layout, g, pointer) => {
    const rest = FLAP_REST[rank] ?? FLAP_REST[FLAP_REST.length - 1];
    const floor = g.tabH + g.h - 5 * g.scale - g.docH / 2;
    const mid = (count - 1) / 2;
    if (phase === 'open') {
      const lift = g.tabH - g.h * 0.02;
      if (layout === 'stack') {
        const strip = Math.min(30 * g.scale, (g.h * 0.62) / Math.max(1, count - 1));
        const fit = 1.04 - rank * 0.05;
        return {
          x: g.w / 2,
          y: g.tabH + g.h * 0.1 - rank * strip - (g.docH * (1.04 - fit)) / 2,
          z: -rank * 18 * g.scale,
          r: 0,
          ry: 0,
          s: fit
        };
      }
      const gap = 12 * g.scale;
      const fit = Math.min(0.94, (g.w * 2.3 - gap * (count - 1)) / (count * g.docW));
      const step = g.docW * fit + gap;
      const tidy = layout === 'row';
      return {
        x: g.w / 2 + (rank - mid) * step,
        y: lift + (tidy ? 0 : SPREAD_DROP[rank] * g.h * 0.7),
        z: 0,
        r: tidy ? 0 : SPREAD_TURN[rank] * 0.55,
        ry: 0,
        s: fit
      };
    }
    const part = phase === 'receive' ? 1.7 : phase === 'peek' ? 1.3 : 1;
    const x = g.w / 2 + rest.x * g.w * part;
    if (phase === 'rest') return { x, y: floor - rest.rise * g.h, z: 0, r: rest.r, ry: 0, s: 1 };
    const near = phase === 'peek' && pointer != null ? Math.max(0, 1 - Math.abs(pointer - x) / (g.w * 0.32)) : 0;
    const rise = rest.rise + (phase === 'receive' ? 0.03 : 0.1);
    return { x, y: floor - rise * g.h - near * near * g.h * 0.09, z: 0, r: rest.r * 1.6, ry: 0, s: 1 };
  };

  function Folder({
    items,
    label,
    mode = 'flap',
    variant = 'solid',
    color = '#141210',
    paperColor = '#F4EFE4',
    tabPosition = 'right',
    layout = 'spread',
    size = 0.9,
    openOn = 'click',
    open,
    defaultOpen = false,
    onOpenChange,
    onItemClick,
    onItemDrop,
    draggable = true,
    zoomOnClick = true,
    peek = true,
    intro = false,
    tilt = 0.4,
    bounce = 0.35,
    speed = 1,
    closeOnOutsideClick = true,
    theme = 'dark',
    className = '',
    style,
    children
  }) {
    const rootRef = useRef(null);
    const backRef = useRef(null);
    const frontRef = useRef(null);
    const groundRef = useRef(null);
    const castRef = useRef(null);
    const shadeRef = useRef(null);
    const itemRefs = useRef([]);
    const measureRef = useRef(null);
    const engineRef = useRef(null);
    const [internalOpen, setInternalOpen] = useState(defaultOpen);
    const [labelWidth, setLabelWidth] = useState(0);
    const [outside, setOutside] = useState([]);
    const isControlled = open !== undefined;
    const isOpen = isControlled ? Boolean(open) : internalOpen;
    const book = mode === 'book';

    const entries = useMemo(() => {
      const custom = Children.toArray(children).filter(isValidElement);
      const source = custom.length ? custom : items?.length ? items : DEFAULT_ITEMS;
      return source.slice(0, MAX_ITEMS);
    }, [children, items]);
    const count = entries.length;

    const scale = Math.max(0.3, Number(size) || 1);
    const g = measureFolder(book, scale, labelWidth, Boolean(label), tabPosition);
    const total = g.tabH + g.h;

    useIsomorphicLayoutEffect(() => {
      const node = measureRef.current;
      if (!node) return undefined;
      setLabelWidth(node.offsetWidth);
    }, [label, scale]);

    const setOpen = useCallback(
      next => {
        const value = typeof next === 'function' ? next(isOpen) : next;
        if (!isControlled) setInternalOpen(value);
        if (value !== isOpen) onOpenChange?.(value);
      },
      [isControlled, isOpen, onOpenChange]
    );

    const setOpenRef = useRef(setOpen);
    setOpenRef.current = setOpen;

    const rgb = parseColor(color);
    const paper = parseColor(paperColor);
    const backTone = mixRgb(rgb, [0, 0, 0], 0.16);
    const insideTop = rgbString(mixRgb(rgb, [0, 0, 0], 0.08));
    const insideBottom = rgbString(backTone);

    const settingsRef = useRef(null);
    settingsRef.current = {
      open: isOpen,
      layout,
      openOn,
      peek,
      intro,
      draggable,
      zoomOnClick,
      closeOnOutsideClick,
      onItemClick,
      onItemDrop,
      tilt: clamp(tilt, 0, 1.5),
      bounce: clamp(bounce, 0, 1),
      speed: clamp(speed, 0.2, 3),
      count,
      entries,
      light: theme === 'light',
      tint: rgb,
      insideTop,
      insideBottom,
      depth: Math.round(Math.max(g.w, g.h) * 5),
      g
    };

    useEffect(() => {
      const root = rootRef.current;
      if (!root) return undefined;

      const spring = value => ({ value, velocity: 0 });
      const state = {
        raf: 0,
        last: 0,
        ready: false,
        hovered: false,
        pressed: false,
        pointerX: 0,
        pointerY: 0,
        deskX: 0,
        deskY: 0,
        hoveredItem: -1,
        phase: 'rest',
        previous: 'rest',
        changedAt: 0,
        front: spring(0),
        rx: spring(0),
        ry: spring(0),
        lift: spring(0),
        squash: spring(1),
        items: [],
        focus: -1,
        drag: null,
        receiving: false,
        receiveUntil: 0,
        suppressClick: false,
        dropCount: 0,
        intro: { phase: 'done', start: 0, opening: false },
        anchor: spring(0),
        appear: 1
      };

      const ensureItems = n => {
        while (state.items.length < n) {
          state.items.push({
            x: spring(0),
            y: spring(0),
            z: spring(0),
            r: spring(0),
            ry: spring(0),
            s: spring(1),
            hover: spring(0),
            mx: spring(0),
            my: spring(0),
            alpha: spring(1),
            place: 'inside',
            rank: 0,
            layer: 'inside',
            elevated: false,
            insertUntil: 0,
            insertFrom: null,
            outX: 0,
            outY: 0,
            outR: 0,
            outS: 1,
            outOrder: 0,
            localX: 0,
            localY: 0,
            fresh: true
          });
        }
        if (state.items.length > n) {
          state.items.length = n;
          if (state.focus >= n) state.focus = -1;
        }
      };

      const integrate = (item, target, omega, zeta, dt) => {
        const k = omega * omega;
        const d = 2 * zeta * omega;
        const steps = Math.max(1, Math.ceil(dt / STEP));
        const step = dt / steps;
        for (let i = 0; i < steps; i++) {
          item.velocity += (k * (target - item.value) - d * item.velocity) * step;
          item.value += item.velocity * step;
        }
        return Math.abs(target - item.value) > 0.01 || Math.abs(item.velocity) > 0.01;
      };

      const snap = (item, value) => {
        item.value = value;
        item.velocity = 0;
      };

      const shiftOf = () => {
        const { g: geometry } = settingsRef.current;
        return geometry.book ? (geometry.w / 2) * clamp(-state.front.value / 180, 0, 1) : 0;
      };

      const phaseOf = (s, time) => {
        if (s.open) return 'open';
        if (state.receiving || time < state.receiveUntil) return 'receive';
        if (s.peek && state.hovered && !state.drag?.active) return 'peek';
        return 'rest';
      };

      const place = (rank, inside, phase, s) => {
        if (s.g.book) return placeBook(rank, inside, phase, s.layout, s.g);
        const pointer = state.hovered ? ((state.pointerX + 1) / 2) * s.g.w : null;
        return placeFlap(rank, inside, phase, s.layout, s.g, pointer);
      };

      const frame = now => {
        state.raf = 0;
        const s = settingsRef.current;
        const { g: geometry } = s;
        ensureItems(s.count);
        const time = now / 1000;
        const dt = state.last ? Math.min(0.05, (now - state.last) / 1000) : 1 / 60;
        state.last = now;
        const zeta = 1 - 0.72 * s.bounce;
        const frontOmega = TAU * (geometry.book ? 1.55 : 2) * s.speed;
        const itemOmega = TAU * 2.1 * s.speed;
        const soft = TAU * 1.6 * s.speed;
        const phase = phaseOf(s, time);
        if (phase !== state.phase) {
          state.previous = state.phase;
          state.phase = phase;
          state.changedAt = now;
        }
        const elapsed = (now - state.changedAt) / 1000;
        let busy = false;

        const ranks = [];
        state.items.forEach((item, index) => {
          if (item.place === 'inside') ranks.push(index);
        });
        const inside = ranks.length;
        ranks.forEach((index, rank) => {
          state.items[index].rank = rank;
        });

        const closing = state.previous === 'open' && phase !== 'open';
        const frontDelay = closing ? ((geometry.book ? 0.1 : 0.06) + 0.035 * inside) / s.speed : 0;
        const frontPhase = elapsed < frontDelay ? state.previous : phase;
        const frontTarget = (geometry.book ? BOOK_ANGLES : FLAP_ANGLES)[frontPhase];

        if (!state.ready) {
          state.ready = true;
          snap(state.front, frontTarget);
          state.items.forEach(item => {
            const slot = place(item.rank, inside, phase, s);
            snap(item.x, slot.x);
            snap(item.y, slot.y);
            snap(item.z, slot.z);
            snap(item.r, slot.r);
            snap(item.s, slot.s);
            snap(item.alpha, 1);
            item.fresh = false;
          });
        }

        busy = integrate(state.front, frontTarget, frontOmega, zeta, dt) || busy;

        const engaged = state.hovered;
        busy = integrate(state.rx, engaged ? -state.pointerY * 7 * s.tilt : 0, soft, 1, dt) || busy;
        busy = integrate(state.ry, engaged ? state.pointerX * 8 * s.tilt : 0, soft, 1, dt) || busy;
        busy = integrate(state.lift, state.hovered && phase !== 'open' ? 1 : 0, soft, 1, dt) || busy;
        busy = integrate(state.squash, state.pressed ? 0.975 : 1, TAU * 3.2, 0.6, dt) || busy;

        const opening = phase === 'open' && state.previous !== 'open';
        const hover = state.hoveredItem;
        const focusScale = 1.35;
        const focusY = geometry.tabH + geometry.h * 0.36;

        state.items.forEach((item, index) => {
          if (item.fresh) {
            const start = place(item.rank, inside, 'rest', s);
            snap(item.x, start.x);
            snap(item.y, start.y);
            snap(item.r, start.r);
            item.fresh = false;
          }
          let target;
          let omega = itemOmega;
          let damping = zeta;
          let lift = 0;

          if (state.focus === index) {
            target = { x: geometry.w / 2, y: focusY, z: 0, r: 0, ry: 0, s: focusScale };
            lift = 2.4;
            item.layer = 'focus';
          } else {
            const order = opening ? item.rank : item.rank;
            const delay = (order * 0.03) / s.speed;
            const active = elapsed >= delay ? phase : state.previous;
            target = place(item.rank, inside, active, s);
            if (active === 'open') {
              lift = hover === index ? 1 : 0.2;
            }
          }

          busy = integrate(item.x, target.x, omega, damping, dt) || busy;
          busy = integrate(item.y, target.y, omega, damping, dt) || busy;
          busy = integrate(item.z, target.z, itemOmega, zeta, dt) || busy;
          busy = integrate(item.r, target.r, omega, damping, dt) || busy;
          busy = integrate(item.s, target.s, itemOmega, zeta, dt) || busy;
          busy = integrate(item.hover, lift, itemOmega, zeta, dt) || busy;
        });

        write();
        if (busy) state.raf = requestAnimationFrame(frame);
        else state.last = 0;
      };

      const write = () => {
        const s = settingsRef.current;
        const { w, h, tabH, frontTop, frontH, docW, docH, scale, book: isBook } = s.g;
        const pivotX = w / 2;
        const pivotY = tabH + h / 2;
        const theta = state.front.value;
        const angle = (theta * Math.PI) / 180;
        const unfold = isBook ? clamp(-theta / 180, 0, 1) : 0;
        const shift = (w / 2) * unfold * (1 - state.anchor.value);
        const zoom = (1 + state.lift.value * 0.012) * state.squash.value;

        const desk = chain(
          move(pivotX, pivotY),
          turnX(state.rx.value),
          turnY(state.ry.value),
          stretch(zoom, zoom),
          move(-pivotX, -pivotY)
        );
        const folder = multiply(desk, move(shift, 0));
        const flipped = isBook && theta < -90;
        const frontZ = flipped ? '3' : '40';

        if (backRef.current) {
          backRef.current.style.transform = toCss(folder);
        }

        const frontMatrix = isBook
          ? chain(folder, move(0, frontTop, 2 * scale * (1 - unfold)), turnY(theta))
          : chain(folder, move(0, tabH + h, 2 * scale), turnX(theta), move(0, -frontH));

        const front = frontRef.current;
        if (front) {
          front.style.transform = toCss(frontMatrix);
          front.style.zIndex = frontZ;
        }

        if (groundRef.current) {
          groundRef.current.style.transform = toCss(multiply(folder, move(0, isBook ? tabH : tabH + h)));
        }

        state.items.forEach((item, index) => {
          const node = itemRefs.current[index];
          const z = 3 * scale + item.z.value + item.hover.value * 26 * scale;
          const matrix = chain(
            desk,
            move(item.x.value, item.y.value, z),
            turnZ(item.r.value),
            stretch(item.s.value, item.s.value),
            move(-docW / 2, -docH / 2)
          );
          let layer = 10 + s.count - item.rank;
          if (item.layer === 'focus') layer = LAYERS.focus;
          else if (state.phase === 'open' && state.hoveredItem === index) layer = 35;

          if (node) {
            node.style.transform = toCss(matrix);
            node.style.zIndex = String(layer);
            node.style.setProperty('--folder-lift', clamp(item.hover.value, 0, 2.4).toFixed(3));
          }
        });
      };

      const wake = () => {
        if (!state.raf) state.raf = requestAnimationFrame(frame);
      };

      engineRef.current = {
        wake,
        select: index => {
          const s = settingsRef.current;
          if (s.zoomOnClick) state.focus = state.focus === index ? -1 : index;
          s.onItemClick?.(s.entries[index], index);
          wake();
        }
      };

      const onPointerMove = event => {
        const box = root.getBoundingClientRect();
        state.pointerX = clamp(((event.clientX - box.left) / box.width) * 2 - 1, -1.5, 1.5);
        state.pointerY = clamp(((event.clientY - box.top) / box.height) * 2 - 1, -1.5, 1.5);
        const target = event.target.closest?.('[data-folder-item]');
        const index = target && root.contains(target) ? Number(target.getAttribute('data-folder-item')) : -1;
        state.hoveredItem = index;
        if (!state.hovered) {
          state.hovered = true;
          if (settingsRef.current.openOn === 'hover') setOpenRef.current(true);
        }
        wake();
      };

      const onPointerLeave = () => {
        state.hovered = false;
        state.pressed = false;
        state.hoveredItem = -1;
        if (settingsRef.current.openOn === 'hover') setOpenRef.current(false);
        wake();
      };

      root.addEventListener('pointermove', onPointerMove);
      root.addEventListener('pointerleave', onPointerLeave);
      wake();

      return () => {
        cancelAnimationFrame(state.raf);
        root.removeEventListener('pointermove', onPointerMove);
        root.removeEventListener('pointerleave', onPointerLeave);
        engineRef.current = null;
      };
    }, []);

    const toggle = () => {
      setOpen(!isOpen);
    };

    const vars = {
      '--folder-rgb': rgb.join(', '),
      '--folder-base': rgbString(rgb),
      '--folder-front-top': rgbString(mixRgb(rgb, [255, 255, 255], 0.12)),
      '--folder-front-bottom': rgbString(mixRgb(rgb, [0, 0, 0], 0.06)),
      '--folder-inside-top': insideTop,
      '--folder-inside-bottom': insideBottom,
      '--folder-tab-ink': luma(backTone) > 0.6 ? 'rgba(0, 0, 0, 0.72)' : 'rgba(255, 255, 255, 0.92)',
      '--folder-paper': rgbString(paper),
      '--folder-paper-ink': rgbString(mixRgb(paper, [0, 0, 0], 0.11)),
      '--folder-paper-ink-strong': rgbString(mixRgb(paper, [0, 0, 0], 0.28)),
      '--folder-paper-text': rgbString(mixRgb(paper, [0, 0, 0], 0.72)),
      '--folder-w': `${g.w}px`,
      '--folder-h': `${g.h}px`,
      '--folder-tab-h': `${g.tabH}px`,
      '--folder-front-y': `${g.frontTop}px`,
      '--folder-front-h': `${g.frontH}px`,
      '--folder-r': `${g.r}px`,
      '--folder-front-radius': book ? `0 ${g.r}px ${g.r}px 0` : `${g.r}px`,
      '--folder-doc-w': `${g.docW}px`,
      '--folder-doc-h': `${g.docH}px`,
      '--folder-depth': `${settingsRef.current ? settingsRef.current.depth : 1200}px`,
      '--folder-scale': scale,
      ...style
    };

    const shape = backPath(g);

    return (
      <div
        ref={rootRef}
        className={`folder-root${className ? ` ${className}` : ''}`}
        data-mode={book ? 'book' : 'flap'}
        data-variant={variant}
        data-theme={theme === 'light' ? 'light' : 'dark'}
        data-open={isOpen ? '' : undefined}
        style={vars}
      >
        <div
          className="folder-stage"
          role="button"
          tabIndex={0}
          aria-expanded={isOpen}
          onClick={toggle}
        >
          <span ref={groundRef} className="folder-layer folder-ground" aria-hidden="true" />
          {book && <span ref={castRef} className="folder-layer folder-cast" aria-hidden="true" />}

          <div ref={backRef} className="folder-layer folder-back" aria-hidden="true">
            <div className="folder-back-fill" style={{ clipPath: `path('${shape}')` }}>
              {book && <span className="folder-gutter" />}
            </div>
            {label && (
              <span className="folder-tab-text" style={{ left: g.tabX, width: g.tabW }}>
                {label}
              </span>
            )}
          </div>

          {entries.map((item, index) => {
            const shape = item?.shape || (item && typeof item === 'object' && item.shape) || undefined;
            return (
              <div
                key={index}
                ref={node => {
                  itemRefs.current[index] = node;
                }}
                className="folder-layer folder-item"
                data-folder-item={index}
                data-item-shape={shape}
                role="button"
                tabIndex={0}
                onClick={e => {
                  e.stopPropagation();
                  engineRef.current?.select(index);
                }}
              >
                {renderItem(item)}
              </div>
            );
          })}

          {book && <span ref={shadeRef} className="folder-layer folder-shade" aria-hidden="true" />}

          <div ref={frontRef} className="folder-layer folder-front" data-face="outer" aria-hidden="true">
            <div className="folder-face folder-face-outer" />
          </div>
        </div>

        {label && (
          <span ref={measureRef} className="folder-tab-text folder-measure" aria-hidden="true">
            {label}
          </span>
        )}
      </div>
    );
  }

  /* -------------------------------------------------------------------------
     Helper to safely normalize track details
     ------------------------------------------------------------------------- */
  function parseDurationStr(str) {
    if (!str) return 210;
    if (typeof str === 'number') return str;
    const parts = String(str).split(':');
    if (parts.length === 2) return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
    return 210;
  }

  function normalizeTrackInfo(input) {
    if (!input) {
      return {
        title: 'Selected Music Disc',
        artist: 'VoidAntara',
        image: 'public/assets/download.jpg',
        duration: 30,
        startTime: 0,
        endTime: 30,
        audioUrl: '',
        serial: 'NO. 01',
        vibeTag: 'HI-FI',
        lyricSample: ''
      };
    }
    const t = input.track || input;
    const totalDur = typeof t.duration === 'string' ? parseDurationStr(t.duration) : (t.duration || 210);
    const dur = typeof input.duration === 'number' ? input.duration : Math.min(totalDur, 30);
    return {
      title: t.title || 'Selected Track',
      artist: t.artist || 'VoidAntara',
      image: t.image || 'public/assets/download.jpg',
      totalDuration: totalDur,
      duration: dur,
      startTime: input.startTime !== undefined ? input.startTime : 0,
      endTime: input.endTime !== undefined ? input.endTime : Math.min(totalDur, 30),
      audioUrl: t.audioUrl || '',
      serial: t.serial || 'NO. 01',
      vibeTag: t.vibeTag || 'HI-FI',
      lyricSample: t.lyricSample || ''
    };
  }

  /* -------------------------------------------------------------------------
     ProjectConfigModal Component
     Appears AFTER both Music and Template are selected.
     Allows trimming the audio (10s-45s) and choosing template color themes,
     then launching the Visualizer Studio.
     ------------------------------------------------------------------------- */
  function ProjectConfigModal({
    open,
    activeMusic,
    activeTemplate,
    onClose,
    onLaunch,
    ambientSynth
  }) {
    const musicInfo = normalizeTrackInfo(activeMusic);
    const tplName = activeTemplate?.name || activeTemplate?.title || 'Liquid Glass Center';
    const tplImage = activeTemplate?.image || 'public/assets/download (1).jpg';
    const totalDuration = musicInfo.totalDuration || 210;

    const [startTime, setStartTime] = useState(musicInfo.startTime || 0);
    const [endTime, setEndTime] = useState(musicInfo.endTime || Math.min(totalDuration, 30));
    const [selectedTheme, setSelectedTheme] = useState(() => {
      try {
        const saved = localStorage.getItem('void_project_template');
        if (saved) {
          const parsed = JSON.parse(saved);
          if (parsed && parsed.theme) return parsed.theme;
        }
      } catch(e) {}
      return activeTemplate?.theme || 'crimson';
    });

    const [isPlaying, setIsPlaying] = useState(false);
    const audioRef = useRef(null);
    const timerRef = useRef(null);

    const stopAudio = useCallback(() => {
      if (timerRef.current) {
        clearTimeout(timerRef.current);
        timerRef.current = null;
      }
      if (audioRef.current) {
        audioRef.current.pause();
        audioRef.current.currentTime = startTime;
      }
      if (ambientSynth) ambientSynth.stop();
      setIsPlaying(false);
    }, [ambientSynth, startTime]);

    useEffect(() => {
      if (open && activeMusic) {
        const info = normalizeTrackInfo(activeMusic);
        const tot = info.totalDuration || 210;
        setStartTime(info.startTime || 0);
        setEndTime(info.endTime || Math.min(tot, 30));
        if (activeTemplate?.theme) setSelectedTheme(activeTemplate.theme);
      } else {
        stopAudio();
      }
    }, [open, activeMusic, activeTemplate]);

    useEffect(() => {
      return () => {
        if (timerRef.current) clearTimeout(timerRef.current);
        if (audioRef.current) audioRef.current.pause();
        if (ambientSynth) ambientSynth.stop();
      };
    }, [ambientSynth]);

    // 32-bar visualizer waveform
    const barsCount = 32;
    const waveBars = useMemo(() => {
      const bars = [];
      for (let i = 0; i < barsCount; i++) {
        const heightPercent = 22 + Math.sin(i * 0.45) * 48 + ((i % 3) * 14);
        const barSec = (i / barsCount) * totalDuration;
        const isSelected = barSec >= startTime && barSec <= endTime;
        bars.push({ heightPercent, isSelected });
      }
      return bars;
    }, [barsCount, totalDuration, startTime, endTime]);

    if (!open || !activeMusic) return null;

    const MIN_LEN = 10;
    const MAX_LEN = 45;
    const duration = Math.max(MIN_LEN, Math.min(MAX_LEN, endTime - startTime));

    const formatTime = (secs) => {
      const m = Math.floor(secs / 60);
      const s = Math.floor(secs % 60);
      return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    };

    const handleStartChange = (val) => {
      const s = Math.max(0, Math.min(totalDuration - MIN_LEN, val));
      setStartTime(s);
      if (endTime - s < MIN_LEN) {
        setEndTime(Math.min(totalDuration, s + MIN_LEN));
      } else if (endTime - s > MAX_LEN) {
        setEndTime(s + MAX_LEN);
      }
    };

    const handleEndChange = (val) => {
      const e = Math.min(totalDuration, Math.max(MIN_LEN, val));
      setEndTime(e);
      if (e - startTime < MIN_LEN) {
        setStartTime(Math.max(0, e - MIN_LEN));
      } else if (e - startTime > MAX_LEN) {
        setStartTime(Math.max(0, e - MAX_LEN));
      }
    };

    const handlePreset = (len, startOffset = 0) => {
      const s = Math.min(totalDuration - len, startOffset);
      const e = s + len;
      setStartTime(s);
      setEndTime(e);
    };

    const toggleAudition = () => {
      if (isPlaying) {
        stopAudio();
      } else {
        setIsPlaying(true);
        if (musicInfo.audioUrl && audioRef.current) {
          audioRef.current.currentTime = startTime;
          audioRef.current.play().catch(() => {});
        } else if (ambientSynth) {
          ambientSynth.start(activeMusic.track || activeMusic);
        }

        const ms = (endTime - startTime) * 1000;
        timerRef.current = setTimeout(() => {
          stopAudio();
        }, ms);
      }
    };

    const themeOptions = [
      { id: 'crimson', name: 'Crimson Ink', color: '#9A332A', border: '#9A332A' },
      { id: 'obsidian', name: 'Obsidian Gold', color: '#f59e0b', border: '#f59e0b' },
      { id: 'violet', name: 'Midnight Violet', color: '#8b5cf6', border: '#8b5cf6' },
      { id: 'arctic', name: 'Arctic Mist', color: '#0284c7', border: '#0284c7' },
      { id: 'emerald', name: 'Emerald Velvet', color: '#10b981', border: '#10b981' }
    ];

    const handleConfirmAndLaunch = () => {
      stopAudio();
      const updatedMusic = {
        track: activeMusic.track || activeMusic,
        startTime,
        endTime,
        duration: endTime - startTime
      };
      const updatedTemplate = {
        ...(activeTemplate || {}),
        theme: selectedTheme
      };

      try {
        localStorage.setItem('void_project_music', JSON.stringify(updatedMusic));
        localStorage.setItem('void_project_template', JSON.stringify(updatedTemplate));
      } catch(e) {}

      if (onLaunch) {
        onLaunch(updatedMusic, updatedTemplate);
      } else {
        const dest = updatedTemplate.templateFile || 'template/Liquid_glass_center.html';
        window.location.href = dest;
      }
    };

    return (
      <div
        data-dark-section="true"
        className="fixed inset-0 z-[360] bg-[#12110F]/85 backdrop-blur-md flex items-center justify-center p-4 select-none overflow-y-auto"
        onClick={() => { stopAudio(); onClose(); }}
      >
        <div
          className="w-full max-w-2xl rounded-2xl bg-[#181715] text-[#EDE7DA] border border-[#EDE7DA]/25 shadow-2xl p-6 sm:p-7 space-y-6 relative my-auto"
          onClick={e => e.stopPropagation()}
        >
          {musicInfo.audioUrl && (
            <audio ref={audioRef} src={musicInfo.audioUrl} preload="auto" />
          )}

          {/* Close button */}
          <button
            type="button"
            onClick={() => { stopAudio(); onClose(); }}
            className="absolute top-5 right-5 w-8 h-8 rounded-full border border-[#EDE7DA]/25 text-[#EDE7DA] hover:bg-[#EDE7DA] hover:text-[#141210] flex items-center justify-center font-mono text-sm transition-colors cursor-pointer"
            aria-label="Close setup modal"
          >
            ✕
          </button>

          {/* Header with Music & Template Side-by-Side */}
          <div className="border-b border-[#EDE7DA]/15 pb-4 pr-8">
            <span className="font-mono text-[10px] tracking-widest uppercase px-2 py-0.5 rounded bg-[#9A332A] text-white font-bold inline-block mb-2">
              PROJECT STUDIO SETUP
            </span>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
              {/* Music Card Mini */}
              <div className="flex items-center gap-3 p-2.5 rounded-xl bg-[#EDE7DA]/8 border border-[#EDE7DA]/15">
                <div
                  className="w-12 h-12 rounded-full relative shrink-0 flex items-center justify-center shadow-md"
                  style={{
                    background: 'radial-gradient(circle, #2d2a27 0%, #151412 50%, #080706 100%)',
                    boxShadow: 'inset 0 0 0 1.5px rgba(237,231,218,0.3)'
                  }}
                >
                  <img
                    src={musicInfo.image}
                    alt={musicInfo.title}
                    className="w-5 h-5 rounded-full object-cover border border-[#141210]"
                    onError={e => { e.currentTarget.style.display = 'none'; }}
                  />
                  <div className="w-1.5 h-1.5 rounded-full bg-[#181715] absolute border border-[#EDE7DA]/70" />
                </div>
                <div className="min-w-0">
                  <span className="block font-mono text-[9px] uppercase tracking-wider text-[#EDE7DA]/55">Music Track</span>
                  <p className="font-mono text-xs font-bold text-white truncate">{musicInfo.title}</p>
                  <p className="font-mono text-[10px] text-[#EDE7DA]/70 truncate">{musicInfo.artist}</p>
                </div>
              </div>

              {/* Template Card Mini */}
              <div className="flex items-center gap-3 p-2.5 rounded-xl bg-[#EDE7DA]/8 border border-[#EDE7DA]/15">
                <img
                  src={tplImage}
                  alt={tplName}
                  className="w-12 h-12 rounded-lg object-cover border border-[#EDE7DA]/20 shrink-0 shadow-md"
                  onError={e => { e.currentTarget.src = 'public/assets/download (1).jpg'; }}
                />
                <div className="min-w-0">
                  <span className="block font-mono text-[9px] uppercase tracking-wider text-[#EDE7DA]/55">Visual Template</span>
                  <p className="font-mono text-xs font-bold text-white truncate">{tplName}</p>
                  <span className="font-mono text-[10px] text-[#9A332A] font-bold">Liquid Glass Synced</span>
                </div>
              </div>
            </div>
          </div>

          {/* 1. MUSIC TIMING TRIMMER SECTION */}
          <div className="space-y-3.5">
            <div className="flex items-center justify-between">
              <span className="font-mono text-xs uppercase tracking-wider font-bold text-[#EDE7DA]">
                1. Music Timing Snippet
              </span>
              <span className="font-mono text-[11px] text-[#EDE7DA]/60">
                Min <strong>10s</strong> &bull; Max <strong>45s</strong>
              </span>
            </div>

            {/* Time Indicators */}
            <div className="grid grid-cols-3 gap-2.5 text-center">
              <div className="p-2 rounded-xl bg-[#EDE7DA]/8 border border-[#EDE7DA]/15">
                <span className="block font-mono text-[9px] text-[#EDE7DA]/55 uppercase">Start</span>
                <span className="font-mono text-sm font-bold text-[#EDE7DA]">{formatTime(startTime)}</span>
              </div>
              <div className="p-2 rounded-xl bg-[#9A332A]/25 border border-[#9A332A]/40 text-[#EDE7DA]">
                <span className="block font-mono text-[9px] text-[#EDE7DA]/75 uppercase">Snippet Duration</span>
                <span className="font-mono text-sm font-bold text-white">{duration}s</span>
              </div>
              <div className="p-2 rounded-xl bg-[#EDE7DA]/8 border border-[#EDE7DA]/15">
                <span className="block font-mono text-[9px] text-[#EDE7DA]/55 uppercase">End</span>
                <span className="font-mono text-sm font-bold text-[#EDE7DA]">{formatTime(endTime)}</span>
              </div>
            </div>

            {/* Waveform Visualization */}
            <div className="h-12 rounded-xl bg-[#141210] border border-[#EDE7DA]/20 p-2 flex items-end justify-between gap-1 overflow-hidden">
              {waveBars.map((bar, i) => (
                <div
                  key={i}
                  className={`w-full rounded-full transition-all duration-300 ${
                    bar.isSelected
                      ? 'bg-gradient-to-t from-[#9A332A] to-[#EDE7DA] opacity-95 shadow-sm'
                      : 'bg-[#EDE7DA] opacity-20'
                  }`}
                  style={{ height: `${bar.heightPercent}%` }}
                />
              ))}
            </div>

            {/* Sliders */}
            <div className="space-y-2 pt-1">
              <div>
                <div className="flex justify-between font-mono text-[10px] text-[#EDE7DA]/70 mb-0.5">
                  <span>Start Position: {formatTime(startTime)}</span>
                  <span>Drag to reposition</span>
                </div>
                <input
                  type="range"
                  min="0"
                  max={totalDuration - MIN_LEN}
                  value={startTime}
                  onChange={e => handleStartChange(parseInt(e.target.value, 10))}
                  className="w-full accent-[#EDE7DA] cursor-pointer"
                />
              </div>

              <div>
                <div className="flex justify-between font-mono text-[10px] text-[#EDE7DA]/70 mb-0.5">
                  <span>End Position: {formatTime(endTime)}</span>
                  <span>(Snippet: {duration}s)</span>
                </div>
                <input
                  type="range"
                  min={startTime + MIN_LEN}
                  max={Math.min(totalDuration, startTime + MAX_LEN)}
                  value={endTime}
                  onChange={e => handleEndChange(parseInt(e.target.value, 10))}
                  className="w-full accent-[#9A332A] cursor-pointer"
                />
              </div>
            </div>

            {/* Presets & Audition Button */}
            <div className="flex flex-wrap items-center justify-between gap-2 pt-1 font-mono text-xs">
              <div className="flex items-center gap-1.5">
                <span className="text-[#EDE7DA]/50 text-[10px]">Presets:</span>
                <button
                  type="button"
                  onClick={() => handlePreset(15, 0)}
                  className="px-2 py-0.5 rounded bg-[#EDE7DA]/10 hover:bg-[#EDE7DA]/25 border border-[#EDE7DA]/15 text-[10px] transition-colors cursor-pointer"
                >
                  15s
                </button>
                <button
                  type="button"
                  onClick={() => handlePreset(30, 15)}
                  className="px-2 py-0.5 rounded bg-[#EDE7DA]/10 hover:bg-[#EDE7DA]/25 border border-[#EDE7DA]/15 text-[10px] transition-colors cursor-pointer"
                >
                  30s
                </button>
                <button
                  type="button"
                  onClick={() => handlePreset(45, 20)}
                  className="px-2 py-0.5 rounded bg-[#EDE7DA]/10 hover:bg-[#EDE7DA]/25 border border-[#EDE7DA]/15 text-[10px] transition-colors cursor-pointer"
                >
                  45s
                </button>
              </div>

              <button
                type="button"
                onClick={toggleAudition}
                className={`px-3 py-1.5 rounded-full font-mono text-[11px] font-semibold flex items-center gap-1.5 border transition-all cursor-pointer ${
                  isPlaying
                    ? 'bg-[#9A332A] text-white border-[#9A332A]'
                    : 'bg-[#EDE7DA]/15 text-[#EDE7DA] border-[#EDE7DA]/30 hover:bg-[#EDE7DA]/25'
                }`}
              >
                <span>{isPlaying ? '❚❚ Stop' : '▶ Preview Snippet'}</span>
              </button>
            </div>
          </div>

          {/* 2. TEMPLATE COLOR / THEME SECTION */}
          <div className="space-y-3 pt-2 border-t border-[#EDE7DA]/15">
            <span className="font-mono text-xs uppercase tracking-wider font-bold text-[#EDE7DA] block">
              2. Template Visual Color Theme
            </span>
            <div className="grid grid-cols-2 sm:grid-cols-5 gap-2 font-mono text-xs">
              {themeOptions.map(th => {
                const isSelected = selectedTheme === th.id;
                return (
                  <button
                    key={th.id}
                    type="button"
                    onClick={() => setSelectedTheme(th.id)}
                    className={`p-2 rounded-xl border flex flex-col items-center gap-1.5 transition-all cursor-pointer ${
                      isSelected
                        ? 'bg-[#EDE7DA]/20 border-white shadow-lg scale-105'
                        : 'bg-[#EDE7DA]/5 border-[#EDE7DA]/15 hover:bg-[#EDE7DA]/10'
                    }`}
                  >
                    <div
                      className="w-5 h-5 rounded-full border border-white/40 shadow-sm"
                      style={{ background: th.color }}
                    />
                    <span className="text-[10px] font-mono text-white text-center leading-tight truncate w-full">
                      {th.name}
                    </span>
                    {isSelected && <span className="text-[8px] text-[#9A332A] font-bold">ACTIVE</span>}
                  </button>
                );
              })}
            </div>
          </div>

          {/* 3. LAUNCH STUDIO ACTION */}
          <div className="flex items-center justify-between gap-3 pt-3 border-t border-[#EDE7DA]/15">
            <button
              type="button"
              onClick={() => { stopAudio(); onClose(); }}
              className="px-4 py-2.5 rounded-full font-mono text-xs text-[#EDE7DA]/70 hover:text-white border border-[#EDE7DA]/20 hover:bg-[#EDE7DA]/10 transition-colors cursor-pointer"
            >
              Cancel
            </button>

            <button
              type="button"
              onClick={handleConfirmAndLaunch}
              className="px-6 py-2.5 rounded-full bg-gradient-to-r from-[#9A332A] to-[#ba3c30] text-white hover:from-[#ba3c30] hover:to-[#da4c40] font-mono text-xs font-bold shadow-xl transition-all hover:scale-105 active:scale-95 flex items-center gap-2 cursor-pointer border border-white/30"
            >
              <span>🚀 Launch Visualizer Studio ({duration}s)</span>
              <span>→</span>
            </button>
          </div>
        </div>
      </div>
    );
  }

  /* -------------------------------------------------------------------------
     ProjectFolderDock Component
     Floating Corner Dock with React Bits Folder component
     Only appears when something is selected.
     Music item is rendered as a REAL CIRCULAR VINYL DISC!
     Template item is rendered as a clean polaroid/screen preset.
     ------------------------------------------------------------------------- */
  function ProjectFolderDock({
    activeMusic,
    activeTemplate,
    onOpenConfig,
    onOpenStudio,
    onClearMusic,
    onClearTemplate,
    onExploreMusic,
    onExploreTemplates
  }) {
    const [folderOpen, setFolderOpen] = useState(false);
    const [configModalOpen, setConfigModalOpen] = useState(false);

    const hasMusic = Boolean(activeMusic);
    const hasTemplate = Boolean(activeTemplate);
    const isReady = hasMusic && hasTemplate;

    const musicDetails = useMemo(() => normalizeTrackInfo(activeMusic), [activeMusic]);
    const templateDetails = activeTemplate || null;

    const folderItems = useMemo(() => {
      if (!hasMusic && !hasTemplate) return [];
      const items = [];

      // 1. Music Item: GENUINE CIRCULAR VINYL DISC (No rectangular file box!)
      if (hasMusic) {
        items.push({
          shape: 'circle',
          content: (
            <div
              className="w-[102px] h-[102px] rounded-full relative flex items-center justify-center select-none shadow-[0_12px_28px_rgba(0,0,0,0.85)] cursor-pointer group/disc"
              style={{
                background: 'radial-gradient(circle, #2d2a27 0%, #151412 35%, #0b0a09 68%, #1a1816 82%, #080706 100%)',
                boxShadow: 'inset 0 0 0 2px rgba(237,231,218,0.3), 0 10px 25px -4px rgba(0,0,0,0.85)'
              }}
              title={`${musicDetails.title} - ${musicDetails.artist} (${musicDetails.duration}s)`}
            >
              {/* Concentric Vinyl Grooves */}
              <div className="absolute inset-1.5 rounded-full border border-white/10 pointer-events-none" />
              <div className="absolute inset-3 rounded-full border border-white/5 pointer-events-none" />
              <div className="absolute inset-5 rounded-full border border-white/10 pointer-events-none" />
              <div className="absolute inset-7 rounded-full border border-white/5 pointer-events-none" />

              {/* Vinyl Sheen Overlay */}
              <div
                className="absolute inset-0 rounded-full pointer-events-none"
                style={{
                  background: 'linear-gradient(135deg, rgba(255,255,255,0.18) 0%, transparent 42%, rgba(255,255,255,0.06) 52%, transparent 100%)'
                }}
              />

              {/* Center Album Art & Spindle Sticker */}
              <div className="w-12 h-12 rounded-full bg-[#181715] border-2 border-[#EDE7DA]/60 relative overflow-hidden flex flex-col items-center justify-center p-0.5 text-center shadow-inner">
                {musicDetails.image && (
                  <img
                    src={musicDetails.image}
                    alt={musicDetails.title}
                    className="absolute inset-0 w-full h-full object-cover opacity-60"
                    onError={e => { e.currentTarget.style.display = 'none'; }}
                  />
                )}
                <div className="relative z-10 w-full px-0.5 pointer-events-none">
                  <span className="block font-mono text-[6.5px] uppercase tracking-wider text-white font-bold truncate max-w-[40px] mx-auto drop-shadow-sm">
                    {musicDetails.title}
                  </span>
                  <span className="block font-mono text-[5.5px] text-[#EDE7DA]/85 truncate max-w-[40px] mx-auto">
                    {musicDetails.artist}
                  </span>
                </div>
                {/* Spindle hole */}
                <div className="w-2.5 h-2.5 rounded-full bg-[#080706] border border-[#EDE7DA]/80 absolute z-20 shadow-inner" />
              </div>

              {/* Floating badge for duration */}
              <span className="absolute -bottom-1 font-mono text-[7.5px] bg-[#9A332A] text-white px-1.5 py-0.2 rounded-full font-bold shadow border border-white/20">
                {musicDetails.duration}s
              </span>
            </div>
          )
        });
      }

      // 2. Template Screen Document
      if (hasTemplate) {
        const tplName = templateDetails?.name || templateDetails?.title || 'Visual Template';
        const tplImg = templateDetails?.image || 'public/assets/download (1).jpg';
        const tplDetail = templateDetails?.detail || 'Preset';

        items.push({
          content: (
            <div className="folder-doc w-full h-full p-2.5 flex flex-col justify-between items-center text-center bg-[#141210] text-[#EDE7DA] rounded-lg border border-[#EDE7DA]/30 shadow-md select-none overflow-hidden">
              <div className="flex items-center justify-between w-full border-b border-[#EDE7DA]/15 pb-1">
                <span className="font-mono text-[8px] uppercase tracking-wider text-[#EDE7DA]/60">Template Screen</span>
                <span className="font-mono text-[8px] bg-[#EDE7DA]/20 text-[#EDE7DA] px-1.5 py-0.2 rounded font-bold">{tplDetail}</span>
              </div>
              <img
                src={tplImg}
                alt={tplName}
                className="w-16 h-10 object-cover rounded border border-[#EDE7DA]/30 my-0.5 shadow-sm"
                onError={e => { e.currentTarget.src = 'public/assets/download (1).jpg'; }}
              />
              <p className="font-mono text-[9px] font-bold truncate max-w-[95px] leading-tight text-white">{tplName}</p>
            </div>
          )
        });
      }

      // 3. Studio Visualizer File Document (when both ready)
      if (isReady) {
        items.push({
          content: (
            <div className="folder-doc w-full h-full p-2.5 flex flex-col justify-between items-center text-center bg-gradient-to-br from-[#9A332A] to-[#6e1e17] text-white rounded-lg border border-white/30 shadow-lg select-none">
              <div className="flex items-center justify-between w-full border-b border-white/20 pb-1">
                <span className="font-mono text-[8px] uppercase tracking-wider text-white/80">Studio File</span>
                <span className="font-mono text-[8px] bg-white text-[#9A332A] px-1.5 py-0.2 rounded font-bold">READY</span>
              </div>
              <div className="text-xl my-0.5">🚀</div>
              <p className="font-mono text-[9px] font-bold leading-tight">Liquid Glass Studio</p>
              <span className="font-mono text-[8px] text-white/90">Click to Setup &amp; Launch</span>
            </div>
          )
        });
      }

      return items;
    }, [hasMusic, hasTemplate, isReady, musicDetails, templateDetails]);

    // Safe to return early AFTER all hooks are registered
    if (!hasMusic && !hasTemplate) return null;

    const handleItemClick = (item, index) => {
      if (isReady) {
        if (onOpenConfig) {
          onOpenConfig();
        } else {
          setConfigModalOpen(true);
        }
      } else if (hasMusic && !hasTemplate) {
        if (onExploreTemplates) onExploreTemplates();
      } else if (!hasMusic && hasTemplate) {
        if (onExploreMusic) onExploreMusic();
      }
    };

    const handleOpenModal = () => {
      if (onOpenConfig) {
        onOpenConfig();
      } else {
        setConfigModalOpen(true);
      }
    };

    return (
      <>
        <aside
          aria-label="Project Studio Dock"
          className="fixed bottom-6 right-6 z-[260] pointer-events-auto flex flex-col items-end gap-2.5 select-none"
        >
          {/* Pure 3D Interactive Folder with NO dark box background */}
          <div className="relative group filter drop-shadow-[0_16px_36px_rgba(0,0,0,0.55)]">
            <Folder
              label={isReady ? 'READY 🚀' : (hasMusic ? 'DISC 🎵' : 'SCREEN 🎬')}
              items={folderItems}
              size={0.84}
              color={isReady ? '#9A332A' : '#181715'}
              paperColor="#EDE7DA"
              variant="glass"
              open={folderOpen}
              onOpenChange={setFolderOpen}
              onItemClick={handleItemClick}
            />

            {/* Floating Minimal Clear/Close Button */}
            <button
              type="button"
              onClick={(e) => {
                e.stopPropagation();
                if (onClearMusic) onClearMusic();
                if (onClearTemplate) onClearTemplate();
              }}
              title="Clear project selection"
              className="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-[#181715]/95 border border-[#EDE7DA]/30 text-[#EDE7DA] hover:bg-rose-600 hover:text-white flex items-center justify-center font-mono text-[10px] shadow-lg transition-transform hover:scale-110 active:scale-95 cursor-pointer"
            >
              ✕
            </button>
          </div>

          {/* Minimal Floating Action Pill */}
          <div className="flex items-center gap-2">
            {isReady ? (
              <button
                type="button"
                onClick={handleOpenModal}
                className="px-4 py-2 rounded-full bg-gradient-to-r from-[#9A332A] to-[#bd3e32] text-white font-mono text-xs font-bold shadow-2xl transition-all hover:scale-105 active:scale-95 flex items-center gap-2 cursor-pointer border border-white/30 animate-pulse cursor-target"
                data-cursor-label="SETUP & LAUNCH"
              >
                <span>🚀</span>
                <span>Configure &amp; Launch Studio ({musicDetails.duration}s)</span>
              </button>
            ) : hasMusic ? (
              <button
                type="button"
                onClick={onExploreTemplates}
                className="px-4 py-2 rounded-full bg-[#EDE7DA] text-[#141210] hover:bg-white font-mono text-xs font-bold shadow-xl transition-all hover:scale-105 active:scale-95 flex items-center gap-1.5 cursor-pointer border border-[#141210]/20 cursor-target"
                data-cursor-label="TEMPLATES"
              >
                <span>🎬 Select Template</span>
                <span>→</span>
              </button>
            ) : (
              <button
                type="button"
                onClick={onExploreMusic}
                className="px-4 py-2 rounded-full bg-[#EDE7DA] text-[#141210] hover:bg-white font-mono text-xs font-bold shadow-xl transition-all hover:scale-105 active:scale-95 flex items-center gap-1.5 cursor-pointer border border-[#141210]/20 cursor-target"
                data-cursor-label="MUSIC"
              >
                <span>🎵 Select Music</span>
                <span>→</span>
              </button>
            )}
          </div>
        </aside>

        {/* Embedded Project Config Modal when invoked directly */}
        <ProjectConfigModal
          open={configModalOpen}
          activeMusic={activeMusic}
          activeTemplate={activeTemplate}
          onClose={() => setConfigModalOpen(false)}
          onLaunch={(mus, tpl) => {
            setConfigModalOpen(false);
            if (onOpenStudio) onOpenStudio(mus, tpl);
            else window.location.href = (tpl?.templateFile || 'template/Liquid_glass_center.html');
          }}
        />
      </>
    );
  }

  /* -------------------------------------------------------------------------
     Smooth Flying Projectile Animation to Project Folder Dock
     Flys disc or template preview smoothly from clicked element into bottom-right dock.
     No reload, no page redirect!
     ------------------------------------------------------------------------- */
  function flyItemToFolder(sourceElOrRect, type, assetUrl, onComplete) {
    try {
      let startRect = null;
      if (sourceElOrRect && typeof sourceElOrRect.getBoundingClientRect === 'function') {
        startRect = sourceElOrRect.getBoundingClientRect();
      } else if (sourceElOrRect && typeof sourceElOrRect.left === 'number') {
        startRect = sourceElOrRect;
      } else {
        startRect = {
          left: window.innerWidth / 2 - 32,
          top: window.innerHeight / 2 - 32,
          width: 64,
          height: 64
        };
      }

      // Target the floating bottom-right dock
      const dockEl = document.querySelector('[aria-label="Project Studio Dock"]') ||
                     document.querySelector('.folder-root') ||
                     document.getElementById('projectFolderDock');

      let targetX = window.innerWidth - 80;
      let targetY = window.innerHeight - 80;

      if (dockEl) {
        const dRect = dockEl.getBoundingClientRect();
        targetX = dRect.left + dRect.width / 2;
        targetY = dRect.top + dRect.height / 2;
      }

      const startX = startRect.left + (startRect.width || 64) / 2;
      const startY = startRect.top + (startRect.height || 64) / 2;

      const flyer = document.createElement('div');
      flyer.className = 'fixed pointer-events-none z-[99999] select-none';
      flyer.style.left = `${startX}px`;
      flyer.style.top = `${startY}px`;
      flyer.style.width = type === 'disc' ? '68px' : '76px';
      flyer.style.height = type === 'disc' ? '68px' : '52px';
      flyer.style.marginLeft = type === 'disc' ? '-34px' : '-38px';
      flyer.style.marginTop = type === 'disc' ? '-34px' : '-26px';
      flyer.style.transition = 'transform 0.62s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.62s cubic-bezier(0.4, 0, 0.2, 1)';
      flyer.style.transform = 'translate3d(0, 0, 0) scale(1) rotate(0deg)';
      flyer.style.opacity = '1';

      if (type === 'disc') {
        flyer.innerHTML = `
          <div class="w-full h-full rounded-full relative flex items-center justify-center shadow-[0_12px_32px_rgba(0,0,0,0.9)] border-2 border-white/60"
               style="background: radial-gradient(circle, #2d2a27 0%, #151412 35%, #0b0a09 70%, #1a1816 85%, #080706 100%);">
            <div class="absolute inset-1 rounded-full border border-white/20"></div>
            <div class="absolute inset-2.5 rounded-full border border-white/10"></div>
            <div class="w-7 h-7 rounded-full bg-[#181715] border border-white/60 relative overflow-hidden flex items-center justify-center">
              ${assetUrl ? `<img src="${assetUrl}" class="w-full h-full object-cover" onerror="this.style.display='none'" />` : ''}
              <div class="w-2 h-2 rounded-full bg-[#080706] border border-white/80 absolute"></div>
            </div>
          </div>
        `;
      } else {
        flyer.innerHTML = `
          <div class="w-full h-full rounded-lg relative overflow-hidden border-2 border-white/60 shadow-[0_12px_32px_rgba(0,0,0,0.9)] bg-[#141210]">
            ${assetUrl ? `<img src="${assetUrl}" class="w-full h-full object-cover" onerror="this.style.display='none'" />` : '<div class="w-full h-full flex items-center justify-center text-white text-xs">🎬</div>'}
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
          </div>
        `;
      }

      document.body.appendChild(flyer);

      // Force layout calculation
      void flyer.offsetWidth;

      const deltaX = targetX - startX;
      const deltaY = targetY - startY;
      const rotateDeg = type === 'disc' ? 720 : 360;

      flyer.style.transform = `translate3d(${deltaX}px, ${deltaY}px, 0) scale(0.2) rotate(${rotateDeg}deg)`;
      flyer.style.opacity = '0.1';

      setTimeout(() => {
        if (flyer.parentNode) flyer.parentNode.removeChild(flyer);

        const landedDock = dockEl || document.querySelector('[aria-label="Project Studio Dock"]') || document.querySelector('.folder-root');
        if (landedDock) {
          landedDock.style.transition = 'transform 0.18s cubic-bezier(0.34, 1.56, 0.64, 1)';
          landedDock.style.transform = 'scale(1.15)';
          setTimeout(() => {
            landedDock.style.transform = 'scale(1)';
          }, 180);
        }

        if (typeof onComplete === 'function') onComplete();
      }, 620);
    } catch (err) {
      console.warn('flyItemToFolder error:', err);
      if (typeof onComplete === 'function') onComplete();
    }
  }

  // Export to window
  window.VoidFolder = Folder;
  window.VoidProjectConfigModal = ProjectConfigModal;
  window.VoidProjectFolderDock = ProjectFolderDock;
  window.normalizeTrackInfo = normalizeTrackInfo;
  window.flyItemToFolder = flyItemToFolder;

})(window);
