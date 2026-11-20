/* UB FoodStall — vendor pages (plain HTML, no styling) */
(function () {
  'use strict';
  var S = window.UBFS;
  var V = window.UBFS_VENDOR || { id: 0, first: 'Vendor', last: '' };
  var esc = S.esc, peso = S.peso;
  var app = document.getElementById('app');
  var msgBox = document.getElementById('msg');
  var stall = S.ensureVendorStall(V.id, V.stall);
  var DAY_NAMES = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

  function msg(text) {
    msgBox.textContent = text;
    clearTimeout(msg._t); msg._t = setTimeout(function () { msgBox.textContent = ''; }, 4000);
  }
  function statusText(info) { return '<b>' + (info.open ? 'OPEN' : 'CLOSED') + '</b> (' + esc(info.detail) + ')'; }
  function stockText(p) {
    if (p.status !== 'Available') return 'Not Available';
    if (p.qty <= 0) return 'Out of Stock';
    if (p.qty <= 5) return 'Low Stock';
    return 'In Stock';
  }
  function ratingText(r) { return r.count ? S.stars(r.avg) + ' ' + r.avg.toFixed(1) + ' (' + r.count + ')' : 'No reviews'; }
  function isLow(p) { return p.status === 'Available' && p.qty > 0 && p.qty <= 5; }
  function fmtDate(iso) { return new Date(iso + 'T00:00:00').toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }); }

  function route() {
    var h = location.hash.replace(/^#\/?/, '').split('?')[0].split('/');
    return { name: h[0] || 'dashboard', arg: h[1] };
  }
  function render() {
    app.onclick = null; app.onchange = null; app.onsubmit = null; app.oninput = null;
    stall = S.stall(stall.id);
    var r = route();
    ({ dashboard: viewDashboard, stall: viewStall, products: viewProducts, product: viewProductForm, reviews: viewReviews }[r.name] || viewDashboard)(r);
  }
  window.addEventListener('hashchange', function () { render(); window.scrollTo(0, 0); });

  /* ---------- shared: open/close + quantity ---------- */
  function openSwitch() {
    return '<p><label><input type="checkbox" id="openSwitch"' + (stall.status === 'Open' ? ' checked' : '') + '> ' +
      '<b>Stall is open today</b></label> (uncheck to show your stall as CLOSED to students)</p>';
  }
  function qtyCtl(p) {
    return '<button type="button" data-qty="' + p.id + '" data-d="-1">-</button> ' +
      '<input type="number" min="0" size="4" value="' + p.qty + '" data-qty-input="' + p.id + '"> ' +
      '<button type="button" data-qty="' + p.id + '" data-d="1">+</button>';
  }
  // handles the open switch and +/- quantity on any vendor page
  function bindCommon(rerender) {
    var handleClick = function (e) {
      var b = e.target.closest('[data-qty]'); if (!b) return false;
      var p = S.product(b.getAttribute('data-qty'));
      S.saveProduct({ id: p.id, qty: Math.max(0, p.qty + (+b.getAttribute('data-d'))) });
      rerender(); msg('Quantity updated.');
      return true;
    };
    var handleChange = function (e) {
      if (e.target.id === 'openSwitch') {
        S.updateStall(stall.id, { status: e.target.checked ? 'Open' : 'Closed' });
        rerender(); msg(e.target.checked ? 'Your stall is now OPEN.' : 'Your stall is now CLOSED.');
        return true;
      }
      var q = e.target.getAttribute('data-qty-input');
      if (q) {
        S.saveProduct({ id: +q, qty: Math.max(0, parseInt(e.target.value, 10) || 0) });
        rerender(); msg('Quantity updated.');
        return true;
      }
      return false;
    };
    // note: handlers must not return false, or the browser cancels checkbox clicks
    app.onclick = function (e) { handleClick(e); };
    app.onchange = function (e) { handleChange(e); };
    bindCommon.click = handleClick; bindCommon.change = handleChange;
  }

  /* ================= DASHBOARD ================= */
  function viewDashboard() {
    stall = S.stall(stall.id);
    var prods = S.products(stall.id), avail = prods.filter(S.isAvailable);
    var attention = prods.filter(function (p) { return isLow(p) || !S.isAvailable(p); });
    var info = S.openInfo(stall), reviews = S.reviewsForStall(stall.id);

    app.innerHTML =
      '<h2>Dashboard</h2>' +
      '<p>Stall #' + S.pad(stall.number) + ' - ' + esc(stall.name) + ' - ' + esc(stall.location) + '</p>' +
      openSwitch() +
      '<table border="1" cellpadding="8">' +
        '<tr><th>Status</th><td>' + statusText(info) + '</td></tr>' +
        '<tr><th>Opening Hours</th><td>' + esc(info.hours) + ', ' + esc(S.fmtDays(stall.days)) + '</td></tr>' +
        '<tr><th>Items Available</th><td>' + avail.length + ' of ' + prods.length + '</td></tr>' +
        '<tr><th>Need Restock</th><td>' + attention.length + '</td></tr>' +
        '<tr><th>Rating</th><td>' + ratingText(S.stallRating(stall.id)) + '</td></tr>' +
      '</table>' +

      '<h3>Low Stock / Out of Stock</h3>' +
      (attention.length ?
        '<table border="1" cellpadding="8"><tr><th>Product</th><th>Status</th><th>Quantity Left</th></tr>' +
        attention.map(function (p) {
          return '<tr><td>' + esc(p.name) + '</td><td>' + stockText(p) + '</td><td>' + qtyCtl(p) + '</td></tr>';
        }).join('') + '</table>'
        : '<p>All products are stocked.</p>') +

      '<h3>Latest Reviews</h3>' + reviewList(reviews.slice(0, 5)) +
      '<p><a href="#/reviews">See all reviews</a></p>';

    bindCommon(viewDashboard);
  }

  /* ================= MY STALL ================= */
  function viewStall() {
    app.innerHTML =
      '<h2>My Stall</h2>' +
      openSwitch() +
      '<form id="stallForm"><table>' +
        '<tr><td>Stall Name:</td><td>' + esc(stall.name) + '</td></tr>' +
        '<tr><td>Stall No.:</td><td>' + S.pad(stall.number) + '</td></tr>' +
        '<tr><td>Location:</td><td>' + esc(stall.location) + '</td></tr>' +
        '<tr><td colspan="2"><small>Stall name and location are managed by the admin.</small></td></tr>' +
        '<tr><td><label for="tagline">Short description:</label></td><td><input id="tagline" size="40" maxlength="60" value="' + esc(stall.tagline) + '"></td></tr>' +
        '<tr><td><label for="opens">Opens at:</label></td><td><input type="time" id="opens" value="' + esc(stall.opens) + '" required></td></tr>' +
        '<tr><td><label for="closes">Closes at:</label></td><td><input type="time" id="closes" value="' + esc(stall.closes) + '" required></td></tr>' +
        '<tr><td>Open on:</td><td>' + DAY_NAMES.map(function (d, i) {
          return '<label><input type="checkbox" name="day" value="' + i + '"' + (stall.days.indexOf(i) !== -1 ? ' checked' : '') + '> ' + d + '</label>';
        }).join(' ') + '</td></tr>' +
        '<tr><td></td><td><button type="submit">Save Changes</button></td></tr>' +
      '</table></form>';

    bindCommon(viewStall);
    app.onsubmit = function (e) {
      e.preventDefault();
      var o = app.querySelector('#opens').value, c = app.querySelector('#closes').value;
      var days = [].slice.call(app.querySelectorAll('[name=day]:checked')).map(function (x) { return +x.value; });
      if (o >= c) { msg('Closing time must be after opening time.'); return; }
      if (!days.length) { msg('Pick at least one day.'); return; }
      S.updateStall(stall.id, { tagline: app.querySelector('#tagline').value.trim(), opens: o, closes: c, days: days });
      render(); msg('Stall details saved.');
    };
  }

  /* ================= PRODUCTS ================= */
  var pFilter = 'all';
  function viewProducts() {
    var prods = S.products(stall.id);
    var list = prods.filter(function (p) {
      if (pFilter === 'available') return S.isAvailable(p);
      if (pFilter === 'low') return isLow(p);
      if (pFilter === 'out') return !S.isAvailable(p);
      return true;
    });
    function opt(v, l) { return '<option value="' + v + '"' + (pFilter === v ? ' selected' : '') + '>' + l + '</option>'; }

    app.innerHTML =
      '<h2>Products</h2>' +
      '<p><a href="#/product/new">Add Product</a></p>' +
      '<p>Show: <select id="pFilter">' + opt('all', 'All') + opt('available', 'Available') + opt('low', 'Low stock') + opt('out', 'Out of stock / Not available') + '</select></p>' +
      (list.length ?
        '<table border="1" cellpadding="8">' +
          '<tr><th>Product</th><th>Category</th><th>Price</th><th>Quantity Left</th><th>Available</th><th>Status</th><th>Rating</th><th>Actions</th></tr>' +
          list.map(function (p) {
            return '<tr><td>' + esc(p.name) + '</td>' +
              '<td>' + esc(S.category(p.category).name) + '</td>' +
              '<td>' + peso(p.price) + '</td>' +
              '<td>' + qtyCtl(p) + '</td>' +
              '<td><input type="checkbox" data-avail="' + p.id + '"' + (p.status === 'Available' ? ' checked' : '') + '></td>' +
              '<td>' + stockText(p) + '</td>' +
              '<td>' + ratingText(S.productRating(p.id)) + '</td>' +
              '<td><a href="#/product/' + p.id + '">Update</a> | <a href="#" data-del="' + p.id + '">Delete</a></td></tr>';
          }).join('') + '</table>'
        : '<p>No products to show.</p>');

    bindCommon(viewProducts);
    var commonClick = bindCommon.click, commonChange = bindCommon.change;
    app.onclick = function (e) {
      var d = e.target.closest('[data-del]');
      if (d) {
        e.preventDefault();
        var p = S.product(d.getAttribute('data-del'));
        if (confirm('Delete "' + p.name + '"? Its reviews will also be removed.')) { S.deleteProduct(p.id); viewProducts(); msg('Product deleted.'); }
        return;
      }
      commonClick(e);
    };
    app.onchange = function (e) {
      if (e.target.id === 'pFilter') { pFilter = e.target.value; viewProducts(); return; }
      var a = e.target.getAttribute('data-avail');
      if (a) {
        S.saveProduct({ id: +a, status: e.target.checked ? 'Available' : 'Not Available' });
        viewProducts(); msg(e.target.checked ? 'Marked as available.' : 'Marked as not available.');
        return;
      }
      commonChange(e);
    };
  }

  function viewProductForm(r) {
    var isNew = r.arg === 'new';
    var p = isNew ? { name: '', desc: '', price: '', qty: 10, category: 'rice', status: 'Available' } : S.product(r.arg);
    if (!p || (!isNew && p.stallId !== stall.id)) { app.innerHTML = '<p>Product not found. <a href="#/products">Back</a></p>'; return; }

    app.innerHTML =
      '<p><a href="#/products">&larr; Back to Products</a></p>' +
      '<h2>' + (isNew ? 'Add Product' : 'Update Product') + '</h2>' +
      '<form id="pForm"><table>' +
        '<tr><td><label for="fName">Product Name:</label></td><td><input id="fName" required maxlength="100" value="' + esc(p.name) + '"></td></tr>' +
        '<tr><td><label for="fDesc">Description:</label></td><td><input id="fDesc" maxlength="80" value="' + esc(p.desc) + '"></td></tr>' +
        '<tr><td><label for="fPrice">Price (₱):</label></td><td><input id="fPrice" type="number" min="1" step="0.01" required value="' + esc(p.price) + '"></td></tr>' +
        '<tr><td><label for="fQty">Quantity:</label></td><td><input id="fQty" type="number" min="0" step="1" required value="' + esc(p.qty) + '"></td></tr>' +
        '<tr><td><label for="fCat">Category:</label></td><td><select id="fCat">' + S.categories.map(function (c) {
          return '<option value="' + c.id + '"' + (c.id === p.category ? ' selected' : '') + '>' + esc(c.name) + '</option>';
        }).join('') + '</select></td></tr>' +
        '<tr><td><label for="fStatus">Status:</label></td><td><select id="fStatus">' +
          '<option' + (p.status === 'Available' ? ' selected' : '') + '>Available</option>' +
          '<option' + (p.status !== 'Available' ? ' selected' : '') + '>Not Available</option></select></td></tr>' +
        '<tr><td><a href="#/products">Cancel</a></td><td><button type="submit">' + (isNew ? 'Add' : 'Update') + '</button></td></tr>' +
      '</table></form>';

    app.onsubmit = function (e) {
      e.preventDefault();
      var data = {
        stallId: stall.id,
        name: app.querySelector('#fName').value.trim(),
        desc: app.querySelector('#fDesc').value.trim(),
        price: Math.round(parseFloat(app.querySelector('#fPrice').value) * 100) / 100,
        qty: Math.max(0, parseInt(app.querySelector('#fQty').value, 10) || 0),
        category: app.querySelector('#fCat').value,
        status: app.querySelector('#fStatus').value
      };
      if (!data.name || !(data.price > 0)) { msg('Enter a product name and a valid price.'); return; }
      if (isNew) data.emoji = '🍽️'; else data.id = p.id;
      S.saveProduct(data);
      location.hash = '#/products';
      msg(isNew ? 'Product added.' : 'Product updated.');
    };
  }

  /* ================= REVIEWS ================= */
  var rProduct = 'all', rStars = 0;
  function reviewList(list) {
    if (!list.length) return '<p>No reviews yet.</p>';
    return '<ul>' + list.map(function (rv) {
      var p = S.product(rv.productId);
      return '<li><b>' + esc(rv.customerName) + '</b> - ' + S.stars(rv.rating) + ' (' + rv.rating + '/5) - ' + fmtDate(rv.date) +
        (p ? ' - on ' + esc(p.name) : '') + (rv.comment ? '<br>' + esc(rv.comment) : '') + '</li>';
    }).join('') + '</ul>';
  }
  function viewReviews() {
    var all = S.reviewsForStall(stall.id), prods = S.products(stall.id);
    var list = all.filter(function (r) { return (rProduct === 'all' || r.productId === +rProduct) && (!rStars || r.rating === rStars); });
    var counts = [0, 0, 0, 0, 0, 0]; all.forEach(function (r) { counts[r.rating]++; });

    app.innerHTML =
      '<h2>Reviews</h2>' +
      '<p>Overall rating: ' + ratingText(S.stallRating(stall.id)) + '</p>' +
      '<table border="1" cellpadding="4"><tr><th>Rating</th><th>Count</th></tr>' +
        [5, 4, 3, 2, 1].map(function (i) { return '<tr><td>' + i + ' star' + (i > 1 ? 's' : '') + '</td><td>' + counts[i] + '</td></tr>'; }).join('') +
      '</table>' +

      '<h3>Rating per Product</h3>' +
      '<table border="1" cellpadding="8"><tr><th>Product</th><th>Rating</th></tr>' +
        prods.map(function (p) { return '<tr><td>' + esc(p.name) + '</td><td>' + ratingText(S.productRating(p.id)) + '</td></tr>'; }).join('') +
      '</table>' +

      '<h3>All Reviews</h3>' +
      '<p>Product: <select id="rProd"><option value="all">All products</option>' +
        prods.map(function (p) { return '<option value="' + p.id + '"' + (String(p.id) === String(rProduct) ? ' selected' : '') + '>' + esc(p.name) + '</option>'; }).join('') +
      '</select> Stars: <select id="rStars"><option value="0">All</option>' +
        [5, 4, 3, 2, 1].map(function (i) { return '<option value="' + i + '"' + (rStars === i ? ' selected' : '') + '>' + i + ' star' + (i > 1 ? 's' : '') + '</option>'; }).join('') +
      '</select></p>' +
      reviewList(list);

    app.onchange = function (e) {
      if (e.target.id === 'rProd') rProduct = e.target.value;
      else if (e.target.id === 'rStars') rStars = +e.target.value;
      else return;
      viewReviews();
    };
  }

  // reflect new student reviews from another tab
  window.addEventListener('storage', function (e) { if (e.key === 'ubfs_frontend_v1') location.reload(); });

  if (!location.hash) history.replaceState(null, '', '#/dashboard');
  render();
})();
