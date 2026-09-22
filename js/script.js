// Sea Pearl Bistro - front-end application logic (extracted from main template)
// NOTE: this still runs on browser localStorage as its data store (as in the original
// prototype). See database/database.sql + includes/db_connect.php for the real MySQL
// schema this maps to once you wire up AJAX/PHP endpoints.

const STORAGE_KEYS = {
            PROFILES: 'seapearl_local_profiles_v3',
            ORDERS: 'seapearl_local_orders_v3',
            BOOKINGS: 'seapearl_local_bookings_v3',
            SUBSCRIBERS: 'seapearl_local_subscribers_v3',
            INQUIRIES: 'seapearl_local_inquiries_v3',
            SESSION_USER: 'seapearl_active_session_user_v3',
            MENU: 'seapearl_local_menu_v3'
        };

        let isAdminLoggedIn = false;

        let localDB = {
            profiles: JSON.parse(localStorage.getItem(STORAGE_KEYS.PROFILES)) || {},
            orders: JSON.parse(localStorage.getItem(STORAGE_KEYS.ORDERS)) || [],
            bookings: JSON.parse(localStorage.getItem(STORAGE_KEYS.BOOKINGS)) || [],
            subscribers: JSON.parse(localStorage.getItem(STORAGE_KEYS.SUBSCRIBERS)) || [],
            inquiries: JSON.parse(localStorage.getItem(STORAGE_KEYS.INQUIRIES)) || []
        };

        const SIMULATED_GUESTS = [
            { name: "Sven Johansson", phone: "0771234567", address: "Thalpe Beach-Bed #12", username: "swede_surf" },
            { name: "Chieko Tanaka", phone: "0711234567", address: "Galle Fort Resort Villa", username: "tokyo_pearl" },
            { name: "Liam Smith", phone: "0721234567", address: "Reef Deck Table #2", username: "melbourne_reef" },
            { name: "Elena Petrova", phone: "0751234567", address: "Habaraduwa Bed #8", username: "sandy_sunset" }
        ];

        let activeUser = JSON.parse(localStorage.getItem(STORAGE_KEYS.SESSION_USER)) || null;
        let wishlist = activeUser ? (activeUser.wishlist || []) : [];
        let cart = [];
        let currentCategory = 'All';
        let currentVegFilter = 'All'; 
        let selectedPaymentMethod = 'COD';
        let activeCustomizingItem = null;
        let activeCustomizationsSelected = [];
        let selectedTableId = null;
        let guestSimulatorInterval = null;
        let leafletMap = null; 

        let isSignupPhoneValid = false;
        let isSignupPasswordStrong = false;
        
        // Timer references for cancellations
        let activeTrackingTimerId = null;
        let bookingsTimerId = null;

        const defaultMenuItems = [
            {
                id: 'm1',
                title: 'Hand-pulled lagoon mud crab kothu',
                price: 3600.00,
                category: 'Entrées',
                desc: 'Delicately chopped organic kothu flatbread wok-tossed with freshly hand-shucked lagoon mud crab claws, organic quail eggs, and a secret black pepper reduction.',
                isVeg: false,
                imgUrl: 'https://images.unsplash.com/photo-1559314809-0d155014e29e?auto=format&fit=crop&q=80&w=600',
                customOptions: [{ name: 'Extra shredded cheese melt', price: 250.00 }, { name: 'Double mud crab portions', price: 950.00 }],
                status: 'Available'
            },
            {
                id: 'm2',
                title: 'Southern deviled red snapper fillets',
                price: 2400.00,
                category: 'Starters',
                desc: 'Crisped ocean snapper fillets tossed in a traditional spicy Unawatuna sweet-chili glazes, combined with fresh capsicums, caramelized red onions, and lime juices.',
                isVeg: false,
                imgUrl: 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&q=80&w=600',
                customOptions: [{ name: 'Spicy level: extreme extra hot', price: 0.00 }, { name: 'Add roasted cashew crown', price: 150.00 }],
                status: 'Available'
            },
            {
                id: 'm3',
                title: 'Grand Unawatuna spiny lobster feast',
                price: 7800.00,
                category: 'Entrées',
                desc: 'Whole premium spiny ocean lobster grilled over slow-burning raw coconut shells, basted with organic garlic herb butter, and paired with thick woodfired paan blocks.',
                isVeg: false,
                imgUrl: 'https://images.unsplash.com/photo-1628243342637-9111c3e22891?auto=format&fit=crop&q=80&w=600',
                customOptions: [{ name: 'Signature grass-fed garlic butter pool', price: 120.00 }, { name: 'Extra slab of woodfired roast paan', price: 80.00 }],
                status: 'Available'
            },
            {
                id: 'm4',
                title: 'Truffled ala theldala croquettes',
                price: 1350.00,
                category: 'Starters',
                desc: 'Hand-mashed local gold potatoes cooked down with chili flakes, red onions, and local curry leaves, coated in breadcrumbs and infused with organic white truffle oil.',
                isVeg: true,
                imgUrl: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&q=80&w=600',
                customOptions: [{ name: 'Organic spicy avocado dip', price: 100.00 }],
                status: 'Available'
            },
            {
                id: 'm5',
                title: 'Unawatuna narang sunset dessert',
                price: 1100.00,
                category: 'Desserts',
                desc: 'Baked local jaggery-infused wattalappam custard, layered with coconut narang orange segments, cardamom reduction, and baked cashew crumblings.',
                isVeg: true,
                imgUrl: 'https://images.unsplash.com/photo-1541783245831-57d6fb0926d3?auto=format&fit=crop&q=80&w=600',
                customOptions: [{ name: 'Extra kithul palm syrup drizzle', price: 50.00 }],
                status: 'Available'
            },
            {
                id: 'm6',
                title: 'Mirissa bluefin tuna tataki',
                price: 2900.00,
                category: 'Entrées',
                desc: 'Premium hand-caught bluefin tuna steak crusted in cracked black peppercorns, flash-seared medium-rare, and served over fresh local sea-green avocado salads.',
                isVeg: false,
                imgUrl: 'https://images.unsplash.com/photo-1534604973900-c43ab4c2e0ab?auto=format&fit=crop&q=80&w=600',
                customOptions: [{ name: 'Chef spicy wasabi-soy dips', price: 100.00 }],
                status: 'Available'
            }
        ];

        let menuItems = JSON.parse(localStorage.getItem(STORAGE_KEYS.MENU)) || defaultMenuItems;

        function playChime(freq = 523.25, type = 'sine', duration = 0.3) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = type;
                osc.frequency.setValueAtTime(freq, ctx.currentTime);
                gain.gain.setValueAtTime(0.04, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.005, ctx.currentTime + duration);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + duration);
            } catch (e) {
                console.log("Audio limitations prevented active feedback playback.");
            }
        }

        window.copyToClipboard = function(text) {
            const tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            playChime(659.25, 'triangle', 0.15);
            showNotification('Promo code copied', `"${text}" copied to your clipboard. Use at checkout!`, 'fa-solid fa-circle-check text-ocean-500');
        };

        window.selectTableOnMap = function(tableId) {
            playChime(440, 'triangle', 0.1);
            selectedTableId = tableId;
            resetSeatingMapVacancies();

            let label = "";
            if (tableId <= 4) label = `Sunset Beach Deck Table #${tableId}`;
            else if (tableId <= 8) label = `Beach Cabana Villa #${tableId}`;
            else label = `Pearl Bar Table #${tableId}`;

            document.getElementById('selected-table-notif').innerHTML = `Table selected: <strong class="text-ocean-900">${label}</strong>`;
            document.getElementById('selected-table-input').value = label;
        };

        function resetSeatingMapVacancies() {
            // Unused map elements handled safely via DOM queries
        }

        function renderFeaturedMenu() {
            const container = document.getElementById('featured-dishes-grid');
            if (!container) return;
            container.innerHTML = '';
            menuItems.slice(0, 6).forEach(item => {
                const wishIcon = wishlist.includes(item.id) ? 'fa-solid text-coral-500 animate-pulse' : 'fa-regular text-slate-400';
                const isSoldOut = item.status === 'Sold Out';
                
                const statusBadge = isSoldOut 
                    ? `<span class="bg-red-50 text-coral-500 border border-red-200 text-xs font-bold px-2.5 py-1 rounded-lg">Sold Out</span>`
                    : `<span class="bg-emerald-50 text-emerald-600 border border-emerald-200 text-xs font-bold px-2.5 py-1 rounded-lg">Available</span>`;

                const vegPill = item.isVeg 
                    ? `<span class="bg-emerald-50 text-emerald-600 border border-emerald-200/50 text-[10px] font-black px-2 py-0.5 rounded-lg flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Veg</span>` 
                    : `<span class="bg-rose-50 text-rose-600 border border-rose-200/50 text-[10px] font-black px-2 py-0.5 rounded-lg flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Non-veg</span>`;

                const actionButton = isSoldOut
                    ? `<button disabled class="w-full mt-auto bg-slate-200 text-slate-400 text-sm font-bold py-3 rounded-xl cursor-not-allowed">Sold Out</button>`
                    : `<button onclick="triggerCustomizer('${item.id}')" class="w-full mt-auto bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-sm text-white font-bold py-3 rounded-xl transition-all shadow-md">Customize & Add</button>`;

                container.innerHTML += `
                    <div class="bg-white rounded-3xl overflow-hidden border border-beige-200 flex flex-col h-full shadow-sm hover:shadow-lg transition-all transform hover:-translate-y-1 text-left">
                        <div class="h-52 overflow-hidden relative">
                            <img src="${item.imgUrl || 'https://images.unsplash.com/photo-1541532713592-79a0317b6b77?auto=format&fit=crop&q=80&w=400'}" alt="${item.title}" class="w-full h-full object-cover">
                            <button onclick="toggleWishlist('${item.id}')" class="absolute top-4 left-4 bg-white/90 w-10 h-10 rounded-xl flex items-center justify-center border border-beige-200 shadow-md"><i class="${wishIcon} fa-heart text-sm"></i></button>
                            <div class="absolute top-4 right-4 flex space-x-1.5 items-center">${vegPill} ${statusBadge}</div>
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="serif-font text-lg font-bold text-ocean-900 mb-2">${item.title}</h3>
                            <p class="text-sm text-slate-500 mb-4 leading-relaxed text-justify line-clamp-3">${item.desc}</p>
                            <span class="block text-base font-extrabold text-coral-500 mb-5 font-mono">LKR ${item.price.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                            ${actionButton}
                        </div>
                    </div>
                `;
            });
        }

        function renderCategoryFilters() {
            renderOrderCategories();
        }

        function renderOrderCategories() {
            const container = document.getElementById('order-categories-container');
            if (!container) return;
            const categories = ['All', 'Starters', 'Entrées', 'Desserts', 'Beverages'];
            container.innerHTML = categories.map(cat => {
                const style = cat === currentCategory ? 'bg-gradient-to-r from-ocean-500 to-aqua-500 text-white font-extrabold shadow-sm' : 'bg-white text-slate-600 border border-beige-200 hover:bg-beige-50';
                return `<button onclick="setOrderCategory('${cat}')" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all ${style}">${cat}</button>`;
            }).join('');
        }

        window.setOrderCategory = function(cat) {
            currentCategory = cat;
            renderOrderCategories();
            filterOrderMenu();
        };

        window.filterOrderMenu = function() {
            const searchVal = document.getElementById('order-search-input').value.toLowerCase();
            const vegOnly = document.getElementById('order-veg-toggle').checked;
            const nonVegOnly = document.getElementById('order-nonveg-toggle').checked;
            const sortVal = document.getElementById('order-sort-select').value;
            const container = document.getElementById('order-items-grid');
            if (!container) return;

            let filtered = [...menuItems];
            
            if (currentCategory !== 'All') filtered = filtered.filter(item => item.category === currentCategory);
            if (searchVal) filtered = filtered.filter(item => item.title.toLowerCase().includes(searchVal));
            if (vegOnly) filtered = filtered.filter(item => item.isVeg);
            if (nonVegOnly) filtered = filtered.filter(item => !item.isVeg);

            // Sorting logic
            if (sortVal === 'name-asc') {
                filtered.sort((a, b) => a.title.localeCompare(b.title));
            } else if (sortVal === 'name-desc') {
                filtered.sort((a, b) => b.title.localeCompare(a.title));
            } else if (sortVal === 'price-asc') {
                filtered.sort((a, b) => a.price - b.price);
            } else if (sortVal === 'price-desc') {
                filtered.sort((a, b) => b.price - a.price);
            }

            container.innerHTML = '';
            filtered.forEach(item => {
                const wishIcon = wishlist.includes(item.id) ? 'fa-solid text-coral-500 animate-pulse' : 'fa-regular text-slate-400';
                
                const defaultCartId = item.id + '_';
                const cartItem = cart.find(ci => ci.cartId === defaultCartId);
                const currentQty = cartItem ? cartItem.quantity : 0;

                const isSoldOut = item.status === 'Sold Out';
                const statusBadge = isSoldOut 
                    ? `<span class="bg-red-50 text-coral-500 border border-red-200 text-xs font-bold px-2.5 py-1 rounded-lg">Sold Out</span>`
                    : `<span class="bg-emerald-50 text-emerald-600 border border-emerald-200 text-xs font-bold px-2.5 py-1 rounded-lg">Available</span>`;

                const vegLabel = item.isVeg 
                    ? `<span class="bg-emerald-50 text-emerald-600 border border-emerald-200/50 text-[10px] font-black px-2 py-0.5 rounded-lg flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Veg</span>` 
                    : `<span class="bg-rose-50 text-rose-600 border border-rose-200/50 text-[10px] font-black px-2 py-0.5 rounded-lg flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Non-veg</span>`;

                let actionControl = '';
                if (isSoldOut) {
                    actionControl = `<span class="text-xs text-slate-400 font-bold bg-slate-100 py-2 px-4 rounded-xl border border-slate-200">Unavailable</span>`;
                } else {
                    actionControl = currentQty > 0 ? `
                        <div class="flex items-center space-x-2 bg-pearl-100 px-3 py-1.5 rounded-xl border border-beige-200 shadow-2xs">
                            <button onclick="changeCartQty('${defaultCartId}', -1)" class="text-slate-500 hover:text-ocean-500 font-extrabold text-sm px-1.5">-</button>
                            <span class="text-sm font-black text-ocean-900 font-mono">${currentQty}</span>
                            <button onclick="changeCartQty('${defaultCartId}', 1)" class="text-slate-500 hover:text-ocean-500 font-extrabold text-sm px-1.5">+</button>
                        </div>
                    ` : `
                        <button onclick="quickAddDefault('${item.id}')" class="bg-gradient-to-r from-ocean-500 to-aqua-500 text-white font-extrabold text-xs py-2 px-4 rounded-xl transition-all shadow-sm">Add to order</button>
                    `;
                }

                container.innerHTML += `
                    <div class="bg-white rounded-2xl overflow-hidden border border-beige-200 flex flex-col h-full shadow-sm hover:shadow-md transition-shadow">
                        <div class="h-44 overflow-hidden relative">
                            <img src="${item.imgUrl || 'https://images.unsplash.com/photo-1541532713592-79a0317b6b77?auto=format&fit=crop&q=80&w=400'}" alt="${item.title}" class="w-full h-full object-cover">
                            <button onclick="toggleWishlist('${item.id}')" class="absolute top-3 left-3 bg-white/95 w-9 h-9 rounded-lg flex items-center justify-center border border-beige-200 shadow-md"><i class="${wishIcon} fa-heart text-sm"></i></button>
                            <div class="absolute top-3 right-3 flex space-x-1 items-center">${vegLabel} ${statusBadge}</div>
                        </div>
                        <div class="p-5 flex flex-col justify-between flex-grow text-left">
                            <div>
                                <h4 class="text-base font-extrabold text-ocean-900 leading-snug mb-2">${item.title}</h4>
                                <p class="text-xs sm:text-sm text-slate-500 mb-3 leading-relaxed text-justify line-clamp-3">${item.desc}</p>
                            </div>
                            <div class="flex items-center justify-between mt-4">
                                <span class="text-base font-black text-coral-500 font-mono">LKR ${item.price.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                                <div class="flex items-center space-x-2">
                                    ${!isSoldOut ? `<button onclick="triggerCustomizer('${item.id}')" class="text-xs font-bold text-slate-400 hover:text-ocean-500 underline py-2 px-2.5">Customize</button>` : ''}
                                    ${actionControl}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        };

        window.quickAddDefault = function(itemId) {
            const item = menuItems.find(m => m.id === itemId);
            if (!item || item.status === 'Sold Out') return;
            const defaultKey = itemId + '_';
            
            const existing = cart.find(ci => ci.cartId === defaultKey);
            if (existing) {
                existing.quantity++;
            } else {
                cart.push({
                    cartId: defaultKey,
                    itemId: item.id,
                    title: item.title,
                    price: item.price,
                    customLabel: 'Original Portion',
                    extraPayments: 0,
                    quantity: 1
                });
            }
            updateCartBadge();
            renderCartItems();
            filterOrderMenu();
            playChime(523.25, 'sine', 0.1);
            showNotification('Dish added', `${item.title} added to your order sheet.`, 'fa-solid fa-basket-shopping text-ocean-500');
        };

        window.toggleCart = function() {
            document.getElementById('cart-sidebar').classList.toggle('hidden');
            renderCartItems();
        };

        window.changeCartQty = function(cartId, change) {
            const index = cart.findIndex(ci => ci.cartId === cartId);
            if (index !== -1) {
                cart[index].quantity += change;
                if (cart[index].quantity <= 0) cart.splice(index, 1);
                
                renderCartItems();
                updateCartBadge();
                filterOrderMenu();
            }
        };

        function updateCartBadge() {
            const badge = document.getElementById('cart-count');
            const total = cart.reduce((acc, ci) => acc + ci.quantity, 0);
            badge.innerText = total;
            badge.className = total > 0 ? "absolute -top-1.5 -right-1.5 bg-gradient-to-r from-ocean-500 to-aqua-500 text-white font-bold text-[10px] w-5 h-5 rounded-full flex items-center justify-center border-2 border-white transition-all scale-100 shadow-md font-sans" : "scale-0";
        }

        function renderCartItems() {
            const container = document.getElementById('cart-items-container');
            if (!container) return;
            container.innerHTML = '';
            if (cart.length === 0) {
                container.innerHTML = `<p class="text-sm text-slate-400 text-center py-8 font-medium">Your basket is currently empty.</p>`;
                document.getElementById('cart-subtotal').innerText = 'LKR 0.00';
                document.getElementById('cart-tax').innerText = 'LKR 0.00';
                document.getElementById('cart-total').innerText = 'LKR 0.00';
                return;
            }
            let subtotal = 0;
            cart.forEach(ci => {
                const baseLinePrice = ci.price + ci.extraPayments;
                subtotal += baseLinePrice * ci.quantity;
                container.innerHTML += `
                    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-beige-200 shadow-2xs animate-fade-in font-sans">
                        <div>
                            <h5 class="text-sm font-bold text-ocean-900">${ci.title}</h5>
                            <span class="block text-xs text-slate-400 font-semibold mt-0.5">${ci.customLabel}</span>
                            <span class="block text-sm font-black text-coral-500 mt-1 font-mono">LKR ${(baseLinePrice * ci.quantity).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                        </div>
                        <div class="flex items-center space-x-2 bg-pearl-100 px-2 py-1 rounded-lg border border-beige-200">
                            <button onclick="changeCartQty('${ci.cartId}', -1)" class="text-slate-400 hover:text-ocean-500 font-black text-base px-1.5">-</button>
                            <span class="text-xs font-black font-mono text-ocean-900">${ci.quantity}</span>
                            <button onclick="changeCartQty('${ci.cartId}', 1)" class="text-slate-400 hover:text-ocean-500 font-black text-base px-1.5">+</button>
                        </div>
                    </div>
                `;
            });
            const tax = subtotal * 0.10;
            const total = subtotal + tax + 300;
            document.getElementById('cart-subtotal').innerText = `LKR ${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
            document.getElementById('cart-tax').innerText = `LKR ${tax.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
            document.getElementById('cart-total').innerText = `LKR ${total.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        }

        window.handlePlaceOrder = function() {
            if (!activeUser) {
                openAuthModal('signin');
                return;
            }
            if (cart.length === 0) return;
            
            const name = document.getElementById('cust-name').value.trim();
            const phone = document.getElementById('cust-phone').value.trim();
            const address = document.getElementById('cust-address').value.trim();

            if (activeUser) {
                activeUser.full_name = name;
                activeUser.mobile = phone;
                activeUser.delivery_address = address;
                activeUser.fullname = name;
                activeUser.phone = phone;
                activeUser.address = address;
                localStorage.setItem(STORAGE_KEYS.SESSION_USER, JSON.stringify(activeUser));
            }

            if (!name || !phone || !address) {
                showNotification('Details requested', 'Please complete recipient coordinates.', 'fa-solid fa-triangle-exclamation text-coral-500');
                return;
            }

            // Real-Time Delivery Area Validation (Southern Coastline Only)
            const allowedAreas = [
                'galle fort', 'unawatuna', 'thalpe', 'mihiripenna', 
                'habaraduwa', 'koggala', 'ahangama', 'weligama', 
                'karapitiya', 'dadalla'
            ];
            const lowerAddress = address.toLowerCase();
            const isInsideCoverage = allowedAreas.some(area => lowerAddress.includes(area));

            if (!isInsideCoverage) {
                // Instantly Reject Checkout - Send immediate cancel notification warning
                playChime(329.63, 'sawtooth', 0.6);
                
                document.getElementById('confirm-modal-icon').className = "fa-solid fa-triangle-exclamation text-rose-500 text-3xl";
                document.getElementById('confirm-modal-title').innerText = "Delivery Area Restrained";
                document.getElementById('confirm-modal-message').innerText = "We apologize, but your location falls outside our strict southern coastline transport bounds. Your order has been declined at check-out. We currently only dispatch gourmet meals within Galle Fort, Unawatuna, Thalpe, Mihiripenna, Habaraduwa, Koggala, Ahangama, Weligama, Karapitiya, and Dadalla.";
                document.getElementById('success-confirmation-modal').classList.remove('hidden');

                showNotification('Order declined', 'Your delivery address is outside our beachfront service limits.', 'fa-solid fa-circle-xmark text-rose-500');
                return;
            }

            const subtotal = cart.reduce((sum, ci) => sum + (ci.price + ci.extraPayments) * ci.quantity, 0);
            const hospitalityCharge = subtotal * 0.10;
            const deliveryFee = 300;
            const total = subtotal + hospitalityCharge + deliveryFee;

            const orderPayload = {
                user_id: activeUser.user_id || null,
                delivery_name: name,
                delivery_phone: phone,
                delivery_address: address,
                payment_method: selectedPaymentMethod === 'Card' ? 'Online Card' : 'COD',
                subtotal: subtotal,
                hospitality_charge: hospitalityCharge,
                delivery_fee: deliveryFee,
                total_amount: total,
                items: cart.map(ci => ({
                    item_id: ci.itemId,
                    quantity: ci.quantity,
                    unit_price: ci.price + ci.extraPayments,
                    customizations: ci.customLabel || 'Original Portion'
                }))
            };

            submitOrderToDatabase(orderPayload)
                .then(data => {
                    if (!data.ok) {
                        showNotification('Order failed', data.message || 'Unable to save order.', 'fa-solid fa-triangle-exclamation text-coral-500');
                        return;
                    }

                    const orderId = data.order_id;
                    const newOrder = {
                        id: orderId,
                        customerName: name,
                        delivery_name: name,
                        full_name: name,
                        username: activeUser.username,
                        phone: phone,
                        delivery_phone: phone,
                        mobile: phone,
                        address: address,
                        delivery_address: address,
                        paymentMethod: selectedPaymentMethod,
                        items: cart.map(ci => ({ title: ci.title, qty: ci.quantity, customLabel: ci.customLabel })),
                        total: total,
                        status: 'Pending',
                        timestamp: new Date().toISOString()
                    };

                    localDB.orders.push(newOrder);
                    syncLocalDB(STORAGE_KEYS.ORDERS, localDB.orders);
                    cart = [];
                    updateCartBadge();
                    toggleCart();

                    playChime(880, 'sine', 0.5);
                    showNotification('Order received', `Ticket ${orderId} is now live!`, 'fa-solid fa-utensils text-ocean-500');
                    switchView('orders');
                    document.getElementById('order-lookup-input').value = orderId;
                    renderOrderTracking(newOrder);
                    renderUserOrdersDropdown();
                    filterOrderMenu();
                })
                .catch(() => {
                    showNotification('Order failed', 'Unable to reach the server.', 'fa-solid fa-triangle-exclamation text-coral-500');
                });
        };

        window.updateSelectedTableDisplay = function(desc) {
            document.getElementById('selected-table-notif').innerText = desc;
            document.getElementById('selected-table-input').value = desc;
        };

        window.handleTableBookingSubmit = function(e) {
            e.preventDefault();
            if (!activeUser) {
                openAuthModal('signin');
                return;
            }

            const tableDesc = document.getElementById('selected-table-input').value;
            if (!tableDesc) {
                showNotification('Table selection required', 'Please select a preferred table on the layout.', 'fa-solid fa-chair text-rose-500');
                return;
            }

            const bkId = 'BK-' + Math.floor(100 + Math.random() * 900);
            const newBooking = {
                id: bkId,
                name: document.getElementById('book-name').value,
                username: activeUser.username,
                phone: document.getElementById('book-phone').value,
                date: document.getElementById('book-date').value,
                time: document.getElementById('book-time').value,
                pref: tableDesc,
                status: 'Approved',
                timestamp: new Date().toISOString() // Track reservation creation
            };

            localDB.bookings.push(newBooking);
            syncLocalDB(STORAGE_KEYS.BOOKINGS, localDB.bookings);
            
            document.getElementById('table-booking-form').reset();
            document.getElementById('selected-table-notif').innerText = "Please tap a table from the deck!";
            document.getElementById('selected-table-input').value = "";
            
            playChime(659.25, 'sine', 0.4);
            showSuccessConfirmation('Seating Confirmed!', `Booking ${bkId} holds your spot for 10 minutes. If needed, you can cancel it directly from your guest profile favorites tab.`);
        };

        function renderProfileBookings() {
            const container = document.getElementById('profile-bookings-items-container');
            const countText = document.getElementById('profile-bookings-count-text');
            if (!container || !activeUser) return;

            if (bookingsTimerId) {
                clearInterval(bookingsTimerId);
            }

            const userBookings = localDB.bookings.filter(b => b.username === activeUser.username);
            countText.innerText = `${userBookings.length} slots`;

            if (userBookings.length === 0) {
                container.innerHTML = `<p class="text-sm text-slate-455 text-center py-4 italic">No table reservations found.</p>`;
                return;
            }

            // Real-Time countdown update loop inside user profile
            function updateBookingsUI() {
                const now = new Date().getTime();
                container.innerHTML = userBookings.map(b => {
                    const createdTime = new Date(b.timestamp).getTime();
                    const diffMs = now - createdTime;
                    const remainingMs = (10 * 60 * 1000) - diffMs; // 10 minutes cancellation limit
                    const isEligible = remainingMs > 0 && b.status === 'Approved';

                    let cancelActionHTML = '';
                    if (isEligible) {
                        const totalSecs = Math.floor(remainingMs / 1000);
                        const mins = Math.floor(totalSecs / 60);
                        const secs = totalSecs % 60;
                        const timerStr = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                        
                        cancelActionHTML = `
                            <div class="mt-3 pt-2 border-t border-rose-100 flex items-center justify-between gap-2">
                                <span class="text-xs text-rose-500 font-mono font-bold flex items-center gap-1"><i class="fa-solid fa-clock"></i>Window: ${timerStr}</span>
                                <button onclick="confirmBookingCancellation('${b.id}')" class="bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-[11px] font-black px-3 py-1.5 rounded-lg transition-all">
                                    Cancel booking
                                </button>
                            </div>
                        `;
                    } else if (b.status === 'Cancelled') {
                        cancelActionHTML = `
                            <div class="mt-3 pt-2 border-t border-slate-100 text-xs text-slate-400 font-bold">
                                <i class="fa-solid fa-ban mr-1"></i> Reservation has been cancelled
                            </div>
                        `;
                    } else {
                        cancelActionHTML = `
                            <div class="mt-3 pt-2 border-t border-slate-100 text-xs text-slate-400 font-bold">
                                <i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i> Seating locked (No longer cancelable)
                            </div>
                        `;
                    }

                    let statusStyle = 'bg-emerald-50 text-emerald-700 border border-emerald-100';
                    if (b.status === 'Cancelled') statusStyle = 'bg-slate-50 text-slate-500 border border-slate-200';

                    return `
                        <div class="bg-white p-4 rounded-xl border border-beige-200 shadow-2xs relative text-left font-sans">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-xs font-black text-ocean-955 uppercase block tracking-wider">${b.pref}</span>
                                    <span class="text-xs font-medium text-slate-500 block mt-1"><i class="fa-solid fa-calendar-day text-coral-500 mr-1"></i>${b.date} at ${b.time}</span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">Code: <strong class="font-mono text-slate-600">${b.id}</strong></span>
                                </div>
                                <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded ${statusStyle}">${b.status}</span>
                            </div>
                            ${cancelActionHTML}
                        </div>
                    `;
                }).join('');
            }

            updateBookingsUI();
            bookingsTimerId = setInterval(updateBookingsUI, 1000);
        }

        window.confirmBookingCancellation = function(bookingId) {
            const b = localDB.bookings.find(bk => bk.id === bookingId);
            if (!b) return;

            b.status = 'Cancelled';
            syncLocalDB(STORAGE_KEYS.BOOKINGS, localDB.bookings);
            
            renderProfileBookings();
            updateStats();

            playChime(329.63, 'sawtooth', 0.4);
            
            document.getElementById('confirm-modal-icon').className = "fa-solid fa-ban text-rose-500 text-3xl";
            document.getElementById('confirm-modal-title').innerText = "Booking Cancelled";
            document.getElementById('confirm-modal-message').innerText = `Your beachfront table reservation (${b.pref}) has been cancelled successfully. Your held coordinates have been released back to the dynamic map.`;
            document.getElementById('success-confirmation-modal').classList.remove('hidden');

            showNotification('Booking Cancelled', `Reservation ${bookingId} has been cancelled.`, 'fa-solid fa-circle-xmark text-rose-500');
        };

        function startOrderCancelCountdown(order) {
            if (activeTrackingTimerId) {
                clearInterval(activeTrackingTimerId);
                activeTrackingTimerId = null;
            }

            const cancelSection = document.getElementById('order-cancellation-section');
            const timerLabel = document.getElementById('order-cancel-timer');
            const cancelBtn = document.getElementById('btn-cancel-order-action');

            if (!cancelSection || !timerLabel || !cancelBtn) return;

            if (order.status !== 'Pending' && order.status !== 'Cancelled') {
                cancelSection.classList.add('hidden');
                return;
            }

            if (order.status === 'Cancelled') {
                cancelSection.classList.remove('hidden');
                timerLabel.innerText = "This order has already been cancelled.";
                timerLabel.className = "block text-xs sm:text-sm text-rose-600 font-bold bg-rose-100/50 px-3 py-1.5 rounded-xl border border-rose-200 w-fit";
                cancelBtn.classList.add('hidden');
                return;
            }

            cancelBtn.classList.remove('hidden');
            timerLabel.className = "block text-xs sm:text-sm text-rose-500 font-mono font-bold bg-white px-2 py-1 rounded border border-rose-100 w-fit";

            function updateTimer() {
                const createdTime = new Date(order.timestamp).getTime();
                const now = new Date().getTime();
                const diffMs = now - createdTime;
                const remainingMs = (10 * 60 * 1000) - diffMs; // 10 minutes cancellation limit

                if (remainingMs <= 0) {
                    cancelSection.classList.add('hidden');
                    clearInterval(activeTrackingTimerId);
                    activeTrackingTimerId = null;
                } else {
                    cancelSection.classList.remove('hidden');
                    const totalSecs = Math.floor(remainingMs / 1000);
                    const mins = Math.floor(totalSecs / 60);
                    const secs = totalSecs % 60;
                    timerLabel.innerText = `Time remaining to cancel: ${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                    
                    cancelBtn.onclick = function() {
                        confirmOrderCancellation(order.id);
                    };
                }
            }

            updateTimer();
            activeTrackingTimerId = setInterval(updateTimer, 1000);
        }

        window.confirmOrderCancellation = function(orderId) {
            const ord = localDB.orders.find(o => o.id === orderId);
            if (!ord) return;

            ord.status = 'Cancelled';
            syncLocalDB(STORAGE_KEYS.ORDERS, localDB.orders);
            
            if (activeTrackingTimerId) {
                clearInterval(activeTrackingTimerId);
                activeTrackingTimerId = null;
            }

            renderOrderTracking(ord);
            renderUserOrdersDropdown();
            updateStats();

            playChime(329.63, 'sawtooth', 0.4);
            
            document.getElementById('confirm-modal-icon').className = "fa-solid fa-ban text-rose-500 text-3xl";
            document.getElementById('confirm-modal-title').innerText = "Order Cancelled";
            document.getElementById('confirm-modal-message').innerText = `Your order ${orderId} has been cancelled successfully. Your payment authorisation has been suspended and released.`;
            document.getElementById('success-confirmation-modal').classList.remove('hidden');

            showNotification('Order Cancelled', `Ticket ${orderId} has been cancelled.`, 'fa-solid fa-circle-xmark text-rose-500');
        };

        window.handleInquirySubmit = function(e) {
            e.preventDefault();
            const inqId = 'INQ-' + Math.floor(500 + Math.random() * 500);
            const newInq = {
                id: inqId,
                name: document.getElementById('contact-name').value,
                email: document.getElementById('contact-email').value,
                phone: document.getElementById('contact-phone').value,
                message: document.getElementById('contact-message').value,
                status: 'Unread',
                timestamp: new Date().toISOString()
            };
            localDB.inquiries.push(newInq);
            syncLocalDB(STORAGE_KEYS.INQUIRIES, localDB.inquiries);
            document.getElementById('contact-inquiry-form').reset();
            showSuccessConfirmation('Message Transmitted!', 'Your gourmet proposal has been safely logged into our inbox ledger.');
        };

        window.handleNewsletterSubmit = function(e, inputId) {
            e.preventDefault();
            const emailInput = document.getElementById(inputId);
            const emailValue = emailInput.value.trim();
            if (!emailValue) return;

            if (localDB.subscribers.some(sub => sub.email.toLowerCase() === emailValue.toLowerCase())) {
                showNotification('Already Subscribed', 'This email is already registered.', 'fa-solid fa-circle-exclamation text-coral-500');
                emailInput.value = '';
                return;
            }

            localDB.subscribers.push({ email: emailValue, timestamp: new Date().toISOString() });
            syncLocalDB(STORAGE_KEYS.SUBSCRIBERS, localDB.subscribers);
            emailInput.value = '';
            showSuccessConfirmation('Welcome to the Club!', 'You are officially registered inside our beachfront tasting circle database.');
        };

        window.handleAdminLogin = function(e) {
            e.preventDefault();
            const passcode = document.getElementById('admin-passcode').value;
            if (passcode === 'admin') {
                isAdminLoggedIn = true;
                document.getElementById('admin-login-gate').classList.add('hidden');
                document.getElementById('admin-workspace-pane').classList.remove('hidden');
                playChime(880, 'sine', 0.25);
                showNotification('Authorized Access', 'Staff credentials verified.', 'fa-solid fa-shield-halved text-emerald-500');
                renderAdminOrders();
            } else {
                playChime(220, 'sawtooth', 0.4);
                showNotification('Access Denied', 'Invalid passcode supplied.', 'fa-solid fa-triangle-exclamation text-rose-500');
            }
        };

        window.switchAdminTab = function(tabName) {
            document.querySelectorAll('.admin-tab-panel').forEach(panel => panel.classList.add('hidden'));
            document.getElementById('admin-tab-content-' + tabName).classList.remove('hidden');
            
            document.querySelectorAll('.admin-tab').forEach(tab => {
                tab.className = 'admin-tab bg-pearl-100 hover:bg-beige-100 text-slate-600 text-xs font-extrabold px-4 py-2.5 rounded-xl transition-all tracking-wider border border-beige-200/80';
            });
            document.getElementById('btn-tab-' + tabName).className = 'admin-tab bg-ocean-500 text-white text-xs font-extrabold px-4 py-2.5 rounded-xl transition-all shadow-xs tracking-wider';
            
            if (tabName === 'orders') renderAdminOrders();
            if (tabName === 'bookings') renderAdminBookings();
            if (tabName === 'menu') renderAdminMenuList();
            if (tabName === 'inquiries') {
                renderAdminInquiries();
                renderAdminSubscribers();
            }
            updateStats();
        };

        function renderAdminOrders() {
            const container = document.getElementById('admin-orders-table');
            if (!container) return;
            document.getElementById('orders-live-count').innerText = localDB.orders.length;
            if (localDB.orders.length === 0) {
                container.innerHTML = `<tr><td colspan="7" class="py-8 text-center text-slate-400 font-medium">No live orders inside active registries.</td></tr>`;
                return;
            }
            container.innerHTML = localDB.orders.map(ord => {
                const detailStr = ord.items.map(i => `<div class="font-semibold text-xs text-ocean-900">${i.title} (${i.qty}x) <span class="text-[10px] text-slate-400">| ${i.customLabel}</span></div>`).join('');
                
                let actionBtn = '';
                if (ord.status === 'Pending') {
                    actionBtn = `<button onclick="updateOrderStatus('${ord.id}', 'Preparing')" class="bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-white text-[10px] font-bold tracking-wider px-3.5 py-2 rounded-lg shadow-sm uppercase">Prepare</button>`;
                } else if (ord.status === 'Preparing') {
                    actionBtn = `<button onclick="updateOrderStatus('${ord.id}', 'Out for Delivery')" class="bg-gradient-to-r from-coral-500 to-amber-500 hover:brightness-110 text-white text-[10px] font-bold tracking-wider px-3.5 py-2 rounded-lg shadow-sm uppercase">Dispatch</button>`;
                } else if (ord.status === 'Out for Delivery') {
                    actionBtn = `<button onclick="updateOrderStatus('${ord.id}', 'Delivered')" class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:brightness-110 text-white text-[10px] font-bold tracking-wider px-3.5 py-2 rounded-lg shadow-sm uppercase">Fulfill</button>`;
                } else if (ord.status === 'Cancelled') {
                    actionBtn = `<span class="text-xs text-rose-500 font-bold"><i class="fa-solid fa-circle-xmark mr-1"></i>Cancelled</span>`;
                } else {
                    actionBtn = `<span class="text-xs text-slate-400 font-semibold"><i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>Completed</span>`;
                }

                let statusBadgeStyle = 'bg-amber-100 text-amber-700';
                if (ord.status === 'Preparing') statusBadgeStyle = 'bg-blue-100 text-blue-700';
                if (ord.status === 'Out for Delivery') statusBadgeStyle = 'bg-coral-100 text-coral-700';
                if (ord.status === 'Delivered') statusBadgeStyle = 'bg-emerald-100 text-emerald-700';
                if (ord.status === 'Cancelled') statusBadgeStyle = 'bg-rose-100 text-rose-700';

                return `
                    <tr class="hover:bg-pearl-100/50">
                        <td class="py-4 px-4 font-mono font-bold text-ocean-500">${ord.id}</td>
                        <td class="py-4 px-4">
                            <div class="font-bold text-slate-800">${ord.customerName || ord.delivery_name || ord.full_name || 'Guest'}</div>
                            <div class="text-[10px] text-slate-400 font-mono">@${ord.username || 'visitor'} | ${ord.phone || ord.delivery_phone || ord.mobile || 'No phone'}</div>
                        </td>
                        <td class="py-4 px-4 space-y-1">${detailStr}</td>
                        <td class="py-4 px-4"><span class="text-xs font-bold text-slate-600">${ord.paymentMethod}</span></td>
                        <td class="py-4 px-4 font-mono font-extrabold text-coral-500">LKR ${ord.total.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td class="py-4 px-4"><span class="text-[10px] font-extrabold px-2.5 py-1 rounded-lg ${statusBadgeStyle}">${ord.status}</span></td>
                        <td class="py-4 px-4 text-right">${actionBtn}</td>
                    </tr>
                `;
            }).join('');
        }

        window.updateOrderStatus = function(ordId, newStatus) {
            const ord = localDB.orders.find(o => o.id === ordId);
            if (ord) {
                ord.status = newStatus;
                syncLocalDB(STORAGE_KEYS.ORDERS, localDB.orders);
                renderAdminOrders();
                updateStats();
                showNotification('Status updated', `Order ${ordId} updated: ${newStatus}.`, 'fa-solid fa-truck-loading text-ocean-500');
                
                const currentLookedUpId = document.getElementById('track-id').innerText;
                if (currentLookedUpId === ordId) {
                    renderOrderTracking(ord);
                }
            }
        };

        function renderAdminBookings() {
            const container = document.getElementById('admin-bookings-table');
            if (!container) return;
            if (localDB.bookings.length === 0) {
                container.innerHTML = `<tr><td colspan="7" class="py-8 text-center text-slate-400 font-medium">No seating requests inside registry.</td></tr>`;
                return;
            }
            container.innerHTML = localDB.bookings.map(b => `
                <tr class="hover:bg-pearl-100/50">
                    <td class="py-4 px-4 font-bold text-slate-800">${b.name} <div class="text-[10px] text-slate-400 font-normal">@${b.username}</div></td>
                    <td class="py-4 px-4 text-slate-600 font-semibold text-xs">${b.date} at ${b.time}</td>
                    <td class="py-4 px-4 font-extrabold text-xs text-ocean-900">Held spot</td>
                    <td class="py-4 px-4"><span class="text-xs font-bold text-coral-500">${b.pref}</span></td>
                    <td class="py-4 px-4 text-xs text-slate-500 italic max-w-xs text-justify">Held successfully</td>
                    <td class="py-4 px-4"><span class="text-[10px] font-extrabold px-2 py-1 rounded bg-slate-100 text-slate-600">${b.status}</span></td>
                    <td class="py-4 px-4 text-right">
                        ${b.status === 'Pending' ? `<button onclick="approveBooking('${b.id}')" class="bg-emerald-500 text-white text-[10px] font-extrabold py-1.5 px-3 rounded-lg shadow-sm">Confirm</button>` : b.status === 'Cancelled' ? `<span class="text-xs text-rose-500 font-semibold"><i class="fa-solid fa-ban mr-1"></i>Cancelled</span>` : `<span class="text-xs text-slate-400 font-semibold"><i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>Approved</span>`}
                    </td>
                </tr>
            `).join('');
        }

        window.approveBooking = function(id) {
            const b = localDB.bookings.find(bk => bk.id === id);
            if (b) {
                b.status = 'Approved';
                syncLocalDB(STORAGE_KEYS.BOOKINGS, localDB.bookings);
                renderAdminBookings();
                updateStats();
                resetSeatingMapVacancies();
                showNotification('Seating confirmed', 'Guest table confirmation activated.', 'fa-solid fa-chair text-ocean-500');
            }
        };

        function renderAdminMenuList() {
            const container = document.getElementById('admin-menu-list');
            if (!container) return;
            container.innerHTML = menuItems.map(item => `
                <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-beige-200 shadow-2xs">
                    <div class="text-left">
                        <span class="text-sm font-extrabold text-ocean-900 block">${item.title} <span class="text-[10px] font-bold text-slate-400">| ${item.category}</span></span>
                        <span class="text-xs font-bold text-coral-500 font-mono">LKR ${item.price.toLocaleString('en-US')}</span>
                        <span class="text-[10px] font-bold ${item.status === 'Sold Out' ? 'text-coral-500' : 'text-emerald-500'} block">Status: ${item.status}</span>
                    </div>
                    <button onclick="deleteMenuItem('${item.id}')" class="text-coral-500 hover:text-coral-600 text-xs p-2.5 bg-pearl-100 rounded-xl hover:shadow-inner"><i class="fa-solid fa-trash"></i></button>
                </div>
            `).join('');
        }

        window.handleCreateMenuItem = function(e) {
            e.preventDefault();
            const title = document.getElementById('menu-title').value.trim();
            const price = parseFloat(document.getElementById('menu-price').value);
            const category = document.getElementById('menu-category').value;
            const isVeg = document.getElementById('menu-is-veg').checked;
            const isSoldOut = document.getElementById('menu-is-soldout').checked;
            const desc = document.getElementById('menu-desc').value.trim();
            const imgUrl = document.getElementById('menu-img-url').value.trim();

            const newId = 'm' + (menuItems.length + 1);
            menuItems.push({ 
                id: newId, 
                title, 
                price, 
                category, 
                isVeg, 
                desc, 
                imgUrl,
                status: isSoldOut ? 'Sold Out' : 'Available'
            });
            syncLocalDB(STORAGE_KEYS.MENU, menuItems);
            resetMenuForm();
            renderAdminMenuList();
            showNotification('Item created', `${title} published to dynamic menu list.`, 'fa-solid fa-pizza-slice text-ocean-500');
            renderFeaturedMenu();
            filterOrderMenu();
        };

        window.deleteMenuItem = function(id) {
            menuItems = menuItems.filter(item => item.id !== id);
            syncLocalDB(STORAGE_KEYS.MENU, menuItems);
            renderAdminMenuList();
            showNotification('Item retracted', 'Dynamic menu item removed.', 'fa-solid fa-trash text-coral-500');
            renderFeaturedMenu();
            filterOrderMenu();
        };

        function resetMenuForm() {
            document.getElementById('admin-menu-form').reset();
        }

        function renderAdminInquiries() {
            const container = document.getElementById('admin-inquiries-list');
            if (!container) return;
            if (localDB.inquiries.length === 0) {
                container.innerHTML = `<p class="text-xs text-slate-455 italic text-center py-6">No letters logged in mailbox.</p>`;
                return;
            }
            container.innerHTML = localDB.inquiries.map(inq => `
                <div class="bg-white p-4 rounded-xl border border-beige-200 mb-3 text-left shadow-2xs animate-fade-in">
                    <h4 class="font-extrabold text-ocean-900 text-xs">${inq.name} <span class="text-[10px] text-slate-400 font-mono">(${inq.email})</span></h4>
                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">Tel: ${inq.phone || 'None'}</p>
                    <p class="text-xs text-slate-600 leading-relaxed mt-2.5 italic bg-pearl-100 p-2.5 rounded-xl border border-beige-200/50 text-justify">"${inq.message}"</p>
                </div>
            `).join('');
        }

        function renderAdminSubscribers() {
            const tableBody = document.getElementById('admin-subs-table');
            if (!tableBody) return;
            document.getElementById('admin-sub-count').innerText = `${localDB.subscribers.length} Members`;
            if (localDB.subscribers.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="2" class="py-3 text-center text-slate-400">No subscribers currently.</td></tr>`;
                return;
            }
            tableBody.innerHTML = localDB.subscribers.map(sub => `
                <tr class="hover:bg-pearl-100/50 text-xs">
                    <td class="py-2 px-3 font-bold text-slate-700">${sub.email}</td>
                    <td class="py-2 px-3 text-slate-400">${sub.timestamp ? sub.timestamp.slice(0, 10) : 'Just now'}</td>
                </tr>
            `).join('');
        }

        function updateStats() {
            const revenue = localDB.orders.filter(o => o.status === 'Delivered').reduce((acc, curr) => acc + curr.total, 0);
            document.getElementById('stat-revenue').innerText = `LKR ${revenue.toLocaleString('en-US', {maximumFractionDigits: 0})}`;
            document.getElementById('stat-orders').innerText = localDB.orders.length;
            document.getElementById('stat-bookings').innerText = localDB.bookings.length;
            document.getElementById('stat-subscribers').innerText = localDB.subscribers.length;
        }

        function showNotification(title, message, iconClass) {
            const notify = document.getElementById('custom-notification');
            document.getElementById('notify-icon').innerHTML = `<i class="${iconClass}"></i>`;
            document.getElementById('notify-title').innerText = title;
            document.getElementById('notify-message').innerText = message;

            notify.classList.remove('translate-y-32', 'opacity-0');
            notify.classList.add('translate-y-0', 'opacity-100');
            setTimeout(() => {
                notify.classList.remove('translate-y-0', 'opacity-100');
                notify.classList.add('translate-y-32', 'opacity-0');
            }, 4000);
        }

        window.handleUserLogout = function() {
            activeUser = null;
            wishlist = [];
            localStorage.removeItem(STORAGE_KEYS.SESSION_USER);
            updateWishlistCountBadge();
            document.getElementById('auth-header-widget').innerHTML = `
                <button onclick="openAuthModal('signin')" class="flex items-center space-x-2 bg-ocean-500 hover:bg-ocean-600 text-pearl-100 text-sm font-bold py-2.5 px-4 rounded-xl border border-aqua-500/40 shadow-sm transition-all">
                    <i class="fa-solid fa-user-lock"></i>
                    <span>Sign In</span>
                </button>
            `;
            showNotification('Session concluded', 'Guest profile disconnected.', 'fa-solid fa-shield-halved text-ocean-500');
            renderFeaturedMenu();
            filterOrderMenu();
            
            const sumContainer = document.getElementById('user-orders-summary');
            if (sumContainer) {
                sumContainer.innerHTML = `
                    <button onclick="openProfileModal('details')" class="w-full text-center bg-pearl-100 hover:bg-beige-100 text-ocean-900 border border-beige-200 py-3 px-4 rounded-xl text-sm font-bold transition-all">
                        Open My Guest Profile
                    </button>
                `;
            }
        };

        window.openProfileModal = function(startTab = 'details') {
            const profileModal = document.getElementById('profile-modal');
            profileModal.classList.remove('hidden');
            if (!activeUser) {
                document.getElementById('profile-gate').classList.remove('hidden');
                document.getElementById('profile-active-container').classList.add('hidden');
            } else {
                document.getElementById('profile-gate').classList.add('hidden');
                document.getElementById('profile-active-container').classList.remove('hidden');
                document.getElementById('profile-username').value = activeUser.username;
                document.getElementById('profile-fullname').value = activeUser.full_name || activeUser.fullname || '';
                document.getElementById('profile-phone').value = activeUser.mobile || activeUser.phone || '';
                document.getElementById('profile-address').value = activeUser.delivery_address || activeUser.address || '';
                toggleProfileModalTab(startTab);
            }
        };

        window.closeProfileModal = function() {
            document.getElementById('profile-modal').classList.add('hidden');
            if (bookingsTimerId) {
                clearInterval(bookingsTimerId);
                bookingsTimerId = null;
            }
        };

        window.toggleProfileModalTab = function(tab) {
            if (bookingsTimerId) {
                clearInterval(bookingsTimerId);
                bookingsTimerId = null;
            }
            
            if (tab === 'details') {
                document.getElementById('profile-tab-details-btn').className = "flex-1 pb-3 text-sm font-extrabold border-b-2 border-ocean-500 text-ocean-900 flex items-center justify-center space-x-2";
                document.getElementById('profile-tab-wishlist-btn').className = "flex-1 pb-3 text-sm font-extrabold border-b-2 border-transparent text-slate-400 flex items-center justify-center space-x-2";
                document.getElementById('profile-tab-bookings-btn').className = "flex-1 pb-3 text-sm font-extrabold border-b-2 border-transparent text-slate-400 flex items-center justify-center space-x-2";
                document.getElementById('profile-details-form').classList.remove('hidden');
                document.getElementById('profile-wishlist-view').classList.add('hidden');
                document.getElementById('profile-bookings-view').classList.add('hidden');
            } else if (tab === 'wishlist') {
                document.getElementById('profile-tab-wishlist-btn').className = "flex-1 pb-3 text-sm font-extrabold border-b-2 border-ocean-500 text-ocean-900 flex items-center justify-center space-x-2";
                document.getElementById('profile-tab-details-btn').className = "flex-1 pb-3 text-sm font-extrabold border-b-2 border-transparent text-slate-400 flex items-center justify-center space-x-2";
                document.getElementById('profile-tab-bookings-btn').className = "flex-1 pb-3 text-sm font-extrabold border-b-2 border-transparent text-slate-400 flex items-center justify-center space-x-2";
                document.getElementById('profile-details-form').classList.add('hidden');
                document.getElementById('profile-wishlist-view').classList.remove('hidden');
                document.getElementById('profile-bookings-view').classList.add('hidden');
                renderProfileWishlist();
            } else if (tab === 'bookings') {
                document.getElementById('profile-tab-bookings-btn').className = "flex-1 pb-3 text-sm font-extrabold border-b-2 border-ocean-500 text-ocean-900 flex items-center justify-center space-x-2";
                document.getElementById('profile-tab-details-btn').className = "flex-1 pb-3 text-sm font-extrabold border-b-2 border-transparent text-slate-400 flex items-center justify-center space-x-2";
                document.getElementById('profile-tab-wishlist-btn').className = "flex-1 pb-3 text-sm font-extrabold border-b-2 border-transparent text-slate-400 flex items-center justify-center space-x-2";
                document.getElementById('profile-details-form').classList.add('hidden');
                document.getElementById('profile-wishlist-view').classList.add('hidden');
                document.getElementById('profile-bookings-view').classList.remove('hidden');
                renderProfileBookings();
            }
        };

        window.handleProfileUpdate = function(e) {
            e.preventDefault();
            if (!activeUser) return;
            activeUser.full_name = document.getElementById('profile-fullname').value.trim();
            activeUser.mobile = document.getElementById('profile-phone').value.trim();
            activeUser.delivery_address = document.getElementById('profile-address').value.trim();
            activeUser.fullname = activeUser.full_name;
            activeUser.phone = activeUser.mobile;
            activeUser.address = activeUser.delivery_address;

            localDB.profiles[activeUser.username] = activeUser;
            syncLocalDB(STORAGE_KEYS.PROFILES, localDB.profiles);
            localStorage.setItem(STORAGE_KEYS.SESSION_USER, JSON.stringify(activeUser));

            showNotification('Profile synchronized', 'Your personal coordinates have been updated.', 'fa-solid fa-circle-check text-ocean-500');
        };

        window.toggleWishlist = function(itemId) {
            if (!activeUser) {
                openAuthModal('signin');
                return;
            }
            const index = wishlist.indexOf(itemId);
            if (index === -1) {
                wishlist.push(itemId);
                showNotification('Favorite added', 'Saved to your profile wishlist.', 'fa-solid fa-heart text-coral-500');
            } else {
                wishlist.splice(index, 1);
                showNotification('Favorite removed', 'Removed from profile record.', 'fa-solid fa-heart-crack text-slate-400');
            }
            activeUser.wishlist = wishlist;
            localDB.profiles[activeUser.username] = activeUser;
            syncLocalDB(STORAGE_KEYS.PROFILES, localDB.profiles);
            localStorage.setItem(STORAGE_KEYS.SESSION_USER, JSON.stringify(activeUser));
            updateWishlistCountBadge();
            renderFeaturedMenu();
            filterOrderMenu();
            renderProfileWishlist();
        };

        function updateWishlistCountBadge() {
            const total = wishlist.length;
            const badge = document.getElementById('wishlist-count');
            if (badge) {
                badge.innerText = total;
                badge.className = total > 0 ? "absolute -top-1.5 -right-1.5 bg-coral-500 text-white font-extrabold text-[10px] w-5 h-5 rounded-full flex items-center justify-center border-2 border-ocean-955 transition-all scale-100 shadow-md font-sans" : "scale-0";
            }
        }

        function renderProfileWishlist() {
            const container = document.getElementById('profile-wishlist-items-container');
            if (!container) return;
            container.innerHTML = '';
            if (wishlist.length === 0) {
                container.innerHTML = `<p class="text-sm text-slate-400 col-span-full text-center py-4 italic font-sans">Your wishlist is currently empty.</p>`;
                return;
            }
            wishlist.forEach(itemId => {
                const item = menuItems.find(m => m.id === itemId);
                if (item) {
                    container.innerHTML += `
                        <div class="bg-pearl-100 p-4 rounded-2xl border border-beige-200 flex justify-between items-center shadow-sm">
                            <div>
                                <span class="text-sm font-extrabold text-ocean-900 block">${item.title}</span>
                                <span class="text-xs font-bold text-coral-500 font-mono">LKR ${item.price.toLocaleString('en-US')}</span>
                            </div>
                            <button onclick="closeProfileModal(); triggerCustomizer('${item.id}')" class="text-aqua-500 hover:text-ocean-500 text-sm p-2 bg-white rounded-xl border border-beige-200 shadow-xs"><i class="fa-solid fa-cart-plus"></i></button>
                        </div>
                    `;
                }
            });
        }

        function renderUserOrdersDropdown() {
            const sumContainer = document.getElementById('user-orders-summary');
            if (!sumContainer || !activeUser) return;

            const userOrders = localDB.orders.filter(o => o.username === activeUser.username);
            if (userOrders.length === 0) {
                sumContainer.innerHTML = `
                    <p class="text-xs text-slate-400 italic mb-3">No active orders found.</p>
                    <button onclick="openProfileModal('details')" class="w-full text-center bg-pearl-100 hover:bg-beige-100 text-ocean-900 border border-beige-200 py-3 px-4 rounded-xl text-sm font-bold transition-all">
                        Open My Guest Profile
                    </button>
                `;
                return;
            }

            let dropdownHTML = `
                <div class="space-y-2 mb-3 font-sans">
                    <label class="block text-[10px] text-slate-400 font-bold tracking-wider uppercase">Quick Track History</label>
                    <select id="user-orders-select" onchange="selectTrackingOrder(this.value)" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 rounded-xl py-2.5 px-3.5 text-xs focus:outline-none focus:border-ocean-500 font-bold">
                        <option value="">Select an order to track...</option>
                        ${userOrders.map(o => `<option value="${o.id}">${o.id} - ${o.status} (LKR ${o.total.toFixed(0)})</option>`).join('')}
                    </select>
                </div>
            `;
            sumContainer.innerHTML = dropdownHTML;
        }

        window.selectTrackingOrder = function(orderId) {
            if (!orderId) return;
            document.getElementById('order-lookup-input').value = orderId;
            handleOrderLookup();
        };

        window.handleOrderLookup = function() {
            const inputVal = document.getElementById('order-lookup-input').value.trim().toUpperCase();
            if (!inputVal) {
                showNotification('Ticket code empty', 'Please provide a valid ticket code.', 'fa-solid fa-circle-exclamation text-coral-500');
                return;
            }

            const foundOrder = localDB.orders.find(o => o.id.toUpperCase() === inputVal);
            if (!foundOrder) {
                showNotification('Ticket not found', 'No records match this code.', 'fa-solid fa-circle-exclamation text-coral-500');
                return;
            }

            renderOrderTracking(foundOrder);
        };

        function renderOrderTracking(order) {
            document.getElementById('tracker-empty-state').classList.add('hidden');
            document.getElementById('active-tracker-container').classList.remove('hidden');

            document.getElementById('track-id').innerText = order.id;
            document.getElementById('track-time').innerText = `Placed: ${new Date(order.timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;
            document.getElementById('track-payment').innerText = `Method: ${order.paymentMethod === 'COD' ? 'Cash on arrival' : 'Secure card online'}`;

            document.getElementById('track-recipient').innerText = order.customerName || order.delivery_name || order.full_name || 'Guest';
            document.getElementById('track-contact').innerText = order.phone || order.delivery_phone || order.mobile || 'No phone';
            document.getElementById('track-address').innerText = order.address || order.delivery_address || 'No address';
            document.getElementById('track-price-total').innerText = `Lkr ${order.total.toLocaleString('en-US', {minimumFractionDigits: 2})}`;

            // Initialize or update active cancellation state countdown
            startOrderCancelCountdown(order);

            const listContainer = document.getElementById('track-items-list');
            listContainer.innerHTML = order.items.map(item => `
                <div class="flex justify-between items-center bg-pearl-100 p-3 rounded-xl border border-beige-200 font-sans">
                    <div>
                        <h4 class="text-xs font-bold text-slate-800">${item.title}</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">${item.customLabel}</span>
                    </div>
                    <span class="text-xs font-mono font-bold text-ocean-900">${item.qty}x</span>
                </div>
            `).join('');

            const statusMap = {
                'Pending': 1,
                'Preparing': 2,
                'Out for Delivery': 3,
                'Delivered': 4
            };

            const activeStage = statusMap[order.status] || 1;
            const progressPercentage = ((activeStage - 1) / 3) * 100;
            const isMobile = window.innerWidth < 768;
            const progressLine = document.getElementById('tracker-progress-bar');
            
            if (isMobile) {
                progressLine.style.width = '100%';
                progressLine.style.height = `${progressPercentage}%`;
            } else {
                progressLine.style.height = '100%';
                progressLine.style.width = `${progressPercentage}%`;
            }

            for (let i = 1; i <= 4; i++) {
                const node = document.getElementById(`step-node-${i}`);
                if (i <= activeStage) {
                    node.className = "w-12 h-12 rounded-full border-4 border-pearl-100 bg-ocean-500 text-white flex items-center justify-center transition-all duration-300 shadow-md scale-110";
                } else {
                    node.className = "w-12 h-12 rounded-full border-4 border-pearl-100 bg-pearl-200 text-slate-400 flex items-center justify-center transition-all duration-300 scale-100";
                }
            }

            const riderStatus = document.getElementById('delivery-partner-status');
            if (order.status === 'Pending') {
                riderStatus.innerText = 'Verifying gourmet coastal harvest...';
            } else if (order.status === 'Preparing') {
                riderStatus.innerText = 'Chef is grilling your marine capture now.';
            } else if (order.status === 'Out for Delivery') {
                riderStatus.innerText = 'Rider is racing down Unawatuna Beach Road!';
            } else if (order.status === 'Delivered') {
                riderStatus.innerText = 'Delivered! Bon Appétit, enjoy your beachside feast!';
            } else if (order.status === 'Cancelled') {
                riderStatus.innerText = 'Order cancelled.';
            }
        }

        window.switchView = function(viewId) {
            document.querySelectorAll('.view-panel').forEach(panel => panel.classList.add('hidden'));
            
            if (activeTrackingTimerId) {
                clearInterval(activeTrackingTimerId);
                activeTrackingTimerId = null;
            }

            const targetPanel = document.getElementById('view-' + viewId);
            if (targetPanel) {
                targetPanel.classList.remove('hidden');
            }

            document.querySelectorAll('nav a').forEach(el => {
                el.className = "text-slate-200 hover:text-aqua-200 transition-colors py-1.5 border-b-2 border-transparent";
            });
            const activeNav = document.getElementById('nav-' + viewId);
            if (activeNav) {
                activeNav.className = "text-aqua-200 font-bold transition-colors py-1.5 border-b-2 border-aqua-500";
            }

            if (viewId === 'coverage') {
                initLeafletMap();
                setTimeout(() => {
                    if (leafletMap) {
                        leafletMap.invalidateSize();
                    }
                }, 120);
            }
        };

        window.openAuthModal = function(initialForm = 'signin') {
            document.getElementById('auth-modal').classList.remove('hidden');
            toggleAuthForm(initialForm);
        };

        window.closeAuthModal = function() {
            document.getElementById('auth-modal').classList.add('hidden');
        };

        window.handleAutofillTrigger = function(username) {
            const cleanedUsername = username.trim().toLowerCase();
            if (localDB.profiles[cleanedUsername]) {
                const profile = localDB.profiles[cleanedUsername];
                document.getElementById('cust-name').value = profile.full_name || profile.fullname || '';
                document.getElementById('cust-phone').value = profile.mobile || profile.phone || '';
                document.getElementById('cust-address').value = profile.delivery_address || profile.address || '';
                
                document.getElementById('autofill-notif').classList.remove('hidden');
                document.getElementById('cart-secure-badge').innerHTML = `<i class="fa-solid fa-lock text-emerald-500 mr-1"></i>autofill synchronized`;
            } else {
                document.getElementById('autofill-notif').classList.add('hidden');
                document.getElementById('cart-secure-badge').innerHTML = `<i class="fa-solid fa-lock text-coral-500 mr-1"></i>autofill inactive`;
            }
        };

        window.toggleMobileMenu = function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        };

        window.togglePaymentForm = function(method) {
            selectedPaymentMethod = method;
            highlightPaymentOption(method);
            document.getElementById('secure-card-form').className = method === 'Card' ? 'space-y-3 bg-pearl-100 p-4 rounded-xl border border-beige-200 shadow-inner' : 'hidden';
        };

        function highlightPaymentOption(method) {
            const codLabel = document.getElementById('pay-opt-cod-label');
            const cardLabel = document.getElementById('pay-opt-card-label');
            if (method === 'COD') {
                codLabel.className = 'cursor-pointer border border-ocean-500 rounded-xl p-3 flex items-center justify-center space-x-1.5 bg-ocean-50 text-ocean-900 font-extrabold';
                cardLabel.className = 'cursor-pointer border border-beige-200 rounded-xl p-3 flex items-center justify-center space-x-1.5 bg-white hover:bg-pearl-100 transition-all text-slate-500 shadow-sm';
            } else {
                cardLabel.className = 'cursor-pointer border border-ocean-500 rounded-xl p-3 flex items-center justify-center space-x-1.5 bg-ocean-50 text-ocean-900 font-extrabold';
                codLabel.className = 'cursor-pointer border border-beige-200 rounded-xl p-3 flex items-center justify-center space-x-1.5 bg-white hover:bg-pearl-100 transition-all text-slate-500 shadow-sm';
            }
        }

        window.formatCardNumber = function(input) {
            let val = input.value.replace(/\D/g, '');
            let formatted = '';
            for (let i = 0; i < val.length; i++) {
                if (i > 0 && i % 4 === 0) formatted += ' ';
                formatted += val[i];
            }
            input.value = formatted;
        };

        window.handleAuthSignIn = function(e) {
            e.preventDefault();
            const username = document.getElementById('signin-username').value.trim().toLowerCase();
            const pass = document.getElementById('signin-password').value;

            const loginForm = new URLSearchParams({
                username: username,
                password: pass
            });

            fetch('api/db.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: loginForm.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (!data.ok) {
                    showNotification('Access Restrained', data.message || 'Invalid secure credentials supplied.', 'fa-solid fa-triangle-exclamation text-coral-500');
                    return;
                }

                activeUser = data.user;
                wishlist = [];
                localStorage.setItem(STORAGE_KEYS.SESSION_USER, JSON.stringify(activeUser));
                updateWishlistCountBadge();

                document.getElementById('cust-name').value = activeUser.full_name || activeUser.fullname || '';
                document.getElementById('cust-phone').value = activeUser.mobile || activeUser.phone || '';
                document.getElementById('cust-address').value = activeUser.delivery_address || activeUser.address || '';
                document.getElementById('signup-name').value = activeUser.full_name || activeUser.fullname || '';
                document.getElementById('signup-phone').value = activeUser.mobile || activeUser.phone || '';
                document.getElementById('signup-address').value = activeUser.delivery_address || activeUser.address || '';

                document.getElementById('auth-header-widget').innerHTML = `
                    <div class="flex items-center space-x-3 bg-ocean-900 border border-ocean-800 px-3 py-1.5 rounded-xl shadow-sm font-sans">
                        <span class="w-2.5 h-2.5 bg-aqua-500 rounded-full"></span>
                        <button onclick="openProfileModal('details')" class="text-xs text-pearl-100 font-bold font-mono hover:text-aqua-500">@${activeUser.username}</button>
                        <button onclick="handleUserLogout()" class="text-slate-400 hover:text-coral-500"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>
                    </div>
                `;
                closeAuthModal();
                showSuccessConfirmation('Welcome back!', `Authorized session activated for ${activeUser.full_name || activeUser.fullname || activeUser.username}.`);
                renderFeaturedMenu();
                filterOrderMenu();
                renderUserOrdersDropdown();
            })
            .catch(() => {
                showNotification('Access Restrained', 'Unable to reach the server.', 'fa-solid fa-triangle-exclamation text-coral-500');
            });
        };

        window.closeSuccessConfirmationModal = function() {
            document.getElementById('success-confirmation-modal').classList.add('hidden');
        };

        window.triggerCustomizer = function(itemId) {
            const foundItem = menuItems.find(item => item.id === itemId);
            if (!foundItem || foundItem.status === 'Sold Out') return;
            activeCustomizingItem = foundItem;
            activeCustomizationsSelected = [];

            document.getElementById('cust-item-title').innerText = `Customize ${foundItem.title}`;
            document.getElementById('cust-item-desc').innerText = `Base Price: LKR ${foundItem.price.toLocaleString('en-US', {minimumFractionDigits: 2})}`;

            const choicesContainer = document.getElementById('custom-choices-container');
            choicesContainer.innerHTML = '';

            const options = foundItem.customOptions || [{ name: 'Extra Portion Size', price: 300.00 }];
            options.forEach(opt => {
                choicesContainer.innerHTML += `
                    <label class="flex items-center justify-between p-3.5 bg-pearl-100 border border-beige-200 rounded-xl cursor-pointer hover:bg-beige-100 transition-colors">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" onchange="toggleCustomOption('${opt.name}', ${opt.price}, this)" class="rounded text-ocean-500 focus:ring-ocean-500 bg-white border-beige-200 w-4 h-4">
                            <span class="text-xs sm:text-sm font-bold text-slate-700">${opt.name}</span>
                        </div>
                        <span class="text-xs font-black text-coral-500 font-mono">+ LKR ${opt.price.toLocaleString('en-US')}</span>
                    </label>
                `;
            });

            document.getElementById('cust-live-total').innerText = `LKR ${foundItem.price.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
            document.getElementById('btn-confirm-customization').onclick = confirmAndAddToCart;
            document.getElementById('customize-modal').classList.remove('hidden');
        };

        window.toggleCustomOption = function(name, price, checkbox) {
            if (checkbox.checked) {
                activeCustomizationsSelected.push({ name: name, price: price });
            } else {
                activeCustomizationsSelected = activeCustomizationsSelected.filter(opt => opt.name !== name);
            }
            let total = activeCustomizingItem.price;
            activeCustomizationsSelected.forEach(opt => total += opt.price);
            document.getElementById('cust-live-total').innerText = `LKR ${total.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        };

        function confirmAndAddToCart() {
            if (!activeCustomizingItem) return;
            const customLabels = activeCustomizationsSelected.map(opt => opt.name);
            const extraCost = activeCustomizationsSelected.reduce((sum, opt) => sum + opt.price, 0);
            const customKey = activeCustomizingItem.id + '_' + customLabels.join('-');

            const existingCartItem = cart.find(ci => ci.cartId === customKey);
            if (existingCartItem) {
                existingCartItem.quantity++;
            } else {
                cart.push({
                    cartId: customKey,
                    itemId: activeCustomizingItem.id,
                    title: activeCustomizingItem.title,
                    price: activeCustomizingItem.price,
                    customLabel: customLabels.join(', ') || 'Original Portion',
                    extraPayments: extraCost,
                    quantity: 1
                });
            }
            updateCartBadge();
            closeCustomizeModal();
            playChime(523.25, 'sine', 0.2);
            showNotification('Choices saved', `${activeCustomizingItem.title} added to your basket.`, 'fa-solid fa-basket-shopping text-ocean-500');
        }

        window.closeCustomizeModal = function() {
            document.getElementById('customize-modal').classList.add('hidden');
            activeCustomizingItem = null;
            activeCustomizationsSelected = [];
        };

        window.handleAuthSignUp = function(e) {
            e.preventDefault();
            const username = document.getElementById('signup-username').value.trim().toLowerCase();
            const pass = document.getElementById('signup-password').value;
            const name = document.getElementById('signup-name').value.trim();
            const phone = document.getElementById('signup-phone').value.trim();
            const address = document.getElementById('signup-address').value.trim();

            const registerForm = new URLSearchParams({
                username: username,
                full_name: name,
                mobile: phone,
                password: pass,
                delivery_address: address
            });

            fetch('api/db.php?action=register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: registerForm.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (!data.ok) {
                    showNotification('Registration failed', data.message || 'Unable to create account.', 'fa-solid fa-triangle-exclamation text-coral-500');
                    return;
                }

                localDB.profiles[username] = {
                    username: username,
                    full_name: name,
                    fullname: name,
                    mobile: phone,
                    phone: phone,
                    delivery_address: address,
                    address: address,
                    passwordHash: secureHash(pass),
                    wishlist: []
                };
                syncLocalDB(STORAGE_KEYS.PROFILES, localDB.profiles);
                showSuccessConfirmation('Registration Complete!', 'Secure guest account created successfully.');
                toggleAuthForm('signin');
                document.getElementById('signin-username').value = username;
                handleAutofillTrigger(username);
            })
            .catch(() => {
                showNotification('Registration failed', 'Unable to reach the server.', 'fa-solid fa-triangle-exclamation text-coral-500');
            });
        };

        function secureHash(input) {
            return btoa(input).split('').reverse().join('');
        }

        function getCurrentUser() {
            return JSON.parse(localStorage.getItem(STORAGE_KEYS.SESSION_USER) || 'null');
        }

        function syncLocalDB(key, data) {
            localStorage.setItem(key, JSON.stringify(data));
        }

        function submitOrderToDatabase(orderPayload) {
            const orderForm = new URLSearchParams();
            Object.entries(orderPayload).forEach(([key, value]) => {
                if (Array.isArray(value)) {
                    orderForm.append(key, JSON.stringify(value));
                } else {
                    orderForm.append(key, value ?? '');
                }
            });

            return fetch('api/db.php?action=place_order', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: orderForm.toString()
            }).then(response => response.json());
        }

        function toggleAuthForm(mode) {
            const isSignin = mode === 'signin';
            document.getElementById('auth-toggle-signin').className = isSignin ? 'flex-grow pb-3 text-sm font-bold border-b-2 border-ocean-500 text-ocean-900' : 'flex-grow pb-3 text-sm font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-600';
            document.getElementById('auth-toggle-signup').className = !isSignin ? 'flex-grow pb-3 text-sm font-bold border-b-2 border-ocean-500 text-ocean-900' : 'flex-grow pb-3 text-sm font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-600';
            document.getElementById('auth-form-signin').className = isSignin ? 'space-y-4' : 'hidden';
            document.getElementById('auth-form-signup').className = !isSignin ? 'space-y-4' : 'hidden';
        }

        function showSuccessConfirmation(title, text) {
            document.getElementById('confirm-modal-icon').className = "fa-solid fa-circle-check text-emerald-500";
            document.getElementById('confirm-modal-title').innerText = title;
            document.getElementById('confirm-modal-message').innerText = text;
            document.getElementById('success-confirmation-modal').classList.remove('hidden');
        }

        function initLeafletMap() {
            if (leafletMap) return;
            try {
                // Centering map coordinates around Unawatuna beachfront
                leafletMap = L.map('delivery-map').setView([6.0092, 80.2484], 12);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(leafletMap);

                // Add delivery zone circle highlight (15km radius)
                L.circle([6.0092, 80.2484], {
                    color: '#005f99',
                    fillColor: '#4cc9f0',
                    fillOpacity: 0.15,
                    radius: 15000 // 15km
                }).addTo(leafletMap);

                // Landmark tags
                L.marker([6.0092, 80.2484]).addTo(leafletMap).bindPopup('<b>Sea Pearl Bistro</b><br>Our beachfront culinary escape.').openPopup();
                L.marker([6.0263, 80.2163]).addTo(leafletMap).bindPopup('<b>Galle Fort</b><br>Heritage outpost.');
                L.marker([5.9995, 80.2796]).addTo(leafletMap).bindPopup('<b>Thalpe</b><br>Beachfront villa sector.');
                L.marker([5.9722, 80.4286]).addTo(leafletMap).bindPopup('<b>Weligama</b><br>Surf front boundary.');
            } catch (err) {
                console.log("Leaflet resources could not construct map layers at this time.", err);
            }
        }

        window.toggleGuestSimulator = function() {
            const isChecked = document.getElementById('simulator-toggle').checked;
            if (isChecked) {
                playChime(659.25, 'sine', 0.4);
                showNotification('Simulation active', 'Tourist stream is now active! Guest orders will generate periodically.', 'fa-solid fa-circle-nodes text-ocean-500');
                guestSimulatorInterval = setInterval(triggerSimulatedOrderAndBooking, 14000);
            } else {
                playChime(329.63, 'sine', 0.3);
                showNotification('Simulation suspended', 'Tourist traffic deactivated.', 'fa-solid fa-circle-pause text-slate-500');
                clearInterval(guestSimulatorInterval);
            }
        };

        function triggerSimulatedOrderAndBooking() {
            const guest = SIMULATED_GUESTS[Math.floor(Math.random() * SIMULATED_GUESTS.length)];
            const dice = Math.random();

            if (dice > 0.4) {
                const orderId = 'ORD-' + Math.floor(1000 + Math.random() * 9000);
                const dishesCount = Math.floor(Math.random() * 2) + 1;
                let itemsList = [];
                let subtotal = 0;
                for (let i = 0; i < dishesCount; i++) {
                    const dish = menuItems[Math.floor(Math.random() * menuItems.length)];
                    itemsList.push({
                        title: dish.title,
                        qty: 1,
                        customLabel: 'Chef Fresh Harvest Selection'
                    });
                    subtotal += dish.price;
                }
                const total = subtotal * 1.10 + 300;

                const simulatedOrder = {
                    id: orderId,
                    customerName: guest.name,
                    username: guest.username,
                    phone: guest.phone,
                    address: guest.address,
                    paymentMethod: Math.random() > 0.5 ? 'Card' : 'COD',
                    items: itemsList,
                    total: total,
                    status: 'Pending',
                    timestamp: new Date().toISOString()
                };

                localDB.orders.push(simulatedOrder);
                syncLocalDB(STORAGE_KEYS.ORDERS, localDB.orders);
                playChime(523.25, 'triangle', 0.5);
                showNotification('Simulated order placed', `New order ${orderId} received from simulated guest ${guest.name}.`, 'fa-solid fa-cloud-arrow-down text-emerald-500');
                
                if (document.getElementById('admin-tab-content-orders').offsetParent !== null) {
                    renderAdminOrders();
                }
            } else {
                const bkId = 'BK-' + Math.floor(100 + Math.random() * 900);
                const tableId = Math.floor(Math.random() * 8) + 1;

                if (localDB.bookings.some(b => b.tableNum == tableId && b.status === 'Approved')) return;

                const simulatedBooking = {
                    id: bkId,
                    name: guest.name,
                    username: guest.username,
                    email: `${guest.username}@hotelresort.com`,
                    date: new Date().toISOString().slice(0, 10),
                    time: "7:30 PM",
                    guests: "4 People",
                    pref: tableId <= 4 ? "Sunset Beach Deck" : "Private Sand Lounge",
                    tableNum: tableId,
                    notes: "Generated by the automated tourism simulator.",
                    status: 'Approved',
                    timestamp: new Date().toISOString()
                };

                localDB.bookings.push(simulatedBooking);
                syncLocalDB(STORAGE_KEYS.BOOKINGS, localDB.bookings);
                playChime(587.33, 'triangle', 0.3);
                showNotification('Simulated table booked', `${guest.name} reserved spot via the map.`, 'fa-solid fa-chair text-aqua-500');

                if (document.getElementById('admin-tab-content-bookings').offsetParent !== null) {
                    renderAdminBookings();
                }
            }
            updateStats();
        }

        window.onload = function() {
            renderFeaturedMenu();
            renderCategoryFilters();
            renderOrderCategories();
            filterOrderMenu();
            highlightPaymentOption('COD');
            
            if (activeUser) {
                document.getElementById('auth-header-widget').innerHTML = `
                    <div class="flex items-center space-x-3 bg-ocean-900 border border-ocean-800 px-4 py-2 rounded-xl">
                        <span class="w-2.5 h-2.5 bg-aqua-500 rounded-full"></span>
                        <button onclick="openProfileModal('details')" class="text-xs text-pearl-100 font-bold font-mono hover:text-aqua-500">@${activeUser.username}</button>
                        <button onclick="handleUserLogout()" class="text-slate-400 hover:text-coral-500 transition-colors"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>
                    </div>
                `;
                document.getElementById('cust-name').value = activeUser.full_name || activeUser.fullname || '';
                document.getElementById('cust-phone').value = activeUser.mobile || activeUser.phone || '';
                document.getElementById('cust-address').value = activeUser.delivery_address || activeUser.address || '';

                updateWishlistCountBadge();
                renderUserOrdersDropdown();
            }
            
            // Default date restriction logic
            const dateInput = document.getElementById('book-date');
            if (dateInput) {
                const today = new Date().toISOString().split('T')[0];
                dateInput.min = today; 
            }

            switchView('home');
        };
