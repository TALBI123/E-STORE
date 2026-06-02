-- =============================================
-- E-STORE : Base de données complète
-- =============================================

CREATE DATABASE IF NOT EXISTS estore
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE estore;

-- ─────────────────────────────────────────────
-- TABLE : users
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)  NOT NULL,
    email         VARCHAR(150)  NOT NULL UNIQUE,
    password_hash VARCHAR(255)  NOT NULL,
    role          ENUM('client','admin') NOT NULL DEFAULT 'client',
    phone         VARCHAR(20)   DEFAULT NULL,
    avatar        VARCHAR(255)  DEFAULT NULL,
    is_active     TINYINT(1)    NOT NULL DEFAULT 1,
    created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role  (role)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : categories
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    parent_id   INT UNSIGNED DEFAULT NULL,
    description TEXT         DEFAULT NULL,
    image       VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cat_parent
        FOREIGN KEY (parent_id) REFERENCES categories(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_slug      (slug),
    INDEX idx_parent_id (parent_id)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : products
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
    id           INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(200)  NOT NULL,
    slug         VARCHAR(200)  NOT NULL UNIQUE,
    description  TEXT          DEFAULT NULL,
    price        DECIMAL(10,2) NOT NULL,
    sale_price   DECIMAL(10,2) DEFAULT NULL,
    stock        INT UNSIGNED  NOT NULL DEFAULT 0,
    sku          VARCHAR(50)   DEFAULT NULL UNIQUE,
    image        VARCHAR(255)  DEFAULT NULL,
    category_id  INT UNSIGNED  DEFAULT NULL,
    is_active    TINYINT(1)    NOT NULL DEFAULT 1,
    views_count  INT UNSIGNED  NOT NULL DEFAULT 0,
    created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                               ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_category_id (category_id),
    INDEX idx_price       (price),
    INDEX idx_is_active   (is_active),
    FULLTEXT INDEX ft_name_desc (name, description)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : product_images
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS product_images (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary TINYINT(1)   NOT NULL DEFAULT 0,
    sort_order INT          NOT NULL DEFAULT 0,
    CONSTRAINT fk_pimg_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : product_attributes
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS product_attributes (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id      INT UNSIGNED NOT NULL,
    attribute_name  VARCHAR(100) NOT NULL,
    attribute_value VARCHAR(200) NOT NULL,
    CONSTRAINT fk_pattr_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_product_attr (product_id, attribute_name)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : addresses
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS addresses (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED  NOT NULL,
    label       VARCHAR(50)   NOT NULL DEFAULT 'Domicile',
    street      VARCHAR(255)  NOT NULL,
    city        VARCHAR(100)  NOT NULL,
    postal_code VARCHAR(10)   DEFAULT NULL,
    country     VARCHAR(100)  NOT NULL DEFAULT 'Maroc',
    is_default  TINYINT(1)    NOT NULL DEFAULT 0,
    CONSTRAINT fk_addr_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : carts
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS carts (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                          ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cart_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : cart_items
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS cart_items (
    id             INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    cart_id        INT UNSIGNED  NOT NULL,
    product_id     INT UNSIGNED  NOT NULL,
    quantity       INT UNSIGNED  NOT NULL DEFAULT 1,
    price_snapshot DECIMAL(10,2) NOT NULL,
    added_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ci_cart
        FOREIGN KEY (cart_id) REFERENCES carts(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ci_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uq_cart_product (cart_id, product_id),
    INDEX idx_cart_id    (cart_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : orders
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS orders (
    id               INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id          INT UNSIGNED  NOT NULL,
    total_amount     DECIMAL(10,2) NOT NULL,
    status           ENUM('pending','confirmed','processing',
                         'shipped','delivered','cancelled')
                     NOT NULL DEFAULT 'pending',
    shipping_address TEXT          NOT NULL,
    notes            TEXT          DEFAULT NULL,
    ordered_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status  (status)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : order_items
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS order_items (
    id         INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    order_id   INT UNSIGNED  NOT NULL,
    product_id INT UNSIGNED  NOT NULL,
    quantity   INT UNSIGNED  NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_oi_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_oi_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_order_id   (order_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : wishlists
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS wishlists (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    added_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wl_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_wl_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uq_wl_user_product (user_id, product_id),
    INDEX idx_user_id    (user_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : reviews
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reviews (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    rating     TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment    TEXT         DEFAULT NULL,
    is_approved TINYINT(1)  NOT NULL DEFAULT 0,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rev_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_rev_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uq_user_product_review (user_id, product_id),
    INDEX idx_product_id (product_id),
    INDEX idx_rating     (rating)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : coupons
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS coupons (
    id              INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    code            VARCHAR(50)   NOT NULL UNIQUE,
    discount_type   ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
    discount_value  DECIMAL(10,2) NOT NULL,
    min_order_amount DECIMAL(10,2) DEFAULT NULL,
    max_uses        INT UNSIGNED  DEFAULT NULL,
    used_count      INT UNSIGNED  NOT NULL DEFAULT 0,
    expires_at      TIMESTAMP     DEFAULT NULL,
    is_active       TINYINT(1)    NOT NULL DEFAULT 1,
    created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code      (code),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABLE : visits
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS visits (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45)  NOT NULL,
    country    VARCHAR(100) DEFAULT NULL,
    city       VARCHAR(100) DEFAULT NULL,
    page       VARCHAR(255) NOT NULL,
    user_agent TEXT         DEFAULT NULL,
    user_id    INT UNSIGNED DEFAULT NULL,
    visited_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_visit_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_ip         (ip_address),
    INDEX idx_visited_at (visited_at),
    INDEX idx_page       (page),
    INDEX idx_country    (country)
) ENGINE=InnoDB;

-- =============================================
-- DONNÉES DE DÉMARRAGE
-- =============================================

-- Catégories principales
INSERT INTO categories (name, slug, parent_id, description) VALUES
('Femmes',     'femmes',  NULL, 'Tous les articles pour femmes'),
('Hommes',     'hommes',  NULL, 'Tous les articles pour hommes'),
('Enfants',    'enfants', NULL, 'Tous les articles pour enfants');

-- Sous-catégories Femmes (parent_id = 1)
INSERT INTO categories (name, slug, parent_id, description) VALUES
('Vêtements Femmes',  'vetements-femmes',  1, 'Robes, tops, pantalons...'),
('Chaussures Femmes', 'chaussures-femmes', 1, 'Escarpins, baskets, sandales...'),
('Sacs Femmes',       'sacs-femmes',       1, 'Sacs à main, sacs à dos...'),
('Accessoires Femmes','accessoires-femmes',1, 'Bijoux, ceintures, écharpes...');

-- Sous-catégories Hommes (parent_id = 2)
INSERT INTO categories (name, slug, parent_id, description) VALUES
('Vêtements Hommes',  'vetements-hommes',  2, 'T-shirts, chemises, pantalons...'),
('Chaussures Hommes', 'chaussures-hommes', 2, 'Baskets, mocassins, boots...'),
('Sacs Hommes',       'sacs-hommes',       2, 'Sacs à dos, sacs de sport...'),
('Accessoires Hommes','accessoires-hommes',2, 'Ceintures, montres, casquettes...');

-- Sous-catégories Enfants (parent_id = 3)
INSERT INTO categories (name, slug, parent_id, description) VALUES
('Vêtements Enfants',  'vetements-enfants',  3, 'Vêtements garçon et fille'),
('Chaussures Enfants', 'chaussures-enfants', 3, 'Chaussures garçon et fille');

-- Administrateur par défaut
-- Mot de passe : Admin@1234  (hash bcrypt généré avec password_hash())
INSERT INTO users (name, email, password_hash, role) VALUES
('Administrateur', 'admin@estore.ma',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'admin');

-- Produits de démonstration
INSERT INTO products (name, slug, description, price, sale_price, stock, sku, category_id) VALUES
('Robe d''été fleurie',    'robe-ete-fleurie',
 'Robe légère à motifs floraux, idéale pour l''été.',
 299.00, 249.00, 15, 'ROB-001', 4),

('Sneakers Blanc Femme',   'sneakers-blanc-femme',
 'Baskets blanches tendance, semelle confortable.',
 450.00, NULL,   20, 'CHF-001', 5),

('Sac à Main Cuir Beige',  'sac-main-cuir-beige',
 'Sac à main en cuir véritable, fermeture dorée.',
 750.00, 650.00, 8,  'SAF-001', 6),

('Chemise Lin Bleu Homme', 'chemise-lin-bleu-homme',
 'Chemise en lin 100%, coupe droite, manches longues.',
 320.00, NULL,   25, 'CHH-001', 8),

('Jean Slim Noir Homme',   'jean-slim-noir-homme',
 'Jean slim stretch, coupe moderne.',
 380.00, 320.00, 30, 'JEH-001', 8),

('Baskets Running Homme',  'baskets-running-homme',
 'Chaussures de running légères, semelle amortissante.',
 520.00, NULL,   18, 'CHH-002', 9),

('T-shirt Enfant Dinosaure','tshirt-enfant-dinosaure',
 'T-shirt coton bio avec motif dinosaure rigolo.',
 89.00,  NULL,   40, 'ENF-001', 11),

('Veste Denim Femme',      'veste-denim-femme',
 'Veste en jean classique, coupe oversize.',
 490.00, 420.00, 12, 'VEF-001', 4);

-- Attributs produits
INSERT INTO product_attributes (product_id, attribute_name, attribute_value) VALUES
(1, 'Taille',   'XS,S,M,L,XL'),
(1, 'Couleur',  'Multicolore'),
(1, 'Matière',  '100% Coton'),
(2, 'Pointure', '36,37,38,39,40,41'),
(2, 'Couleur',  'Blanc'),
(3, 'Couleur',  'Beige'),
(3, 'Matière',  'Cuir véritable'),
(4, 'Taille',   'S,M,L,XL,XXL'),
(4, 'Couleur',  'Bleu ciel'),
(4, 'Matière',  '100% Lin'),
(5, 'Taille',   '28,30,32,34,36'),
(5, 'Couleur',  'Noir'),
(6, 'Pointure', '40,41,42,43,44,45'),
(6, 'Couleur',  'Gris/Orange'),
(7, 'Taille',   '2ans,4ans,6ans,8ans,10ans'),
(7, 'Couleur',  'Vert'),
(8, 'Taille',   'XS,S,M,L,XL'),
(8, 'Couleur',  'Bleu denim');

-- Coupon de bienvenue
INSERT INTO coupons (code, discount_type, discount_value, min_order_amount, max_uses) VALUES
('BIENVENUE10', 'percent', 10.00, 200.00, 100),
('ETE2025',     'fixed',   50.00, 500.00, 50);