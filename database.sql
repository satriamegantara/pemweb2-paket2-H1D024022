CREATE DATABASE IF NOT EXISTS umkmart CHARACTER
SET
    utf8mb4 COLLATE utf8mb4_unicode_ci;

USE umkmart;

CREATE TABLE
    users (
        id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM ('admin', 'penjual', 'pembeli') DEFAULT 'pembeli',
        phone VARCHAR(20),
        address TEXT,
        email_verified_at TIMESTAMP NULL,
        remember_token VARCHAR(100) NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    );

CREATE TABLE
    stores (
        id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        user_id BIGINT UNSIGNED UNIQUE NOT NULL,
        store_name VARCHAR(100) NOT NULL,
        description TEXT,
        logo VARCHAR(255),
        address TEXT,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
    );

CREATE TABLE
    categories (
        id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(80) NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL,
        icon VARCHAR(255),
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    );

CREATE TABLE
    products (
        id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        store_id BIGINT UNSIGNED NOT NULL,
        category_id BIGINT UNSIGNED NOT NULL,
        name VARCHAR(150) NOT NULL,
        slug VARCHAR(180) UNIQUE NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        stock INT UNSIGNED DEFAULT 0,
        weight INT UNSIGNED COMMENT 'gram',
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (store_id) REFERENCES stores (id),
        FOREIGN KEY (category_id) REFERENCES categories (id)
    );

CREATE TABLE
    product_images (
        id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        product_id BIGINT UNSIGNED NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        is_primary BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
    );

CREATE TABLE
    orders (
        id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        order_code VARCHAR(30) UNIQUE NOT NULL,
        buyer_id BIGINT UNSIGNED NOT NULL,
        total_amount DECIMAL(12, 2) NOT NULL,
        shipping_address TEXT NOT NULL,
        payment_proof VARCHAR(255),
        status ENUM (
            'menunggu_pembayaran',
            'dibayar',
            'diproses',
            'dikirim',
            'selesai',
            'dibatalkan'
        ) DEFAULT 'menunggu_pembayaran',
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (buyer_id) REFERENCES users (id) order_id BIGINT UNSIGNED NOT NULL,
        product_id BIGINT UNSIGNED NOT NULL,
        store_id BIGINT UNSIGNED NOT NULL,
        qty INT UNSIGNED NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        subtotal DECIMAL(12, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products (id),
        FOREIGN KEY (store_id) REFERENCES stores (id)
    );

CREATE TABLE
    order_items (
        id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
        order_id BIGINT UNSIGNED NOT NULL,
        product_id BIGINT UNSIGNED NOT NULL,
        store_id BIGINT UNSIGNED NOT NULL,
        qty INT UNSIGNED NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        subtotal DECIMAL(12, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products (id),
        FOREIGN KEY (store_id) REFERENCES stores (id)
    );