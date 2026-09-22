-- =====================================================================
-- Sea Pearl Bistro - Online Food Ordering & Restaurant Management System
-- Database schema (MySQL / MariaDB)
--
-- This mirrors the data currently held in browser localStorage by
-- js/script.js (profiles, menu, orders, bookings, subscribers, inquiries)
-- so the front end can be wired up to a real backend via PHP/AJAX.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS seapearl_bistro
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE seapearl_bistro;

-- ---------------------------------------------------------------------
-- Users (guest / customer accounts)  <- localStorage "profiles"
-- ---------------------------------------------------------------------
CREATE TABLE users (
    user_id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username        VARCHAR(50)  NOT NULL UNIQUE,
    full_name       VARCHAR(120) NOT NULL,
    mobile          VARCHAR(20)  NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    delivery_address VARCHAR(255) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Administrators / staff accounts (for the Management Hub)
-- ---------------------------------------------------------------------
CREATE TABLE admins (
    admin_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username        VARCHAR(50)  NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('staff','administrator') NOT NULL DEFAULT 'staff',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Menu items  <- localStorage "menu" (defaultMenuItems)
-- ---------------------------------------------------------------------
CREATE TABLE menu_items (
    item_id         VARCHAR(10) PRIMARY KEY,           -- e.g. 'm1'
    title           VARCHAR(150) NOT NULL,
    category        VARCHAR(50)  NOT NULL,              -- Starters, Entrées, Desserts, ...
    price           DECIMAL(10,2) NOT NULL,
    description     TEXT NULL,
    is_veg          TINYINT(1) NOT NULL DEFAULT 0,
    image_url       VARCHAR(500) NULL,
    status          ENUM('Available','Sold Out') NOT NULL DEFAULT 'Available',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Optional customisations per menu item (e.g. "Extra cheese +250")
CREATE TABLE menu_item_options (
    option_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id         VARCHAR(10) NOT NULL,
    option_name     VARCHAR(150) NOT NULL,
    extra_price     DECIMAL(10,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Delivery zones / coverage rules
-- ---------------------------------------------------------------------
CREATE TABLE delivery_zones (
    zone_id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    zone_name       VARCHAR(100) NOT NULL,
    min_order_value DECIMAL(10,2) NOT NULL DEFAULT 1000.00,
    flat_delivery_rate DECIMAL(10,2) NOT NULL DEFAULT 300.00,
    service_hours   VARCHAR(50) NOT NULL DEFAULT '10:30 AM - 11:30 PM'
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Orders  <- localStorage "orders"
-- ---------------------------------------------------------------------
CREATE TABLE orders (
    order_id        VARCHAR(20) PRIMARY KEY,            -- e.g. 'ORD-1234'
    user_id         INT UNSIGNED NULL,
    zone_id         INT UNSIGNED NULL,
    delivery_name   VARCHAR(120) NOT NULL,
    delivery_phone  VARCHAR(20)  NOT NULL,
    delivery_address VARCHAR(255) NOT NULL,
    payment_method  ENUM('COD','Online Card') NOT NULL DEFAULT 'COD',
    subtotal        DECIMAL(10,2) NOT NULL,
    hospitality_charge DECIMAL(10,2) NOT NULL DEFAULT 0,
    delivery_fee    DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_amount    DECIMAL(10,2) NOT NULL,
    status          ENUM('Received','Preparing','Out for Delivery','Delivered','Cancelled')
                     NOT NULL DEFAULT 'Received',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (zone_id) REFERENCES delivery_zones(zone_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE order_items (
    order_item_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id        VARCHAR(20) NOT NULL,
    item_id         VARCHAR(10) NOT NULL,
    quantity        INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price      DECIMAL(10,2) NOT NULL,
    customizations  VARCHAR(500) NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Tables (beach-deck floor layout)
-- ---------------------------------------------------------------------
CREATE TABLE restaurant_tables (
    table_id        VARCHAR(20) PRIMARY KEY,            -- e.g. 'Premium T1', 'Beach T3'
    zone            VARCHAR(60) NOT NULL,                -- 'Premium Sunset Deck' / 'Beach Sand Lounge'
    seats           INT UNSIGNED NOT NULL DEFAULT 2,
    status          ENUM('Available','Selected','Occupied') NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table reservations / bookings  <- localStorage "bookings"
-- ---------------------------------------------------------------------
CREATE TABLE reservations (
    reservation_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NULL,
    table_id        VARCHAR(20) NOT NULL,
    full_name       VARCHAR(120) NOT NULL,
    contact_mobile  VARCHAR(20) NOT NULL,
    reservation_date DATE NOT NULL,
    arrival_time    TIME NOT NULL,
    status          ENUM('Held','Confirmed','Cancelled','Completed') NOT NULL DEFAULT 'Held',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (table_id) REFERENCES restaurant_tables(table_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Contact form enquiries  <- localStorage "inquiries"
-- ---------------------------------------------------------------------
CREATE TABLE inquiries (
    inquiry_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(120) NOT NULL,
    email           VARCHAR(150) NOT NULL,
    telephone       VARCHAR(20) NULL,
    message         TEXT NOT NULL,
    is_read         TINYINT(1) NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Newsletter / promo subscribers  <- localStorage "subscribers"
-- ---------------------------------------------------------------------
CREATE TABLE subscribers (
    subscriber_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(150) NOT NULL UNIQUE,
    subscribed_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================================
-- Seed data
-- =====================================================================

INSERT INTO delivery_zones (zone_name, min_order_value, flat_delivery_rate, service_hours) VALUES
('Unawatuna Centre', 1000.00, 300.00, '10:30 AM - 11:30 PM'),
('Galle Fort',        1000.00, 300.00, '10:30 AM - 11:30 PM'),
('Thalpe Shoreline',  1000.00, 300.00, '10:30 AM - 11:30 PM'),
('Mihiripenna',       1000.00, 300.00, '10:30 AM - 11:30 PM'),
('Habaraduwa Zone',   1000.00, 300.00, '10:30 AM - 11:30 PM'),
('Weligama Sand Point',1000.00, 300.00, '10:30 AM - 11:30 PM');

INSERT INTO menu_items (item_id, title, category, price, description, is_veg, image_url, status) VALUES
('m1', 'Hand-pulled lagoon mud crab kothu', 'Entrées', 3600.00,
 'Delicately chopped organic kothu flatbread wok-tossed with freshly hand-shucked lagoon mud crab claws, organic quail eggs, and a secret black pepper reduction.',
 0, 'https://images.unsplash.com/photo-1559314809-0d155014e29e?auto=format&fit=crop&q=80&w=600', 'Available'),
('m2', 'Southern deviled red snapper fillets', 'Starters', 2400.00,
 'Crisped ocean snapper fillets tossed in a traditional spicy Unawatuna sweet-chili glaze, combined with fresh capsicums, caramelized red onions, and lime juice.',
 0, 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&q=80&w=600', 'Available'),
('m3', 'Grand Unawatuna spiny lobster feast', 'Entrées', 7800.00,
 'Whole premium spiny ocean lobster grilled over slow-burning raw coconut shells, basted with organic garlic herb butter, and paired with thick woodfired paan blocks.',
 0, 'https://images.unsplash.com/photo-1628243342637-9111c3e22891?auto=format&fit=crop&q=80&w=600', 'Available'),
('m4', 'Truffled ala theldala croquettes', 'Starters', 1350.00,
 'Hand-mashed local gold potatoes cooked down with chili flakes, red onions, and local curry leaves, coated in breadcrumbs and infused with organic white truffle oil.',
 1, 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&q=80&w=600', 'Available'),
('m5', 'Unawatuna narang sunset dessert', 'Desserts', 1100.00,
 'Baked local jaggery-infused wattalappam custard, layered with coconut narang orange segments, cardamom reduction, and baked cashew crumbs.',
 1, 'https://images.unsplash.com/photo-1541783245831-57d6fb0926d3?auto=format&fit=crop&q=80&w=600', 'Available'),
('m6', 'Mirissa bluefin tuna tataki', 'Entrées', 2900.00,
 'Premium hand-caught bluefin tuna steak crusted in cracked black peppercorns, flash-seared medium-rare, and served over fresh local sea-green avocado salad.',
 0, 'https://images.unsplash.com/photo-1534604973900-c43ab4c2e0ab?auto=format&fit=crop&q=80&w=600', 'Available');

INSERT INTO menu_item_options (item_id, option_name, extra_price) VALUES
('m1', 'Extra shredded cheese melt', 250.00),
('m1', 'Double mud crab portions', 950.00),
('m2', 'Spicy level: extreme extra hot', 0.00),
('m2', 'Add roasted cashew crown', 150.00),
('m3', 'Signature grass-fed garlic butter pool', 120.00),
('m3', 'Extra slab of woodfired roast paan', 80.00),
('m4', 'Organic spicy avocado dip', 100.00),
('m5', 'Extra kithul palm syrup drizzle', 50.00),
('m6', 'Chef spicy wasabi-soy dip', 100.00);

INSERT INTO restaurant_tables (table_id, zone, seats, status) VALUES
('Premium T1', 'Premium Sunset Deck', 2, 'Available'),
('Premium T2', 'Premium Sunset Deck', 4, 'Available'),
('Premium T3', 'Premium Sunset Deck', 2, 'Occupied'),
('Premium T4', 'Premium Sunset Deck', 6, 'Available'),
('Beach T1',   'Beach Sand Lounge',   4, 'Available'),
('Beach T2',   'Beach Sand Lounge',   4, 'Occupied'),
('Beach T3',   'Beach Sand Lounge',   2, 'Available'),
('Beach T4',   'Beach Sand Lounge',   8, 'Available');

-- Default administrator account placeholder.
-- Generate a real password hash first (run this in PHP):
--     php -r "echo password_hash('YOUR_PASSWORD', PASSWORD_BCRYPT), PHP_EOL;"
-- then paste the result below before running this script.
INSERT INTO admins (username, password_hash, role) VALUES
('admin', 'REPLACE_WITH_password_hash_OUTPUT', 'administrator');
