<?php

require_once __DIR__ . '/config/db.php';
session_start();

?>
<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sea Pearl Bistro | Staff Management Hub</title>
    
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

    <header class="sticky top-0 z-40 bg-ocean-955 border-b border-ocean-900/60 shadow-lg" id="admin-app-header">
        <div class="w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4 h-20 flex items-center justify-between">
            <a class="flex items-center space-x-3 group" href="index.php">
                <div class="w-11 h-11 bg-gradient-to-tr from-ocean-500 via-aqua-500 to-coral-500 rounded-xl flex items-center justify-center text-white font-black shadow-md">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="text-left">
                    <span class="text-lg font-bold tracking-tight text-pearl-100">Sea Pearl Bistro</span>
                    <span class="block text-xs text-aqua-200 tracking-widest font-semibold">Staff Management Hub</span>
                </div>
            </a>
            <a href="index.php" class="flex items-center space-x-2 bg-ocean-900/50 hover:bg-ocean-900 text-slate-200 text-sm font-bold py-2.5 px-4 rounded-xl border border-ocean-800 transition-all">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Website</span>
            </a>
        </div>
    </header>

    <main class="flex-grow">
        <section id="view-admin" class="view-panel w-full max-w-[96%] lg:max-w-[98%] mx-auto px-4 py-8 animate-fade-in">
            
            <!-- Secure Admin Login Gate -->
            <div id="admin-login-gate" class="max-w-md mx-auto bg-white border border-beige-200 rounded-3xl overflow-hidden shadow-2xl p-6 text-left my-10">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-ocean-50 text-ocean-500 rounded-full flex items-center justify-center text-xl mx-auto mb-2 border border-ocean-200">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <h3 class="serif-font text-2xl font-bold text-ocean-900 mb-1">Management Hub gate</h3>
                    <p class="text-xs text-slate-400">Exclusive workspace access only for authorized staff.</p>
                </div>
                <form id="admin-login-form" onsubmit="handleAdminLogin(event)" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Workspace passcode</label>
                        <input type="password" id="admin-passcode" required placeholder="Enter passcode (Hint: admin)" class="w-full bg-pearl-100 border border-beige-200 rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-ocean-500 font-sans">
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-ocean-500 to-aqua-500 text-white font-extrabold py-3 px-4 rounded-xl text-sm transition-all shadow-md">
                        Authorize desk access
                    </button>
                </form>
            </div>

            <!-- Authentic Admin Workspace Pane (Hidden until verified) -->
            <div id="admin-workspace-pane" class="hidden space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-beige-200 pb-5 mb-6 gap-4">
                    <div class="text-left">
                        <div class="flex items-center space-x-2 text-xs text-aqua-500 font-extrabold tracking-widest mb-1 animate-pulse">
                            <span class="w-2 h-2 bg-aqua-500 rounded-full"></span>
                            <span>Management Link Active</span>
                        </div>
                        <h1 class="serif-font text-3xl font-bold text-ocean-900">Staff Control Workstation</h1>
                    </div>
                    <!-- Guest Simulator Switch Widget -->
                    <div class="bg-gradient-to-r from-ocean-900 to-ocean-955 border border-ocean-800 p-4 rounded-2xl flex items-center justify-between space-x-4 text-white">
                        <div class="text-left">
                            <span class="block text-[10px] tracking-widest text-aqua-400 font-bold">Auto-Guest simulator</span>
                            <span class="text-xs text-slate-300">Generates simulated tourists</span>
                        </div>
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" id="simulator-toggle" onchange="toggleGuestSimulator()" class="sr-only peer">
                            <div class="w-12 h-6 bg-slate-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:bg-emerald-500 peer-checked:bg-emerald-950 border border-slate-600 relative"></div>
                        </label>
                    </div>
                </div>

                <!-- Global statistics trackers -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="bg-white border border-beige-200 p-5 rounded-2xl flex items-center justify-between shadow-xs text-left">
                        <div>
                            <span class="text-xs text-slate-400 font-extrabold block">Delivered revenue</span>
                            <span class="text-xl font-black text-ocean-900" id="stat-revenue">LKR 0.00</span>
                        </div>
                        <div class="w-12 h-12 bg-coral-100 text-coral-500 rounded-xl flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-solid fa-money-bill-trend-up"></i>
                        </div>
                    </div>
                    <div class="bg-white border border-beige-200 p-5 rounded-2xl flex items-center justify-between shadow-xs text-left">
                        <div>
                            <span class="text-xs text-slate-400 font-extrabold block">Orders count</span>
                            <span class="text-xl font-black text-ocean-900" id="stat-orders">0</span>
                        </div>
                        <div class="w-12 h-12 bg-ocean-50 text-ocean-500 rounded-xl flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                    </div>
                    <div class="bg-white border border-beige-200 p-5 rounded-2xl flex items-center justify-between shadow-xs text-left">
                        <div>
                            <span class="text-xs text-slate-400 font-extrabold block">Active bookings</span>
                            <span class="text-xl font-black text-ocean-900" id="stat-bookings">0</span>
                        </div>
                        <div class="w-12 h-12 bg-aqua-100 text-aqua-500 rounded-xl flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-solid fa-chair"></i>
                        </div>
                    </div>
                    <div class="bg-white border border-beige-200 p-5 rounded-2xl flex items-center justify-between shadow-xs text-left">
                        <div>
                            <span class="text-xs text-slate-400 font-extrabold block">Tasting club</span>
                            <span class="text-xl font-black text-ocean-900" id="stat-subscribers">0</span>
                        </div>
                        <div class="w-12 h-12 bg-beige-200/50 text-coral-500 rounded-xl flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-solid fa-envelope-open"></i>
                        </div>
                    </div>
                </div>

                <!-- Tabbed navigation panel -->
                <div class="bg-white border border-beige-200 rounded-3xl overflow-hidden shadow-sm">
                    <div class="border-b border-beige-200 bg-pearl-50 p-4 flex flex-wrap gap-2">
                        <button onclick="switchAdminTab('orders')" id="btn-tab-orders" class="admin-tab bg-ocean-500 text-white text-xs font-extrabold px-4 py-2.5 rounded-xl transition-all shadow-xs tracking-wider">
                            <i class="fa-solid fa-cash-register mr-1"></i> Orders queue
                        </button>
                        <button onclick="switchAdminTab('bookings')" id="btn-tab-bookings" class="admin-tab bg-pearl-100 hover:bg-beige-100 text-slate-600 text-xs font-extrabold px-4 py-2.5 rounded-xl transition-all tracking-wider border border-beige-200/80">
                            <i class="fa-solid fa-chair mr-1 text-ocean-500"></i> Seating map bookings
                        </button>
                        <button onclick="switchAdminTab('menu')" id="btn-tab-menu" class="admin-tab bg-pearl-100 hover:bg-beige-100 text-slate-600 text-xs font-extrabold px-4 py-2.5 rounded-xl transition-all tracking-wider border border-beige-200/80">
                            <i class="fa-solid fa-utensils mr-1 text-coral-500"></i> Menu customizer
                        </button>
                        <button onclick="switchAdminTab('inquiries')" id="btn-tab-inquiries" class="admin-tab bg-pearl-100 hover:bg-beige-100 text-slate-600 text-xs font-extrabold px-4 py-2.5 rounded-xl transition-all tracking-wider border border-beige-200/80">
                            <i class="fa-solid fa-envelope-open-text mr-1 text-aqua-500"></i> Inbox & Tasting
                        </button>
                    </div>

                    <!-- ADMIN TAB 1: ORDER QUEUE (Live Tracking updates) -->
                    <div id="admin-tab-content-orders" class="admin-tab-panel p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-black text-ocean-900 tracking-wider flex items-center">
                                <i class="fa-solid fa-clipboard-list text-coral-500 mr-2"></i> Current Incoming Queues
                            </h2>
                            <span class="text-xs text-slate-400 text-left">Total Registered Count: <span id="orders-live-count" class="font-extrabold font-mono text-ocean-500">0</span></span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-beige-200 text-[10px] text-slate-400 tracking-widest font-extrabold bg-pearl-100">
                                        <th class="py-3 px-4">Order ID</th>
                                        <th class="py-3 px-4">Customer Details</th>
                                        <th class="py-3 px-4">Dishes & Selections</th>
                                        <th class="py-3 px-4">Payment</th>
                                        <th class="py-3 px-4">Total Amount</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4 text-right font-mono">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-orders-table" class="divide-y divide-pearl-200 text-sm"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ADMIN TAB 2: SEATING BOOKINGS -->
                    <div id="admin-tab-content-bookings" class="admin-tab-panel hidden p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-black text-ocean-900 tracking-wider text-left">
                                <i class="fa-solid fa-chair text-aqua-500 mr-2"></i> Active Seating Registry
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-beige-200 text-[10px] text-slate-400 tracking-widest font-extrabold bg-pearl-100">
                                        <th class="py-3 px-4">Guest</th>
                                        <th class="py-3 px-4">Date & Time</th>
                                        <th class="py-3 px-4">Guests Cover</th>
                                        <th class="py-3 px-4">Preferred Seating</th>
                                        <th class="py-3 px-4">Comments</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4 text-right font-mono">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-bookings-table" class="divide-y divide-pearl-200 text-sm"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ADMIN TAB 3: MENU CUSTOMIZER -->
                    <div id="admin-tab-content-menu" class="admin-tab-panel hidden p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="bg-pearl-100 p-5 rounded-xl border border-beige-200 h-fit text-left">
                                <h3 class="text-sm font-extrabold text-ocean-500 tracking-wider mb-4 flex items-center">
                                    <i class="fa-solid fa-circle-plus text-coral-500 mr-2"></i> Add/Edit Menu Item
                                </h3>
                                <form id="admin-menu-form" onsubmit="handleCreateMenuItem(event)" class="space-y-4">
                                    <input type="hidden" id="edit-item-id">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Item Title</label>
                                        <input type="text" id="menu-title" required class="w-full bg-white border border-beige-200 rounded-xl py-2 px-3 text-sm text-slate-800 focus:outline-none focus:border-ocean-500 font-semibold">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">Price (LKR)</label>
                                            <input type="number" step="0.01" id="menu-price" required class="w-full bg-white border border-beige-200 rounded-xl py-2 px-3 text-sm text-slate-800 focus:outline-none focus:border-ocean-500 font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">Category</label>
                                            <select id="menu-category" class="w-full bg-white border border-beige-200 rounded-xl py-2 px-3 text-sm text-slate-800 focus:outline-none focus:border-ocean-500 font-bold">
                                                <option value="Starters">Starters</option>
                                                <option value="Entrées">Entrées</option>
                                                <option value="Desserts">Desserts</option>
                                                <option value="Beverages">Beverages</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="checkbox" id="menu-is-veg" class="rounded border-beige-200 text-ocean-500 focus:ring-ocean-500 bg-white">
                                            <label class="text-xs font-bold text-slate-500">Vegetarian</label>
                                        </div>
                                        <div class="flex items-center space-x-1.5">
                                            <input type="checkbox" id="menu-is-soldout" class="rounded border-beige-200 text-coral-500 focus:ring-coral-500 bg-white">
                                            <label class="text-xs font-bold text-slate-500">Mark sold out</label>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Description</label>
                                        <textarea id="menu-desc" rows="3" required class="w-full bg-white border border-beige-200 rounded-xl py-2 px-3 text-sm text-slate-800 focus:outline-none focus:border-ocean-500"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Image URL</label>
                                        <input type="url" id="menu-img-url" required class="w-full bg-white border border-beige-200 rounded-xl py-2 px-3 text-sm text-slate-800 focus:outline-none focus:border-ocean-500">
                                    </div>
                                    <div class="pt-2 flex gap-3">
                                        <button type="submit" class="flex-grow bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-white text-xs font-extrabold py-2.5 px-4 rounded-xl transition-all">
                                            Save item
                                        </button>
                                        <button type="button" onclick="resetMenuForm()" class="bg-pearl-200 hover:bg-beige-100 text-slate-600 text-xs font-extrabold py-2.5 px-4 rounded-xl transition-all">
                                            Clear
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="lg:col-span-2 bg-pearl-100 p-5 rounded-xl border border-beige-200 text-left">
                                <h3 class="text-sm font-extrabold text-ocean-900 tracking-wider mb-4">Active Dishes Catalog</h3>
                                <div class="space-y-3 max-h-[450px] overflow-y-auto pr-2" id="admin-menu-list"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ADMIN TAB 4: LETTERS & INQUIRIES & NEWSLETTER -->
                    <div id="admin-tab-content-inquiries" class="admin-tab-panel hidden p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-sm font-extrabold text-ocean-900 tracking-wider text-left">
                                        <i class="fa-solid fa-envelope-open-text text-coral-500 mr-2"></i> Letters and Kind Notes
                                    </h2>
                                </div>
                                <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2" id="admin-inquiries-list"></div>
                            </div>
                            <div class="bg-pearl-100 p-5 rounded-xl border border-beige-200 text-left">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-sm font-extrabold text-ocean-900 tracking-wider">Tasting Club Members</h2>
                                    <span class="text-sm text-ocean-500 font-extrabold" id="admin-sub-count">0 Members</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-sm">
                                        <thead>
                                            <tr class="border-b border-beige-200 text-[10px] text-slate-400 tracking-widest font-extrabold bg-white">
                                                <th class="py-2.5 px-3">Subscriber Email</th>
                                                <th class="py-2.5 px-3">Date Joined</th>
                                            </tr>
                                        </thead>
                                        <tbody id="admin-subs-table" class="divide-y divide-pearl-200"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

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

    <div id="customize-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ocean-900/40 backdrop-blur-sm hidden">
        <div class="bg-white border border-beige-200 rounded-3xl max-w-md w-full p-6 relative shadow-2xl">
            <button onclick="closeCustomizeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
            <h3 class="serif-font text-2xl font-bold text-ocean-900 mb-1" id="cust-item-title">Customize Plate</h3>
            <p class="text-slate-500 text-sm mb-5" id="cust-item-desc">Tweak the plate exactly how you would like it served.</p>
            <div class="space-y-4 mb-5 text-left">
                <h4 class="text-xs font-extrabold tracking-wider text-ocean-500">Optional Extras</h4>
                <div class="space-y-3" id="custom-choices-container"></div>
            </div>
            <div class="flex items-center justify-between border-t border-beige-200 pt-4 mt-5">
                <span class="text-sm font-bold text-slate-400">Total Custom Price</span>
                <span id="cust-live-total" class="text-lg font-black text-coral-500 font-mono">LKR 0.00</span>
            </div>
            <button id="btn-confirm-customization" class="w-full mt-4 bg-gradient-to-r from-ocean-500 to-aqua-500 hover:brightness-110 text-white font-bold py-3 px-5 rounded-xl shadow-md transition-all text-sm tracking-wider text-center">
                Confirm and Add
            </button>
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
                        <span class="block text-[10px] text-aqua-200 tracking-wider font-semibold">Unawatuna Reef Sanctuary</span>
                    </div>
                </a>
                <p class="text-slate-400 text-sm leading-relaxed text-justify">
                    A luxurious oceanside dining retreat where fresh line-caught fish and interactive table bookings meet warm Sri Lankan hospitality.
                </p>
            </div>

            <div class="text-left">
                <h4 class="text-white font-extrabold text-sm mb-5 tracking-wider">Quick Links</h4>
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
                <h4 class="text-white font-extrabold text-sm mb-5 tracking-wider">Opening Hours</h4>
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
                <h4 class="text-white font-extrabold text-sm mb-5 tracking-wider">Contact</h4>
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
                    <span class="block text-[10px] text-slate-500 font-black">Secure Checkout</span>
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

    <script src="js/script.js"></script>
</body>
</html>
