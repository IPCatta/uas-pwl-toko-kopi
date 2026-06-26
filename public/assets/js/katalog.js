/* katalog.js — AJAX filter & live search untuk halaman katalog */
/* Butuh window.BASE_URL diset di view sebelum file ini di-load */

(function () {
  'use strict';

  const grid      = document.getElementById('product-grid');
  const searchEl  = document.getElementById('search-q');
  const pills     = document.querySelectorAll('.filter-pill[data-kategori]');

  if (!grid) return;

  let currentKategori = parseInt(document.querySelector('.filter-pill.active')?.dataset?.kategori ?? 0) || 0;
  let debounceTimer   = null;

  /* -------- helpers ---------------------------------------- */

  function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
  }

  function formatRp(n) {
    return Number(n).toLocaleString('id-ID');
  }

  function renderProducts(list) {
    if (!list || list.length === 0) {
      grid.innerHTML = '<div class="empty-state"><h3>Produk tidak ditemukan</h3><p>Coba kata kunci atau kategori lain.</p></div>';
      return;
    }

    grid.innerHTML = list.map(function (p) {
      var imgHtml = p.foto_thumb
        ? '<img src="' + escHtml(window.BASE_URL + p.foto_thumb) + '" alt="' + escHtml(p.nama) + '" loading="lazy">'
        : '<div class="product-card__placeholder">Belum ada foto</div>';

      var soldOut = (parseInt(p.stok) === 0)
        ? '<span class="product-card__sold-out">Stok Habis</span>'
        : '';

      return '<a href="' + window.BASE_URL + '/produk/' + escHtml(String(p.id)) + '" class="product-card">'
        + '<div class="product-card__img">' + imgHtml + '</div>'
        + '<div class="product-card__body">'
        +   '<div class="product-card__name">' + escHtml(p.nama) + '</div>'
        +   '<div class="product-card__price">Rp ' + formatRp(p.harga) + '</div>'
        +   soldOut
        + '</div>'
        + '</a>';
    }).join('');
  }

  function fetchProducts(q, kategoriId) {
    var params = new URLSearchParams();
    if (q)          params.set('q',       q);
    if (kategoriId) params.set('kategori', kategoriId);

    fetch(window.BASE_URL + '/api/produk?' + params.toString())
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success) renderProducts(data.data);
      })
      .catch(function () {
        /* silent — SSR content remains */
      });
  }

  /* -------- search debounce -------------------------------- */

  if (searchEl) {
    searchEl.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function () {
        fetchProducts(searchEl.value.trim(), currentKategori);
      }, 320);
    });
  }

  /* -------- category filter pills -------------------------- */

  pills.forEach(function (pill) {
    pill.addEventListener('click', function (e) {
      e.preventDefault();
      pills.forEach(function (p) { p.classList.remove('active'); });
      pill.classList.add('active');
      currentKategori = parseInt(pill.dataset.kategori) || 0;
      fetchProducts(searchEl ? searchEl.value.trim() : '', currentKategori);
    });
  });

  /* -------- search form submit (no page reload) ------------ */

  var searchForm = document.getElementById('search-form');
  if (searchForm) {
    searchForm.addEventListener('submit', function (e) {
      e.preventDefault();
      if (searchEl) fetchProducts(searchEl.value.trim(), currentKategori);
    });
  }

}());
