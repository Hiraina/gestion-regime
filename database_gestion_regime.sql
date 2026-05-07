DROP DATABASE IF EXIST gestion_regime;

CREATE DATABASE gestion_regime;
USE gestion_regime;

CREATE OR REPLACE TABLE gender(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);

CREATE OR REPLACE TABLE user(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    gender_id INT,
    is_gold TINYINT(1),
    FOREIGN KEY (gender_id) REFERENCES gender(id)
);

CREATE OR REPLACE TABLE wallet(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    balance DECIMAL(19,4),
    FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE OR REPLACE TABLE transactionType(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);

CREATE OR REPLACE TABLE transaction(
    id INT AUTO_INCREMENT PRIMARY KEY,
    wallet_id INT,
    amount DECIMAL(19,4),
    transaction_type_id INT,
    created_at DATETIME,
    FOREIGN KEY (wallet_id) REFERENCES wallet(id),
    FOREIGN KEY (transaction_type_id) REFERENCES transactionType(id)
);

CREATE OR REPLACE TABLE code(
    id INT AUTO_INCREMENT PRIMARY KEY,
    code_value VARCHAR(50),
    amount DECIMAL(19, 4),
    used_by_user_id INT,
    date_of_use DATETIME,
    is_used TINYINT(1),
    FOREIGN KEY (used_by_user_id) REFERENCES user(id)
);

CREATE OR REPLACE TABLE bodyMeasurement(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    height DECIMAL(5,2),
    weight DECIMAL(6,2),
    created_at DATE,
    FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE OR REPLACE TABLE goal(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120),
    description TEXT
);

CREATE OR REPLACE TABLE userGoal(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    goal_id,
    start_date DATE,
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (goal_id) REFERENCES goal(id)
);

CREATE OR REPLACE TABLE planTemplate(
    id INT AUTO_INCREMENT PRIMARY KEY,
    goal_id INT,
    imc_min DECIMAL(5,2),
    imc_max DECIMAL(5,2),
    duration INT,
    FOREIGN KEY (goal_id) REFERENCES goal(id)
);

CREATE OR REPLACE TABLE diet(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    description TEXT
);

CREATE OR REPLACE TABLE dietDurationPricing(
    id INT AUTO_INCREMENT PRIMARY KEY,
    diet_id INT,
    duration_days INT,
    price DECIMAL(19, 4),
    FOREIGN KEY (diet_id) REFERENCES diet(id)
);

CREATE OR REPLACE TABLE foodCategory(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50)
);

CREATE OR REPLACE TABLE dietComposition(
    diet_id INT,
    category_id INT,
    percentage DECIMAL(5,4),
    FOREIGN KEY (diet_id) REFERENCES diet(id),
    FOREIGN KEY (category_id) REFERENCES foodCategory(id),
    PRIMARY KEY (diet_id, category_id)
);

CREATE OR REPLACE TABLE activity(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    description TEXT
);

CREATE OR REPLACE TABLE templateDiet(
    template_id INT,
    diet_id INT,
    FOREIGN KEY (template_id) REFERENCES planTemplate(id),
    FOREIGN KEY (diet_id) REFERENCES diet(id),
    PRIMARY KEY(template_id, diet_id)
);

CREATE OR REPLACE TABLE templateActivity(
    template_id INT,
    activity_id INT,
    FOREIGN KEY (template_id) REFERENCES planTemplate(id),
    FOREIGN KEY (activity_id) REFERENCES activity(id),
    PRIMARY KEY (template_id, activity_id)    
);

CREATE OR REPLACE TABLE recommendation(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    template_id INT,
    generated_at DATETIME,
    start_date DATE,
    end_date DATE,
    status VARCHAR(20),
    trigger_measurement_id INT,
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (template_id) REFERENCES planTemplate(id),
    FOREIGN KEY (trigger_measurement_id) REFERENCES bodyMeasurement(id)
);

CREATE OR REPLACE TABLE userDietPurchase(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    diet_id INT,
    duration_days INT,
    price_paid DECIMAL(19,4),
    discount_applied DECIMAL(19,4),
    FOREIGN KEY (user_id) REFERENCES user(id),
    FOREIGN KEY (diet_id) REFERENCES diet(id)
);