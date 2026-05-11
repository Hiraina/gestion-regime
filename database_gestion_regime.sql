DROP DATABASE IF EXISTS gestion_regime;

CREATE DATABASE gestion_regime;
USE gestion_regime;

CREATE OR REPLACE TABLE gender(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);

CREATE OR REPLACE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    birth_date DATE,
    gender_id INT,
    is_gold TINYINT(1),
    FOREIGN KEY (gender_id) REFERENCES gender(id)
);

CREATE OR REPLACE TABLE wallets(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    balance DECIMAL(19,4),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE OR REPLACE TABLE transaction_types(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);

CREATE OR REPLACE TABLE transactions(
    id INT AUTO_INCREMENT PRIMARY KEY,
    wallet_id INT,
    amount DECIMAL(19,4),
    transaction_type_id INT,
    created_at DATETIME,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id),
    FOREIGN KEY (transaction_type_id) REFERENCES transaction_types(id)
);

CREATE OR REPLACE TABLE codes(
    id INT AUTO_INCREMENT PRIMARY KEY,
    code_value VARCHAR(50),
    amount DECIMAL(19, 4),
    used_by_user_id INT,
    date_of_use DATETIME,
    FOREIGN KEY (used_by_user_id) REFERENCES users(id)
);

CREATE OR REPLACE TABLE body_measurements(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    height DECIMAL(5,2),
    weight DECIMAL(6,2),
    created_at DATE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE OR REPLACE TABLE goals(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120),
    description TEXT
);

CREATE OR REPLACE TABLE user_goals(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    goal_id INT,
    start_date DATE,
    min_kg DECIMAL(6,2),
    max_kg DECIMAL(6,2),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (goal_id) REFERENCES goals(id)
);

CREATE OR REPLACE TABLE diets(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    description TEXT
);

CREATE OR REPLACE TABLE diet_duration_pricing(
    id INT AUTO_INCREMENT PRIMARY KEY,
    diet_id INT,
    price_per_day DECIMAL(19, 4),
    FOREIGN KEY (diet_id) REFERENCES diets(id)
);

CREATE OR REPLACE TABLE food_categories(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);

CREATE OR REPLACE TABLE food_items(
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(50),
    calories_per_100g DECIMAL(19,4),
    FOREIGN KEY (category_id) REFERENCES food_categories(id)
);

CREATE OR REPLACE TABLE food_distributions(
    diet_id INT,
    category_id INT,
    percentage DECIMAL(5,2),
    FOREIGN KEY (diet_id) REFERENCES diets(id),
    FOREIGN KEY (category_id) REFERENCES food_categories(id),
    PRIMARY KEY(diet_id, category_id)
);

CREATE OR REPLACE TABLE activities(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    description TEXT,
    met_value DECIMAL(6,2)
);

CREATE OR REPLACE TABLE recommendations(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    diet_id INT,
    generated_at DATETIME,
    start_date DATE,
    end_date DATE,
    status VARCHAR(20),
    trigger_measurement_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (diet_id) REFERENCES diets(id),
    FOREIGN KEY (trigger_measurement_id) REFERENCES body_measurements(id)
);


CREATE OR REPLACE TABLE diet_compositions(
    id INT AUTO_INCREMENT PRIMARY KEY,
    recommendation_id INT,
    diet_id INT,
    food_item_id INT,
    quantity DECIMAL(19,4),
    FOREIGN KEY (recommendation_id) REFERENCES recommendations(id),
    FOREIGN KEY (diet_id) REFERENCES diets(id),
    FOREIGN KEY (food_item_id) REFERENCES food_items(id)
);

CREATE OR REPLACE TABLE recommendation_activities(
    recommendation_id INT,
    activity_id INT,
    frequency_per_week INT,
    duration_minutes INT,
    FOREIGN KEY (recommendation_id) REFERENCES recommendations(id),
    FOREIGN KEY (activity_id) REFERENCES activities(id),
    PRIMARY KEY (recommendation_id, activity_id)
);

CREATE OR REPLACE TABLE user_diet_purchases(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    diet_id INT,
    duration_days INT,
    price_paid DECIMAL(19,4),
    discount_applied DECIMAL(19,4),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (diet_id) REFERENCES diets(id)
);




CREATE OR REPLACE TABLE user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    age INT NOT NULL,
    num_telephone VARCHAR(20) NOT NULL,
    adresse TEXT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id)
);