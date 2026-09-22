<?php

include_once 'config/db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? null;

?>
<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sea Pearl Bistro | Online Ordering &amp; Table Reservation</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
          theme: {
            extend: {
              colors: {
                ocean: {
                  50: '#f0f7fc',
                  100: '#e1eef7',
                  200: '#bce0f4',
                  500: '#005f99', 
                  600: '#004c7b',
                  900: '#003050',
                  955: '#001a2e', 
                },
                aqua: {
                  100: '#e1f8ff',
                  200: '#bbf0ff',
                  500: '#4cc9f0', 
                  600: '#23bfe0',
                },
                pearl: {
                  50: '#fcfdfe',
                  100: '#f8f9fa', 
                  200: '#f0f2f5',
                },
                coral: {
                  100: '#ffeceb',
                  500: '#ff6f61', 
                  600: '#ff4b3a',
                },
                beige: {
                  100: '#fdfbf7',
                  200: '#ead7b7', 
                  300: '#dcc194',
                }
              },
            },
          },
        }
    </script>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=300;400;500;600;700;800&family=Playfair+Display:ital,wght=0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Leaflet Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <link rel="stylesheet" href="css/style.css">

</head>
<body class="bg-pearl-100 text-ocean-955 antialiased flex flex-col min-h-full font-sans">

    <header id="main-app-header" class="sticky top-0 z-40 bg-ocean-955 border-b border-ocean-900/60 shadow-lg transition-all duration-300">
        <div class="w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4 h-22 flex items-center justify-between">
            <a href="#" onclick="switchView('home')" class="flex items-center space-x-3 group">
                <div class="w-12 h-12 bg-gradient-to-tr from-ocean-500 via-aqua-500 to-coral-500 rounded-xl flex items-center justify-center text-white font-black shadow-md shadow-ocean-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="seaLogoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#005f99" />
                                <stop offset="100%" stop-color="#4cc9f0" />
                            </linearGradient>
                        </defs>
                        <circle cx="12" cy="12" r="10" fill="url(#seaLogoGrad)" />
                        <path d="M4 14C7.5 11 10.5 15 14 13C17.5 11 19 8.5 20 8" stroke="#f8f9fa" stroke-width="1.2" stroke-linecap="round" opacity="0.3"/>
                        <path d="M3 16C6 14 8 18 12 16C16 14 19 11 21 11" stroke="#4cc9f0" stroke-width="1.2" stroke-linecap="round" opacity="0.6"/>
                        <path d="M2.5 14.5C6 12.5 8.5 15 12 13.5C15.5 12 18.5 8.5 20.5 8C19 11 16.5 15 12.5 16.5C8.5 18 5 16 2.5 14.5Z" fill="#005f99" />
                        <circle cx="12" cy="11" r="3" fill="#f8f9fa" stroke="#ff6f61" stroke-width="1" />
                        <circle cx="11.2" cy="10.2" r="0.8" fill="#ffffff" />
                    </svg>
                </div>
                <div class="text-left">
                    <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-pearl-100 via-aqua-100 to-beige-200 bg-clip-text text-transparent">Sea Pearl Bistro</span>
                    <span class="block text-xs text-aqua-200 tracking-widest font-semibold">Unawatuna Reef Sanctuary</span>
                </div>
            </a>

            <!-- Nav Links -->
            <nav class="hidden lg:flex items-center space-x-7 text-base font-semibold">
                <a href="#" id="nav-home" onclick="switchView('home')" class="text-slate-200 hover:text-aqua-200 transition-colors py-1.5 border-b-2 border-transparent">Home</a>
                <a href="#" id="nav-about" onclick="switchView('about')" class="text-slate-200 hover:text-aqua-200 transition-colors py-1.5 border-b-2 border-transparent">About Us</a>
                <a href="#" id="nav-order-online" onclick="switchView('order-online')" class="text-slate-200 hover:text-aqua-200 transition-colors py-1.5 border-b-2 border-transparent">Order Online</a>
                <a href="#" id="nav-coverage" onclick="switchView('coverage')" class="text-slate-200 hover:text-aqua-200 transition-colors py-1.5 border-b-2 border-transparent">Delivery Coverage</a>
                <a href="#" id="nav-book" onclick="switchView('book')" class="text-slate-200 hover:text-aqua-200 transition-colors py-1.5 border-b-2 border-transparent">Reserve Table</a>
                <a href="#" id="nav-orders" onclick="switchView('orders')" class="text-slate-200 hover:text-aqua-200 transition-colors py-1.5 border-b-2 border-transparent">Track Order</a>
                <a href="#" id="nav-contact" onclick="switchView('contact')" class="text-slate-200 hover:text-aqua-200 transition-colors py-1.5 border-b-2 border-transparent">Get In Touch</a>
            </nav>

            <!-- Action Widgets -->
            <div class="flex items-center space-x-4">
                <div id="auth-header-widget" class="flex items-center">
                    <button onclick="openAuthModal('signin')" class="flex items-center space-x-2 bg-ocean-500 hover:bg-ocean-600 text-pearl-100 text-sm font-bold py-2.5 px-4 rounded-xl border border-aqua-500/40 shadow-sm transition-all">
                        <i class="fa-solid fa-user-lock"></i>
                        <span>Sign In</span>
                    </button>
                </div>

                <button onclick="openProfileModal('wishlist')" class="relative p-2.5 bg-ocean-900/40 hover:bg-ocean-900 text-slate-300 hover:text-coral-500 rounded-xl transition-all flex items-center justify-center border border-ocean-800" title="Saved Favorites">
                    <i class="fa-solid fa-heart text-lg"></i>
                    <span id="wishlist-count" class="absolute -top-1.5 -right-1.5 bg-coral-500 text-white font-extrabold text-[10px] w-5 h-5 rounded-full flex items-center justify-center border border-ocean-955 transition-all scale-0 font-sans">0</span>
                </button>

                <button onclick="toggleCart()" class="relative p-2.5 bg-ocean-900/40 hover:bg-ocean-900 text-slate-300 hover:text-aqua-500 rounded-xl transition-all flex items-center justify-center border border-ocean-800" title="Your Orders">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                    <span id="cart-count" class="absolute -top-1.5 -right-1.5 bg-aqua-500 text-ocean-955 font-extrabold text-[10px] w-5 h-5 rounded-full flex items-center justify-center border border-ocean-955 transition-all scale-0 font-sans">0</span>
                </button>

                <button onclick="window.location.href='admin.php'" class="hidden sm:flex items-center space-x-2 bg-gradient-to-r from-ocean-500 to-aqua-500 text-white text-sm font-bold py-2.5 px-4 rounded-xl shadow-md transition-all border border-aqua-200/20 hover:brightness-110" title="Staff Workspace">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Management Hub</span>
                </button>

                <button id="mobile-menu-btn" onclick="toggleMobileMenu()" class="lg:hidden text-slate-200 hover:text-aqua-500 focus:outline-none p-2.5 rounded-lg bg-ocean-900/50">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden lg:hidden bg-ocean-955 border-b border-ocean-900 py-4 px-6 space-y-3 shadow-2xl">
            <a href="#" onclick="switchView('home'); toggleMobileMenu();" class="block py-2 text-slate-200 hover:text-aqua-500 font-bold border-b border-ocean-900/60">Home</a>
            <a href="#" onclick="switchView('about'); toggleMobileMenu();" class="block py-2 text-slate-200 hover:text-aqua-500 font-bold border-b border-ocean-900/60">About Us</a>
            <a href="#" onclick="switchView('order-online'); toggleMobileMenu();" class="block py-2 text-slate-200 hover:text-aqua-500 font-bold border-b border-ocean-900/60">Order Online</a>
            <a href="#" onclick="switchView('coverage'); toggleMobileMenu();" class="block py-2 text-slate-200 hover:text-aqua-500 font-bold border-b border-ocean-900/60">Delivery Coverage</a>
            <a href="#" onclick="switchView('book'); toggleMobileMenu();" class="block py-2 text-slate-200 hover:text-aqua-500 font-bold border-b border-ocean-900/60">Reserve Table</a>
            <a href="#" onclick="switchView('orders'); toggleMobileMenu();" class="block py-2 text-slate-200 hover:text-aqua-500 font-bold border-b border-ocean-900/60">Track Order</a>
            <a href="#" onclick="switchView('contact'); toggleMobileMenu();" class="block py-2 text-slate-200 hover:text-aqua-500 font-bold border-b border-ocean-900/60">Get In Touch</a>
            <button onclick="window.location.href='admin.php';" class="w-full mt-2 py-3 text-center bg-gradient-to-r from-ocean-500 to-aqua-500 text-white font-extrabold rounded-xl text-sm flex items-center justify-center space-x-2">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Management Hub</span>
            </button>
        </div>
    </header>

    <main class="flex-grow">
        <section id="view-home" class="view-panel">
            <div class="relative overflow-hidden py-24 bg-cover bg-center border-b border-beige-200" style="background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=80');">
                <div class="absolute inset-0 bg-gradient-to-r from-ocean-955/95 via-ocean-900/80 to-ocean-900/40"></div>
                
                <div class="w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4 relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                    <div class="lg:col-span-7 text-left">
                        <span class="text-aqua-500 font-bold tracking-widest text-sm block mb-2 uppercase">Unawatuna reef sanctuary</span>
                        <h1 class="serif-font text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-white mb-6 leading-tight">
                            Exquisite sea flavors, <br><span class="bg-gradient-to-r from-aqua-500 via-pearl-100 to-coral-500 bg-clip-text text-transparent italic">interactive beachfront dining.</span>
                        </h1>
                        <p class="text-slate-300 text-base sm:text-lg mb-8 max-w-xl leading-relaxed text-justify">
                            Welcome to Unawatuna’s ultimate beachfront escape. Pick your favorite visual table directly on the sandy deck, order woodfired coastal feasts crafted with local love, and cancel or track your delicacies up to the moment they hit your lounger.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button onclick="switchView('order-online')" class="bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-white font-extrabold px-8 py-4 rounded-2xl shadow-lg shadow-ocean-500/25 transition-all flex items-center justify-center space-x-3 text-base tracking-wide">
                                <span>Order Online Now</span>
                                <i class="fa-solid fa-arrow-right text-sm"></i>
                            </button>
                            <button onclick="switchView('book')" class="bg-white/10 hover:bg-white/20 border border-white/20 text-white font-extrabold px-8 py-4 rounded-2xl transition-all flex items-center justify-center space-x-3 text-base tracking-wide">
                                <i class="fa-solid fa-map-location-dot text-sm text-coral-500"></i>
                                <span>Interactive Table Bookings</span>
                            </button>
                        </div>
                    </div>
                    <div class="lg:col-span-5 relative flex justify-center">
                        <div class="absolute -inset-4 bg-gradient-to-tr from-ocean-500 via-aqua-500 to-coral-500 rounded-3xl opacity-20 blur-2xl"></div>
                        <img src="https://media.istockphoto.com/id/1316145932/photo/table-top-view-of-spicy-food.webp?a=1&b=1&s=612x612&w=0&k=20&c=scseGeDCjSghwD2RELSaaT2Pn2NQz0gflEQ4BuiTSjs=" alt="Fresh Sea Pearl Bistro Dishes" class="relative rounded-[2rem] border-2 border-white/15 shadow-2xl max-h-[420px] w-full object-cover">
                    </div>
                </div>
            </div>

            <!-- BEACHFRONT SPECIAL OFFERS -->
            <div class="bg-gradient-to-b from-pearl-50 to-white py-16 border-b border-beige-200/80">
                <div class="w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4">
                    <div class="text-left mb-8">
                        <span class="text-coral-500 font-extrabold tracking-wider text-sm block">Exclusive Coastal Discounts</span>
                        <h2 class="serif-font text-3xl sm:text-4xl font-bold mt-1 text-ocean-900">Special Beachfront Offers & Promos</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="bg-white p-6 rounded-2xl border border-beige-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all text-left">
                            <div>
                                <span class="bg-coral-100 text-coral-500 text-xs font-black px-3 py-1 rounded-md">Happy Hour Special</span>
                                <h3 class="serif-font text-xl font-bold mt-3 mb-2 text-ocean-900">Sandy Sunset Cocktails</h3>
                                <p class="text-slate-500 text-sm sm:text-base text-justify leading-relaxed mb-4">Enjoy 20% off on all mocktails and ocean starters between 5:00 PM & 7:00 PM daily.</p>
                            </div>
                            <div class="bg-pearl-100 border border-beige-200/80 p-3 rounded-xl flex items-center justify-between">
                                <span class="font-mono text-sm font-bold text-slate-500">Code: <strong class="text-ocean-900 text-sm">SUNSET20</strong></span>
                                <button onclick="copyToClipboard('SUNSET20')" class="bg-ocean-500 hover:bg-ocean-600 text-white font-extrabold text-xs py-1.5 px-3 rounded-lg transition-all shadow-xs flex items-center space-x-1">
                                    <i class="fa-solid fa-copy"></i> <span>Copy</span>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-beige-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all text-left">
                            <div>
                                <span class="bg-aqua-100 text-ocean-900 text-xs font-black px-3 py-1 rounded-md">Gastronomy Coupon</span>
                                <h3 class="serif-font text-xl font-bold mt-3 mb-2 text-ocean-900">Free Pearl Shell Dessert</h3>
                                <p class="text-slate-500 text-sm sm:text-base text-justify leading-relaxed mb-4">Receive a free coconut custard plate with every premium lobster or crab entrée checkout.</p>
                            </div>
                            <div class="bg-pearl-100 border border-beige-200/80 p-3 rounded-xl flex items-center justify-between">
                                <span class="font-mono text-sm font-bold text-slate-500">Code: <strong class="text-ocean-900 text-sm">FREEPEARL</strong></span>
                                <button onclick="copyToClipboard('FREEPEARL')" class="bg-ocean-500 hover:bg-ocean-600 text-white font-extrabold text-xs py-1.5 px-3 rounded-lg transition-all shadow-xs flex items-center space-x-1">
                                    <i class="fa-solid fa-copy"></i> <span>Copy</span>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-beige-200 shadow-sm flex flex-col justify-between hover:shadow-md transition-all text-left">
                            <div>
                                <span class="bg-amber-100 text-amber-800 text-xs font-black px-3 py-1 rounded-md">Seating Discount</span>
                                <h3 class="serif-font text-xl font-bold mt-3 mb-2 text-ocean-900">Reef Feast Group Platter</h3>
                                <p class="text-slate-500 text-sm sm:text-base text-justify leading-relaxed mb-4">Book table seatings for 6+ guests on our interactive map to unlock 15% discount on checkout.</p>
                            </div>
                            <div class="bg-pearl-100 border border-beige-200/80 p-3 rounded-xl flex items-center justify-between">
                                <span class="font-mono text-sm font-bold text-slate-500">Code: <strong class="text-ocean-900 text-sm">REEFGROUP</strong></span>
                                <button onclick="copyToClipboard('REEFGROUP')" class="bg-ocean-500 hover:bg-ocean-600 text-white font-extrabold text-xs py-1.5 px-3 rounded-lg transition-all shadow-xs flex items-center space-x-1">
                                    <i class="fa-solid fa-copy"></i> <span>Copy</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Value Props -->
            <div class="bg-white py-16 border-b border-beige-200/80">
                <div class="w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4">
                    <h2 class="serif-font text-3xl font-bold text-ocean-900 mb-8 text-left">Platform Capabilities</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="bg-white p-8 rounded-2xl border border-beige-200/60 shadow-xs hover:shadow-sm transition-all flex items-start space-x-4 text-left">    
                            <div class="text-coral-500 text-3xl bg-coral-100/60 p-3 rounded-xl"><i class="fa-solid fa-compass"></i></div>
                            <div>
                                <h3 class="text-lg font-bold mb-2 text-ocean-900">Custom Visual Systems</h3>
                                <p class="text-slate-500 text-sm sm:text-base text-justify leading-relaxed">Book precise beach spots visually. Track order cooking statuses on our real-time Staff Kitchen Display screen.</p>
                            </div>
                        </div>
                        <div class="bg-white p-8 rounded-2xl border border-beige-200/60 shadow-xs hover:shadow-sm transition-all flex items-start space-x-4 text-left">
                            <div class="text-ocean-500 text-3xl bg-ocean-100 p-3 rounded-xl"><i class="fa-solid fa-motorcycle"></i></div>
                            <div>
                                <h3 class="text-lg font-bold mb-2 text-ocean-900">Order & Reservation Cancellations</h3>
                                <p class="text-slate-500 text-sm sm:text-base text-justify leading-relaxed">Change of mind? Instantly cancel orders and visual table bookings within 10 minutes of creation.</p>
                            </div>
                        </div>
                        <div class="bg-white p-8 rounded-2xl border border-beige-200/60 shadow-xs hover:shadow-sm transition-all flex items-start space-x-4 text-left">
                            <div class="text-aqua-500 text-3xl bg-aqua-100 p-3 rounded-xl"><i class="fa-solid fa-people-group"></i></div>
                            <div>
                                <h3 class="text-lg font-bold mb-2 text-ocean-900">Dynamic Traffic Generator</h3>
                                <p class="text-slate-500 text-sm sm:text-base text-justify leading-relaxed">Turn on the Auto-Guest Simulator in management to watch the bistro system dynamically self-populate and route!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SIGNATURE PLATES GRID -->
            <div class="py-16 w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10">
                    <div class="text-left">
                        <span class="text-coral-600 font-extrabold tracking-widest text-sm uppercase">Chef's Ocean Creations</span>
                        <h2 class="serif-font text-3xl sm:text-4xl font-bold mt-1 text-ocean-900 font-black">Our Signature Plates</h2>
                    </div>
                    <button onclick="switchView('order-online')" class="text-ocean-500 hover:text-aqua-600 font-extrabold flex items-center space-x-2 text-base mt-4 sm:mt-0 transition-colors">
                        <span>Browse Entire Seafood Ledger</span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8" id="featured-dishes-grid">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <!-- TASTING CLUB ATTACHED ONLY TO HOME VIEW -->
            <section class="bg-gradient-to-r from-ocean-900 to-ocean-955 text-white py-16 border-t border-ocean-900">
                <div class="w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-7 space-y-3 text-left">
                        <span class="text-coral-500 font-bold tracking-widest text-sm flex items-center space-x-2 uppercase">
                            <i class="fa-solid fa-envelope-open-text"></i>
                            <span>Tasting Club Privilege</span>
                        </span>
                        <h3 class="serif-font text-3xl font-bold text-white">Join Our Shorefront Culinary Tasting Circle</h3>
                        <p class="text-slate-300 text-sm sm:text-base max-w-xl text-justify leading-relaxed">
                            Be the first to hear about special beach sunset parties, exclusive chef dynamic menus, freshly imported rock oyster batches, and custom promo vouchers. No spam, only delicious coastal invitations.
                        </p>
                    </div>
                    <div class="lg:col-span-5">
                        <form onsubmit="handleNewsletterSubmit(event, 'footer-club-input')" class="flex flex-col sm:flex-row gap-3 max-w-md ml-auto">
                            <input type="email" id="footer-club-input" required placeholder="Enter your email address" class="flex-grow bg-white/10 border border-white/20 focus:border-aqua-500 focus:bg-white focus:text-slate-900 rounded-xl py-3 px-4 text-sm text-white placeholder-slate-400 focus:outline-none transition-all">
                            <button type="submit" class="bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-white font-extrabold py-3 px-6 rounded-xl text-sm tracking-wider transition-all shadow-md">
                                Subscribe Now
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </section>

        <section id="view-about" class="view-panel hidden py-16 w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4">
            <div class="text-center mb-12">
                <span class="text-coral-500 font-bold tracking-widest text-sm uppercase">Our Sea Heritage</span>
                <h1 class="serif-font text-4xl sm:text-5xl font-bold mt-1 text-ocean-900">Washed Ashore in Unawatuna</h1>
                <p class="text-slate-500 max-w-2xl mx-auto mt-3 text-base sm:text-lg">Blending the fresh maritime bounty of Sri Lanka with premium hospitality and modern systems.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center mb-12">
                <div class="lg:col-span-7 space-y-6 text-left">
                    <h2 class="serif-font text-3xl font-bold text-ocean-900 leading-snug">Inspired by Reef Divers, Cooked with Open Fire</h2>
                    <p class="text-slate-600 text-base sm:text-lg leading-relaxed text-justify">
                        Standing directly on the pristine beach of Unawatuna, Sea Pearl Bistro was created to honor the traditional coastal lifestyles of southern Sri Lanka. Our kitchen works closely with regional outrigger boat crews, sourcing line-caught tuna, jumbo lagoon prawns, and rock oysters directly from local waters.
                    </p>
                    <p class="text-slate-600 text-base sm:text-lg leading-relaxed text-justify">
                        Our newly deployed electronic systems now allow beachgoers and resort residents to place orders directly from loungers, visually select tables on our beach deck, and cancel bookings within ten minutes.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4">
                        <div class="bg-white border border-beige-200 p-5 rounded-xl flex items-center space-x-4 shadow-sm">
                            <div class="text-coral-500 text-3xl"><i class="fa-solid fa-water"></i></div>
                            <div>
                                <h4 class="font-extrabold text-ocean-900 text-sm">Sustainable Sourcing</h4>
                                <p class="text-xs text-slate-400 mt-0.5">100% traceably harvested sea-fare.</p>
                            </div>
                        </div>
                        <div class="bg-white border border-beige-200 p-5 rounded-xl flex items-center space-x-4 shadow-sm">
                            <div class="text-ocean-500 text-3xl"><i class="fa-solid fa-fire-burner"></i></div>
                            <div>
                                <h4 class="font-extrabold text-ocean-900 text-sm">Coconut Husk Fires</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Organic slow woodfire roasting.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-5 relative">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-ocean-500 via-aqua-500 to-coral-500 rounded-3xl opacity-15 blur-2xl"></div>
                    <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&q=80&w=800" alt="Sea Pearl Bistro Luxury Table Setup" class="relative rounded-[2rem] border border-beige-200 shadow-xl w-full object-cover h-80">
                </div>
            </div>
        </section>

        <section id="view-order-online" class="view-panel hidden w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4 py-16 bg-white rounded-3xl border border-beige-200/50 shadow-xs my-6">
            <div class="text-center mb-10">
                <span class="text-coral-500 font-bold tracking-widest text-sm uppercase">Fresh Shorefront Kitchen</span>
                <h1 class="serif-font text-4xl sm:text-5xl font-black mt-2 text-ocean-900">Order Menus</h1>
                <p class="text-slate-500 text-base sm:text-lg mt-2">Explore our delicious selection of freshly prepped beachfront dishes.</p>
            </div>

            <!-- Main Catalog Grid (Expanded to full-screen width, premium white layout) -->
            <div class="space-y-6">
                <div class="bg-pearl-100 p-5 rounded-2xl border border-beige-200 flex flex-col xl:flex-row xl:items-center justify-between gap-4 shadow-xs">
                    <div class="flex flex-wrap gap-2" id="order-categories-container"></div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative flex-grow sm:w-64">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" id="order-search-input" oninput="filterOrderMenu()" placeholder="Search feast..." class="w-full bg-white text-slate-800 placeholder-slate-400 border border-beige-200 focus:border-ocean-500 focus:outline-none rounded-xl py-2 pl-9 pr-4 text-sm transition-all shadow-2xs">
                        </div>

                        <select id="order-sort-select" onchange="filterOrderMenu()" class="bg-white border border-beige-200 rounded-xl py-2 px-3 text-sm text-slate-800 focus:outline-none focus:border-ocean-500 transition-all shadow-2xs font-semibold">
                            <option value="name-asc">Name (A-Z)</option>
                            <option value="name-desc">Name (Z-A)</option>
                            <option value="price-asc">Price (Low to High)</option>
                            <option value="price-desc">Price (High to Low)</option>
                        </select>

                        <div class="flex items-center space-x-3 bg-white border border-beige-200 py-1.5 px-3 rounded-xl shadow-2xs">
                            <label class="flex items-center space-x-1.5 cursor-pointer select-none">
                                <input type="checkbox" id="order-veg-toggle" onchange="filterOrderMenu()" class="rounded text-emerald-500 focus:ring-emerald-500 bg-white border-beige-200">
                                <span class="text-xs font-bold text-slate-600 flex items-center gap-1">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                                    <span>Veg only</span>
                                </span>
                            </label>
                            <label class="flex items-center space-x-1.5 cursor-pointer select-none border-l border-slate-100 pl-3">
                                <input type="checkbox" id="order-nonveg-toggle" onchange="filterOrderMenu()" class="rounded text-rose-500 focus:ring-rose-500 bg-white border-beige-200">
                                <span class="text-xs font-bold text-slate-600 flex items-center gap-1">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                                    <span>Non-veg only</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 4 Columns Grid Layout for Large Screens -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6" id="order-items-grid"></div>
            </div>
        </section>

        <section id="view-coverage" class="view-panel hidden py-16 w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4">
            <div class="text-center mb-12">
                <span class="text-coral-500 font-bold tracking-widest text-sm uppercase">Oceanside Boundaries</span>
                <h1 class="serif-font text-4xl sm:text-5xl font-bold mt-1 text-ocean-900">Delivery Coverage</h1>
                <p class="text-slate-500 max-w-2xl mx-auto mt-2 text-base sm:text-lg">Real-time coordinates verification for gourmet coastal transport along the southern shoreline.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-7 bg-white border border-beige-200 p-6 sm:p-8 rounded-2xl shadow-sm text-left space-y-6">
                    <h2 class="serif-font text-2xl font-extrabold text-ocean-900 tracking-tight border-b border-beige-200 pb-3">
                        Important information about your order
                    </h2>

                    <div class="space-y-2">
                        <h3 class="text-base font-bold text-ocean-900">Products & Minimum Orders</h3>
                        <ul class="list-disc pl-5 space-y-1.5 text-sm sm:text-base text-slate-600 leading-relaxed text-justify">
                            <li>Products available for delivery are listed on the website, are subject to availability and may change without prior notice.</li>
                            <li>Prices of products may change at the discretion of Sea Pearl Bistro.</li>
                            <li class="font-bold text-ocean-900">Purchases should be of a minimum value of LKR 1,500/-.</li>
                        </ul>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-base font-bold text-ocean-900">Delivery Charges, Delivery Times and Payment</h3>
                        <ul class="list-disc pl-5 space-y-1.5 text-sm sm:text-base text-slate-600 leading-relaxed text-justify">
                            <li>A flat delivery charge of LKR 300 will be added to the final pricing.</li>
                            <li>Orders are delivered daily from 10:30 a.m. to 11:30 p.m. If an order is placed after 10:30 p.m., it will be delivered the following day.</li>
                            <li>We accept Visa, Mastercard, and cash on arrival.</li>
                            <li>We do not deliver to areas outside of our verified boundary limit. Any orders received for other areas will be cancelled immediately at checkout.</li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-beige-100">
                        <h3 class="text-base font-bold text-ocean-900 mb-2">Supported Locations (Our Verified Zone)</h3>
                        <div class="grid grid-cols-2 gap-3 text-sm text-slate-600">
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Galle Fort</span>
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Unawatuna</span>
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Thalpe</span>
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Mihiripenna</span>
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Habaraduwa</span>
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Koggala</span>
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Ahangama</span>
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Weligama</span>
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Karapitiya</span>
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Dadalla</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-4">
                    <div class="bg-white border border-beige-200 p-5 rounded-2xl shadow-sm">
                        <h3 class="serif-font text-lg font-bold text-ocean-900 mb-3 text-left">Live Feeder Zone Map</h3>
                        <div id="delivery-map" class="w-full rounded-xl border border-beige-200 bg-slate-100"></div>
                        <span class="block text-xs text-slate-400 mt-2 text-justify">The highlighted area represents our 15km shoreline delivery limit. Orders requested outside this boundary cannot be delivered.</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="view-book" class="view-panel py-16 w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4 animate-fade-in">
            <div class="text-center mb-12 max-w-2xl mx-auto">
                <span class="text-coral-500 font-extrabold tracking-widest text-sm bg-coral-50 px-5 py-2 rounded-full uppercase">Your oceanside table awaits</span>
                <h1 class="serif-font text-4xl sm:text-5xl font-black mt-4 text-ocean-900 tracking-tight">Table Reservation</h1>
                <p class="text-slate-500 text-base sm:text-lg mt-2">Select your preferred beach spot on our interactive map layout and secure your dining experience instantly.</p>
            </div>

            <div class="max-w-4xl mx-auto mb-8 bg-white border border-beige-200 p-5 rounded-2xl flex flex-wrap items-center justify-center gap-8 text-base font-bold text-slate-700 shadow-2xs">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-white border border-slate-300 block"></span>
                    <span>Available</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-ocean-500 block"></span>
                    <span>Your selection</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-md bg-rose-100 border border-rose-200 block text-rose-400 flex items-center justify-center text-xs"><i class="fa-solid fa-ban"></i></span>
                    <span>Already reserved</span>
                </div>
            </div>

            <form id="table-booking-form" onsubmit="handleTableBookingSubmit(event)">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start max-w-7xl mx-auto">
                    
                    <div class="lg:col-span-8 bg-white border border-beige-200 rounded-2xl p-6 shadow-sm space-y-8">
                        <h3 class="text-lg font-bold text-ocean-900 mb-4 border-b pb-2 flex items-center gap-2">
                            <i class="fa-solid fa-map-marked-alt text-ocean-500"></i>
                            <span>Interactive beach deck layout</span>
                        </h3>

                        <div class="space-y-4 text-left">
                            <h4 class="text-sm font-black text-amber-600 flex items-center gap-2 bg-amber-50 px-3 py-1.5 rounded-lg w-fit">
                                <i class="fa-solid fa-crown"></i> VVIP Sunset Deck (Prime sea view)
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <label class="relative block cursor-pointer group">
                                    <input type="radio" name="selected_table" value="VVIP Deck T1" onclick="updateSelectedTableDisplay('VVIP Deck T1 (Ideal for 2 people - Prime sea view)')" required class="peer sr-only">
                                    <div class="p-5 rounded-xl border bg-white border-slate-200 hover:border-ocean-400 hover:bg-slate-50 text-slate-800 font-extrabold flex flex-col items-center justify-center text-base transition-all shadow-2xs peer-checked:bg-ocean-500 peer-checked:text-white peer-checked:border-ocean-600">
                                        <i class="fa-solid fa-champagne-glasses text-lg mb-1 opacity-80"></i>
                                        <span>VVIP T1</span>
                                        <span class="text-xs font-normal opacity-80 mt-1">2 seats</span>
                                    </div>
                                </label>
                                <label class="relative block cursor-pointer group">
                                    <input type="radio" name="selected_table" value="VVIP Deck T2" onclick="updateSelectedTableDisplay('VVIP Deck T2 (Ideal for 4 people - Prime sea view)')" required class="peer sr-only">
                                    <div class="p-5 rounded-xl border bg-white border-slate-200 hover:border-ocean-400 hover:bg-slate-50 text-slate-800 font-extrabold flex flex-col items-center justify-center text-base transition-all shadow-2xs peer-checked:bg-ocean-500 peer-checked:text-white peer-checked:border-ocean-600">
                                        <i class="fa-solid fa-wine-glass text-lg mb-1 opacity-80"></i>
                                        <span>VVIP T2</span>
                                        <span class="text-xs font-normal opacity-80 mt-1">4 seats</span>
                                    </div>
                                </label>
                                <div class="p-5 rounded-xl border bg-rose-50/50 border-rose-200 text-rose-400 font-extrabold flex flex-col items-center justify-center text-base cursor-not-allowed opacity-75">
                                    <i class="fa-solid fa-circle-user text-lg mb-1 opacity-60"></i>
                                    <span>VVIP T3</span>
                                    <span class="text-xs font-normal opacity-80 mt-1">Reserved</span>
                                </div>
                                <label class="relative block cursor-pointer group">
                                    <input type="radio" name="selected_table" value="VVIP Deck T4" onclick="updateSelectedTableDisplay('VVIP Deck T4 (Ideal for 6 people - Family lounge)')" required class="peer sr-only">
                                    <div class="p-5 rounded-xl border bg-white border-slate-200 hover:border-ocean-400 hover:bg-slate-50 text-slate-800 font-extrabold flex flex-col items-center justify-center text-base transition-all shadow-2xs peer-checked:bg-ocean-500 peer-checked:text-white peer-checked:border-ocean-600">
                                        <i class="fa-solid fa-people-group text-lg mb-1 opacity-80"></i>
                                        <span>VVIP T4</span>
                                        <span class="text-xs font-normal opacity-80 mt-1">6 seats</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-4 text-left">
                            <h4 class="text-sm font-black text-ocean-600 flex items-center gap-2 bg-ocean-50 px-3 py-1.5 rounded-lg w-fit">
                                <i class="fa-solid fa-umbrella-beach"></i> Beach Sand Lounge (On the sand)
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <label class="relative block cursor-pointer group">
                                    <input type="radio" name="selected_table" value="Beach Sand T1" onclick="updateSelectedTableDisplay('Beach Sand T1 (Ideal for 4 people)')" required class="peer sr-only">
                                    <div class="p-5 rounded-xl border bg-white border-slate-200 hover:border-ocean-400 hover:bg-slate-50 text-slate-800 font-extrabold flex flex-col items-center justify-center text-base transition-all shadow-2xs peer-checked:bg-ocean-500 peer-checked:text-white peer-checked:border-ocean-600">
                                        <span>Beach T1</span>
                                        <span class="text-xs font-normal opacity-80 mt-1">4 seats</span>
                                    </div>
                                </label>
                                <div class="p-5 rounded-xl border bg-rose-50/50 border-rose-200 text-rose-400 font-extrabold flex flex-col items-center justify-center text-base cursor-not-allowed opacity-75">
                                    <span>Beach T2</span>
                                    <span class="text-xs font-normal opacity-80 mt-1">Reserved</span>
                                </div>
                                <label class="relative block cursor-pointer group">
                                    <input type="radio" name="selected_table" value="Beach Sand T3" onclick="updateSelectedTableDisplay('Beach Sand T3 (Romantic canopy)')" required class="peer sr-only">
                                    <div class="p-5 rounded-xl border bg-white border-slate-200 hover:border-ocean-400 hover:bg-slate-50 text-slate-800 font-extrabold flex flex-col items-center justify-center text-base transition-all shadow-2xs peer-checked:bg-ocean-500 peer-checked:text-white peer-checked:border-ocean-600">
                                        <span>Beach T3</span>
                                        <span class="text-xs font-normal opacity-80 mt-1">2 seats</span>
                                    </div>
                                </label>
                                <label class="relative block cursor-pointer group">
                                    <input type="radio" name="selected_table" value="Beach Sand T4" onclick="updateSelectedTableDisplay('Beach Sand T4 (Group table)')" required class="peer sr-only">
                                    <div class="p-5 rounded-xl border bg-white border-slate-200 hover:border-ocean-400 hover:bg-slate-50 text-slate-800 font-extrabold flex flex-col items-center justify-center text-base transition-all shadow-2xs peer-checked:bg-ocean-500 peer-checked:text-white peer-checked:border-ocean-600">
                                        <span>Beach T4</span>
                                        <span class="text-xs font-normal opacity-80 mt-1">8 seats</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-4 text-left">
                            <h4 class="text-sm font-black text-emerald-600 flex items-center gap-2 bg-emerald-50 px-3 py-1.5 rounded-lg w-fit">
                                <i class="fa-solid fa-hotel"></i> Indoor Premium Reef (A/C luxury)
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <label class="relative block cursor-pointer group">
                                    <input type="radio" name="selected_table" value="Indoor Reef T1" onclick="updateSelectedTableDisplay('Indoor Reef T1 (Luxury window view)')" required class="peer sr-only">
                                    <div class="p-5 rounded-xl border bg-white border-slate-200 hover:border-ocean-400 hover:bg-slate-50 text-slate-800 font-extrabold flex flex-col items-center justify-center text-base transition-all shadow-2xs peer-checked:bg-ocean-500 peer-checked:text-white peer-checked:border-ocean-600">
                                        <span>Indoor T1</span>
                                        <span class="text-xs font-normal opacity-80 mt-1">4 seats</span>
                                    </div>
                                </label>
                                <label class="relative block cursor-pointer group">
                                    <input type="radio" name="selected_table" value="Indoor Reef T2" onclick="updateSelectedTableDisplay('Indoor Reef T2 (Luxury family setup)')" required class="peer sr-only">
                                    <div class="p-5 rounded-xl border bg-white border-slate-200 hover:border-ocean-400 hover:bg-slate-50 text-slate-800 font-extrabold flex flex-col items-center justify-center text-base transition-all shadow-2xs peer-checked:bg-ocean-500 peer-checked:text-white peer-checked:border-ocean-600">
                                        <span>Indoor T2</span>
                                        <span class="text-xs font-normal opacity-80 mt-1">6 seats</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-4 space-y-6 bg-white border border-beige-200 rounded-2xl p-6 shadow-sm text-left">
                        <h3 class="text-lg font-bold text-ocean-900 border-b pb-2 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-coral-500"></i>
                            <span>Reservation specifics</span>
                        </h3>
                        
                        <div class="bg-gradient-to-r from-ocean-50 to-aqua-50 text-ocean-900 p-4 rounded-xl border border-ocean-200/60 text-base font-bold flex flex-col gap-1">
                            <span class="text-xs text-slate-400 font-normal tracking-wide">Selected layout target:</span>
                            <strong id="selected-table-notif" class="text-ocean-700 text-lg transition-all duration-300">Please tap a table from the deck!</strong>
                        </div>
                        
                        <div class="space-y-4">
                            <input type="text" id="selected-table-input" readonly placeholder="No table chosen yet" class="w-full bg-slate-100 border border-slate-200 rounded-xl py-3 px-4 text-base font-mono hidden">
                            
                            <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">Full name</label>
                                <input type="text" id="book-name" required placeholder="Enter full name" class="w-full bg-pearl-100 border border-beige-200 rounded-xl py-3 px-4 text-base focus:outline-none focus:border-ocean-500 transition-all">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">Contact phone number</label>
                                <input type="tel" id="book-phone" required placeholder="e.g. +94 77 123 4567" class="w-full bg-pearl-100 border border-beige-200 rounded-xl py-3 px-4 text-base focus:outline-none focus:border-ocean-500 transition-all">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-bold text-slate-600 mb-1">Date</label>
                                    <input type="date" id="book-date" required class="w-full bg-pearl-100 border border-beige-200 rounded-xl py-3 px-4 text-base focus:outline-none focus:border-ocean-500 transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-600 mb-1">Arrival time</label>
                                    <input type="time" id="book-time" required class="w-full bg-pearl-100 border border-beige-200 rounded-xl py-3 px-4 text-base focus:outline-none focus:border-ocean-500 transition-all">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-white font-extrabold py-4 px-5 rounded-xl shadow-lg transition-all text-base tracking-wide flex items-center justify-center gap-2 mt-2">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Confirm & hold table (10-min cancellation)</span>
                        </button>
                    </div>

                </div>
            </form>
        </section>

        <section id="view-orders" class="view-panel hidden py-16 w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4">
            <div class="text-center mb-10">
                <span class="text-ocean-500 font-bold tracking-widest text-sm uppercase">Live Status Desk</span>
                <h1 class="serif-font text-4xl font-bold mt-1 text-ocean-900 font-black">Follow Your Feast's Journey to the Shore</h1>
                <p class="text-slate-500 mt-2 text-base sm:text-lg font-medium">Check on your freshly prepped seafood or view your order registry.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white border border-beige-200 p-6 rounded-2xl shadow-sm text-left">
                    <h3 class="text-sm font-bold text-ocean-900 mb-3 tracking-wider uppercase">Quick Order Search</h3>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-grow">
                            <i class="fa-solid fa-receipt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" id="order-lookup-input" placeholder="Enter Order ID (e.g., ORD-1234)" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 focus:outline-none rounded-xl py-3 pl-9 pr-4 text-sm font-mono">
                        </div>
                        <button onclick="handleOrderLookup()" class="bg-ocean-500 hover:bg-ocean-600 text-white font-bold py-3 px-6 rounded-xl text-sm tracking-wider transition-all shadow-md flex-shrink-0">
                            Track Status
                        </button>
                    </div>
                </div>

                <div class="bg-white border border-beige-200 p-6 rounded-2xl shadow-sm text-left flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-ocean-900 mb-2 tracking-wider uppercase">Your History</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-4">View previously recorded dining sessions instantly.</p>
                    </div>
                    <div id="user-orders-summary">
                        <button onclick="openProfileModal('details')" class="w-full text-center bg-pearl-100 hover:bg-beige-100 text-ocean-900 border border-beige-200 py-3 px-4 rounded-xl text-sm font-bold transition-all">
                            Open My Guest Profile
                        </button>
                    </div>
                </div>
            </div>

            <div id="active-tracker-container" class="hidden bg-white border border-beige-200 rounded-2xl overflow-hidden shadow-lg text-left">
                <div class="bg-gradient-to-r from-ocean-500 to-ocean-600 p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center space-x-2 text-xs font-bold text-aqua-200 tracking-widest mb-1 uppercase">
                            <span class="w-2.5 h-2.5 rounded-full bg-aqua-500 animate-pulse"></span>
                            <span>Live Feeder Link Active</span>
                        </div>
                        <h2 class="serif-font text-2xl font-bold">Ticket: <span id="track-id" class="font-mono text-aqua-200">ORD-0000</span></h2>
                    </div>
                    <div class="text-sm text-white/90 font-bold">
                        <div id="track-time">Placed: Loading...</div>
                        <div class="text-xs text-aqua-100 font-mono mt-1" id="track-payment">Method: ---</div>
                    </div>
                </div>

                <div class="p-8 space-y-8">
                    <!-- Instant Cancellation Section for Active Guest Order -->
                    <div id="order-cancellation-section" class="hidden bg-rose-50 border border-rose-200 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-5 font-sans animate-fade-in">
                        <div class="space-y-1">
                            <span class="text-xs text-rose-600 font-black tracking-widest uppercase flex items-center gap-1.5">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span>Cancellation Period Active</span>
                            </span>
                            <p class="text-sm sm:text-base font-extrabold text-slate-800 leading-snug">Changed your mind? You can cancel within 10 minutes of placing the order.</p>
                            <span id="order-cancel-timer" class="block text-xs sm:text-sm text-rose-500 font-mono font-bold bg-white px-3 py-1 rounded border border-rose-100 w-fit">Time remaining: 10:00</span>
                        </div>
                        <button id="btn-cancel-order-action" class="w-full sm:w-auto bg-gradient-to-r from-rose-500 to-coral-600 hover:brightness-110 text-white font-extrabold text-sm tracking-wider px-6 py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center gap-2 flex-shrink-0">
                            <i class="fa-solid fa-ban"></i>
                            <span>Cancel entire order</span>
                        </button>
                    </div>

                    <div class="relative flex flex-col md:flex-row justify-between items-center gap-6 md:gap-4">
                        <div class="absolute left-[23px] top-6 bottom-6 w-1 md:left-6 md:right-6 md:h-1 md:w-auto md:top-1/2 md:-translate-y-1/2 bg-pearl-200 z-0">
                            <div id="tracker-progress-bar" class="h-full md:h-full bg-gradient-to-r from-ocean-500 via-aqua-500 to-coral-500 transition-all duration-700" style="width: 0%; height: 0%;"></div>
                        </div>

                        <div class="flex md:flex-col items-center text-left md:text-center space-x-4 md:space-x-0 relative z-10 w-full md:w-1/4">
                            <div id="step-node-1" class="w-12 h-12 rounded-full border-4 border-pearl-100 bg-pearl-200 text-slate-500 flex items-center justify-center transition-all duration-300">
                                <i class="fa-solid fa-receipt text-sm"></i>
                            </div>
                            <div class="md:mt-3">
                                <h4 class="text-sm font-extrabold text-ocean-900 tracking-wider">Order Placed</h4>
                                <p class="text-xs text-slate-400">Kitchen notified</p>
                            </div>
                        </div>

                        <div class="flex md:flex-col items-center text-left md:text-center space-x-4 md:space-x-0 relative z-10 w-full md:w-1/4">
                            <div id="step-node-2" class="w-12 h-12 rounded-full border-4 border-pearl-100 bg-pearl-200 text-slate-500 flex items-center justify-center transition-all duration-300">
                                <i class="fa-solid fa-fire text-sm"></i>
                            </div>
                            <div class="md:mt-3">
                                <h4 class="text-sm font-extrabold text-ocean-900 tracking-wider">Kitchen Grill</h4>
                                <p class="text-xs text-slate-400">Chef prepping spices</p>
                            </div>
                        </div>

                        <div class="flex md:flex-col items-center text-left md:text-center space-x-4 md:space-x-0 relative z-10 w-full md:w-1/4">
                            <div id="step-node-3" class="w-12 h-12 rounded-full border-4 border-pearl-100 bg-pearl-200 text-slate-500 flex items-center justify-center transition-all duration-300">
                                <i class="fa-solid fa-motorcycle text-sm"></i>
                            </div>
                            <div class="md:mt-3">
                                <h4 class="text-sm font-extrabold text-ocean-900 tracking-wider">On The Way</h4>
                                <p class="text-xs text-slate-400">Rider dispatched</p>
                            </div>
                        </div>

                        <div class="flex md:flex-col items-center text-left md:text-center space-x-4 md:space-x-0 relative z-10 w-full md:w-1/4">
                            <div id="step-node-4" class="w-12 h-12 rounded-full border-4 border-pearl-100 bg-pearl-200 text-slate-500 flex items-center justify-center transition-all duration-300">
                                <i class="fa-solid fa-circle-check text-sm"></i>
                            </div>
                            <div class="md:mt-3">
                                <h4 class="text-sm font-extrabold text-ocean-900 tracking-wider">Feast Arrived</h4>
                                <p class="text-xs text-slate-400">Served & Ready</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-beige-200">
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold text-ocean-500 tracking-wider flex items-center uppercase">
                                <i class="fa-solid fa-location-dot mr-2"></i> Destination Details
                            </h3>
                            <div class="bg-pearl-100 p-5 rounded-xl border border-beige-200 space-y-3">
                                <div class="text-sm">
                                    <span class="block text-slate-455 font-bold text-[10px] tracking-wider uppercase">Recipient Guest</span>
                                    <span id="track-recipient" class="font-bold text-slate-800 text-sm">---</span>
                                </div>
                                <div class="text-sm">
                                    <span class="block text-slate-455 font-bold text-[10px] tracking-wider uppercase">Contact Number</span>
                                    <span id="track-contact" class="font-semibold text-slate-800">---</span>
                                </div>
                                <div class="text-sm">
                                    <span class="block text-slate-455 font-bold text-[10px] tracking-wider uppercase">Where should we bring your feast?</span>
                                    <span id="track-address" class="text-slate-500 font-medium text-sm">---</span>
                                </div>
                            </div>

                            <div id="delivery-agent-card" class="bg-gradient-to-r from-beige-100 to-pearl-100 border border-beige-200 p-4 rounded-xl flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-full bg-ocean-500/10 text-ocean-500 border border-ocean-500/20 flex items-center justify-center text-lg flex-shrink-0">
                                    <i class="fa-solid fa-helmet-safety text-coral-500"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800">Assigned Delivery Partner</h4>
                                    <p class="text-xs text-slate-500 font-semibold" id="delivery-partner-status">Pending status updates</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-ocean-500 tracking-wider flex items-center mb-3 uppercase">
                                <i class="fa-solid fa-utensils mr-2"></i> Meal Breakdown
                            </h3>
                            <div id="track-items-list" class="space-y-3 max-h-[220px] overflow-y-auto pr-2 mb-4"></div>
                            <div class="border-t border-beige-200 pt-3 flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400 tracking-wider uppercase">Grand Total (Hospitable Care + Delivery)</span>
                                <span id="track-price-total" class="font-black text-lg text-coral-500">LKR 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="tracker-empty-state" class="bg-white border border-beige-250 p-12 rounded-2xl text-center shadow-sm">
                <div class="w-14 h-14 bg-pearl-100 text-slate-400 rounded-full flex items-center justify-center text-2xl mx-auto mb-4 border border-beige-200">
                    <i class="fa-solid fa-compass-drafting text-ocean-500"></i>
                </div>
                <h3 class="serif-font text-xl font-bold text-ocean-900 mb-1">No Active Order Selected</h3>
                <p class="text-slate-500 text-sm max-w-sm mx-auto leading-relaxed">Please submit an order, lookup an active receipt ID above, or check your profile registration list.</p>
            </div>
        </section>

        <section id="view-contact" class="view-panel hidden py-16 w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4">
            <div class="text-center mb-12">
                <span class="text-coral-500 font-bold tracking-widest text-sm uppercase">Get In Touch</span>
                <h1 class="serif-font text-4xl sm:text-5xl font-black mt-2 text-ocean-900">Bespoke Guest Inquiry Desk</h1>
                <p class="text-slate-500 text-base sm:text-lg mt-2">Connect with our guest relationship experts for catering requests, events, or reservations.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Contact info panel -->
                <div class="lg:col-span-5 bg-gradient-to-b from-ocean-900 to-ocean-955 rounded-3xl p-8 text-white text-left space-y-8 flex flex-col justify-between">
                    <div>
                        <span class="text-xs text-aqua-400 font-black tracking-widest uppercase block mb-1">Response Guarantee</span>
                        <h2 class="serif-font text-3xl font-bold leading-tight">Concierge Support</h2>
                        <p class="text-slate-300 text-sm leading-relaxed mt-3 text-justify">
                            Whether you need guidance choosing a dynamic table, custom options on a tandoori lobster feast, or special catering along the beach sands, our concierge responds in real-time.
                        </p>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-xl text-aqua-500 border border-white/15 flex-shrink-0"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <span class="block text-[10px] text-slate-400 font-bold tracking-widest uppercase">Location</span>
                                <span class="font-extrabold text-sm sm:text-base">Yaddehimulla Road, Unawatuna Beach, Galle, Sri Lanka</span>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-xl text-aqua-500 border border-white/15 flex-shrink-0"><i class="fa-solid fa-phone"></i></div>
                            <div>
                                <span class="block text-[10px] text-slate-400 font-bold tracking-widest uppercase">Hotlines</span>
                                <span class="font-extrabold text-sm sm:text-base block">(+94) 91 224 8888</span>
                                <span class="font-extrabold text-sm sm:text-base">(+94) 91 224 8889</span>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-xl text-aqua-500 border border-white/15 flex-shrink-0"><i class="fa-solid fa-envelope-open"></i></div>
                            <div>
                                <span class="block text-[10px] text-slate-400 font-bold tracking-widest uppercase">Email contact</span>
                                <span class="font-bold text-sm sm:text-base break-all underline cursor-pointer text-aqua-300">hello@seapearlunawatuna.com</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-white/10 flex items-center justify-between">
                        <div>
                            <span class="block text-[9px] text-slate-400 font-black tracking-widest uppercase">Average feedback time</span>
                            <span class="text-xs font-bold text-emerald-400 flex items-center gap-1.5 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping inline-block"></span>
                                <span>Under 15 minutes</span>
                            </span>
                        </div>
                        <div class="flex space-x-3">
                            <a href="#" class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-xs text-white hover:bg-white/20 transition-all"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-xs text-white hover:bg-white/20 transition-all"><i class="fa-brands fa-facebook"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Form Panel -->
                <div class="lg:col-span-7 bg-white border border-beige-200 rounded-3xl p-8 shadow-sm text-left">
                    <h3 class="serif-font text-2xl font-bold text-ocean-900 mb-6 border-b pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-envelope-open-text text-coral-500"></i>
                        <span>Transmit a gourmet inquiry</span>
                    </h3>

                    <form id="contact-inquiry-form" onsubmit="handleInquirySubmit(event)" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1 tracking-wider uppercase">Full name</label>
                                <input type="text" id="contact-name" required placeholder="Samantha Perera" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 focus:bg-white rounded-xl py-3 px-4 text-sm focus:outline-none transition-all shadow-2xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1 tracking-wider uppercase">Email address</label>
                                <input type="email" id="contact-email" required placeholder="samantha@outlook.com" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 focus:bg-white rounded-xl py-3 px-4 text-sm focus:outline-none transition-all shadow-2xs">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 tracking-wider uppercase">Telephone number</label>
                            <input type="tel" id="contact-phone" required placeholder="e.g. +94 77 123 4567" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 focus:bg-white rounded-xl py-3 px-4 text-sm focus:outline-none transition-all shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 tracking-wider uppercase">Message details</label>
                            <textarea id="contact-message" required placeholder="Describe your request, timing, or dynamic menu ideas here..." rows="4" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 focus:bg-white rounded-xl py-3 px-4 text-sm focus:outline-none transition-all shadow-2xs"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-white font-extrabold py-3.5 px-6 rounded-xl text-sm tracking-widest transition-all shadow-md uppercase">
                            Send message
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <div id="customize-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ocean-900/40 backdrop-blur-sm hidden">
        <div class="bg-white border border-beige-200 rounded-3xl max-w-md w-full p-6 relative shadow-2xl">
            <button onclick="closeCustomizeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
            <h3 class="serif-font text-2xl font-bold text-ocean-900 mb-1" id="cust-item-title">Customize Plate</h3>
            <p class="text-slate-500 text-sm mb-5" id="cust-item-desc">Tweak the plate exactly how you would like it served.</p>
            <div class="space-y-4 mb-5 text-left">
                <h4 class="text-xs font-extrabold tracking-wider text-ocean-500 uppercase">Optional Extras</h4>
                <div class="space-y-3" id="custom-choices-container"></div>
            </div>
            <div class="flex items-center justify-between border-t border-beige-200 pt-4 mt-5">
                <span class="text-sm font-bold text-slate-400">Total Custom Price</span>
                <span id="cust-live-total" class="text-lg font-black text-coral-500 font-mono">LKR 0.00</span>
            </div>
            <button id="btn-confirm-customization" class="w-full mt-4 bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-white font-bold py-3 px-5 rounded-xl shadow-md transition-all text-sm tracking-wider text-center uppercase">
                Confirm and Add
            </button>
        </div>
    </div>

    <div id="profile-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ocean-900/40 backdrop-blur-sm hidden">
        <div class="bg-white border border-beige-200 rounded-3xl max-w-2xl w-full overflow-hidden shadow-2xl relative flex flex-col max-h-[90vh]">
            <button onclick="closeProfileModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-855 z-10 bg-pearl-100 w-9 h-9 rounded-full flex items-center justify-center border border-beige-200">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
            <div class="bg-gradient-to-r from-ocean-500 to-ocean-600 p-6 text-white flex-shrink-0 text-left">
                <div class="flex items-center space-x-3 mb-1">
                    <i class="fa-solid fa-id-card text-3xl text-aqua-500"></i>
                    <h3 class="serif-font text-2xl font-extrabold tracking-tight">Your Culinary Profile</h3>
                </div>
                <p class="text-white/90 text-sm font-bold tracking-widest uppercase">Saved preferences & reservations</p>
            </div>
            <div class="flex-grow overflow-y-auto p-6 space-y-5">
                <div id="profile-gate" class="text-center py-8 space-y-4">
                    <div class="w-16 h-16 bg-pearl-100 text-coral-500 rounded-full flex items-center justify-center text-2xl mx-auto border border-beige-200">
                        <i class="fa-solid fa-user-slash"></i>
                    </div>
                    <h4 class="font-bold text-ocean-900 text-lg">Profile Login Required</h4>
                    <p class="text-sm text-slate-500 max-w-md mx-auto leading-relaxed">Please register or log in to synchronize your favorite dishes, past dine-ins, and checkout address details.</p>
                    <button onclick="closeProfileModal(); openAuthModal('signin');" class="bg-ocean-500 hover:bg-ocean-600 text-white font-bold px-6 py-3 rounded-xl text-xs tracking-wider transition-all uppercase">
                        Log In Now
                    </button>
                </div>
                <div id="profile-active-container" class="hidden space-y-5">
                    <div class="flex border-b border-beige-200">
                        <button id="profile-tab-details-btn" onclick="toggleProfileModalTab('details')" class="flex-1 pb-3 text-sm font-extrabold border-b-2 border-ocean-500 text-ocean-900 flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-user-gear"></i>
                            <span>Personal Details</span>
                        </button>
                        <button id="profile-tab-wishlist-btn" onclick="toggleProfileModalTab('wishlist')" class="flex-1 pb-3 text-sm font-extrabold border-b-2 border-transparent text-slate-400 hover:text-slate-600 flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-heart text-coral-500"></i>
                            <span>Favorites</span>
                        </button>
                        <button id="profile-tab-bookings-btn" onclick="toggleProfileModalTab('bookings')" class="flex-1 pb-3 text-sm font-extrabold border-b-2 border-transparent text-slate-400 hover:text-slate-600 flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-calendar-days text-ocean-500"></i>
                            <span>Reservations</span>
                        </button>
                    </div>

                    <form id="profile-details-form" onsubmit="handleProfileUpdate(event)" class="space-y-4 text-left font-sans">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Username (Read-Only)</label>
                                <input type="text" id="profile-username" readonly class="w-full bg-pearl-100 text-slate-500 border border-beige-200 rounded-xl py-2.5 px-4 text-sm focus:outline-none cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Full Name</label>
                                <input type="text" id="profile-fullname" required class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 rounded-xl py-2.5 px-4 text-sm focus:outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Mobile Number</label>
                                <input type="text" id="profile-phone" required class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 rounded-xl py-2.5 px-4 text-sm focus:outline-none font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Default Delivery Coordinates</label>
                                <input type="text" id="profile-address" required class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 rounded-xl py-2.5 px-4 text-sm focus:outline-none">
                            </div>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="w-full bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-white font-extrabold py-3 px-5 rounded-xl text-xs tracking-wider shadow-md uppercase">
                                <i class="fa-solid fa-floppy-disk mr-1.5"></i> Save Details
                            </button>
                        </div>
                    </form>

                    <div id="profile-wishlist-view" class="hidden space-y-4 text-left">
                        <div class="flex items-center justify-between text-sm text-slate-400">
                            <span>Your saved wishlist favorites</span>
                            <span id="profile-wishlist-count-text" class="font-bold text-ocean-900">0 items</span>
                        </div>
                        <div id="profile-wishlist-items-container" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                    </div>

                    <div id="profile-bookings-view" class="hidden space-y-4 text-left">
                        <div class="flex items-center justify-between text-sm text-slate-400 font-bold">
                            <span>Your table reservations</span>
                            <span id="profile-bookings-count-text" class="text-ocean-900 font-mono">0 slots</span>
                        </div>
                        <div id="profile-bookings-items-container" class="space-y-4 max-h-[250px] overflow-y-auto pr-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="auth-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ocean-900/40 backdrop-blur-sm hidden">
        <div class="bg-white border border-beige-200 rounded-3xl max-w-md w-full overflow-hidden shadow-2xl relative">
            <button onclick="closeAuthModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800 z-10 bg-pearl-100 w-9 h-9 rounded-full flex items-center justify-center border border-beige-200">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
            <div class="bg-gradient-to-r from-ocean-500 to-ocean-600 p-5 text-white text-left">
                <div class="flex items-center space-x-3 mb-1">
                    <i class="fa-solid fa-id-card text-2xl text-aqua-500"></i>
                    <h3 class="serif-font text-xl font-extrabold tracking-tight">Our Guest Vault</h3>
                </div>
                <p class="text-white/90 text-xs font-bold tracking-widest uppercase">Secure connection portal</p>
            </div>
            <div class="p-6 space-y-5">
                <div class="flex border-b border-beige-200">
                    <button id="auth-toggle-signin" onclick="toggleAuthForm('signin')" class="flex-1 pb-3 text-sm font-extrabold border-b-2 border-ocean-500 text-ocean-900">Sign In</button>
                    <button id="auth-toggle-signup" onclick="toggleAuthForm('signup')" class="flex-1 pb-3 text-sm font-extrabold border-b-2 border-transparent text-slate-400 hover:text-slate-600">Register</button>
                </div>
                
                <form id="auth-form-signin" onsubmit="handleAuthSignIn(event)" class="space-y-4 text-left">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Your Username</label>
                        <div class="relative font-mono">
                            <i class="fa-solid fa-at absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" id="signin-username" required oninput="handleAutofillTrigger(this.value)" placeholder="e.g. ceylon_foodie" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 rounded-xl py-2.5 pl-9 pr-4 text-sm focus:outline-none font-semibold">
                        </div>
                        <span id="autofill-notif" class="hidden text-xs text-emerald-600 font-semibold mt-1.5 flex items-center space-x-1">
                            <i class="fa-solid fa-circle-check animate-pulse text-emerald-500"></i>
                            <span>Profile verified! Details are auto-filled.</span>
                        </span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Password</label>
                        <div class="relative font-mono">
                            <i class="fa-solid fa-key absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="password" id="signin-password" required placeholder="••••••••" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 rounded-xl py-2.5 pl-9 pr-4 text-sm focus:outline-none font-sans">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-white font-extrabold py-3 px-5 rounded-xl text-xs tracking-widest transition-all uppercase">
                        Access Profile
                    </button>
                </form>

                <form id="auth-form-signup" onsubmit="handleAuthSignUp(event)" class="space-y-4 text-left font-sans hidden">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Username</label>
                            <input type="text" id="signup-username" required placeholder="ceylon_foodie" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 rounded-xl py-2.5 px-3 text-sm focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Password</label>
                            <input type="password" id="signup-password" required placeholder="••••••••" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 rounded-xl py-2.5 px-3 text-sm focus:outline-none font-sans">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Full Name</label>
                        <input type="text" id="signup-name" required placeholder="Samantha Perera" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 rounded-xl py-2.5 px-3 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Mobile Number</label>
                        <input type="text" id="signup-phone" required placeholder="e.g. +94 77 123 4567" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 rounded-xl py-2.5 px-3 text-sm focus:outline-none font-sans">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Delivery Address or Bed Number</label>
                        <textarea id="signup-address" required placeholder="E.g., Beach Bed 4 or Thalpe Beach Villa" rows="2" class="w-full bg-pearl-100 text-slate-800 border border-beige-200 focus:border-ocean-500 rounded-xl py-2.5 px-3 text-sm focus:outline-none font-sans"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-white font-extrabold py-3 px-5 rounded-xl text-xs tracking-widest transition-all uppercase">
                        Create Guest Profile
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="cart-sidebar" class="fixed inset-0 z-50 overflow-hidden hidden">
        <div class="absolute inset-0 bg-ocean-955/20 backdrop-blur-sm" onclick="toggleCart()"></div>
        <div class="absolute inset-y-0 right-0 max-w-full flex">
            <div class="w-screen max-w-md bg-white border-l border-beige-200 shadow-2xl flex flex-col">
                <div class="p-6 border-b border-beige-200 flex items-center justify-between">
                    <h2 class="serif-font text-xl font-bold text-ocean-900 flex items-center">
                        <i class="fa-solid fa-bag-shopping text-coral-500 mr-2"></i> Your Order
                    </h2>
                    <button onclick="toggleCart()" class="text-slate-400 hover:text-slate-800 p-1"><i class="fa-solid fa-xmark text-2xl"></i></button>
                </div>
                <div class="flex-grow overflow-y-auto p-6 space-y-5 bg-pearl-100/50 text-left font-sans">
                    <div class="space-y-4" id="cart-items-container"></div>
                    <div class="space-y-4 pt-5 border-t border-beige-200">
                        <p class="text-xs font-bold text-ocean-500 tracking-widest flex items-center justify-between uppercase">
                            <span>Delivery Destination</span>
                            <span id="cart-secure-badge" class="text-[10px] text-slate-400 font-normal lowercase"><i class="fa-solid fa-lock mr-1 text-coral-500"></i>autofill inactive</span>
                        </p>
                        <input type="text" id="cust-name" required placeholder="Your Lovely Name" class="w-full bg-white border border-beige-200 rounded-lg py-2.5 px-3.5 text-sm focus:outline-none focus:border-ocean-500 text-slate-800 shadow-2xs font-semibold">
                        <input type="text" id="cust-phone" required placeholder="Phone Number (e.g. +94 77 123 4567)" class="w-full bg-white border border-beige-200 rounded-lg py-2.5 px-3.5 text-sm focus:outline-none focus:border-ocean-500 text-slate-800 shadow-2xs font-mono">
                        <textarea id="cust-address" required placeholder="Enter delivery address (e.g., Unawatuna Beach Road)" rows="2" class="w-full bg-white border border-beige-200 rounded-lg py-2.5 px-3.5 text-sm focus:outline-none focus:border-ocean-500 text-slate-800 shadow-2xs"></textarea>
                        
                        <div class="space-y-3 pt-2">
                            <label class="block text-xs font-bold text-slate-500 tracking-wider uppercase">Choose Payment Method</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer border border-beige-200 rounded-xl p-3 flex items-center justify-center space-x-2 bg-white hover:bg-pearl-100 transition-all text-slate-700 shadow-2xs font-bold" id="pay-opt-cod-label">
                                    <input type="radio" name="payment-method" value="COD" checked onchange="togglePaymentForm('COD')" class="sr-only">
                                    <i class="fa-solid fa-money-bill-wave text-ocean-500 text-sm"></i>
                                    <span class="text-xs">Cash on arrival</span>
                                </label>
                                <label class="cursor-pointer border border-beige-200 rounded-xl p-3 flex items-center justify-center space-x-2 bg-white hover:bg-pearl-100 transition-all text-slate-700 shadow-2xs font-bold" id="pay-opt-card-label">
                                    <input type="radio" name="payment-method" value="Card" onchange="togglePaymentForm('Card')" class="sr-only">
                                    <i class="fa-solid fa-credit-card text-coral-500 text-sm"></i>
                                    <span class="text-xs">Online card</span>
                                </label>
                            </div>
                        </div>
                        <div id="secure-card-form" class="hidden space-y-3 bg-white p-4 rounded-xl border border-beige-200 shadow-inner font-sans font-mono">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Cardholder Name</label>
                                <input type="text" id="card-holder" placeholder="Alex De Silva" class="w-full bg-pearl-100 border border-beige-200 rounded-lg py-2 px-3 text-xs focus:outline-none focus:border-ocean-500 text-slate-800 placeholder-slate-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Card Number</label>
                                <input type="text" id="card-num" oninput="formatCardNumber(this)" maxlength="19" placeholder="4111 2222 3333 4444" class="w-full bg-pearl-100 border border-beige-200 rounded-lg py-2 px-3 text-xs focus:outline-none focus:border-ocean-500 text-slate-800">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" id="card-exp" maxlength="5" placeholder="MM/YY" class="w-full bg-pearl-100 border border-beige-200 rounded-lg py-2 px-3 text-xs text-center text-slate-800">
                                <input type="password" id="card-cvc" maxlength="3" placeholder="CVV" class="w-full bg-pearl-100 border border-beige-200 rounded-lg py-2 px-3 text-xs text-center text-slate-800">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-beige-200 bg-white p-6 space-y-4 shadow-inner text-left font-sans">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-455 font-medium">Subtotal</span>
                        <span id="cart-subtotal" class="font-bold text-ocean-900">LKR 0.00</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-455 font-medium">Hospitable Care & Taxes (10%)</span>
                        <span id="cart-tax" class="font-bold text-ocean-900">LKR 0.00</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-455 font-medium">Delivery Flat Charge</span>
                        <span class="font-bold text-ocean-900">LKR 300.00</span>
                    </div>
                    <div class="flex items-center justify-between text-base border-t border-beige-200 pt-3 font-black">
                        <span class="text-ocean-900">Total Amount</span>
                        <span id="cart-total" class="text-coral-500 text-xl font-mono">LKR 0.00</span>
                    </div>
                    <button onclick="handlePlaceOrder()" class="w-full bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-white font-extrabold py-3.5 px-5 rounded-xl shadow-md tracking-wider flex items-center justify-center space-x-2 text-sm uppercase">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>Send order to the kitchen</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="success-confirmation-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ocean-955/45 backdrop-blur-sm hidden">
        <div class="bg-white border border-beige-200 rounded-3xl max-w-md w-full p-8 text-center shadow-2xl relative">
            <button onclick="closeSuccessConfirmationModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800"><i class="fa-solid fa-xmark text-xl"></i></button>
            <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-5 border border-emerald-200">
                <i id="confirm-modal-icon" class="fa-solid fa-circle-check"></i>
            </div>
            <h3 id="confirm-modal-title" class="serif-font text-3xl font-bold text-ocean-900 mb-3">Confirmed!</h3>
            <p id="confirm-modal-message" class="text-slate-600 text-sm mb-8 leading-relaxed text-justify sm:text-center"></p>
            <button onclick="closeSuccessConfirmationModal()" class="w-full bg-gradient-to-r from-ocean-500 to-aqua-500 text-white font-bold py-3.5 px-6 rounded-xl shadow-lg text-sm uppercase tracking-widest">
                Terrific, thank you!
            </button>
        </div>
    </div>

    <div id="custom-notification" class="fixed bottom-6 right-6 z-50 max-w-sm w-full bg-white border border-beige-200 rounded-2xl shadow-2xl p-5 transform translate-y-32 opacity-0 transition-all duration-300 pointer-events-none flex items-start space-x-3">
        <div id="notify-icon" class="text-ocean-500 text-2xl pt-0.5"></div>
        <div>
            <h4 id="notify-title" class="font-bold text-ocean-900 text-sm">Update</h4>
            <p id="notify-message" class="text-xs text-slate-500 mt-1 leading-relaxed text-justify"></p>
        </div>
    </div>

    <footer class="bg-ocean-955 text-white py-16 border-t border-ocean-900/60 font-sans text-sm">
        <div class="w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4 grid grid-cols-1 md:grid-cols-5 gap-10">
            
            <div class="space-y-5 text-left">
                <a href="#" onclick="switchView('home')" class="flex items-center space-x-3 group">
                    <div class="w-14 h-14 bg-gradient-to-tr from-ocean-500 via-aqua-500 to-coral-500 rounded-xl flex items-center justify-center text-white font-black shadow-md shadow-ocean-500/10 group-hover:scale-105 transition-transform">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" fill="#005f99" />
                            <circle cx="12" cy="11" r="3" fill="#f8f9fa" stroke="#ff6f61" stroke-width="1" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-pearl-100 via-aqua-100 to-beige-200 bg-clip-text text-transparent block">Sea Pearl Bistro</span>
                        <span class="block text-[10px] text-aqua-200 tracking-wider font-semibold uppercase">Unawatuna Reef Sanctuary</span>
                    </div>
                </a>
                <p class="text-slate-400 text-sm leading-relaxed text-justify">
                    A luxurious oceanside dining retreat where fresh line-caught fish and interactive table bookings meet warm Sri Lankan hospitality.
                </p>
            </div>

            <div class="text-left">
                <h4 class="text-white font-extrabold text-sm mb-5 tracking-wider uppercase">Quick Links</h4>
                <ul class="space-y-3 text-slate-350 font-medium">
                    <li><a href="#" onclick="switchView('contact')" class="hover:text-aqua-200 transition-colors">Contact Us</a></li>
                    <li><a href="#" onclick="switchView('about')" class="hover:text-aqua-200 transition-colors">About Us</a></li>
                    <li><a href="#" onclick="switchView('order-online')" class="hover:text-aqua-200 transition-colors">All Categories</a></li>
                    <li><a href="#" onclick="switchView('coverage')" class="hover:text-aqua-200 transition-colors">Delivery Areas</a></li>
                    <li><a href="#" onclick="switchView('about')" class="hover:text-aqua-200 transition-colors">Privacy policy</a></li>
                    <li><a href="#" onclick="switchView('about')" class="hover:text-aqua-200 transition-colors">Terms & conditions</a></li>
                </ul>
            </div>

            <div class="text-left">
                <h4 class="text-white font-extrabold text-sm mb-5 tracking-wider uppercase">Opening Hours</h4>
                <div class="space-y-5">
                    <div>
                        <span class="block font-bold text-white">Delivery Hours</span>
                        <span class="text-slate-350">11:00 am to 11:00 pm</span>
                    </div>
                    <div>
                        <span class="block font-bold text-white">Take-Away Hours</span>
                        <span class="text-slate-350">11:00 am to 11:00 pm</span>
                    </div>
                    <div class="pt-3 border-t border-slate-800">
                        <span class="text-coral-500 font-extrabold">Open 7 days a week</span>
                        <p class="text-slate-400 text-xs mt-1 text-justify">Including holidays & coastal festivals.</p>
                    </div>
                </div>
            </div>

            <div class="text-left">
                <h4 class="text-white font-extrabold text-sm mb-5 tracking-wider uppercase">Contact</h4>
                <ul class="space-y-3.5 text-slate-350 text-justify">
                    <li class="flex items-start space-x-2">
                        <i class="fa-solid fa-map-location text-aqua-500 mt-1 flex-shrink-0"></i>
                        <span>Yaddehimulla Road, Unawatuna Beach, Galle, Sri Lanka</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <i class="fa-solid fa-phone text-aqua-500"></i>
                        <span>(+94) 91 224 8888</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <i class="fa-brands fa-whatsapp text-emerald-500 text-base"></i>
                        <span class="font-bold text-emerald-400">(+94) 777 864 864</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <i class="fa-solid fa-envelope-open text-coral-500"></i>
                        <a href="mailto:hello@seapearlunawatuna.com" class="hover:text-aqua-200 underline">hello@seapearlunawatuna.com</a>
                    </li>
                </ul>
            </div>

            <div class="space-y-5 text-left">
                <h4 class="text-white font-extrabold text-sm tracking-wider uppercase font-black">Follow Us</h4>
                <ul class="space-y-2.5 text-slate-350 font-semibold">
                    <li><a href="#" class="hover:text-aqua-200 flex items-center space-x-2"><i class="fa-brands fa-facebook"></i> <span>Facebook</span></a></li>
                    <li><a href="#" class="hover:text-aqua-200 flex items-center space-x-2"><i class="fa-brands fa-instagram"></i> <span>Instagram</span></a></li>
                </ul>
                <div class="pt-5 border-t border-slate-800 space-y-3">
                    <span class="block text-[10px] text-slate-500 font-black uppercase">Secure Checkout</span>
                    <div class="flex items-center space-x-2">
                        <span class="bg-white/5 border border-white/10 rounded px-2 py-0.5 text-slate-300 font-bold font-mono text-[10px]">Visa</span>
                        <span class="bg-white/5 border border-white/10 rounded px-2 py-0.5 text-slate-300 font-bold font-mono text-[10px]">Mastercard</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-14 pt-10 border-t border-slate-800/80 text-center px-4">
            <p class="text-lg font-bold text-slate-300 tracking-wide">
                &copy; 2026 Sea Pearl Bistro. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Leaflet Map JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
