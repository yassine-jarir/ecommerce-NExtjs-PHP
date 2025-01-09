 CREATE DATABASE ecom_Craft;
USE ecom_Craft;

 CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'client') DEFAULT 'client',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

 CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

 CREATE TABLE commades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

 CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commades_id INT NOT NULL,
    produits_id INT NOT NULL,
    quantity INT NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commades_id) REFERENCES commades(id) ON DELETE CASCADE,
    FOREIGN KEY (produits_id) REFERENCES produits(id) ON DELETE CASCADE
);

  
 
CREATE TRIGGER before_produit_delete
BEFORE DELETE ON produits
FOR EACH ROW
BEGIN
     SET NEW.deleted_at = CURRENT_TIMESTAMP;
END;
 

 INSERT INTO users (name, email, password, role, is_active) 
VALUES ('Admin', 'admin@example.com', MD5('admin_password'), 'admin', TRUE);
 