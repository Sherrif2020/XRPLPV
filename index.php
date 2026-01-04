<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>XRP Lightning Dashboard</title>
  <script type="module" src="https://unpkg.com/three@0.158.0/build/three.module.js"></script>

  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
  <style>
    /* ==== GLOBAL ==== */
    body {
      width: 100%;
      height: 100%;
      color: #0ff;
      font-family: 'Orbitron', sans-serif;
      overflow: hidden;
    }
    /* ==== DASHBOARD FRAME ==== */
    #dashboard {
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      position: relative;
      overflow: hidden;
    }
    /* ==== STYLE MODES ==== */
    .glass-mode {
      border: 2px solid #0ff;
      box-shadow: 0 0 40px rgba(0,255,255,0.6), inset 0 0 25px rgba(0,255,255,0.3);
      background: linear-gradient(180deg, rgba(0,20,40,0.9) 0%, rgba(0,0,0,0.95) 100%);
      backdrop-filter: blur(12px);
    }
    .terminal-mode {
      border: 2px solid #111;
      box-shadow: inset 0 0 20px rgba(0,255,255,0.1);
      background: radial-gradient(circle at center, #000, #050505 90%);
    }
    /* ==== TOGGLE BUTTON ==== */
    #mode-toggle {
      position: absolute;
      top: 50px;
      left: 465px;
      width: 100px;
      background: rgba(0,255,255,0.15);
      color: #0ff;
      border: 1px solid #0ff;
      border-radius: 2px;
      font-family: 'Orbitron', sans-serif;
      cursor: pointer;
      padding: 2px;
     
      z-index: 30;
      font-size: 9px;
    }
    #mode-toggle:hover {
      background: rgba(0,255,255,0.4);
      box-shadow: 0 0 15px #0ff;
    }
    /* ==== NAVIGATION MENU ==== */
    #nav-menu {
      position: absolute;
      top: 5px;
      left: 465px;
      z-index: 30;
      background: rgba(0, 20, 40, 0.9);
      border: 1px solid #0ff;
      border-radius: 2px;
      padding: 0px;
      box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
    }
    #nav-menu select,
    #nav-menu input[type="range"] {
      margin: 1px 0;
      padding: 1px;
      background: rgba(0, 0, 0, 0.85);
      border: 1px solid #0ff;
      color: #0ff;
      font-family: 'Orbitron', sans-serif;
      font-size: 9px;
      border-radius: 1px;
      width: 100px;
      accent-color: #0ff;
    }
    #nav-menu span {
      color: #0ff;
      font-size: 9px;
      margin-left: 5px;
    }
    /* ==== HOLOGRAPH PRICE (Overlay) ==== */
    #holograph-price-overlay {
      position: absolute;
      top: 15px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 36px;
      color: #00ffff;
      text-shadow: 0 0 15px #00ffff, 0 0 30px #00ff00, 0 0 60px #00ff00;
      animation: holographPulse 2s infinite;
      pointer-events: none;
      z-index: 25;
    }
    @keyframes holographPulse {
      0%, 100% { opacity: 0.8; text-shadow: 0 0 15px #0ff, 0 0 30px #0f0; }
      50% { opacity: 1; text-shadow: 0 0 35px #0ff, 0 0 65px #0f0; }
    }
    /* ==== GLOBE ==== */
    #world-map {
      flex: 1;
      position: relative;
      overflow: hidden;
    }
    canvas#globe {
      width: 100%;
      height: 100%;
      display: block;
    }
    /* ==== PANELS ==== */
    .panel {
      background: rgba(0,0,15,0.9);
      border: 1px solid #0ff;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,255,255,0.5);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transition: all 0.4s ease;
     
    }
    .panel header {
      background: rgba(0,0,0,0.85);
      text-align: center;
      padding: 1px;
      font-size: 12px;
      border-bottom: 1px solid #0ff;
      text-transform: uppercase;
    }
    .panel-content {
      flex: 1;
      overflow-y: hidden;
      padding: 1px;
      font-size: 14px;
    }
    .trade {
      border-bottom: 1px solid rgba(0,255,255,0.3);
      padding: 1px;
    }
   
    .activity-chart {
      margin-bottom: 10px;
      position: relative;
    }
    .activity-chart canvas {
      border: 1px solid #0ff;
      background: rgba(0, 0, 0, 0.7);
      border-radius: 4px;
    }
    .chart-label {
      position: absolute;
      top: 50%;
      left: 5px;
      color: #0ff;
      font-size: 18px;
      font-family: 'Orbitron', sans-serif;
      white-space: nowrap;
    }
    /* ==== PANEL POSITIONS ==== */
    #floating-trades-panel {
      position: absolute;
      top: 5px;
      left: 5px;
      width: 450px;
      height: 99%;
    }
    #floating-counter-panel {
      position: absolute;
      bottom: 5px;
      right: 5px;
      width: 450px;
      height: 100px;
    }
    #floating-legend-panel {
      position: absolute;
      top: 5px;
      right: 270px;
      width: 185px;
      height: 675px;
      z-index: 10;
      resize: both;
      overflow: hidden;
    }
    #floating-volume-panel {
      position: absolute;
      top: 5px;
      right: 5px;
      width: 260px;
      height: 675px;
      overflow-y: hidden;
    }
    #floating-custom-panel {
      position: absolute;
      bottom: 5px;
      right: 5px;
      width: 450px;
      height: 280px;
      z-index: 10;
    }

    #legend-list {
      display: flex;
      flex-direction: column;
      width: 100%;
      padding: 5px;
    }
    #legend-list div {
      display: flex;
      align-items: center;
      margin-bottom: 4px;
    }
    #legend-list div div {
      width: 15px;
      height: 15px;
      margin-right: 5px;
      border: 1px solid #0ff;
    }
    #floating-image-panel {
      position: absolute;
      bottom: 20px;
      right: 20px;
      width: 120px;
      height: 120px;
      z-index: 10;
    }
    #floating-image-panel img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    #rotation-speed-control {
      position: absolute;
      top: 25px;
      left: 465px;
      z-index: 30;
      background: rgba(0, 20, 40, 0.9);
      border: 1px solid #0ff;
      border-radius: 1px;
      padding: 1px;
      box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
    }
    #rotation-speed-control input[type="range"] {
      margin: 0;
      padding: 0;
      background: rgba(0, 0, 0, 0.85);
      border: 1px solid #0ff;
      color: #0ff;
      font-family: 'Orbitron', sans-serif;
      font-size: 3px;
      border-radius: 1px;
      width: 95px;
      accent-color: #0ff;
    }
    #rotation-speed-control span {
      color: #0ff;
      font-size: 9px;
      margin-left: 1px;
    }
 
    /* ==== WALLET MONITOR STYLES ==== */
    h1 {
      color: #00ffff;
      font-size: 15px;
      margin-top: 0px;
      margin-bottom: 5px;
    }
    #addr {
      font-size: 14px;
      color: #0f0;
      margin-top: px;
      margin-bottom: 10px;
      word-break: break-all;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }
    th, td {
    
      font-size: 13px;
      text-align: left;
    }
    th {
      color: #0ff;
      background: #181820;
    }
    tr.incoming td {
      color: #0f0;
    }
    tr.outgoing td {
      color: #f55;
    }
    #status {
      font-size: 13px;
      color: #aaa;
      margin-top: 20px;
    }
    #lastUpdated {
      color: #0ff;
    }
    /* ==== NEW TRADE COUNTER PANELS ==== */
    #topLeftPanel, #topRightPanel {
      position: fixed;
      bottom: 5px;
      width: 497.5px;
      height: 60px;
      z-index: 100;
    }
    #topLeftPanel { left: 455px; }
    #topRightPanel { right: 460px; }
    #topLeftPanel .panel-content,
    #topRightPanel .panel-content { height: 100%; }
    #topLeftPanel canvas,
    #topRightPanel canvas {
      width: 100%;
      height: 100%;
      border: 1px solid #0ff;
      background: rgba(0, 0, 0, 0.7);
      border-radius: 1px;
    }
  </style>
</head>
<body>
  <div id="dashboard" class="glass-mode">
    <button id="mode-toggle">Terminal Mode</button>
    <div id="rotation-speed-control">
      <input type="range" id="rotation-speed-slider" min="0.001" max="0.01" step="0.001" value="0.003">
    </div>
    <div id="nav-menu">
      <select id="color-scheme-select">
        <option value="default">Default</option>
        <option value="neon">Neon Glow</option>
        <option value="pastel">Pastel Hues</option>
        <option value="dark">Dark Spectrum</option>
        <option value="vibrant">Vibrant Burst</option>
      </select>
    </div>
 
    <div id="holograph-price-overlay">XRP Live View: $0.0000</div>
    <div id="main">
      <div id="world-map">
        <canvas id="globe"></canvas>
      </div>
      
      <div id="floating-trades-panel" class="panel">
        <div class="panel-content" id="floating-trades-list"></div>
      </div>
      <div id="floating-counter-panel" class="panel">
        <div class="panel-content">
          <p>Total trades: <span id="trade-counter-total">0</span></p>
          <p>Minute: <span id="trade-counter-min">0</span>
            (Δ: <span id="trade-delta-min">0</span>)</p>
          <p>Hour: <span id="trade-counter-hour">0</span>
            (Δ: <span id="trade-delta-hour">0</span>)</p>
        </div>
      </div>
      <div id="floating-volume-panel" class="panel">
        <div class="panel-content" id="volume-bars-container"></div>
      </div>
      <div id="floating-legend-panel" class="panel">
        <div class="panel-content" id="legend-list"></div>
      </div>
    
      <div id="right-panel">
        <div id="right-panel-content">
          <canvas id="priceChart"></canvas>
        </div>
      </div>
    </div>
    
  


<div id="sound-panel" style="
  position: fixed; 
  top: 70px; left: 460px; 
  background: rgba(0,0,0,0.7); 
  color: #fff; 
  padding: 1px 9px; 
  border-radius: 10px; 
  font-family: sans-serif; 
  z-index: 9999;">
  
  <select id="soundSelector" style="margin-top: 5px; width: 160px;">
    <option value="./images-sounds/28.mp3">1</option>
    <option value="./images-sounds/31.mp3">2</option>
    <option value="./images-sounds/30.mp3">3</option>
    <option value="./images-sounds/22.mp3">4</option>
  </select>
</div>



<script>
    if (!window.Chart) {
      const s = document.createElement('script');
      s.src = "https://cdn.jsdelivr.net/npm/chart.js";
      s.onload = () => console.log('Chart.js loaded');
      document.head.appendChild(s);
    }
  </script>
  <!-- Wallet Monitor -->
 
  <!-- Mode Toggle -->
  <script>
    const toggleBtn = document.getElementById('mode-toggle');
    const dashboard = document.getElementById('dashboard');
    let isGlass = true;
    toggleBtn.addEventListener('click', () => {
      isGlass = !isGlass;
      dashboard.classList.toggle('glass-mode', isGlass);
      dashboard.classList.toggle('terminal-mode', !isGlass);
      toggleBtn.textContent = isGlass ? 'Terminal Mode' : 'Neon Mode';
    });
  </script>
  <!-- Three.js Globe & Trading Logic -->
  <script type="module">
    import * as THREE from 'https://unpkg.com/three@0.158.0/build/three.module.js';
    let scene, camera, renderer, globe;
    const GLOBE_RADIUS = 5, lightningBolts = [], domes = new Map();
    const MAX_ELEMENTS = 3000;
    const tradeActivity = new Map();
    scene = new THREE.Scene();
    scene.fog = new THREE.FogExp2(0x000000, 0.01595);
    camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    camera.position.z = 9;
    renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('globe'), alpha: true, antialias: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.toneMappingExposure = 0.0514;
    const globeGroup = new THREE.Group();
    scene.add(globeGroup);
    scene.add(new THREE.AmbientLight(0x00ffff, 0.05));
    const pointLight = new THREE.PointLight(0x00ffff, 0.5, 100);
    pointLight.position.set(10, 10, 10);
    scene.add(pointLight);
    const loader = new THREE.TextureLoader();
    loader.load('red.png', texture => {
      const mat = new THREE.MeshPhongMaterial({ map: texture, shininess: 0.01, opacity: 0.35, transparent: true, side: THREE.DoubleSide });
      globe = new THREE.Mesh(new THREE.SphereGeometry(GLOBE_RADIUS, 128, 128), gloss);
      globeGroup.add(globe);
    });
    // Trade counters
    let tradeCounters = {
        total: 0,
        minute: [],
        hour: [],
        '12h': [],
        '24h': [],
        second: { count: 0, volume: 0, history: [] },
        minuteData: { count: 0, volume: 0, history: [] },
        hourData: { count: 0, volume: 0, history: [] }
    };
    const now = new Date();
    window.lastMinute = now.getMinutes();
    window.lastHour = now.getHours();
    let lastSecond = now.getSeconds();
    let last12h = Math.floor(now.getTime() / (12 * 60 * 60 * 1000));
    let last24h = Math.floor(now.getTime() / (24 * 60 * 60 * 1000));
    // Color schemes
   const colorSchemes = {
  default: {
    Binance:0x3fd7ff, Coinbase:0xff5c8a, Kraken:0x9f6bff, OKX:0xffb84d, KuCoin:0x42f5a1,
    Bybit:0x89f542, Bitstamp:0xffd24d, Bitfinex:0xff4da6, Huobi:0xff704d, "Gate.io":0x4dd2ff,
    Bitrue:0xbf40ff, CoinEx:0xff884d, Bitget:0xff66ff, Bitmart:0x66ff4d, Bitso:0xb3ff66,
    Bitflyer:0x4d94ff, MEXC:0xbd59ff, HitBTC:0x59bdff, Poloniex:0xff5959, Upbit:0xad59ff
  },

  neon: {
    Binance:0x00ffbf, Coinbase:0xff00aa, Kraken:0x00ff66, OKX:0xff5500, KuCoin:0x00ffe1,
    Bybit:0x8cff00, Bitstamp:0xff0099, Bitfinex:0xff66cc, Huobi:0xff3300, "Gate.io":0x00ffee,
    Bitrue:0xaa00ff, CoinEx:0xff8800, Bitget:0xff00ff, Bitmart:0x99ff33, Bitso:0x33ffc4,
    Bitflyer:0x33a1ff, MEXC:0xcc33ff, HitBTC:0x33ff99, Poloniex:0xff4444, Upbit:0xdd33ff
  },

  pastel: {
    Binance:0xb8e6ff, Coinbase:0xffb3c6, Kraken:0xd1b3ff, OKX:0xffe0b3, KuCoin:0xb3ffe0,
    Bybit:0xd6ffb3, Bitstamp:0xffc2b3, Bitfinex:0xffb3e0, Huobi:0xffccb3, "Gate.io":0xb3f0ff,
    Bitrue:0xe6b3ff, CoinEx:0xffd6b3, Bitget:0xffb3ff, Bitmart:0xd9ffb3, Bitso:0xb3ffe6,
    Bitflyer:0xb3ccff, MEXC:0xe0b3ff, HitBTC:0xb3e0ff, Poloniex:0xffb3b3, Upbit:0xd1b3ff
  },

  dark: {
    Binance:0x007acc, Coinbase:0x1b5e20, Kraken:0x4a148c, OKX:0xbf360c, KuCoin:0x004d40,
    Bybit:0x1a237e, Bitstamp:0x5d4037, Bitfinex:0x880e4f, Huobi:0x6d1b1b, "Gate.io":0x004d66,
    Bitrue:0x4527a0, CoinEx:0xb71c1c, Bitget:0x311b92, Bitmart:0x33691e, Bitso:0x2e7d32,
    Bitflyer:0x01579b, MEXC:0x311b92, HitBTC:0x0d47a1, Poloniex:0x3e2723, Upbit:0x1a237e
  },

  vibrant: {
    Binance:0xff6f00, Coinbase:0x00e5ff, Kraken:0xff00ff, OKX:0x00c853, KuCoin:0xffa000,
    Bybit:0xff4081, Bitstamp:0x00ff6f, Bitfinex:0xff80ab, Huobi:0xff3d00, "Gate.io":0x00bcd4,
    Bitrue:0x2979ff, CoinEx:0xe53935, Bitget:0xffd600, Bitmart:0x64dd17, Bitso:0xab47bc,
    Bitflyer:0x2196f3, MEXC:0x7c4dff, HitBTC:0x26a69a, Poloniex:0xd50000, Upbit:0x9c27b0
  }
};

    let SOURCE_GLOWS = colorSchemes.default;
    function changeColorScheme() {
      const select = document.getElementById('color-scheme-select');
      const scheme = select.value;
      SOURCE_GLOWS = colorSchemes[scheme];
      lightningBolts.forEach(bolt => {
        if (bolt.glow && bolt.source) {
          const glowColor = new THREE.Color(SOURCE_GLOWS[bolt.source] || 0x00ffff);
          bolt.glow.material.color.copy(glowColor);
          if (bolt.endpoint) bolt.endpoint.material.color.copy(glowColor);
          if (bolt.branches) bolt.branches.forEach(b => b.material.color.copy(glowColor));
        }
      });
      const trades = document.getElementById('floating-trades-list').children;
      for (let trade of trades) {
        const sourceMatch = trade.textContent.match(/^[^\[]+/);
        if (sourceMatch) {
          const source = sourceMatch[0].trim();
          const glowColor = new THREE.Color(SOURCE_GLOWS[source] || 0x00ffff);
          trade.style.color = '#' + glowColor.getHexString();
        }
      }
      const volumeBars = document.getElementById('volume-bars-container').getElementsByClassName('volume-bar');
      Array.from(volumeBars).forEach(bar => {
        const source = bar.dataset.source;
        const fill = bar.querySelector('.fill');
        const label = bar.querySelector('.volume-label');
        const glowColor = new THREE.Color(SOURCE_GLOWS[source] || 0x00ffff);
        fill.style.backgroundColor = '#' + glowColor.getHexString();
        label.style.color = '#' + glowColor.getHexString();
      });
      renderer.render(scene, camera);
    }
    document.getElementById('color-scheme-select').addEventListener('change', changeColorScheme);
 // ========== SOUND SYSTEM ==========
let selectedLightningSound = "22.mp3";
const soundSelector = document.getElementById('soundSelector');
soundSelector.addEventListener('change', e => selectedLightningSound = e.target.value);

function playLightningSound(volume = 1, loop = false) {
  const audio = new Audio(selectedLightningSound);
  audio.volume = Math.min(1, volume);
  audio.loop = loop;
  audio.play().catch(() => {}); // silently ignore autoplay restrictions
  return audio;
}

// ========== LIGHTNING GENERATION ==========
function createLightning(lat, lon, volume, source = 'default') {
  if (lightningBolts.length + domes.size >= MAX_ELEMENTS) {
    const oldest = lightningBolts.shift();
    if (oldest) {
      globeGroup.remove(oldest.mesh);
      if (oldest.glow) globeGroup.remove(oldest.glow);
      if (oldest.endpoint) globeGroup.remove(oldest.endpoint);
      if (oldest.branches) oldest.branches.forEach(b => globeGroup.remove(b));
    }
  }

  const phi = (90 - lat) * Math.PI / 90;
  const theta = (lon + 90) * Math.PI / 90;
  const end = new THREE.Vector3(
    GLOBE_RADIUS * Math.sin(phi) * Math.cos(theta),
    GLOBE_RADIUS * Math.cos(phi),
    GLOBE_RADIUS * Math.sin(phi) * Math.sin(theta)
  );
  const start = new THREE.Vector3(0, 0, 0);
  const segments = 130 + Math.floor(Math.random() * 150);
  const points = [];

  for (let i = 0; i <= segments; i++) {
    const t = i / segments;
    const jitter = Math.pow(1 - t, 2);
    const offset = new THREE.Vector3(
      (Math.random() - 0.5) * 0.5 * jitter,
      (Math.random() - 0.5) * 0.5 * jitter,
      (Math.random() - 0.5) * 0.5 * jitter
    );
    points.push(start.clone().lerp(end, t).add(offset));
  }

  const gradient = [0xff0000, 0xff6600, 0xffcc00, 0x99ff00, 0x33ff33, 0xffffff];
  const colorIndex = Math.min(gradient.length - 1, Math.floor((volume / 130) * gradient.length));
  const color = gradient[colorIndex];
  const geom = new THREE.BufferGeometry().setFromPoints(points);
  const mat = new THREE.LineBasicMaterial({
    color,
    transparent: true,
    opacity: Math.min(1.8, volume / 50),
    blending: THREE.AdditiveBlending
  });
  const bolt = new THREE.Line(geom, mat);
  globeGroup.add(bolt);

  const glowColor = new THREE.Color(SOURCE_GLOWS[source] || 0x00ffff);
  const glowMat = new THREE.LineBasicMaterial({
    color: glowColor,
    transparent: true,
    opacity: mat.opacity * 0.35,
    blending: THREE.AdditiveBlending
  });
  const glow = new THREE.Line(geom.clone(), glowMat);
  globeGroup.add(glow);

  const sphereGeom = new THREE.SphereGeometry(0.06, 8, 8);
  const sphereMat = new THREE.MeshBasicMaterial({ color: glowColor });
  const endpoint = new THREE.Mesh(sphereGeom, sphereMat);
  endpoint.position.copy(end);
  globeGroup.add(endpoint);

  const branches = [];
  const branchCount = Math.random() < 0.1 ? 1 : 2;
  for (let b = 0; b < branchCount; b++) {
    const branchStartIndex = Math.floor(segments * (0.3 + Math.random() * 0.6));
    const branchPoints = [];
    const branchSegments = segments - branchStartIndex;
    const branchStart = points[branchStartIndex].clone();
    for (let i = 0; i <= branchSegments; i++) {
      const t = i / branchSegments;
      const jitter = Math.pow(1 - t, 2);
      const offset = new THREE.Vector3(
        (Math.random() - 0.3) * 0.9 * jitter,
        (Math.random() - 0.3) * 0.9 * jitter,
        (Math.random() - 0.3) * 0.9 * jitter
      );
      branchPoints.push(branchStart.clone().lerp(end, t * 0.5).add(offset));
    }
    const branchGeom = new THREE.BufferGeometry().setFromPoints(branchPoints);
    const branchMat = new THREE.LineBasicMaterial({
      color: glowColor,
      transparent: true,
      opacity: mat.opacity * 0.6,
      blending: THREE.AdditiveBlending
    });
    const branch = new THREE.Line(branchGeom, branchMat);
    globeGroup.add(branch);
    branches.push(branch);
  }

  lightningBolts.push({ mesh: bolt, glow, life: 0, endpoint, branches, source });

  // ========== SOUND TRIGGER ==========
  const hum = playLightningSound(0.00595, false);
  if (branches && branches.length > 0) {
    branches.forEach((branch, i) => {
      setTimeout(() => {
        const branchSound = playLightningSound(0.0645, false);
        branchSound.playbackRate = 0.59 + Math.random() * 0.3;
      }, i * 180 + Math.random() * 120);
    });
  }

  const fadeInterval = setInterval(() => {
    if (hum.volume > 0.02) hum.volume -= 0.005;
    else {
      clearInterval(fadeInterval);
      hum.pause();
    }
  }, 100);
}

// ========== LIGHTNING FADE ==========
function fadeLightning() {
  for (let i = lightningBolts.length - 1; i >= 0; i--) {
    const b = lightningBolts[i];
    b.life += 0.0055;
    const fade = 1 - b.life;
    b.mesh.material.opacity = fade * 0.5;
    if (b.glow) b.glow.material.opacity = fade * 0.95;
    if (b.branches) b.branches.forEach(br => br.material.opacity = fade * 1.6);

    if (fade <= 0) {
      globeGroup.remove(b.mesh);
      if (b.glow) globeGroup.remove(b.glow);
      if (b.endpoint) globeGroup.remove(b.endpoint);
      if (b.branches) b.branches.forEach(br => globeGroup.remove(br));
      lightningBolts.splice(i, 1);
    }
  }
}

// ========== ANIMATION LOOP ==========
function animate() {
  requestAnimationFrame(animate);
  const rotationSpeedSlider = document.getElementById('rotation-speed-slider');
  const rotationSpeed = parseFloat(rotationSpeedSlider.value) || 0.003;
  globeGroup.rotation.y -= rotationSpeed;
  fadeLightning();
  renderer.render(scene, camera);
}
animate();
    // Trade list
    function addTradeToList(source, vol, price, type = "Trade", walletAddr = null) {
      const el = document.createElement('div');
      el.className = 'trade';
      const glowColor = new THREE.Color(SOURCE_GLOWS[source] || 0x00ffff);
      el.style.color = '#' + glowColor.getHexString();
      el.textContent = `${source} [${type}]: ${vol.toFixed(6)} XRP @ $${price.toFixed(8)}${walletAddr ? ` - Wallet: ${walletAddr}` : ''}`;
      const list = document.getElementById('floating-trades-list');
      list.prepend(el);
      if (list.children.length > 40) list.lastElementChild.remove();
      updateTradeActivity(source, vol, price, type);
      updateTradeCounters(price, vol);
      updateTradeCharts();
    }
    function updateTradeActivity(source, vol, price, type) {
      const now = Date.now();
      const activity = tradeActivity.get(source) || {count:0, lastUpdate:now, lastTradeTime:now, tradeRate:0, tradeHistory:new Array(60).fill(10)};
      activity.count++;
      activity.lastTradeTime = now;
      tradeActivity.set(source, activity);
      updateActivityBars();
    }


    function updateActivityBars() {
  const container = document.getElementById('volume-bars-container');
  const now = Date.now();

  const activeSources = Array.from(tradeActivity.entries()).filter(([_, d]) => (now - d.lastTradeTime) <= 35000); // keep bars visible 15s

  const totalTrades = activeSources.reduce((sum, [_, d]) => sum + d.count, 0);
  const avgTradeRate = activeSources.length > 0 ? totalTrades / activeSources.length : 0;

  activeSources.forEach(([source, data]) => {
    const timeDiff = (now - data.lastTradeTime) / 1000; // seconds since last trade
    let pressure = data.tradeRate || 0;

    // Apply decay over time
    const decayFactor = Math.max(0, 1 - timeDiff / 10); // fades over 10s
    pressure *= decayFactor;

    // Add sensitivity to recent trades
    const tradeBoost = data.count * 5; // each trade adds pressure
    const frequencyBoost = avgTradeRate > 0 ? (avgTradeRate / (timeDiff + 1)) * 5 : 0;
    pressure = Math.min(100, pressure + tradeBoost + frequencyBoost);

    // Clamp pressure
    pressure = Math.max(0, pressure);

    // Update history
    data.tradeHistory = data.tradeHistory || [];
    if (data.tradeHistory.length > 150) data.tradeHistory.shift();
    data.tradeHistory.push(pressure);
    data.tradeRate = pressure;
    data.count = 0; // reset for next interval
    data.lastUpdate = now;

    // Create chart div if missing
    let chartDiv = container.querySelector(`.activity-chart[data-source="${source}"]`);
    if (!chartDiv) {
      chartDiv = document.createElement('div');
      chartDiv.className = 'activity-chart';
      chartDiv.dataset.source = source;

      const canvas = document.createElement('canvas');
      canvas.height = 50;

      const label = document.createElement('div');
      label.className = 'chart-label';

      chartDiv.appendChild(canvas);
      chartDiv.appendChild(label);
      container.appendChild(chartDiv);
    }

    // Draw chart
    const canvas = chartDiv.querySelector('canvas');
    const ctx = canvas.getContext('2d');
    const label = chartDiv.querySelector('.chart-label');
    canvas.width = container.offsetWidth - 15;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    const lineColor = SOURCE_GLOWS[source] ? `#${new THREE.Color(SOURCE_GLOWS[source]).getHexString()}` : '#00ffff';
    ctx.fillStyle = lineColor + '40';
    ctx.strokeStyle = lineColor;
    ctx.lineWidth = 3.5;
    ctx.lineJoin = 'round';

    const dataPoints = data.tradeHistory;
    const xStep = canvas.width / (dataPoints.length - 1);

    // Fill
    ctx.beginPath();
    ctx.moveTo(0, canvas.height - (dataPoints[0] * canvas.height / 100));
    for (let i = 1; i < dataPoints.length; i++) {
      const x = i * xStep;
      const y = canvas.height - (dataPoints[i] * canvas.height / 100);
      ctx.lineTo(x, y);
    }
    ctx.lineTo(canvas.width, canvas.height);
    ctx.lineTo(0, canvas.height);
    ctx.fill();

    // Stroke
    ctx.beginPath();
    ctx.moveTo(0, canvas.height - (dataPoints[0] * canvas.height / 100));
    for (let i = 1; i < dataPoints.length; i++) {
      const x = i * xStep;
      const y = canvas.height - (dataPoints[i] * canvas.height / 100);
      ctx.lineTo(x, y);
    }
    ctx.stroke();

    // Update label
    label.textContent = `${source} [ ${pressure.toFixed(1)}%`;
  });

  // Remove inactive charts smoothly
  const charts = container.getElementsByClassName('activity-chart');
  Array.from(charts).forEach(chart => {
    const source = chart.dataset.source;
    const data = tradeActivity.get(source);
    if (!data || (now - data.lastTradeTime) > 15000) { // remove after 15s
      container.removeChild(chart);
      tradeActivity.delete(source);
    }
  });
    }
    // Trade counters
    function updateTradeCounters(price, volume) {
      const now = new Date();
      const currentMinute = now.getMinutes();
      const currentHour = now.getHours();
      const current12h = Math.floor(now.getTime()/(12*60*60*1000));
      const current24h = Math.floor(now.getTime()/(24*60*60*1000));
      const counters = {
        total: document.getElementById('trade-counter-total'),
        min: document.getElementById('trade-counter-min'),
        hour: document.getElementById('trade-counter-hour'),
        '12h': document.getElementById('trade-counter-12h'),
        '24h': document.getElementById('trade-counter-24h'),
        deltaMin: document.getElementById('trade-delta-min'),
        highMin: document.getElementById('trade-high-min'),
        lowMin: document.getElementById('trade-low-min'),
        avgMin: document.getElementById('trade-avg-min'),
        deltaHour: document.getElementById('trade-delta-hour'),
        highHour: document.getElementById('trade-high-hour'),
        lowHour: document.getElementById('trade-low-hour'),
        avgHour: document.getElementById('trade-avg-hour'),
        delta12h: document.getElementById('trade-delta-12h'),
        high12h: document.getElementById('trade-high-12h'),
        low12h: document.getElementById('trade-low-12h'),
        avg12h: document.getElementById('trade-avg-12h'),
        delta24h: document.getElementById('trade-delta-24h'),
        high24h: document.getElementById('trade-high-24h'),
        low24h: document.getElementById('trade-low-24h'),
        avg24h: document.getElementById('trade-avg-24h')
      };
      tradeCounters.total = (tradeCounters.total||0) + 1;
      if (counters.total) counters.total.textContent = tradeCounters.total;
      if (currentMinute !== window.lastMinute) { tradeCounters.minute=[]; window.lastMinute=currentMinute; }
      tradeCounters.minute.push(price);
      const minCount = tradeCounters.minute.length;
      if (counters.min) counters.min.textContent = minCount;
      const minDelta = minCount>1 ? price - tradeCounters.minute[minCount-2] : 0;
      if (counters.deltaMin) counters.deltaMin.textContent = minDelta.toFixed(4);
      const minHigh = Math.max(...tradeCounters.minute)||0;
      if (counters.highMin) counters.highMin.textContent = minHigh.toFixed(4);
      const minLow = Math.min(...tradeCounters.minute)||0;
      if (counters.lowMin) counters.lowMin.textContent = minLow.toFixed(4);
      const minAvg = minCount>0 ? tradeCounters.minute.reduce((a,b)=>a+b,0)/minCount : 0;
      if (counters.avgMin) counters.avgMin.textContent = minAvg.toFixed(4);
      if (currentHour !== window.lastHour) { tradeCounters.hour=[]; window.lastHour=currentHour; }
      tradeCounters.hour.push(price);
      const hourCount = tradeCounters.hour.length;
      if (counters.hour) counters.hour.textContent = hourCount;
      const hourDelta = hourCount>1 ? price - tradeCounters.hour[hourCount-2] : 0;
      if (counters.deltaHour) counters.deltaHour.textContent = hourDelta.toFixed(4);
      const hourHigh = Math.max(...tradeCounters.hour)||0;
      if (counters.highHour) counters.highHour.textContent = hourHigh.toFixed(4);
      const hourLow = Math.min(...tradeCounters.hour)||0;
      if (counters.lowHour) counters.lowHour.textContent = hourLow.toFixed(4);
      const hourAvg = hourCount>0 ? tradeCounters.hour.reduce((a,b)=>a+b,0)/hourCount : 0;
      if (counters.avgHour) counters.avgHour.textContent = hourAvg.toFixed(4);
      if (current12h !== last12h) { tradeCounters['12h']=[]; last12h=current12h; }
      tradeCounters['12h'].push(price);
      const h12Count = tradeCounters['12h'].length;
      if (counters['12h']) counters['12h'].textContent = h12Count;
      const h12Delta = h12Count>1 ? price - tradeCounters['12h'][h12Count-2] : 0;
      if (counters.delta12h) counters.delta12h.textContent = h12Delta.toFixed(4);
      const h12High = Math.max(...tradeCounters['12h'])||0;
      if (counters.high12h) counters.high12h.textContent = h12High.toFixed(4);
      const h12Low = Math.min(...tradeCounters['12h'])||0;
      if (counters.low12h) counters.low12h.textContent = h12Low.toFixed(4);
      const h12Avg = h12Count>0 ? tradeCounters['12h'].reduce((a,b)=>a+b,0)/h12Count : 0;
      if (counters.avg12h) counters.avg12h.textContent = h12Avg.toFixed(4);
      if (current24h !== last24h) { tradeCounters['24h']=[]; last24h=current24h; }
      tradeCounters['24h'].push(price);
      const h24Count = tradeCounters['24h'].length;
      if (counters['24h']) counters['24h'].textContent = h24Count;
      const h24Delta = h24Count>1 ? price - tradeCounters['24h'][h24Count-2] : 0;
      if (counters.delta24h) counters.delta24h.textContent = h24Delta.toFixed(4);
      const h24High = Math.max(...tradeCounters['24h'])||0;
      if (counters.high24h) counters.high24h.textContent = h24High.toFixed(4);
      const h24Low = Math.min(...tradeCounters['24h'])||0;
      if (counters.low24h) counters.low24h.textContent = h24Low.toFixed(4);
      const h24Avg = h24Count>0 ? tradeCounters['24h'].reduce((a,b)=>a+b,0)/h24Count : 0;
      if (counters.avg24h) counters.avg24h.textContent = h24Avg.toFixed(4);
      tradeCounters.second.count++;
      tradeCounters.second.volume += volume;
      if (currentMinute !== window.lastMinute) {
        tradeCounters.minuteData.history.push({count:tradeCounters.minuteData.count, volume:tradeCounters.minuteData.volume});
        if (tradeCounters.minuteData.history.length>60) tradeCounters.minuteData.history.shift();
        tradeCounters.minuteData.count = 0;
        tradeCounters.minuteData.volume = 0;
        window.lastMinute = currentMinute;
      }
      tradeCounters.minuteData.count += tradeCounters.second.count;
      tradeCounters.minuteData.volume += tradeCounters.second.volume;
      if (currentHour !== window.lastHour) {
        tradeCounters.hourData.history.push({count:tradeCounters.hourData.count, volume:tradeCounters.hourData.volume});
        if (tradeCounters.hourData.history.length>24) tradeCounters.hourData.history.shift();
        tradeCounters.hourData.count = 0;
        tradeCounters.hourData.volume = 0;
        window.lastHour = currentHour;
      }
      tradeCounters.hourData.count += tradeCounters.minuteData.count;
      tradeCounters.hourData.volume += tradeCounters.minuteData.volume;
      if (now.getSeconds() !== lastSecond) {
        tradeCounters.second.history.push({count:tradeCounters.second.count, volume:tradeCounters.second.volume});
        if (tradeCounters.second.history.length>60) tradeCounters.second.history.shift();
        tradeCounters.second.count = 0;
        tradeCounters.second.volume = 0;
        lastSecond = now.getSeconds();
      }
    }
    // WebSocket wrapper
    function safeWS(url, onopen, onmsg) {
      try {
        const ws = new WebSocket(url);
        ws.onopen = () => {
          console.log(`${url} Connected at ${new Date().toLocaleTimeString()}`);
          onopen?.(ws);
        };
        ws.onmessage = e => {
          const d = JSON.parse(e.data);
          if (onmsg) {
            const delay = 800 / (typeof speedMultiplier !== 'undefined' ? speedMultiplier : 1);
            setTimeout(()=>onmsg(e,ws), Math.random()*delay);
          }
        };
        ws.onerror = err => { console.error(`${url} Error:`, err); ws.close(); };
        ws.onclose = () => {
          console.log(`${url} Disconnected, retrying in 5s...`);
          setTimeout(()=>safeWS(url,onopen,onmsg),5000);
        };
      } catch(e) { console.error(`WebSocket creation failed for ${url}:`, e); }
    }
    // WebSocket connections (all exchanges now use USD pairs or are converted to USD)
    safeWS("wss://stream.binance.com:9443/ws/xrpusdt@trade",
      ws=>console.log("Binance Connected"),
      (e,ws)=>{ const d=JSON.parse(e.data);
        const price=parseFloat(d.p), vol=parseFloat(d.q);
        document.getElementById('holograph-price-overlay').textContent=`XRP Live Price: $${price.toFixed(4)}`;
        createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'Binance');
        addTradeToList('Binance',vol,price,"Trade",`Binance:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
      });
    safeWS("wss://ws.bitstamp.net",
      ws=>ws.send(JSON.stringify({event:"bts:subscribe",data:{channel:"live_trades_xrpusd"}})),
      (e,ws)=>{ const d=JSON.parse(e.data);
        if (d.event==="trade"){
          const price=parseFloat(d.data.price), vol=parseFloat(d.data.amount);
          createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'Bitstamp');
          addTradeToList('Bitstamp',vol,price,"Trade",`Bitstamp:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
        }});
    safeWS("wss://ws-feed.exchange.coinbase.com",
      ws=>ws.send(JSON.stringify({type:"subscribe",channels:[{name:"matches",product_ids:["XRP-USD"]}]})),
      (e,ws)=>{ const d=JSON.parse(e.data);
        if (d.type==="match"){
          const price=parseFloat(d.price), vol=parseFloat(d.size);
          createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'Coinbase');
          addTradeToList('Coinbase',vol,price,"Trade",`Coinbase:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
        }});
    safeWS("wss://ws.kraken.com",
      ws=>ws.send(JSON.stringify({event:"subscribe",pair:["XRP/USD"],subscription:{name:"trade"}})),
      (e,ws)=>{ const d=JSON.parse(e.data);
        if (Array.isArray(d) && d[1]!=="hb" && Array.isArray(d[1])){
          d[1].forEach(t=>{
            const price=parseFloat(t[0]), vol=parseFloat(t[1]);
            createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'Kraken');
            addTradeToList('Kraken',vol,price,"Trade",`Kraken:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
          });
        }});
    safeWS("wss://ws-api.kucoin.com/",
      ws=>ws.send(JSON.stringify({id:"1",type:"subscribe",topic:"/market/trade:XRP-USDT",response:true})),
      (e,ws)=>{ const d=JSON.parse(e.data);
        if (d.type==="message" && d.topic==="/market/trade:XRP-USDT"){
          const price=parseFloat(d.data.price), vol=parseFloat(d.data.size);
          createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'KuCoin');
          addTradeToList('KuCoin',vol,price,"Trade",`KuCoin:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
        }});
    safeWS("wss://ws.okx.com:8443/ws/v5/public",
      ws=>ws.send(JSON.stringify({op:"subscribe",args:[{channel:"trades",instId:"XRP-USDT"}]})),
      (e,ws)=>{ const d=JSON.parse(e.data);
        if (d.arg && d.data){
          d.data.forEach(t=>{
            const price=parseFloat(t.p), vol=parseFloat(t.s);
            createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'OKX');
            addTradeToList('OKX',vol,price,"Trade",`OKX:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
          });
        }});
    safeWS("wss://stream.bybit.com/v5/public/spot",
      ws=>ws.send(JSON.stringify({op:"subscribe",args:[{channel:"trade",instId:"XRPUSDT"}]})),
      (e,ws)=>{ const d=JSON.parse(e.data);
        if (d.data){
          d.data.forEach(t=>{
            const price=parseFloat(t.p), vol=parseFloat(t.v);
            createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'Bybit');
            addTradeToList('Bybit',vol,price,"Trade",`Bybit:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
          });
        }});
    safeWS("wss://ws.bitrue.com/market",
      ws=>ws.send(JSON.stringify({method:"subscribe",params:["trade.XRPUSDT"]})),
      (e,ws)=>{ const d=JSON.parse(e.data);
        if (d && d.data && d.data.length>0){
          d.data.forEach(t=>{
            const price=parseFloat(t.p), vol=parseFloat(t.q);
            createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'Bitrue');
            addTradeToList('Bitrue',vol,price,"Trade",`Bitrue:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
          });
        }});
    safeWS("wss://api-pub.bitfinex.com/ws/2",
      ws=>ws.send(JSON.stringify({event:"subscribe",channel:"trades",symbol:"tXRPUSD"})),
      (e,ws)=>{ const d=JSON.parse(e.data);
        if (Array.isArray(d) && d[1]!=="hb"){
          d[1].forEach(t=>{
            const price=parseFloat(t[3]), vol=parseFloat(t[2]);
            createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'Bitfinex');
            addTradeToList('Bitfinex',vol,price,"Trade",`Bitfinex:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
          });
        }});
    
    safeWS("wss://socket.coinex.com/",
      ws=>ws.send(JSON.stringify({method:"subscribe",params:["trade:XRPUSDT"]})),
      (e,ws)=>{ const d=JSON.parse(e.data);
        if (d.method==="trade.update" && d.params){
          const price=parseFloat(d.params[0].price), vol=parseFloat(d.params[0].volume);
          createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'CoinEx');
          addTradeToList('CoinEx',vol,price,"Trade",`CoinEx:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
        }});
    safeWS("wss://ws-manager-compress.bitmart.com/api?protocol=1.1",
      ws=>ws.send(JSON.stringify({op:"subscribe",args:["spot/trade:XRP_USDT"]})),
      (e,ws)=>{ const d=JSON.parse(e.data);
        if (d.channel==='spot/trade:XRP_USDT' && d.data && d.data.length>0){
          d.data.forEach(t=>{
            const price=parseFloat(t.price), vol=parseFloat(t.size);
            createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'Bitmart');
            addTradeToList('Bitmart',vol,price,"Trade",`Bitmart:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
          });
        }});
    safeWS("wss://xrplcluster.com",
      ws=>ws.send(JSON.stringify({id:"sub",command:"subscribe",streams:["transactions"]})),
      (e,ws)=>{ const d=JSON.parse(e.data);
        if (d.type==="transaction" && d.transaction.TransactionType==="Payment"){
          const vol = parseFloat(d.transaction.Amount)/1_000_000;
          if (vol<100_000_000){
            const price = parseFloat(document.getElementById('holograph-price-overlay').textContent.replace(/\D/g,''))/10000 || 0.5;
            createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'XRPL');
            addTradeToList('XRPL',vol,price,"Ledger Payment");
          }
        }});
    safeWS("wss://ws.bitget.com/spot/v1/stream",
      ws=>ws.send(JSON.stringify({op:"subscribe",args:["trade:XRPUSDT"]})),
      (e,ws)=>{ const d=JSON.parse(e.data);
        if (d.data && d.data.length>0){
          d.data.forEach(t=>{
            const price=parseFloat(t.price), vol=parseFloat(t.size);
            createLightning(Math.random()*180-90, Math.random()*360-180, vol, 'Bitget');
            addTradeToList('Bitget',vol,price,"Trade",`Bitget:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
          });
        }});safeWS("wss://api.huobi.pro/ws",
  ws=>ws.send(JSON.stringify({sub:"market.xrpusdt.trade.detail",id:"id1"})),
  (e,ws)=>{
    const blob=e.data instanceof Blob ? e.data : null;
    if(blob){ blob.arrayBuffer().then(buf=>{
      const d=JSON.parse(pako.inflate(new Uint8Array(buf),{to:"string"}));
      if(d.tick&&d.tick.data){
        d.tick.data.forEach(t=>{
          const price=parseFloat(t.price), vol=parseFloat(t.amount);
          createLightning(Math.random()*180-90, Math.random()*360-180, vol,'Huobi');
          addTradeToList('Huobi',vol,price,"Trade",`Huobi:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
        });
      }
    });}
  });

// 10. GATE.IO
safeWS("wss://api.gateio.ws/ws/v4/",
  ws=>ws.send(JSON.stringify({time:Date.now(),channel:"spot.trades",event:"subscribe",payload:["XRP_USDT"]})),
  (e,ws)=>{
    const d=JSON.parse(e.data);
    if(d.event==="update"&&d.result){
      d.result.forEach(t=>{
        const price=parseFloat(t.price), vol=parseFloat(t.amount);
        createLightning(Math.random()*180-90, Math.random()*360-180, vol,'Gate.io');
        addTradeToList('Gate.io',vol,price,"Trade",`Gate.io:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
      });
    }
  });

// 11. BITRUE
safeWS("wss://ws.bitrue.com/market",
  ws=>ws.send(JSON.stringify({method:"SUBSCRIBE",params:["trade.XRPUSDT"],id:1})),
  (e,ws)=>{
    const d=JSON.parse(e.data);
    if(d.s==="XRPUSDT"&&d.p){
      const price=parseFloat(d.p), vol=parseFloat(d.q);
      createLightning(Math.random()*180-90, Math.random()*360-180, vol,'Bitrue');
      addTradeToList('Bitrue',vol,price,"Trade",`Bitrue:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
    }
  });

// 12. COINEX
safeWS("wss://socket.coinex.com/",
  ws=>ws.send(JSON.stringify({method:"sub.deals",params:["XRPUSDT"],id:1})),
  (e,ws)=>{
    const d=JSON.parse(e.data);
    if(d.method==="deals.update"&&d.params){
      d.params[1].forEach(t=>{
        const price=parseFloat(t[1]), vol=parseFloat(t[2]);
        createLightning(Math.random()*180-90, Math.random()*360-180, vol,'CoinEx');
        addTradeToList('CoinEx',vol,price,"Trade",`CoinEx:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
      });
    }
  });

// 13. BITGET
safeWS("wss://ws.bitget.com/spot/v1/stream",
  ws=>ws.send(JSON.stringify({op:"subscribe",args:["trade:XRPUSDT"]})),
  (e,ws)=>{
    const d=JSON.parse(e.data);
    if(d.action==="update"){
      d.data.forEach(t=>{
        const price=parseFloat(t.p), vol=parseFloat(t.v);
        createLightning(Math.random()*180-90, Math.random()*360-180, vol,'Bitget');
        addTradeToList('Bitget',vol,price,"Trade",`Bitget:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
      });
    }
  });

// 14. BITMART
safeWS("wss://ws-manager-compress.bitmart.com/api?protocol=1.1",
  ws=>ws.send(JSON.stringify({op:"subscribe",args:["spot/trade:XRP_USDT"]})),
  (e,ws)=>{
    const d=JSON.parse(e.data);
    if(d.table==="spot/trade"){
      d.data.forEach(t=>{
        const price=parseFloat(t.price), vol=parseFloat(t.size);
        createLightning(Math.random()*180-90, Math.random()*360-180, vol,'Bitmart');
        addTradeToList('Bitmart',vol,price,"Trade",`Bitmart:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
      });
    }
  });

// 15. BITSO
safeWS("wss://ws.bitso.com",
  ws=>ws.send(JSON.stringify({action:"subscribe",book:"xrp_mxn",type:"trades"})),
  (e,ws)=>{
    const d=JSON.parse(e.data);
    if(d.type==="trade"&&d.payload){
      d.payload.forEach(t=>{
        const price=parseFloat(t.r), vol=parseFloat(t.a);
        createLightning(Math.random()*180-90, Math.random()*360-180, vol,'Bitso');
        addTradeToList('Bitso',vol,price,"Trade",`Bitso:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
      });
    }
  });

// 16. BITFLYER
safeWS("wss://ws.lightstream.bitflyer.com/json-rpc",
  ws=>ws.send(JSON.stringify({method:"subscribe",params:{channel:"lightning_executions_XRP_JPY"},id:123})),
  (e,ws)=>{
    const d=JSON.parse(e.data);
    if(d.params&&d.params.message){
      d.params.message.forEach(t=>{
        const price=parseFloat(t.price), vol=parseFloat(t.size);
        createLightning(Math.random()*180-90, Math.random()*360-180, vol,'Bitflyer');
        addTradeToList('Bitflyer',vol,price,"Trade",`Bitflyer:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
      });
    }
  });

// 17. MEXC
safeWS("wss://wbs.mexc.com/raw/ws",
  ws=>ws.send(JSON.stringify({method:"SUBSCRIPTION",params:["spot@public.deals.v3.api@XRP_USDT"],id:1})),
  (e,ws)=>{
    const d=JSON.parse(e.data);
    if(d.data&&d.data.deals){
      d.data.deals.forEach(t=>{
        const price=parseFloat(t.p), vol=parseFloat(t.v);
        createLightning(Math.random()*180-90, Math.random()*360-180, vol,'MEXC');
        addTradeToList('MEXC',vol,price,"Trade",`MEXC:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
      });
    }
  });

// 18. HITBTC
safeWS("wss://api.hitbtc.com/api/2/ws",
  ws=>ws.send(JSON.stringify({method:"subscribeTrades",params:{symbol:"XRPUSDT"},id:1})),
  (e,ws)=>{
    const d=JSON.parse(e.data);
    if(d.method==="updateTrades"&&d.params){
      d.params.data.forEach(t=>{
        const price=parseFloat(t.price), vol=parseFloat(t.quantity);
        createLightning(Math.random()*180-90, Math.random()*360-180, vol,'HitBTC');
        addTradeToList('HitBTC',vol,price,"Trade",`HitBTC:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
      });
    }
  });

// 19. POLONIEX
safeWS("wss://api2.poloniex.com",
  ws=>ws.send(JSON.stringify({command:"subscribe",channel:"USDT_XRP"})),
  (e,ws)=>{
    const d=JSON.parse(e.data);
    if(Array.isArray(d)&&Array.isArray(d[2])){
      const t=d[2];
      const price=parseFloat(t[2]), vol=parseFloat(t[3]);
      createLightning(Math.random()*180-90, Math.random()*360-180, vol,'Poloniex');
      addTradeToList('Poloniex',vol,price,"Trade",`Poloniex:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
    }
  });

// 20. UPBIT
safeWS("wss://api.upbit.com/websocket/v1",
  ws=>ws.send(JSON.stringify([{ticket:"xrpfeed"},{type:"trade",codes:["USDT-XRP"]}])),
  (e,ws)=>{
    if(e.data instanceof Blob){
      e.data.text().then(txt=>{
        const d=JSON.parse(txt);
        if(d.ty==="trade"){
          const price=parseFloat(d.tp), vol=parseFloat(d.tv);
          createLightning(Math.random()*180-90, Math.random()*360-180, vol,'Upbit');
          addTradeToList('Upbit',vol,price,"Trade",`Upbit:${Math.random().toString(36).substr(2,8).toUpperCase()}`);
        }
      });
    }
  });
    
  // XRPL legend + trade sound effects
let walletAddresses = new Set();
let buyAccounts = new Set();
let sellAccounts = new Set();

// --- SOUND ENGINE ---
const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

function playDrumSound(type, value = 1) {
  const osc = audioCtx.createOscillator();
  const gain = audioCtx.createGain();

  // Normalize trade value → [0,1]
  const vol = Math.min(1, Math.max(0.05, value / 5000));
  const baseFreq = Math.max(40, 300 - value / 30); // large trade = lower pitch
  const now = audioCtx.currentTime;

  // Choose tone style
  switch (type) {
    case 'buy': // kick-like
      osc.type = 'sine';
      osc.frequency.setValueAtTime(baseFreq, now);
      osc.frequency.exponentialRampToValueAtTime(30, now + 0.25);
      gain.gain.setValueAtTime(vol, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);
      break;

    case 'sell': // snare-like
      osc.type = 'triangle';
      osc.frequency.setValueAtTime(baseFreq * 1.5, now);
      osc.frequency.exponentialRampToValueAtTime(200, now + 0.15);
      gain.gain.setValueAtTime(vol * 0.9, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.2);
      break;

    default: // neutral tick / hat
      osc.type = 'square';
      osc.frequency.setValueAtTime(600 + value / 10, now);
      gain.gain.setValueAtTime(vol * 0.3, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.05);
      break;
  }

  osc.connect(gain).connect(audioCtx.destination);
  osc.start(now);
  osc.stop(now + 0.4);
}

// --- TRANSACTION PROCESSING ---
function processTransactionData(txData) {
  if (!txData.transaction) return;
  const tx = txData.transaction;
  const { Account, Destination } = tx;
  if (Account) walletAddresses.add(Account);
  if (Destination) walletAddresses.add(Destination);

  if (tx.TransactionType === "OfferCreate") {
    const isSell = typeof tx.TakerGets === 'string' || (tx.TakerGets && tx.TakerGets.currency === 'XRP');
    const isBuy = typeof tx.TakerPays === 'string' || (tx.TakerPays && tx.TakerPays.currency === 'XRP');

    if (isSell) {
      sellAccounts.add(tx.Account);
      const value = parseFloat(tx.TakerGets?.value || tx.TakerGets || 0) / 1000000;
      playDrumSound('sell', value);
    }
    if (isBuy) {
      buyAccounts.add(tx.Account);
      const value = parseFloat(tx.TakerPays?.value || tx.TakerPays || 0) / 1000000;
      playDrumSound('buy', value);
    }
  }

  // Trim oldest entries
  if (walletAddresses.size > 25) {
    const oldest = Array.from(walletAddresses)[0];
    walletAddresses.delete(oldest);
    if (buyAccounts.has(oldest)) buyAccounts.delete(oldest);
    if (sellAccounts.has(oldest)) sellAccounts.delete(oldest);
  }
  updateLegendPanel();
}

// --- LEGEND PANEL ---
function updateLegendPanel() {
  const list = document.getElementById('legend-list');
  if (!list) return;
  list.innerHTML = '';
  const all = Array.from(walletAddresses);
  if (all.length === 0) {
    list.innerHTML = '<div><span>No wallet addresses available</span></div>';
    return;
  }
  all.forEach(acc => {
    const div = document.createElement('div');
    let colorClass = 'neutral';
    if (buyAccounts.has(acc)) colorClass = 'buy';
    else if (sellAccounts.has(acc)) colorClass = 'sell';
    const cDiv = document.createElement('div');
    cDiv.style.cssText = `background-color:${colorClass === 'buy' ? '#0f0' : (colorClass === 'sell' ? '#f00' : '#0ff')};
                          width:15px;height:15px;margin-right:5px;border:1px solid #0ff;`;
    const txt = document.createElement('span');
    txt.textContent = acc.substring(0, 15) + '...';
    div.appendChild(cDiv);
    div.appendChild(txt);
    list.appendChild(div);
  });
  const lbl = document.createElement('div');
  lbl.innerHTML = '<span style="color:#0f0;">Buy</span> <span style="color:#f00;">Sell</span> <span style="color:#0ff;">Neutral</span>';
  lbl.style.fontSize = '8px';
  lbl.style.marginTop = '5px';
  list.appendChild(lbl);
}

// --- LEDGER SUBSCRIPTION ---
function initLedgerSubscription() {
  safeWS("wss://s1.ripple.com",
    ws => {
      ws.send(JSON.stringify({ id: "sub_transactions", command: "subscribe", streams: ["transactions"] }));
      console.log("Subscribed to XRPL transactions at " + new Date().toLocaleTimeString());
    },
    (e, ws) => {
      const d = JSON.parse(e.data);
      if (d.type === "transaction") processTransactionData(d);
    });
}

document.addEventListener('DOMContentLoaded', () => {
  initLedgerSubscription();
  updateLegendPanel();
});
// ===================================================================
// LINKED PANELS: 1-MINUTE (topLeftPanel) | 1-HOUR (topRightPanel) | PRICE+VOLUME (floating-legend-panel)
// ===================================================================

let lastMinute = -1;
let lastHour = -1;
let currentPrice = 0;

// Ensure history structures exist
tradeCounters.minuteData = tradeCounters.minuteData || { history: [], count: 0, volume: 0 };
tradeCounters.hourData = tradeCounters.hourData || { history: [], count: 0, volume: 0 };

// === ON EACH TRADE: Update price, count, volume ===
function onLiveTrade(price, volume) {
  currentPrice = price;
  const now = new Date();
  const min = now.getMinutes();
  const hour = now.getHours();

  // === MINUTE BUCKET ===
  if (min !== lastMinute) {
    tradeCounters.minuteData.history.push({
      time: min,
      price: currentPrice,
      count: tradeCounters.minuteData.count,
      volume: tradeCounters.minuteData.volume
    });
    if (tradeCounters.minuteData.history.length > 60) tradeCounters.minuteData.history.shift();
    tradeCounters.minuteData.count = 0;
    tradeCounters.minuteData.volume = 0;
    lastMinute = min;
  }
  tradeCounters.minuteData.count++;
  tradeCounters.minuteData.volume += volume;

  // === HOUR BUCKET ===
  if (hour !== lastHour) {
    tradeCounters.hourData.history.push({
      time: hour,
      price: currentPrice,
      count: tradeCounters.hourData.count,
      volume: tradeCounters.hourData.volume
    });
    if (tradeCounters.hourData.history.length > 24) tradeCounters.hourData.history.shift();
    tradeCounters.hourData.count = 0;
    tradeCounters.hourData.volume = 0;
    lastHour = hour;
  }
  tradeCounters.hourData.count += tradeCounters.minuteData.count;
  tradeCounters.hourData.volume += tradeCounters.minuteData.volume;
}

// === CHART INITIALIZATION: LINK TO CORRECT PANELS ===
document.addEventListener('DOMContentLoaded', () => {
  const createChart = (containerId, canvasId, lineColor, barColor) => {
    const container = document.getElementById(containerId);
    if (!container) return null;

    container.style.padding = '0';
    container.style.overflow = 'hidden';
    container.innerHTML = `<canvas id="${canvasId}"></canvas>`;
    const ctx = document.getElementById(canvasId)?.getContext('2d');
    if (!ctx) return null;

    return new Chart(ctx, {
      data: {
        labels: [],
        datasets: [
          {
            type: 'line',
            label: 'Price',
            data: [],
            borderColor: lineColor,
            backgroundColor: lineColor + '20',
            borderWidth: 2,
            pointRadius: 0,
            tension: 0.3,
            yAxisID: 'price'
          },
          {
            type: 'bar',
            label: 'Volume',
            data: [],
            backgroundColor: barColor,
            yAxisID: 'volume'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        layout: { padding: 0 },
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { display: false }, tooltip: { enabled: false } },
        scales: {
          x: { display: false, grid: { display: false, drawBorder: false } },
          price: { display: false, grid: { display: false, drawBorder: false } },
          volume: { display: false, grid: { display: false, drawBorder: false } }
        }
      }
    });
  };

  // Link panels to charts
  window.chartMin = createChart('topLeftPanel', 'minuteTradeChart', '#00ff99', 'rgba(255,204,0,0.3)');
  window.chartHour = createChart('topRightPanel', 'hourTradeChart', '#00ccff', 'rgba(255,102,102,0.3)');

  // Start 1-second interval update loop
  setInterval(updateChartsRealtime, 1000);
});

// === REAL-TIME CHART UPDATE FUNCTION (1 SECOND) ===
function updateChartsRealtime() {
  // === MINUTE CHART UPDATE ===
  if (window.chartMin && tradeCounters.minuteData.history.length > 0) {
    const data = tradeCounters.minuteData.history.slice(-60);
    window.chartMin.data.labels = data.map(h => h.time);
    window.chartMin.data.datasets[0].data = data.map(h => h.price);
    window.chartMin.data.datasets[1].data = data.map(h => h.volume);
    window.chartMin.update('quiet');
  }

  // === HOUR CHART UPDATE ===
  if (window.chartHour && tradeCounters.hourData.history.length > 0) {
    const data = tradeCounters.hourData.history.slice(-24);
    window.chartHour.data.labels = data.map(h => h.time);
    window.chartHour.data.datasets[0].data = data.map(h => h.price);
    window.chartHour.data.datasets[1].data = data.map(h => h.volume);
    window.chartHour.update('quiet');
  }
}

// === HOOK INTO addTradeToList ===
const originalAddTradeToList = addTradeToList;
addTradeToList = function(source, vol, price, type, walletAddr) {
  originalAddTradeToList(source, vol, price, type, walletAddr);
  onLiveTrade(price, vol);
};

// === HISTORICAL DATA PREFILL (BINANCE) ===
async function fetchHistoricalData() {
  try {
    const [minRes, hourRes] = await Promise.all([
      fetch('https://api.binance.com/api/v3/klines?symbol=XRPUSDT&interval=1m&limit=60'),
      fetch('https://api.binance.com/api/v3/klines?symbol=XRPUSDT&interval=1h&limit=24')
    ]);
    const minData = await minRes.json();
    const hourData = await hourRes.json();

    if (!tradeCounters.minuteData.history.length) {
      tradeCounters.minuteData.history = minData.map(k => ({
        time: new Date(k[0]).getMinutes(),
        price: parseFloat(k[4]),
        count: 0,
        volume: parseFloat(k[5])
      }));
    }

    if (!tradeCounters.hourData.history.length) {
      tradeCounters.hourData.history = hourData.map(k => ({
        time: new Date(k[0]).getHours(),
        price: parseFloat(k[4]),
        count: 0,
        volume: parseFloat(k[5])
      }));
    }

    currentPrice = parseFloat(minData[minData.length - 1][4]);
  } catch (e) {
    console.error('Historical fetch error:', e);
  }
}
document.addEventListener('DOMContentLoaded', fetchHistoricalData);


  </script>
</body>
</html>