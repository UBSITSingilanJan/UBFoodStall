/* =========================================================
   UB FoodStall — front-end data store
   ---------------------------------------------------------
   FRONT-END ONLY. All stalls, products and reviews below are
   SAMPLE DATA kept in the browser (localStorage). Nothing here
   writes to MySQL.

   When the back-end is ready, replace the functions in this
   file with fetch() calls to PHP endpoints that return the
   same shapes, and the customer + vendor pages keep working.

   Data shapes
     stall   { id, vendorId, number, name, location, status ('Open'|'Closed'),
               opens 'HH:MM', closes 'HH:MM', days [0-6, 0=Sun], tagline, cover }
     product { id, stallId, name, price, qty, status ('Available'|'Not Available'),
               category, emoji, desc }
     review  { id, productId, stallId, customerId, customerName, rating 1-5,
               comment, date 'YYYY-MM-DD' }
   ========================================================= */
(function () {
  'use strict';

  var KEY = 'ubfs_frontend_v1';

  var CATEGORIES = [
    { id: 'chicken',  name: 'Chicken',    emoji: '🍗', color: '#FFE8D6' },
    { id: 'pork',     name: 'Pork',       emoji: '🥓', color: '#FFE1E6' },
    { id: 'beef',     name: 'Beef',       emoji: '🥩', color: '#F9E0DA' },
    { id: 'siomai',   name: 'Siomai',     emoji: '🥟', color: '#FFF3D1' },
    { id: 'rice',     name: 'Rice Meals', emoji: '🍛', color: '#FFF0DC' },
    { id: 'noodles',  name: 'Noodles',    emoji: '🍜', color: '#FDEBD3' },
    { id: 'snacks',   name: 'Snacks',     emoji: '🍟', color: '#FFF6CC' },
    { id: 'drinks',   name: 'Drinks',     emoji: '🥤', color: '#DFF3FF' },
    { id: 'desserts', name: 'Desserts',   emoji: '🍰', color: '#FBE3F3' }
  ];

  var WEEKDAYS = [1, 2, 3, 4, 5, 6];      // Mon–Sat
  var ALLWEEK  = [0, 1, 2, 3, 4, 5, 6];

  function seed() {
    var stalls = [
      { id: 3, vendorId: 9,  number: 3, name: 'Chicken Masarap', location: 'Building A 1st Floor', status: 'Open',
        opens: '07:00', closes: '17:00', days: WEEKDAYS, tagline: 'Fried chicken, rice meals & more', cover: '🍗' },
      { id: 1, vendorId: 101, number: 1, name: 'Siomai King', location: 'Building F Canteen', status: 'Open',
        opens: '07:30', closes: '18:00', days: WEEKDAYS, tagline: 'Steamed & fried siomai all day', cover: '🥟' },
      { id: 2, vendorId: 102, number: 2, name: "Lutong Bahay ni Aling Nena", location: 'Building A 1st Floor', status: 'Open',
        opens: '10:00', closes: '15:00', days: WEEKDAYS, tagline: 'Home-cooked Filipino ulam', cover: '🍛' },
      { id: 4, vendorId: 103, number: 4, name: "Kape't Tinapay", location: 'Main Lobby', status: 'Open',
        opens: '06:30', closes: '20:00', days: ALLWEEK, tagline: 'Coffee, milk tea & pastries', cover: '☕' },
      { id: 5, vendorId: 104, number: 5, name: 'Sweet Tooth', location: 'Building S Ground Floor', status: 'Open',
        opens: '09:00', closes: '19:00', days: WEEKDAYS, tagline: 'Halo-halo, leche flan & cakes', cover: '🍧' },
      { id: 6, vendorId: 105, number: 6, name: 'Noodle House', location: 'Building F Canteen', status: 'Closed',
        opens: '08:00', closes: '17:00', days: WEEKDAYS, tagline: 'Pancit, mami & ramen', cover: '🍜' },
      { id: 7, vendorId: 106, number: 7, name: 'Ihaw-Ihaw Corner', location: 'Covered Court Area', status: 'Open',
        opens: '11:00', closes: '19:00', days: WEEKDAYS, tagline: 'Pork BBQ, isaw & grilled liempo', cover: '🔥' }
    ];

    var p = [];
    var pid = 1;
    function add(stallId, name, price, qty, category, emoji, desc, status) {
      p.push({ id: pid++, stallId: stallId, name: name, price: price, qty: qty,
               status: status || 'Available', category: category, emoji: emoji, desc: desc || '' });
    }
    // Stall 3 — Chicken Masarap
    add(3, 'Fried Chicken w/ Rice', 85, 24, 'chicken', '🍗', 'Crispy 1-pc chicken with garlic rice');
    add(3, 'Chicken Adobo Rice Meal', 75, 12, 'chicken', '🍛', 'Classic adobo, rice and egg');
    add(3, 'Chicken Fillet Burger', 60, 0, 'chicken', '🍔', 'Breaded fillet, lettuce, mayo');
    add(3, 'Chicken Skin', 35, 30, 'snacks', '🍟', 'Crunchy chicken skin with vinegar');
    add(3, 'Iced Tea (Large)', 25, 40, 'drinks', '🧋', 'House-brewed, lightly sweet');
    // Stall 1 — Siomai King
    add(1, 'Pork Siomai (4pcs)', 25, 60, 'siomai', '🥟', 'Steamed, with chili-garlic');
    add(1, 'Beef Siomai (4pcs)', 35, 32, 'siomai', '🥟', 'Steamed beef siomai');
    add(1, 'Chicken Siomai (4pcs)', 30, 20, 'chicken', '🥟', 'Lighter chicken filling');
    add(1, 'Siomai Rice', 50, 18, 'rice', '🍚', '4 pcs pork siomai + java rice');
    add(1, 'Sago\'t Gulaman', 20, 0, 'drinks', '🥤', 'Brown sugar syrup, sago, gulaman', 'Not Available');
    // Stall 2 — Aling Nena
    add(2, 'Pork Sinigang', 80, 10, 'pork', '🍲', 'Sour tamarind soup with rice');
    add(2, 'Pork Adobo', 75, 14, 'pork', '🥘', 'Soy-vinegar braised pork');
    add(2, 'Beef Caldereta', 95, 6, 'beef', '🥘', 'Tomato-based beef stew');
    add(2, 'Chicken Tinola', 75, 8, 'chicken', '🍲', 'Ginger chicken soup');
    add(2, 'Extra Rice', 15, 50, 'rice', '🍚', '');
    // Stall 4 — Kape't Tinapay
    add(4, 'Iced Americano', 65, 30, 'drinks', '☕', 'Double shot over ice');
    add(4, 'Wintermelon Milk Tea', 70, 25, 'drinks', '🧋', 'With pearls');
    add(4, 'Ensaymada', 35, 15, 'desserts', '🥐', 'Buttery, with cheese');
    add(4, 'Ham & Cheese Pandesal', 40, 0, 'snacks', '🥪', 'Toasted pandesal sandwich');
    add(4, 'Chocolate Chip Cookie', 30, 22, 'desserts', '🍪', '');
    // Stall 5 — Sweet Tooth
    add(5, 'Halo-Halo', 75, 20, 'desserts', '🍧', 'Ube, leche flan, beans, crushed ice');
    add(5, 'Leche Flan Cup', 45, 12, 'desserts', '🍮', '');
    add(5, 'Mango Graham Float', 60, 0, 'desserts', '🥭', 'Layered mango, cream, graham', 'Not Available');
    add(5, 'Buko Juice', 35, 18, 'drinks', '🥥', 'Fresh young coconut');
    // Stall 6 — Noodle House (closed)
    add(6, 'Pancit Canton', 55, 15, 'noodles', '🍜', 'Stir-fried egg noodles');
    add(6, 'Beef Mami', 70, 10, 'beef', '🍜', 'Noodle soup with beef');
    add(6, 'Chicken Mami', 60, 10, 'noodles', '🍜', '');
    // Stall 7 — Ihaw-Ihaw
    add(7, 'Pork BBQ (2 sticks)', 50, 40, 'pork', '🍢', 'Sweet-glazed, charcoal grilled');
    add(7, 'Grilled Liempo w/ Rice', 95, 9, 'pork', '🥓', 'Pork belly, atchara, rice');
    add(7, 'Chicken Isaw (5 sticks)', 30, 25, 'chicken', '🍢', 'With spiced vinegar');
    add(7, 'Inihaw na Hotdog', 25, 0, 'snacks', '🌭', '');

    var names = ['Janelle S.', 'Marc S.', 'Chesney T.', 'Paolo R.', 'Bea C.', 'Kyle D.', 'Andrea M.'];
    var comments = [
      'Sulit! Big serving for the price.', 'Always fresh and hot.', 'Masarap, will buy again.',
      'A bit salty today but still good.', 'Perfect after class.', 'Long line at lunch but worth it.',
      'Good but sometimes runs out early.', 'My go-to every Monday.'
    ];
    var r = [];
    var rid = 1;
    p.forEach(function (prod, i) {
      var count = (i * 7) % 4;           // 0–3 sample reviews per product
      for (var k = 0; k < count; k++) {
        var rating = 5 - ((i + k) % 3 === 0 ? 1 : 0) - ((i + k) % 7 === 0 ? 1 : 0);
        var d = new Date(); d.setDate(d.getDate() - (k * 3 + (i % 5) + 1));
        r.push({ id: rid++, productId: prod.id, stallId: prod.stallId, customerId: null,
                 customerName: names[(i + k) % names.length], rating: rating,
                 comment: comments[(i * 3 + k) % comments.length], date: d.toISOString().slice(0, 10) });
      }
    });

    return { stalls: stalls, products: p, reviews: r, nextProductId: pid, nextReviewId: rid };
  }

  var state;
  function load() {
    try {
      var raw = window.localStorage.getItem(KEY);
      if (raw) { state = JSON.parse(raw); return; }
    } catch (e) { /* storage blocked — fall back to memory */ }
    state = seed();
    save();
  }
  function save() {
    try { window.localStorage.setItem(KEY, JSON.stringify(state)); } catch (e) { /* ignore */ }
  }
  load();

  /* ---------- helpers ---------- */
  function toMin(hhmm) { var a = String(hhmm).split(':'); return (+a[0]) * 60 + (+a[1] || 0); }
  function fmtTime(hhmm) {
    var m = toMin(hhmm), h = Math.floor(m / 60), mm = m % 60, ap = h >= 12 ? 'PM' : 'AM';
    var h12 = h % 12 === 0 ? 12 : h % 12;
    return h12 + ':' + (mm < 10 ? '0' : '') + mm + ' ' + ap;
  }
  var DAY_NAMES = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  function fmtDays(days) {
    var d = (days || []).slice().sort();
    if (d.length === 7) return 'Daily';
    if (d.join() === '1,2,3,4,5,6') return 'Mon–Sat';
    if (d.join() === '1,2,3,4,5') return 'Mon–Fri';
    return d.map(function (x) { return DAY_NAMES[x]; }).join(', ');
  }

  /** Is the stall open right now? Manual "Closed" status from the vendor always wins. */
  function openInfo(stall, now) {
    now = now || new Date();
    var hours = fmtTime(stall.opens) + ' – ' + fmtTime(stall.closes);
    if (stall.status === 'Closed') {
      return { open: false, label: 'Closed', detail: 'Closed by vendor today', hours: hours };
    }
    var day = now.getDay(), mins = now.getHours() * 60 + now.getMinutes();
    var o = toMin(stall.opens), c = toMin(stall.closes);
    var todayOpen = stall.days.indexOf(day) !== -1;
    if (todayOpen && mins >= o && mins < c) {
      var left = c - mins;
      return { open: true, label: 'Open', detail: left <= 60 ? 'Closing soon · ' + fmtTime(stall.closes) : 'Closes ' + fmtTime(stall.closes), hours: hours, closingSoon: left <= 60 };
    }
    if (todayOpen && mins < o) {
      return { open: false, label: 'Closed', detail: 'Opens today ' + fmtTime(stall.opens), hours: hours };
    }
    for (var i = 1; i <= 7; i++) {
      var nd = (day + i) % 7;
      if (stall.days.indexOf(nd) !== -1) {
        return { open: false, label: 'Closed', detail: 'Opens ' + (i === 1 ? 'tomorrow' : DAY_NAMES[nd]) + ' ' + fmtTime(stall.opens), hours: hours };
      }
    }
    return { open: false, label: 'Closed', detail: 'No schedule set', hours: hours };
  }

  function isAvailable(prod) { return prod.status === 'Available' && prod.qty > 0; }

  function ratingOf(list) {
    if (!list.length) return { avg: 0, count: 0 };
    var t = 0; list.forEach(function (r) { t += r.rating; });
    return { avg: t / list.length, count: list.length };
  }

  /* ---------- public API ---------- */
  var Store = {
    categories: CATEGORIES,
    category: function (id) { return CATEGORIES.filter(function (c) { return c.id === id; })[0]; },
    fmtTime: fmtTime,
    fmtDays: fmtDays,
    openInfo: openInfo,
    isAvailable: isAvailable,

    stalls: function () { return state.stalls.slice().sort(function (a, b) { return a.number - b.number; }); },
    stall: function (id) { return state.stalls.filter(function (s) { return s.id === +id; })[0]; },
    stallByVendor: function (vid) { return state.stalls.filter(function (s) { return s.vendorId === +vid; })[0]; },

    products: function (stallId) {
      return state.products.filter(function (p) { return stallId == null || p.stallId === +stallId; });
    },
    product: function (id) { return state.products.filter(function (p) { return p.id === +id; })[0]; },

    reviewsForProduct: function (pid) {
      return state.reviews.filter(function (r) { return r.productId === +pid; })
        .sort(function (a, b) { return b.date < a.date ? -1 : b.date > a.date ? 1 : b.id - a.id; });
    },
    reviewsForStall: function (sid) {
      return state.reviews.filter(function (r) { return r.stallId === +sid; })
        .sort(function (a, b) { return b.date < a.date ? -1 : b.date > a.date ? 1 : b.id - a.id; });
    },
    productRating: function (pid) { return ratingOf(Store.reviewsForProduct(pid)); },
    /** Stall rating = average of every product review in that stall. */
    stallRating: function (sid) { return ratingOf(Store.reviewsForStall(sid)); },

    addReview: function (productId, rating, comment, user) {
      var prod = Store.product(productId);
      if (!prod) return null;
      var rev = { id: state.nextReviewId++, productId: prod.id, stallId: prod.stallId,
                  customerId: user && user.id || null, customerName: (user && user.short) || 'Student',
                  rating: Math.max(1, Math.min(5, +rating)), comment: String(comment || '').trim(),
                  date: new Date().toISOString().slice(0, 10) };
      state.reviews.push(rev); save(); return rev;
    },

    /* Link a logged-in vendor to their stall. Name/location come from the real
       food_stall row (managed by admin); everything else stays front-end. */
    ensureVendorStall: function (vendorId, db) {
      var s = Store.stallByVendor(vendorId);
      if (!s) {
        var maxId = 0, maxNum = 0;
        state.stalls.forEach(function (x) { maxId = Math.max(maxId, x.id); maxNum = Math.max(maxNum, x.number); });
        s = { id: maxId + 1, vendorId: +vendorId, number: (db && +db.Stall_ID) || maxNum + 1, name: 'My Stall', location: '',
              status: 'Closed', opens: '08:00', closes: '17:00', days: WEEKDAYS.slice(), tagline: '', cover: '🍽️' };
        state.stalls.push(s);
      }
      if (db) {
        if (db.stallname) s.name = db.stallname;
        if (db.location) s.location = db.location;
      }
      save(); return s;
    },

    /* vendor-side edits */
    updateStall: function (id, patch) {
      var s = Store.stall(id); if (!s) return;
      Object.keys(patch).forEach(function (k) { s[k] = patch[k]; }); save(); return s;
    },
    saveProduct: function (data) {
      if (data.id) {
        var p = Store.product(data.id);
        Object.keys(data).forEach(function (k) { p[k] = data[k]; });
      } else {
        data.id = state.nextProductId++;
        state.products.push(data);
      }
      save(); return data;
    },
    deleteProduct: function (id) {
      state.products = state.products.filter(function (p) { return p.id !== +id; });
      state.reviews = state.reviews.filter(function (r) { return r.productId !== +id; });
      save();
    },
    reset: function () { state = seed(); save(); }
  };

  /* shared UI helpers */
  Store.esc = function (s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  };
  Store.peso = function (n) { return '₱' + Number(n).toFixed(2); };
  /** Text stars, e.g. 4.4 -> "★★★★☆" */
  Store.stars = function (avg) {
    var n = Math.round(avg), out = '';
    for (var i = 1; i <= 5; i++) out += i <= n ? '★' : '☆';
    return out;
  };
  Store.pad = function (n) { return (n < 10 ? '0' : '') + n; };

  window.UBFS = Store;
})();
