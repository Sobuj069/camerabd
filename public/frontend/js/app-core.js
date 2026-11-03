    // ============================================
    // UNIVERSAL CAMERA CART ENGINE (LocalStorage)
    // ============================================
    window.CameraCart = {
      key: 'cb_cart',
      get: function() {
        try {
          const data = localStorage.getItem(this.key);
          return data ? JSON.parse(data) : [];
        } catch(e) {
          return [];
        }
      },
      save: function(items) {
        try {
          localStorage.setItem(this.key, JSON.stringify(items));
        } catch(e) {}
        this.sync();
      },
      add: function(item, openDrawer = true) {
        let items = this.get();
        const targetName = String(item.name || '').trim().toLowerCase();
        const targetId = item.id ? String(item.id) : null;
        
        const idx = items.findIndex(i => {
          if (targetId && i.id && String(i.id) === targetId) return true;
          return String(i.name || '').trim().toLowerCase() === targetName;
        });

        const qtyToAdd = parseInt(item.qty) || 1;

        if (idx > -1) {
          items[idx].qty = (parseInt(items[idx].qty) || 1) + qtyToAdd;
        } else {
          items.push({
            id: item.id || null,
            name: item.name || 'Cinema Product',
            price: parseFloat(item.price) || 0,
            image: item.image || '',
            barcode: item.barcode || item.sku || '',
            url: item.url || '#',
            qty: qtyToAdd
          });
        }
        this.save(items);
        showToast(`Added "${item.name}" to bag!`, 'success');

        // GA4 / GTM DataLayer AddToCart Event
        if (typeof window.pushToDataLayer === 'function') {
          window.pushToDataLayer('add_to_cart', {
            currency: 'BDT',
            value: (parseFloat(item.price) || 0) * (parseInt(qtyToAdd) || 1),
            items: [{
              item_id: String(item.id || item.barcode || item.sku || item.name),
              item_name: item.name || 'Cinema Product',
              affiliation: (window.__APP_CONFIG && window.__APP_CONFIG.siteName) || "CAMERA BD",
              currency: 'BDT',
              price: parseFloat(item.price) || 0,
              quantity: parseInt(qtyToAdd) || 1
            }]
          });
        }

        // Meta Pixel AddToCart Event
        try {
          if (!window._hasGtm && typeof fbq === 'function') {
            fbq('track', 'AddToCart', {
              content_name: item.name || 'Cinema Product',
              content_ids: item.id ? [String(item.id)] : [String(item.barcode || item.name)],
              content_type: 'product',
              value: (parseFloat(item.price) || 0) * (parseInt(qtyToAdd) || 1),
              currency: 'BDT'
            });
          }
        } catch(e) {
          console.warn('Meta Pixel AddToCart error:', e);
        }

        if (openDrawer) {
          this.openDrawer();
        }
      },
      updateQty: function(index, delta) {
        let items = this.get();
        if (items[index]) {
          const newQty = (parseInt(items[index].qty) || 1) + delta;
          if (newQty <= 0) {
            this.remove(index);
          } else {
            items[index].qty = newQty;
            this.save(items);
          }
        }
      },
      setQty: function(index, qty) {
        let items = this.get();
        if (items[index]) {
          const newQty = parseInt(qty) || 1;
          if (newQty <= 0) {
            this.remove(index);
          } else {
            items[index].qty = newQty;
            this.save(items);
          }
        }
      },
      remove: function(index) {
        let items = this.get();
        if (items[index]) {
          const removedItem = items[index];
          const removedName = removedItem.name;

          // GA4 / GTM DataLayer RemoveFromCart Event
          if (typeof window.pushToDataLayer === 'function') {
            window.pushToDataLayer('remove_from_cart', {
              currency: 'BDT',
              value: (parseFloat(removedItem.price) || 0) * (parseInt(removedItem.qty) || 1),
              items: [{
                item_id: String(removedItem.id || removedItem.barcode || removedItem.sku || removedItem.name),
                item_name: removedItem.name || 'Cinema Product',
                affiliation: (window.__APP_CONFIG && window.__APP_CONFIG.siteName) || "CAMERA BD",
                currency: 'BDT',
                price: parseFloat(removedItem.price) || 0,
                quantity: parseInt(removedItem.qty) || 1
              }]
            });
          }
          items.splice(index, 1);
          this.save(items);
          showToast(`Removed "${removedName}" from cart`, 'info');
        }
      },
      clear: function() {
        this.save([]);
        showToast('Cart cleared', 'info');
      },
      clearState: function() {
        this.sync();
      },
      getCount: function() {
        const items = this.get();
        return items.reduce((sum, i) => sum + (parseInt(i.qty) || 1), 0);
      },
      getSubtotal: function() {
        const items = this.get();
        return items.reduce((sum, i) => sum + ((parseFloat(i.price) || 0) * (parseInt(i.qty) || 1)), 0);
      },
      sync: function() {
        const count = this.getCount();
        const subtotal = this.getSubtotal();

        // Update counters
        document.querySelectorAll('#top-cart-count, #desktop-cart-badge, #mobile-cart-badge, #cart-drawer-count').forEach(el => {
          if (el) el.textContent = count;
        });

        const subtotalFormatted = '৳ ' + Math.round(subtotal).toLocaleString('en-US') + ' TK';
        document.querySelectorAll('#cart-drawer-subtotal, #desktop-cart-total').forEach(el => {
          if (el) el.textContent = subtotalFormatted;
        });

        this.renderDrawer();
      },
      openDrawer: function() {
        const drawer = document.getElementById('cart-drawer');
        const overlay = document.getElementById('cart-drawer-overlay');
        if (drawer && overlay) {
          drawer.classList.remove('translate-x-full');
          overlay.classList.remove('hidden');
        }
      },
      closeDrawer: function() {
        const drawer = document.getElementById('cart-drawer');
        const overlay = document.getElementById('cart-drawer-overlay');
        if (drawer && overlay) {
          drawer.classList.add('translate-x-full');
          overlay.classList.add('hidden');
        }
      },
      renderDrawer: function() {
        const container = document.getElementById('cart-drawer-items');
        if (!container) return;

        const items = this.get();
        if (items.length === 0) {
          container.innerHTML = `
            <div class="py-16 text-center text-text-muted text-xs">
              <span class="material-symbols-outlined text-[42px] text-slate-300 block mb-2">shopping_bag</span>
              <p class="font-bold text-slate-700">Your bag is currently empty</p>
              <p class="text-[11px] text-slate-400 mt-1">Browse our collection and add camera gear!</p>
            </div>
          `;
          return;
        }

        let html = '';
        items.forEach((item, idx) => {
          const imgHtml = item.image 
            ? `<img src="${item.image}" alt="${item.name}" class="w-full h-full object-contain" />`
            : `<span class="material-symbols-outlined text-primary text-[20px]">videocam</span>`;

          html += `
            <div class="flex items-center gap-3 p-3 bg-surface-base rounded-2xl border border-surface-border">
              <div class="w-14 h-14 bg-white rounded-xl p-1 shrink-0 border border-surface-border flex items-center justify-center">
                ${imgHtml}
              </div>
              <div class="flex-1 min-w-0">
                <h4 class="font-bold text-xs text-text-display truncate">${item.name}</h4>
                <div class="text-xs font-bold text-primary mt-0.5">৳ ${Math.round(parseFloat(item.price) || 0).toLocaleString('en-US')} TK</div>
                <div class="flex items-center justify-between mt-1 text-[11px]">
                  <div class="flex items-center border border-surface-border bg-surface-container rounded-md p-0.5">
                    <button onclick="CameraCart.updateQty(${idx}, -1)" class="w-5 h-5 rounded bg-surface-card flex items-center justify-center font-bold hover:bg-primary hover:text-white transition-colors">-</button>
                    <span class="w-6 text-center font-bold">${item.qty || 1}</span>
                    <button onclick="CameraCart.updateQty(${idx}, 1)" class="w-5 h-5 rounded bg-surface-card flex items-center justify-center font-bold hover:bg-primary hover:text-white transition-colors">+</button>
                  </div>
                  <button onclick="CameraCart.remove(${idx})" class="text-rec-red-base hover:underline font-bold text-[10px]">Remove</button>
                </div>
              </div>
            </div>
          `;
        });
        container.innerHTML = html;
      }
    };

    // CameraWishlist Manager
    window.CameraWishlist = {
      key: 'camerawishlist_v1',
      get: function() {
        try {
          const data = localStorage.getItem(this.key);
          return data ? JSON.parse(data) : [];
        } catch (e) {
          return [];
        }
      },
      save: function(items) {
        try {
          localStorage.setItem(this.key, JSON.stringify(items));
        } catch (e) {}
        this.sync();
      },
      has: function(idOrName) {
        const items = this.get();
        return items.some(i => (i.id && i.id == idOrName) || (i.name && i.name.toLowerCase() === String(idOrName).toLowerCase()));
      },
      add: function(item) {
        let items = this.get();
        const exists = items.some(i => (item.id && i.id == item.id) || (i.name && i.name.toLowerCase() === item.name.toLowerCase()));
        if (exists) {
          showToast(`"${item.name}" is already in your Wishlist!`, 'info');
          return false;
        }
        items.unshift({
          id: item.id || null,
          name: item.name || 'Camera Product',
          price: parseFloat(item.price) || 0,
          old_price: parseFloat(item.old_price) || 0,
          image: item.image || '',
          url: item.url || (item.slug ? `/product-details/${item.slug}` : (item.id ? `/product-details/${item.id}` : '#')),
          slug: item.slug || item.id || '',
          brand: item.brand || 'CAMERA BD',
          in_stock: item.in_stock !== false,
          reviews_count: item.reviews_count || 12
        });
        this.save(items);
        showToast(`"${item.name}" added to Wishlist!`, 'success');

        // GA4 / GTM DataLayer AddToWishlist Event
        if (typeof window.pushToDataLayer === 'function') {
          window.pushToDataLayer('add_to_wishlist', {
            currency: 'BDT',
            value: parseFloat(item.price) || 0,
            items: [{
              item_id: String(item.id || item.slug || item.name),
              item_name: item.name || 'Camera Product',
              affiliation: (window.__APP_CONFIG && window.__APP_CONFIG.siteName) || "CAMERA BD",
              currency: 'BDT',
              price: parseFloat(item.price) || 0,
              quantity: 1
            }]
          });
        }
        return true;
      },
      remove: function(identifier) {
        let items = this.get();
        let removedName = '';
        if (typeof identifier === 'number' && identifier < items.length) {
          removedName = items[identifier]?.name || '';
          items.splice(identifier, 1);
        } else {
          const idx = items.findIndex(i => (i.id && i.id == identifier) || (i.name && i.name.toLowerCase() === String(identifier).toLowerCase()));
          if (idx !== -1) {
            removedName = items[idx]?.name || '';
            items.splice(idx, 1);
          }
        }
        this.save(items);
        if (removedName) {
          showToast(`Removed "${removedName}" from Wishlist`, 'info');
        }
      },
      toggle: function(item, btnElement = null) {
        const name = typeof item === 'object' ? item.name : item;
        const isPresent = this.has(item.id || name);
        if (isPresent) {
          this.remove(item.id || name);
          if (btnElement) {
            const icon = btnElement.querySelector('.material-symbols-outlined') || btnElement;
            icon.textContent = 'favorite_border';
            btnElement.classList.remove('text-red-500', 'text-primary');
            btnElement.classList.add('text-text-muted', 'text-gray-300');
          }
        } else {
          const payload = typeof item === 'object' ? item : { name: item };
          this.add(payload);
          if (btnElement) {
            const icon = btnElement.querySelector('.material-symbols-outlined') || btnElement;
            icon.textContent = 'favorite';
            btnElement.classList.add('text-red-500');
            btnElement.classList.remove('text-text-muted', 'text-gray-300');
          }
        }
        this.sync();
      },
      clear: function() {
        this.save([]);
        showToast('Wishlist cleared', 'info');
      },
      addAllToCart: function() {
        const items = this.get();
        if (items.length === 0) {
          showToast('Your wishlist is empty!', 'error');
          return;
        }
        if (window.CameraCart) {
          items.forEach(i => {
            CameraCart.add({
              id: i.id,
              name: i.name,
              price: i.price,
              image: i.image,
              barcode: i.sku || i.barcode || '',
              url: i.url || '#',
              qty: 1
            }, false);
          });
          CameraCart.sync();
          CameraCart.openDrawer();
          showToast(`All ${items.length} items added to Cart!`, 'success');
        }
      },
      getCount: function() {
        return this.get().length;
      },
      sync: function() {
        const count = this.getCount();
        document.querySelectorAll('#top-wishlist-count, #desktop-wishlist-count, #mobile-wishlist-count, #mobile-wishlist-badge, #wishlist-header-count, #wishlist-page-count').forEach(el => {
          if (el) el.textContent = count;
        });

        // Highlight active wishlist items
        document.querySelectorAll('[data-wishlist-product], [data-wishlist-id], .wishlist-btn').forEach(btn => {
          const prodName = btn.getAttribute('data-wishlist-product');
          const prodId = btn.getAttribute('data-wishlist-id');
          if (prodName || prodId) {
            const icon = btn.querySelector('.material-symbols-outlined') || btn;
            if (this.has(prodId || prodName)) {
              icon.textContent = 'favorite';
              btn.classList.add('text-red-500');
              btn.classList.remove('text-text-muted', 'text-gray-300');
            } else {
              icon.textContent = 'favorite_border';
              btn.classList.remove('text-red-500');
            }
          }
        });

        window.dispatchEvent(new CustomEvent('camerawishlist:updated', {
          detail: { items: this.get(), count: count }
        }));
      }
    };

    function toggleCartDrawer() {
      const drawer = document.getElementById('cart-drawer');
      if (drawer && drawer.classList.contains('translate-x-full')) {
        CameraCart.openDrawer();
      } else {
        CameraCart.closeDrawer();
      }
    }

    function addToCart(productName, price, imageUrl, id = null, barcode = '', url = '#') {
      CameraCart.add({ id: id, name: productName, price: price, image: imageUrl, barcode: barcode, url: url, qty: 1 }, true);
    }

    function buyNow(productName, price, imageUrl, id = null, barcode = '', url = '#') {
      CameraCart.add({ id: id, name: productName, price: price, image: imageUrl, barcode: barcode, url: url, qty: 1 }, false);
      window.location.href = (window.__APP_CONFIG && window.__APP_CONFIG.checkoutUrl) || "/checkout";
    }

    function toggleWishlist(btn, itemOrName, price = 0, image = '', slug = '', id = null, brand = '') {
      let payload = {};
      if (typeof itemOrName === 'object' && itemOrName !== null) {
        payload = itemOrName;
      } else {
        payload = {
          id: id,
          name: itemOrName,
          price: price,
          image: image,
          slug: slug,
          url: slug ? `/product-details/${slug}` : (id ? `/product-details/${id}` : '#'),
          brand: brand
        };
      }
      CameraWishlist.toggle(payload, btn);
    }

    function removeCartItem(btn, price, idx = null) {
      if (idx !== null) {
        CameraCart.remove(idx);
      } else {
        CameraCart.sync();
      }
    }

    // Initialize cart & wishlist on load
    function initCartWishlist() {
      if (window.requestIdleCallback) {
        requestIdleCallback(function() {
          CameraCart.sync();
          CameraWishlist.sync();
        });
      } else {
        setTimeout(function() {
          CameraCart.sync();
          CameraWishlist.sync();
        }, 100);
      }
    }
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initCartWishlist);
    } else {
      initCartWishlist();
    }

    // Mobile Search Modal Handlers
    const searchModal = document.getElementById('mobile-search-modal');
    const searchBtn = document.getElementById('mobile-search-btn');
    const searchClose = document.getElementById('mobile-search-close');

    if (searchBtn && searchModal) {
      searchBtn.addEventListener('click', () => {
        searchModal.classList.remove('hidden');
        searchModal.classList.add('flex');
        const input = document.getElementById('mobile-search-input');
        if (input) {
          setTimeout(() => input.focus(), 50);
        }
      });
    }

    function closeMobileSearch() {
      if (searchModal) {
        searchModal.classList.add('hidden');
        searchModal.classList.remove('flex');
      }
    }

    if (searchClose) {
      searchClose.addEventListener('click', closeMobileSearch);
    }

    // Live AJAX Product Search Engine
    (function initLiveSearchEngine() {
      const liveSearchUrl = (window.__APP_CONFIG && window.__APP_CONFIG.searchLiveUrl) || "/search/live";
      
      // Desktop elements
      const desktopInput = document.getElementById('desktop-search-input');
      const desktopCategory = document.getElementById('desktop-search-category');
      const desktopResults = document.getElementById('desktop-search-results');
      const desktopClear = document.getElementById('desktop-search-clear');
      
      // Mobile elements
      const mobileInput = document.getElementById('mobile-search-input');
      const mobileResults = document.getElementById('mobile-search-results');
      const mobileDefault = document.getElementById('mobile-search-default');

      let debounceTimer = null;

      function renderSearchResults(data, container, isMobile = false) {
        if (!data || !data.results || data.results.length === 0) {
          container.innerHTML = `
            <div class="p-6 text-center text-xs text-slate-500 bg-white rounded-2xl">
              <span class="material-symbols-outlined text-[32px] text-slate-300 mb-1 block">search_off</span>
              <p class="font-bold text-slate-700">No matching products found</p>
              <p class="text-[11px] text-slate-400 mt-0.5">Try searching with a different model, name, or barcode</p>
            </div>
          `;
          container.classList.remove('hidden');
          return;
        }

        let html = `<div class="p-2 space-y-1 bg-white rounded-2xl">`;
        html += `<div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex justify-between border-b border-slate-100 mb-1">
          <span>Matching Products (${data.total} found)</span>
          <span class="text-primary font-bold">Instant Results</span>
        </div>`;

        data.results.forEach(p => {
          const imgHtml = p.has_real_image 
            ? `<img src="${p.image}" alt="${p.name}" class="w-11 h-11 object-contain rounded-lg border border-slate-100 p-0.5 shrink-0 bg-white" />`
            : `<div class="w-11 h-11 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 shrink-0"><span class="material-symbols-outlined text-[22px]">videocam</span></div>`;

          html += `
            <a href="${p.url}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-emerald-50/70 group transition-all border border-transparent hover:border-emerald-100">
              ${imgHtml}
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5">
                  <span class="text-[9px] font-bold text-emerald-800 uppercase bg-emerald-100 px-1.5 py-0.2 rounded">${p.category}</span>
                  ${p.barcode ? `<span class="text-[9px] text-slate-400">#${p.barcode}</span>` : ''}
                </div>
                <h4 class="text-xs font-bold text-slate-800 group-hover:text-primary transition-colors truncate mt-0.5">${p.name}</h4>
              </div>
              <div class="text-right shrink-0">
                <span class="text-xs font-extrabold text-primary block">${p.formatted_price}</span>
                ${p.old_price ? `<span class="text-[10px] text-slate-400 line-through block">${p.old_price}</span>` : ''}
              </div>
            </a>
          `;
        });

        html += `
          <div class="pt-2 mt-1 border-t border-slate-100 px-1 pb-1">
            <a href="${data.view_all_url}" class="block text-center bg-[#006241] hover:bg-[#004d33] text-white text-xs font-bold py-2 rounded-xl transition-colors shadow-xs">
              View All ${data.total} Results for "${data.query}" →
            </a>
          </div>
        </div>`;

        container.innerHTML = html;
        container.classList.remove('hidden');
      }

      function fetchSuggestions(query, category, container, isMobile = false) {
        if (!query || query.trim().length === 0) {
          container.innerHTML = '';
          container.classList.add('hidden');
          if (isMobile && mobileDefault) {
            mobileDefault.classList.remove('hidden');
            mobileResults.classList.add('hidden');
          }
          return;
        }

        fetch(`${liveSearchUrl}?q=${encodeURIComponent(query.trim())}&category=${encodeURIComponent(category || 'all')}`)
          .then(res => res.json())
          .then(data => {
            if (isMobile) {
              if (mobileDefault) mobileDefault.classList.add('hidden');
              mobileResults.classList.remove('hidden');
            }
            renderSearchResults(data, container, isMobile);
          })
          .catch(err => {
            console.error('Search error:', err);
          });
      }

      // Desktop Search Input Listener
      if (desktopInput && desktopResults) {
        desktopInput.addEventListener('input', function() {
          const val = this.value;
          if (desktopClear) {
            desktopClear.classList.toggle('hidden', val.length === 0);
          }
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(() => {
            const cat = desktopCategory ? desktopCategory.value : 'all';
            fetchSuggestions(val, cat, desktopResults, false);
          }, 200);
        });

        if (desktopClear) {
          desktopClear.addEventListener('click', function() {
            desktopInput.value = '';
            desktopClear.classList.add('hidden');
            desktopResults.innerHTML = '';
            desktopResults.classList.add('hidden');
            desktopInput.focus();
          });
        }

        if (desktopCategory) {
          desktopCategory.addEventListener('change', function() {
            if (desktopInput.value.trim().length > 0) {
              fetchSuggestions(desktopInput.value, this.value, desktopResults, false);
            }
          });
        }

        // Close on outside click
        document.addEventListener('click', function(e) {
          if (!e.target.closest('#desktop-search-container')) {
            desktopResults.classList.add('hidden');
          }
        });
      }

      // Mobile Search Input Listener
      if (mobileInput && mobileResults) {
        mobileInput.addEventListener('input', function() {
          const val = this.value;
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(() => {
            fetchSuggestions(val, 'all', mobileResults, true);
          }, 200);
        });
      }
    })();

    // Popular Cinema Filter Tabs
    function filterCategory(category, activeBtn) {
      const tabs = document.querySelectorAll('.cinema-tab');
      tabs.forEach(t => {
        t.className = 'cinema-tab px-3.5 py-1.5 rounded-lg text-label-md font-label-md text-text-muted hover:text-text-display transition-colors';
      });
      activeBtn.className = 'cinema-tab px-3.5 py-1.5 rounded-lg text-label-md font-label-md bg-primary text-on-primary font-bold shadow-xs';

      const cards = document.querySelectorAll('.product-card');
      cards.forEach(card => {
        if (category === 'all' || card.getAttribute('data-category') === category) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    }

    // Mobile Bottom Nav Active Switcher
    function setActiveNav(element) {
      document.querySelectorAll('.mobile-nav-item').forEach(item => {
        item.className = 'mobile-nav-item flex flex-col items-center justify-center gap-0.5 w-14 h-14 text-text-muted hover:text-on-surface transition-colors';
      });
      element.className = 'mobile-nav-item flex flex-col items-center justify-center gap-0.5 w-14 h-14 transition-colors text-primary font-bold';
    }

    // Newsletter Subscription
    function handleSubscribe() {
      const input = document.getElementById('newsletter-email');
      if (input && input.value.includes('@')) {
        showToast('Welcome to " + ((window.__APP_CONFIG && window.__APP_CONFIG.siteName) || "CAMERA BD") + " VIP! Flat ৳ 1,500 coupon sent to your inbox.', 'success');
        input.value = '';
      } else {
        showToast('Please enter a valid email address.', 'error');
      }
    }

    // Synchronized Live Countdown Timer based on Dynamic End Date
    (function initCountdown() {
      const timerWrap = document.getElementById('flash-deal-timer-wrap');
      if (!timerWrap) return;

      const rawEndDate = timerWrap.getAttribute('data-end-date');
      let targetTime = new Date(rawEndDate ? rawEndDate.replace(' ', 'T') : '').getTime();
      if (isNaN(targetTime)) {
        targetTime = Date.now() + 7 * 24 * 3600 * 1000;
      }

      const daysEls = document.querySelectorAll('.cd-days');
      const daysSepEls = document.querySelectorAll('.cd-days-sep');
      const hoursEls = document.querySelectorAll('.cd-hours');
      const minsEls = document.querySelectorAll('.cd-mins');
      const secsEls = document.querySelectorAll('.cd-secs');

      function updateCountdown() {
        const now = Date.now();
        const diff = targetTime - now;

        if (diff <= 0) {
          hoursEls.forEach(el => el.textContent = '00');
          minsEls.forEach(el => el.textContent = '00');
          secsEls.forEach(el => el.textContent = '00');
          daysEls.forEach(el => el.classList.add('hidden'));
          daysSepEls.forEach(el => el.classList.add('hidden'));
          return;
        }

        const totalSecs = Math.floor(diff / 1000);
        const days = Math.floor(totalSecs / 86400);
        const hours = Math.floor((totalSecs % 86400) / 3600);
        const mins = Math.floor((totalSecs % 3600) / 60);
        const secs = totalSecs % 60;

        if (days > 0) {
          daysEls.forEach(el => {
            el.textContent = String(days).padStart(2, '0') + 'd';
            el.classList.remove('hidden');
          });
          daysSepEls.forEach(el => el.classList.remove('hidden'));
        } else {
          daysEls.forEach(el => el.classList.add('hidden'));
          daysSepEls.forEach(el => el.classList.add('hidden'));
        }

        const hStr = String(hours).padStart(2, '0');
        const mStr = String(mins).padStart(2, '0');
        const sStr = String(secs).padStart(2, '0');

        hoursEls.forEach(el => el.textContent = hStr);
        minsEls.forEach(el => el.textContent = mStr);
        secsEls.forEach(el => el.textContent = sStr);
      }

      updateCountdown();
      setTimeout(function() { setInterval(updateCountdown, 1000); }, 8000);
    })();

    // Auto-Sliding Category Carousel
    (function initCategorySlider() {
      const track = document.getElementById('category-slider-track');
      const prevBtn = document.getElementById('cat-slider-prev');
      const nextBtn = document.getElementById('cat-slider-next');
      if (!track) return;

      let isPaused = false;
      let autoSlideTimer = null;

      const getScrollStep = () => {
        const firstCard = track.querySelector('a');
        if (firstCard) {
          return firstCard.offsetWidth + 16; // width + gap
        }
        return 160;
      };

      const slideNext = () => {
        const maxScroll = track.scrollWidth - track.clientWidth;
        if (track.scrollLeft >= maxScroll - 15) {
          track.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
          track.scrollBy({ left: getScrollStep(), behavior: 'smooth' });
        }
      };

      const slidePrev = () => {
        if (track.scrollLeft <= 15) {
          track.scrollTo({ left: track.scrollWidth, behavior: 'smooth' });
        } else {
          track.scrollBy({ left: -getScrollStep(), behavior: 'smooth' });
        }
      };

      const startAutoSlide = () => {
        if (autoSlideTimer) clearInterval(autoSlideTimer);
        autoSlideTimer = setInterval(() => {
          if (!isPaused) {
            slideNext();
          }
        }, 2500);
      };

      track.addEventListener('mouseenter', () => { isPaused = true; });
      track.addEventListener('mouseleave', () => { isPaused = false; });
      track.addEventListener('touchstart', () => { isPaused = true; }, { passive: true });
      track.addEventListener('touchend', () => { 
        setTimeout(() => { isPaused = false; }, 2000); 
      }, { passive: true });

      if (nextBtn) {
        nextBtn.addEventListener('click', () => {
          slideNext();
        });
      }
      if (prevBtn) {
        prevBtn.addEventListener('click', () => {
          slidePrev();
        });
      }

      // Category slider is user-controlled via swipe and prev/next buttons
    })();

    // Hollyland Showcase Slider Controls
    (function initHollylandSlider() {
      const hlTrack = document.getElementById('hollyland-track');
      const hlPrev = document.getElementById('hollyland-prev-btn');
      const hlNext = document.getElementById('hollyland-next-btn');
      const hlCount = document.getElementById('hollyland-showing-count');
      if (!hlTrack) return;

      const getStep = () => {
        const card = hlTrack.querySelector('div.snap-start');
        return card ? (card.offsetWidth + 14) * 2 : 440;
      };

      if (hlNext) {
        hlNext.addEventListener('click', () => {
          hlTrack.scrollBy({ left: getStep(), behavior: 'smooth' });
        });
      }

      if (hlPrev) {
        hlPrev.addEventListener('click', () => {
          hlTrack.scrollBy({ left: -getStep(), behavior: 'smooth' });
        });
      }
    })();

    // Hero Dynamic Banner Auto Slider (Desktop & Mobile Unified)
      (function initHeroSlider() {
        let currentHeroSlideIndex = 0;
        const heroSlides = document.querySelectorAll('.hero-slide, .desktop-hero-slide, .mobile-hero-slide');
        const heroDots = document.querySelectorAll('.hero-dot, .desktop-hero-dot');
        const totalHeroSlides = heroSlides.length;
        let heroSliderTimer = null;

        function loadRemainingHeroSlides() {
          document.querySelectorAll('.hero-slide img[data-src], .mobile-hero-slide img[data-src], .desktop-hero-slide img[data-src]').forEach(function(img) {
            var ds = img.getAttribute('data-src');
            if (ds) {
              img.src = ds;
              img.removeAttribute('data-src');
            }
          });
        }
        window.goToHeroSlide = function(index) {
          loadRemainingHeroSlides();
          if (totalHeroSlides <= 1) return;
          currentHeroSlideIndex = (index + totalHeroSlides) % totalHeroSlides;

          heroSlides.forEach((slide, i) => {
            if (i === currentHeroSlideIndex) {
              slide.classList.remove('opacity-0', 'z-0', 'pointer-events-none');
              slide.classList.add('opacity-100', 'z-10');
            } else {
              slide.classList.remove('opacity-100', 'z-10');
              slide.classList.add('opacity-0', 'z-0', 'pointer-events-none');
            }
          });

          heroDots.forEach((dot, i) => {
            if (i === currentHeroSlideIndex) {
              dot.classList.add('bg-white', 'w-5', 'lg:w-6');
              dot.classList.remove('bg-white/50', 'w-2');
            } else {
              dot.classList.remove('bg-white', 'w-5', 'lg:w-6');
              dot.classList.add('bg-white/50', 'w-2');
            }
          });

          resetHeroTimer();
        };

        window.nextHeroSlide = function() {
          goToHeroSlide(currentHeroSlideIndex + 1);
        };

        window.prevHeroSlide = function() {
          goToHeroSlide(currentHeroSlideIndex - 1);
        };

        function resetHeroTimer() {
          if (heroSliderTimer) clearInterval(heroSliderTimer);
          if (totalHeroSlides > 1) {
            heroSliderTimer = setInterval(window.nextHeroSlide, 6000);
          }
        }

        if (totalHeroSlides > 1) {
          setTimeout(resetHeroTimer, 8000);
          const hSlider = document.getElementById('hero-slider') || document.getElementById('desktop-hero-slider');
          if (hSlider) {
            hSlider.addEventListener('mouseenter', () => clearInterval(heroSliderTimer));
            hSlider.addEventListener('mouseleave', resetHeroTimer);
          }
        }
      })();
