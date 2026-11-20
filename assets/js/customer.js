/* UB FoodStall — customer pages (plain HTML, no styling, view-only menu) */
(function () {
  'use strict';
  var S = window.UBFS;
  var USER = window.UBFS_USER || { first: 'Customer', short: 'Student' };
  var esc = S.esc, peso = S.peso;
  var app = document.getElementById('app');
  var msgBox = document.getElementById('msg');

  /* ---------------- helpers ---------------- */
  function msg(text) {
    msgBox.textContent = text;
    clearTimeout(msg._t); msg._t = setTimeout(function () { msgBox.textContent = ''; }, 4000);
  }
  function statusText(info) {
    if (!info.open) return '<b>CLOSED</b> (' + esc(info.detail) + ')';
    return '<b>OPEN</b> (' + esc(info.detail) + ')';
  }
  function ratingText(r) {
    if (!r.count) return 'No reviews yet';
    return S.stars(r.avg) + ' ' + r.avg.toFixed(1) + ' (' + r.count + ' review' + (r.count === 1 ? '' : 's') + ')';
  }
  function stockText(p) {
    if (p.status !== 'Available') return 'Not Available';
    if (p.qty <= 0) return 'Out of Stock';
    if (p.qty <= 5) return 'Available (only ' + p.qty + ' left)';
    return 'Available';
  }
  function plural(n, word) { return n + ' ' + word + (n === 1 ? '' : 's'); }
  function fmtDate(iso) {
    return new Date(iso + 'T00:00:00').toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
  }
  function norm(s) { return String(s || '').toLowerCase(); }
  function availableCount(stallId) { return S.products(stallId).filter(S.isAvailable).length; }

  /* ---------------- router ---------------- */
  function parseHash() {
    var h = location.hash.replace(/^#\/?/, '');
    var parts = h.split('?'), path = parts[0].split('/'), q = {};
    (parts[1] || '').split('&').forEach(function (kv) {
      if (!kv) return; var a = kv.split('=');
      q[decodeURIComponent(a[0])] = decodeURIComponent((a[1] || '').replace(/\+/g, ' '));
    });
    return { name: path[0] || 'stalls', arg: path[1], q: q };
  }
  function render() {
    var r = parseHash();
    app.onclick = null; app.onchange = null; app.onsubmit = null; app.oninput = null;
    var views = { stalls: viewStalls, stall: viewStall, product: viewProduct, food: viewFood,
                  search: viewSearch, account: viewAccount, policy: viewPolicy };
    (views[r.name] || viewStalls)(r);
    window.scrollTo(0, 0);
  }
  window.addEventListener('hashchange', render);

  /* ================= STALLS ================= */
  var stallFilter = 'all';
  function viewStalls() {
    var all = S.stalls();
    var openCount = all.filter(function (s) { return S.openInfo(s).open; }).length;
    var list = all.slice();
    if (stallFilter === 'open') list = list.filter(function (s) { return S.openInfo(s).open; });
    if (stallFilter === 'closed') list = list.filter(function (s) { return !S.openInfo(s).open; });
    if (stallFilter === 'top') list.sort(function (a, b) { return S.stallRating(b.id).avg - S.stallRating(a.id).avg; });

    app.innerHTML =
      '<h2>Food Stalls</h2>' +
      '<p>' + openCount + ' of ' + all.length + ' food stalls are open right now.</p>' +
      '<p>Show: <select id="stallFilter">' +
        opt('all', 'All stalls') + opt('open', 'Open now') + opt('closed', 'Closed') + opt('top', 'Top rated') +
      '</select></p>' +
      (list.length ?
        '<table border="1" cellpadding="8">' +
          '<tr><th>Stall No.</th><th>Stall Name</th><th>Location</th><th>Status</th><th>Opening Hours</th><th>Rating</th><th>Available Items</th><th>Action</th></tr>' +
          list.map(function (s) {
            var info = S.openInfo(s), r = S.stallRating(s.id);
            return '<tr>' +
              '<td>Stall #' + S.pad(s.number) + '</td>' +
              '<td>' + esc(s.name) + '<br><small>' + esc(s.tagline) + '</small></td>' +
              '<td>' + esc(s.location) + '</td>' +
              '<td>' + statusText(info) + '</td>' +
              '<td>' + esc(info.hours) + '<br><small>' + esc(S.fmtDays(s.days)) + '</small></td>' +
              '<td>' + ratingText(r) + '</td>' +
              '<td>' + availableCount(s.id) + ' of ' + S.products(s.id).length + '</td>' +
              '<td><a href="#/stall/' + s.id + '">View Stall</a></td>' +
            '</tr>';
          }).join('') +
        '</table>'
        : '<p>No stalls match this filter.</p>');

    function opt(v, label) { return '<option value="' + v + '"' + (stallFilter === v ? ' selected' : '') + '>' + label + '</option>'; }
    app.onchange = function (e) {
      if (e.target.id === 'stallFilter') { stallFilter = e.target.value; viewStalls(); }
    };
  }

  /* ================= STALL DETAIL ================= */
  var stallState = { stallId: null, cat: 'all', availOnly: false };
  function viewStall(r) {
    var s = S.stall(r.arg);
    if (!s) { app.innerHTML = '<p>Stall not found. <a href="#/stalls">Back to Stalls</a></p>'; return; }
    if (stallState.stallId !== s.id) { stallState = { stallId: s.id, cat: 'all', availOnly: false }; }
    if (r.q.cat) stallState.cat = r.q.cat;

    var info = S.openInfo(s), rating = S.stallRating(s.id);
    var prods = S.products(s.id), availProds = prods.filter(S.isAvailable);
    var cats = [];
    prods.forEach(function (p) { if (cats.indexOf(p.category) === -1) cats.push(p.category); });
    if (stallState.cat !== 'all' && cats.indexOf(stallState.cat) === -1) stallState.cat = 'all';
    var shown = prods.filter(function (p) {
      return (stallState.cat === 'all' || p.category === stallState.cat) && (!stallState.availOnly || S.isAvailable(p));
    });

    var prices = availProds.map(function (p) { return +p.price; });
    var reviews = S.reviewsForStall(s.id);

    app.innerHTML =
      '<p><a href="#/stalls">&larr; Back to Stalls</a></p>' +
      '<h2>Stall #' + S.pad(s.number) + ' - ' + esc(s.name) + '</h2>' +
      '<p>' + esc(s.tagline) + '</p>' +
      '<ul>' +
        '<li>Status: ' + statusText(info) + '</li>' +
        '<li>Location: ' + esc(s.location) + '</li>' +
        '<li>Opening hours: ' + esc(info.hours) + ', ' + esc(S.fmtDays(s.days)) + '</li>' +
        '<li>Rating: ' + ratingText(rating) + '</li>' +
        '<li>Available items: ' + availProds.length + ' of ' + prods.length + '</li>' +
        (prices.length ?
          '<li>Cheapest: ' + peso(Math.min.apply(null, prices)) +
          ' | Average price: ' + peso(prices.reduce(function (a, b) { return a + b; }, 0) / prices.length) +
          ' | Most expensive: ' + peso(Math.max.apply(null, prices)) + '</li>' : '') +
      '</ul>' +
      '<p><i>' + (info.open
        ? 'This menu is for viewing only. No online orders. Visit the stall to buy.'
        : 'This stall is closed. ' + esc(info.detail) + '.') + '</i></p>' +

      '<h3>Menu</h3>' +
      '<p>Category: <select id="catFilter"><option value="all">All (' + prods.length + ')</option>' +
        cats.map(function (c) {
          var cat = S.category(c), n = prods.filter(function (p) { return p.category === c; }).length;
          return '<option value="' + c + '"' + (stallState.cat === c ? ' selected' : '') + '>' + esc(cat.name) + ' (' + n + ')</option>';
        }).join('') +
      '</select> ' +
      '<label><input type="checkbox" id="availOnly"' + (stallState.availOnly ? ' checked' : '') + '> Available only</label></p>' +
      (shown.length ?
        '<table border="1" cellpadding="8">' +
          '<tr><th>Product</th><th>Category</th><th>Price</th><th>Quantity Left</th><th>Status</th><th>Rating</th><th>Action</th></tr>' +
          shown.map(function (p) {
            return '<tr>' +
              '<td>' + esc(p.name) + (p.desc ? '<br><small>' + esc(p.desc) + '</small>' : '') + '</td>' +
              '<td>' + esc(S.category(p.category).name) + '</td>' +
              '<td>' + peso(p.price) + '</td>' +
              '<td>' + (p.status === 'Available' ? p.qty : 0) + '</td>' +
              '<td>' + stockText(p) + '</td>' +
              '<td>' + ratingText(S.productRating(p.id)) + '</td>' +
              '<td><a href="#/product/' + p.id + '">View / Rate</a></td>' +
            '</tr>';
          }).join('') +
        '</table>'
        : '<p>No items here right now.</p>') +

      '<h3>Student Reviews (' + reviews.length + ')</h3>' +
      ratingBreakdown(reviews) +
      reviewList(reviews.slice(0, 10), true) +
      (reviews.length > 10 ? '<p><small>Showing the 10 latest. Open a product to read all its reviews.</small></p>' : '');

    app.onchange = function (e) {
      if (e.target.id === 'catFilter') stallState.cat = e.target.value;
      else if (e.target.id === 'availOnly') stallState.availOnly = e.target.checked;
      else return;
      history.replaceState(null, '', '#/stall/' + s.id);
      viewStall({ arg: s.id, q: {} });
    };
  }

  function ratingBreakdown(list) {
    if (!list.length) return '';
    var counts = [0, 0, 0, 0, 0, 0];
    list.forEach(function (r) { counts[r.rating]++; });
    var rows = '';
    for (var i = 5; i >= 1; i--) rows += '<tr><td>' + i + ' star' + (i > 1 ? 's' : '') + '</td><td>' + counts[i] + '</td></tr>';
    return '<table border="1" cellpadding="4"><tr><th>Rating</th><th>Count</th></tr>' + rows + '</table>';
  }
  function reviewList(list, showItem) {
    if (!list.length) return '<p>No reviews yet.</p>';
    return '<ul>' + list.map(function (rv) {
      var p = showItem ? S.product(rv.productId) : null;
      var mine = USER.id && rv.customerId === USER.id;
      return '<li><b>' + esc(rv.customerName) + (mine ? ' (You)' : '') + '</b> - ' + S.stars(rv.rating) + ' (' + rv.rating + '/5) - ' +
        fmtDate(rv.date) + (p ? ' - on ' + esc(p.name) : '') +
        (rv.comment ? '<br>' + esc(rv.comment) : '') + '</li>';
    }).join('') + '</ul>';
  }

  /* ================= PRODUCT DETAIL + REVIEW ================= */
  var HINTS = ['', 'Poor', 'Fair', 'Good', 'Very good', 'Excellent'];
  function viewProduct(r) {
    var p = S.product(r.arg);
    if (!p) { app.innerHTML = '<p>Product not found. <a href="#/stalls">Back to Stalls</a></p>'; return; }
    var s = S.stall(p.stallId), info = S.openInfo(s), rating = S.productRating(p.id), reviews = S.reviewsForProduct(p.id);

    app.innerHTML =
      '<p><a href="#/stall/' + s.id + '">&larr; Back to ' + esc(s.name) + '</a></p>' +
      '<h2>' + esc(p.name) + '</h2>' +
      (p.desc ? '<p>' + esc(p.desc) + '</p>' : '') +
      '<ul>' +
        '<li>Stall: Stall #' + S.pad(s.number) + ' - ' + esc(s.name) + ' (' + (info.open ? 'Open' : 'Closed') + ')</li>' +
        '<li>Category: ' + esc(S.category(p.category).name) + '</li>' +
        '<li>Price: ' + peso(p.price) + '</li>' +
        '<li>Quantity left: ' + (p.status === 'Available' ? p.qty : 0) + '</li>' +
        '<li>Status: ' + stockText(p) + '</li>' +
        '<li>Rating: ' + ratingText(rating) + '</li>' +
      '</ul>' +
      '<p><i>This menu is for viewing only. No online orders. Buy directly at the stall.</i></p>' +

      '<h3>Rate this product</h3>' +
      '<form id="reviewForm">' +
        '<fieldset><legend>Your rating</legend>' +
          [5, 4, 3, 2, 1].map(function (i) {
            return '<label><input type="radio" name="rating" value="' + i + '" required> ' + S.stars(i) + ' ' + i + ' - ' + HINTS[i] + '</label><br>';
          }).join('') +
        '</fieldset>' +
        '<p><label for="reviewText">Comment</label><br>' +
        '<textarea id="reviewText" maxlength="300" rows="4" cols="50" placeholder="Taste, serving size, price..."></textarea></p>' +
        '<button type="submit">Post Review</button>' +
      '</form>' +

      '<h3>Reviews (' + reviews.length + ')</h3>' +
      ratingBreakdown(reviews) +
      reviewList(reviews, false);

    app.onsubmit = function (e) {
      e.preventDefault();
      var picked = app.querySelector('input[name=rating]:checked');
      if (!picked) { msg('Please choose a star rating.'); return; }
      S.addReview(p.id, +picked.value, app.querySelector('#reviewText').value, USER);
      viewProduct(r);
      msg('Thanks! Your ' + picked.value + '-star review was posted.');
    };
  }

  /* ================= FOOD ================= */
  function stallsWithCategory(cid) {
    return S.stalls().map(function (s) {
      var items = S.products(s.id).filter(function (p) { return p.category === cid; });
      return { stall: s, items: items, avail: items.filter(S.isAvailable) };
    }).filter(function (x) { return x.items.length; })
      .sort(function (a, b) {
        var oa = S.openInfo(a.stall).open ? 0 : 1, ob = S.openInfo(b.stall).open ? 0 : 1;
        return oa - ob || b.avail.length - a.avail.length;
      });
  }
  function viewFood(r) {
    var active = r.arg && S.category(r.arg) ? r.arg : null;
    var html = '<h2>Food</h2><p>Choose a dish to see which food stalls have it.</p><ul>' +
      S.categories.map(function (c) {
        var n = stallsWithCategory(c.id).length;
        var label = esc(c.name) + ' (' + plural(n, 'stall') + ')';
        return '<li>' + (active === c.id ? '<b>' + label + '</b>' : '<a href="#/food/' + c.id + '">' + label + '</a>') + '</li>';
      }).join('') + '</ul>';

    if (active) {
      var cat = S.category(active), list = stallsWithCategory(active);
      html += '<h3>Food stalls with ' + esc(cat.name) + '</h3>';
      html += list.length ?
        '<table border="1" cellpadding="8">' +
          '<tr><th>Stall</th><th>Status</th><th>Rating</th><th>' + esc(cat.name) + ' Items</th><th>Action</th></tr>' +
          list.map(function (x) {
            var s = x.stall;
            return '<tr>' +
              '<td>Stall #' + S.pad(s.number) + ' - ' + esc(s.name) + '</td>' +
              '<td>' + statusText(S.openInfo(s)) + '</td>' +
              '<td>' + ratingText(S.stallRating(s.id)) + '</td>' +
              '<td><ul>' + x.items.map(function (p) {
                return '<li>' + esc(p.name) + ' - ' + peso(p.price) + ' (' + stockText(p) + ')</li>';
              }).join('') + '</ul></td>' +
              '<td><a href="#/stall/' + s.id + '?cat=' + active + '">Go to Stall</a></td>' +
            '</tr>';
          }).join('') +
        '</table>'
        : '<p>No stall sells this yet.</p>';
    }
    app.innerHTML = html;
  }

  /* ================= SEARCH ================= */
  var RECENT_KEY = 'ubfs_recent_searches';
  function getRecent() { try { return JSON.parse(localStorage.getItem(RECENT_KEY)) || []; } catch (e) { return []; } }
  function pushRecent(q) {
    q = q.trim(); if (!q) return;
    var list = getRecent().filter(function (x) { return x.toLowerCase() !== q.toLowerCase(); });
    list.unshift(q);
    try { localStorage.setItem(RECENT_KEY, JSON.stringify(list.slice(0, 6))); } catch (e) {}
  }

  function runSearch(q) {
    var tokens = norm(q).split(/\s+/).filter(Boolean);
    if (!tokens.length) return null;
    function hit(hay) { hay = norm(hay); return tokens.every(function (t) { return hay.indexOf(t) !== -1; }); }

    var products = S.products().filter(function (p) {
      var c = S.category(p.category);
      return hit(p.name + ' ' + p.desc + ' ' + (c ? c.name + ' ' + c.id : ''));
    });
    var stallNameHits = S.stalls().filter(function (s) { return hit(s.name + ' ' + s.tagline + ' ' + s.location + ' stall ' + s.number); });

    var byStall = {};
    products.forEach(function (p) { (byStall[p.stallId] = byStall[p.stallId] || []).push(p); });
    stallNameHits.forEach(function (s) { byStall[s.id] = byStall[s.id] || []; });

    // score each stall: available matches, open now, rating, name match
    var ranked = Object.keys(byStall).map(function (id) {
      var s = S.stall(id), items = byStall[id], info = S.openInfo(s), rt = S.stallRating(s.id);
      var avail = items.filter(S.isAvailable), nameHit = stallNameHits.indexOf(s) !== -1;
      var score = avail.length * 3 + items.length + (info.open ? 8 : 0) + rt.avg * 1.5 + (nameHit ? 4 : 0);
      return { stall: s, items: items, avail: avail, info: info, rating: rt, score: score, nameHit: nameHit };
    }).sort(function (a, b) { return b.score - a.score; });

    var best = ranked.filter(function (x) { return x.info.open && (x.avail.length || x.nameHit); })[0] || null;
    products.sort(function (a, b) { return (S.isAvailable(b) ? 1 : 0) - (S.isAvailable(a) ? 1 : 0) || a.price - b.price; });
    return { q: q, products: products, stalls: ranked, best: best };
  }

  function whyText(x, q) {
    var bits = [];
    if (x.avail.length) bits.push('it has ' + plural(x.avail.length, '"' + esc(q) + '" item') + ' available');
    else if (x.nameHit) bits.push('its name matches "' + esc(q) + '"');
    bits.push('it is open now (' + esc(x.info.detail.toLowerCase()) + ')');
    if (x.rating.count) bits.push('it is rated ' + x.rating.avg.toFixed(1) + ' stars by ' + plural(x.rating.count, 'student'));
    if (x.avail.length) {
      var cheapest = x.avail.slice().sort(function (a, b) { return a.price - b.price; })[0];
      bits.push('prices start at ' + peso(cheapest.price));
    }
    return 'Recommended because ' + bits.join(', ') + '.';
  }

  // live suggestions shown under the search box while typing
  function suggestHtml(res) {
    if (!res) return '';
    if (!res.products.length && !res.stalls.length) return '<p>No matches for "' + esc(res.q) + '".</p>';
    var html = '<p><b>Suggestions:</b></p><ul>';
    if (res.best) {
      html += '<li><b>Recommended stall:</b> <a href="#/stall/' + res.best.stall.id + '">Stall #' + S.pad(res.best.stall.number) + ' - ' +
        esc(res.best.stall.name) + '</a> (' + (res.best.avail.length ? plural(res.best.avail.length, 'matching item') + ', ' : '') + 'Open)</li>';
    }
    res.stalls.filter(function (x) { return !res.best || x.stall.id !== res.best.stall.id; }).slice(0, 4).forEach(function (x) {
      html += '<li>Stall: <a href="#/stall/' + x.stall.id + '">' + esc(x.stall.name) + '</a> (' + (x.info.open ? 'Open' : 'Closed') + ')</li>';
    });
    res.products.slice(0, 6).forEach(function (p) {
      html += '<li>Food: <a href="#/product/' + p.id + '">' + esc(p.name) + '</a> - ' + esc(S.stall(p.stallId).name) + ' - ' + peso(p.price) + ' (' + stockText(p) + ')</li>';
    });
    return html + '</ul>';
  }

  function resultsHtml(res) {
    if (!res) {
      var recent = getRecent();
      return (recent.length ? '<h3>Recent searches</h3><p>' +
          recent.map(function (q) { return '<button type="button" data-q="' + esc(q) + '">' + esc(q) + '</button>'; }).join(' ') +
          ' <button type="button" data-clear-recent>Clear</button></p>' : '') +
        '<h3>Popular searches</h3><p>' +
          ['Chicken', 'Siomai', 'Milk tea', 'Halo-halo', 'Rice', 'Pork BBQ', 'Dessert'].map(function (q) {
            return '<button type="button" data-q="' + q + '">' + q + '</button>';
          }).join(' ') + '</p>';
    }
    if (!res.products.length && !res.stalls.length) {
      return '<p>No food stalls or food match "' + esc(res.q) + '". Check the spelling or try another word.</p>';
    }
    var html = '';
    if (res.best) {
      var b = res.best;
      html += '<h3>Recommended Stall for "' + esc(res.q) + '"</h3>' +
        '<p><b>Stall #' + S.pad(b.stall.number) + ' - ' + esc(b.stall.name) + '</b> - ' + statusText(b.info) + '<br>' +
        esc(b.stall.location) + ' | ' + esc(b.info.hours) + ' | ' + ratingText(b.rating) + '</p>' +
        '<p>' + whyText(b, res.q) + '</p>' +
        (b.avail.length ? '<ul>' + b.avail.map(function (p) {
          return '<li>' + esc(p.name) + ' - ' + peso(p.price) + ' (' + p.qty + ' left)</li>';
        }).join('') + '</ul>' : '') +
        '<p><a href="#/stall/' + b.stall.id + '">Go to this stall</a></p>';
    } else if (res.stalls.length) {
      html += '<p><b>Stalls with "' + esc(res.q) + '" are closed or sold out right now.</b></p>';
    }

    var others = res.stalls.filter(function (x) { return !res.best || x.stall.id !== res.best.stall.id; });
    if (others.length) {
      html += '<h3>Other Food Stalls</h3><table border="1" cellpadding="8">' +
        '<tr><th>Stall</th><th>Status</th><th>Matching Items</th><th>Rating</th><th>Action</th></tr>' +
        others.map(function (x) {
          return '<tr><td>Stall #' + S.pad(x.stall.number) + ' - ' + esc(x.stall.name) + '</td>' +
            '<td>' + statusText(x.info) + '</td>' +
            '<td>' + (x.items.length ? x.avail.length + ' of ' + x.items.length + ' available' : '-') + '</td>' +
            '<td>' + ratingText(x.rating) + '</td>' +
            '<td><a href="#/stall/' + x.stall.id + '">View Stall</a></td></tr>';
        }).join('') + '</table>';
    }
    if (res.products.length) {
      html += '<h3>Food matching "' + esc(res.q) + '" (' + res.products.length + ')</h3><table border="1" cellpadding="8">' +
        '<tr><th>Product</th><th>Stall</th><th>Price</th><th>Quantity Left</th><th>Status</th><th>Action</th></tr>' +
        res.products.map(function (p) {
          var s = S.stall(p.stallId);
          return '<tr><td>' + esc(p.name) + '</td><td>' + esc(s.name) + '</td><td>' + peso(p.price) + '</td>' +
            '<td>' + (p.status === 'Available' ? p.qty : 0) + '</td><td>' + stockText(p) + '</td>' +
            '<td><a href="#/product/' + p.id + '">View / Rate</a></td></tr>';
        }).join('') + '</table>';
    }
    return html;
  }

  function viewSearch(r) {
    var q = r.q.q || '';
    app.innerHTML =
      '<h2>Search</h2>' +
      '<form id="searchForm">' +
        '<input type="search" id="q" size="40" autocomplete="off" placeholder="Search food stalls or food, e.g. chicken" value="' + esc(q) + '"> ' +
        '<button type="submit">Search</button>' +
      '</form>' +
      '<div id="suggest"></div>' +
      '<div id="results">' + resultsHtml(q ? runSearch(q) : null) + '</div>';

    var input = app.querySelector('#q'), sug = app.querySelector('#suggest');
    function doSearch(v) {
      v = v.trim();
      sug.innerHTML = '';
      pushRecent(v);
      history.replaceState(null, '', '#/search' + (v ? '?q=' + encodeURIComponent(v) : ''));
      app.querySelector('#results').innerHTML = resultsHtml(v ? runSearch(v) : null);
    }
    app.oninput = function (e) {
      if (e.target !== input) return;
      sug.innerHTML = input.value.trim() ? suggestHtml(runSearch(input.value)) : '';
    };
    app.onsubmit = function (e) { e.preventDefault(); doSearch(input.value); };
    app.onclick = function (e) {
      var c = e.target.closest('[data-q]');
      if (c) { input.value = c.getAttribute('data-q'); doSearch(input.value); return; }
      if (e.target.closest('[data-clear-recent]')) {
        try { localStorage.removeItem(RECENT_KEY); } catch (er) {}
        doSearch('');
        return;
      }
      if (e.target.closest('#suggest a')) pushRecent(input.value);
    };
    if (!q) input.focus();
  }

  /* ================= ACCOUNT ================= */
  var CONN_KEY = 'ubfs_connected_' + (USER.id || 'guest');
  function getConn() {
    try { return JSON.parse(localStorage.getItem(CONN_KEY)) || { google: true, microsoft: false, facebook: false }; }
    catch (e) { return { google: true, microsoft: false, facebook: false }; }
  }
  var POLICY = {
    terms: { title: 'Terms of Use', body:
      '<p><b>1. About the service.</b> UB FoodStall lets the University of Baguio community view campus food stalls, their menus, prices, stock and opening hours. It is a view-only directory. There is no online ordering, payment or delivery.</p>' +
      '<p><b>2. Accounts.</b> Only students, instructors and staff with a valid school email may register. Keep your password private. You are responsible for activity on your account.</p>' +
      '<p><b>3. Accuracy.</b> Prices, quantities and hours are updated by each vendor and may change during the day. Always confirm at the stall.</p>' +
      '<p><b>4. Reviews.</b> Reviews must be honest, based on your own experience, and respectful. No profanity, harassment, spam or personal information.</p>' +
      '<p><b>5. Moderation.</b> The administrator may remove reviews or suspend accounts that break these terms.</p>' },
    privacy: { title: 'Privacy Policy', body:
      '<p>We collect your name, school email, department, year level and user type when you register, plus any reviews you post.</p>' +
      '<p>Your reviews show your first name and last initial to other users. Your email and other profile details are never shown publicly.</p>' +
      '<p>Data is used only to run UB FoodStall and is handled in line with the Data Privacy Act of 2012 (RA 10173). You may ask the administrator to correct or delete your data.</p>' },
    review: { title: 'Review Guidelines', body:
      '<ul><li>Write about taste, serving size, price, cleanliness and service.</li><li>Rate the specific product you bought.</li>' +
      '<li>No offensive language, personal attacks or fake reviews.</li><li>Do not post phone numbers, IDs or other personal details.</li></ul>' }
  };

  function viewAccount() {
    var fullName = (USER.first + ' ' + (USER.last || '')).trim();
    var myReviews = 0;
    S.stalls().forEach(function (s) {
      myReviews += S.reviewsForStall(s.id).filter(function (r) { return USER.id && r.customerId === USER.id; }).length;
    });
    var conn = getConn();
    function yr(y) { if (!y) return '-'; var n = +y; return n + (n === 1 ? 'st' : n === 2 ? 'nd' : n === 3 ? 'rd' : 'th') + ' Year'; }
    function connRow(id, name, locked) {
      return '<tr><td>' + name + '</td><td>' + (conn[id] ? 'Connected' : 'Not connected') + '</td>' +
        '<td><label><input type="checkbox" data-conn="' + id + '"' + (conn[id] ? ' checked' : '') + (locked ? ' disabled' : '') + '> ' +
        (locked ? 'School account' : 'Connect') + '</label></td></tr>';
    }
    function faq(q, a) { return '<details><summary>' + q + '</summary><p>' + a + '</p></details>'; }

    app.innerHTML =
      '<h2>My Account</h2>' +
      '<h3>Profile Information</h3>' +
      '<table border="1" cellpadding="8">' +
        '<tr><th>Full Name</th><td>' + esc(fullName) + '</td></tr>' +
        '<tr><th>School Email</th><td>' + esc(USER.email || '-') + '</td></tr>' +
        '<tr><th>Department</th><td>' + esc(USER.department || '-') + '</td></tr>' +
        '<tr><th>Year Level</th><td>' + esc(yr(USER.year)) + '</td></tr>' +
        '<tr><th>User Type</th><td>' + esc(USER.type || 'Student') + '</td></tr>' +
        '<tr><th>Customer ID</th><td>' + esc(USER.id) + '</td></tr>' +
        '<tr><th>Reviews Posted</th><td>' + myReviews + '</td></tr>' +
      '</table>' +

      '<h3>Connected Accounts</h3>' +
      '<table border="1" cellpadding="8"><tr><th>Account</th><th>Status</th><th></th></tr>' +
        connRow('google', 'UB Google Workspace (' + esc(USER.email || 'school email') + ')', true) +
        connRow('microsoft', 'Microsoft 365') +
        connRow('facebook', 'Facebook') +
      '</table>' +

      '<h3>Help Center</h3>' +
      faq('How do I order food?', 'You cannot order online. UB FoodStall only shows the menu. Check what is available, then go to the stall to buy.') +
      faq('Why is a stall marked "Closed"?', 'Either it is outside the stall\'s opening hours, or the vendor closed it for the day. The stall shows when it opens next.') +
      faq('What does "Out of Stock" mean?', 'The vendor has 0 left of that product. "Not Available" means the vendor is not selling it today.') +
      faq('How are stall ratings computed?', 'A stall\'s star rating is the average of all product reviews posted for that stall.') +
      faq('How do I write a review?', 'Open a stall, click "View / Rate" on a product, choose 1 to 5 stars, add a comment, then click Post Review.') +
      faq('I found wrong prices or info.', 'Tell the vendor at the stall, or contact the UB FoodStall admin.') +

      '<h3>Terms and Policies</h3>' +
      '<ul>' +
        '<li><a href="#/policy/terms">Terms of Use</a></li>' +
        '<li><a href="#/policy/privacy">Privacy Policy</a></li>' +
        '<li><a href="#/policy/review">Review Guidelines</a></li>' +
      '</ul>' +
      '<p><a href="logout.php">Logout</a></p>';

    app.onchange = function (e) {
      var inp = e.target.closest('[data-conn]'); if (!inp) return;
      var c = getConn(); c[inp.getAttribute('data-conn')] = inp.checked;
      try { localStorage.setItem(CONN_KEY, JSON.stringify(c)); } catch (er) {}
      viewAccount();
      msg(inp.checked ? 'Account connected.' : 'Account disconnected.');
    };
  }

  function viewPolicy(r) {
    var p = POLICY[r.arg];
    if (!p) { app.innerHTML = '<p>Page not found. <a href="#/account">Back to Account</a></p>'; return; }
    app.innerHTML = '<p><a href="#/account">&larr; Back to Account</a></p><h2>' + p.title + '</h2>' + p.body +
      '<p><small>Last updated November 2026</small></p>';
  }

  // reflect vendor edits made in another tab
  window.addEventListener('storage', function (e) { if (e.key === 'ubfs_frontend_v1') location.reload(); });

  if (!location.hash) history.replaceState(null, '', '#/stalls');
  render();
})();
