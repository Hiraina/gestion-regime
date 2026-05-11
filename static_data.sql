INSERT INTO gender(name) VALUES
('Homme'),
('Femme');

INSERT INTO transaction_types(name) VALUES
('credit'),
('debit');

INSERT INTO goals(name) VALUES
('Perte de poids'),
('Prise de poids'),
('Atteindre l\'IMC idéal');

INSERT INTO goals (name, description) VALUES
('Perdre du poids', 'Réduire son poids'),
('Prendre du poids', 'Augmenter son poids'),
('IMC idéal', 'Atteindre un IMC équilibré');

INSERT INTO food_categories(name) VALUES
('Viande'),
('Poisson'),
('Volaille');

INSERT INTO food_items(category_id, name, calories_per_100g) VALUES
(1, 'Boeuf', 250),
(1, 'Porc', 240),
(1, 'Agneau', 294),
(2, 'Saumon', 208),
(2, 'Thon', 132),
(2, 'Morue', 82),
(3, 'Poulet', 165),
(3, 'Dinde', 135),
(3, 'Canard', 337);

INSERT INTO activities(name, description, met_value) VALUES
('Marche rapide', 'Marche à un rythme soutenu', 3.8),
('Course à pied', 'Jogging ou course légère', 9.8),
('Cyclisme', 'Vélo ou cyclisme indoor', 8.0),
('Natation', 'Nage libre ou crawl', 7.0),
('Yoga', 'Séance de yoga relaxante', 2.5);

INSERT INTO codes(code_value, amount) VALUES
('abcd', 1500),
('efgh', 75),
('1234', 10000);
