# Conception Base de Donnée

## Synthèse

Cette base de données est conçue pour supporter un système de gestion de régime alimentaire et d’activités physiques.

Elle gère les utilisateurs, leurs portefeuilles, leurs informations corporelles, leurs objectifs ainsi que leurs programmes nutritionnels.

Le système suit la progression des utilisateurs, gère les transactions financières et fournit des recommandations personnalisées basées sur les données utilisateur.

Certaines parties du système sont automatisées (recommandations, suivi, etc.), tandis que d’autres dépendent des informations fournies par les utilisateurs (mesures corporelles, objectifs, etc.).

## Tables
### Gender
| Field | Type | Description |
|------|------|------------|
|id    | INT  |  Primary Key |
|name  | VARCHAR (50) | e.g. Homme / Femme |


### Users
| Field | Type | Description |
|------|------|--------------|
|id    | INT  |  Primary Key |
|name  | VARCHAR (50) |
|email | VARCHAR (255) | unique|
|password | VARCHAR (255)|
|birth_date| DATE ||
|gender_id | INT | Foreign Key|
|is_gold| TINYINT(1) | 0 (False) / 1 (True)|

### Wallets
| Field | Type | Description |
|-------|------|-------------|
|id     |  INT | Primary Key |
|user_id|  INT | Foreign Key |
|balance| DECIMAL (19, 4) |  |


### Transaction_types
| Field | Type | Description |
|-------|------|-------------|
|id     |  INT | Primary Key |
|name|  VARCHAR(50) | (credit, debit, ...) |


### Transactions
| Field | Type | Description |
|-------|------|-------------|
|id     | INT  | PRIMARY KEY |
|wallet_id| INT| FOREIGN KEY | 
|amount| DECIMAL (19, 4) ||
|transaction_type_id| INT | FOREIGN KEY|
|created_at| DATETIME ||


### Codes
| Field | Type | Description |
|-------|------|-------------|
|id     | INT  | PRIMARY KEY |
|code_value| VARCHAR(50) | e.g "A25SX71D" |
|amount| DECIMAL(19,4) | somme obtenue en rentrant le Codes|
|used_by_user_id| INT | FK |
|date_of_use| DATETIME ||

### Body_measurements
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|user_id | INT | FOREIGN KEY |
|height | DECIMAL (5, 2) | en cm, utiliser pour le calcul IMC et BMR|
|weight | DECIMAL (6, 2) | en kg, utiliser pour le calcul IMC et BMR|
|created_at| DATE | utiliser pour suivre les progès |

### Goals
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|name| VARCHAR(120) | prise de poids, perte de poids, atteindre son IMC ideal |
|description | TEXT ||

### User_goals
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|user_id | INT |  FOREIGN KEY |
|goal_id | INT | FOREIGN KEY |
|min_kg| DECIMAL(6,2)||
|max_kg| DECIMAL(6,2)||
|start_date| DATE | |

### Plan_templates
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|goal_id | INT | FOREIGN KEY |
|imc_min| DECIMAL(5,2) ||
|imc_max| DECIMAL(5,2) ||
|duration_days| INT | Durée en jour |

### Diets
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|name | VARCHAR(50) | e.g Diets A|
|description | TEXT ||

### Diet_duration_pricing
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|diet_id | INT | FK |
|duration_days| INT ||
|price | DECIMAL (19, 4)||

### Food_categories
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|name| VARCHAR(50) | e.g viande, poisson, volaille |

### Food_items
| Field | Type | Description |
|-------|------|-------------|
|id| INT| PRIMARY KEY|
|category_id| INT | FK|
|name| VARCHAR(50)| e.g. cuisse de poulet, saumon|
|calories_per_100g| DECIMAL(19,4) | en Kcal|

### Diet_compositions
| Field | Type | Description |
|-------|------|-------------|
|diet_id | INT | PRIMARY KEY / FK | 
|food_item_id| INT| PRIMARY KEY / FK|
|quantity| DECIMAL (19, 4)| en gramme |
|percentage| DECIMAL (5, 2)||

### Activities
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|name | VARCHAR (50) | |
|description | TEXT ||
|met_value| DECIMAL(6,2) | utiliser pour le calcule des calories brulées|

### Template_diets
| Field | Type | Description |
|-------|------|-------------|
|template_id | INT | PRIMARY KEY / FK |
|diet_id| INT | PRIMARY KEY / FK |

### Template_activities
| Field | Type | Description |
|-------|------|-------------|
|template_id | INT | PRIMARY KEY / FK |
|activity_id | INT | PRIMARY KEY / FK |
|frequency_per_week| INT | |
|duration_minutes| INT | |

### Recommendations
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|user_id | INT | FK |
|template_id | INT | FK |
|generated_at | DATETIME | date de génération |
|start_date | DATE | |
|end_date | DATE | |
|status | VARCHAR(20) | active, rejected, completed |
|trigger_measurement_id | INT | FK (BodyMeasurement_id) |

### User_diet_purchases
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|user_id| INT | FK |
|diet_id| INT | FK |
|duration_days| INT ||
|price_paid| DECIMAL (19, 4)| prix après remise |
|discount_applied| DECIMAL (19, 4)| remise |


## Relations
- Un `Users` dispose d'un `Wallets`.
- Un `Wallets` appartient à un `Users`.
- Un `Users` peut avoir de nombreuses `Transactions` (via `Wallets`).
- Un `Users` peut avoir de nombreuses `Body_measurements`.
- Un `Users` peut avoir de nombreux `Goals` via `User_goals`.
- Un `Users` peut avoir de nombreuses `Recommendations`.
- Un `Users` peut effectuer de `User_diet_purchases`.

- Un `Wallets` contient plusieurs `Transactions`.
- Une `Transactions` appartient à un `Wallets`.
- Une `Transactions` a un `Transaction_types`.

- Un `Users` peut avoir plusieurs `Goals`.
- Un `Goals` peut être attribué à plusieurs `Users` via `User_goals`.
- `User_goals` relie un `Users` à un `Goals`.

- Un `Plan_templates` est associé à un `Goals`.
- Un `Goals` peut avoir plusieurs `Plan_templates`.

- Un `Diets` peut avoir plusieurs `Diet_duration_pricing`.
- Un `Food_items` appartient a un `Food_categories`.
- Un `Food_categories` peut avoir plusieurs `Food_items`.
- Un `Diets` peut contenir plusieurs `Food_items` via `Diet_compositions`.
- Un `Food_items` peut appartenir a plusieurs `Diets` via `Diet_compositions`.

- Un `Plan_templates` peut avoir plusieurs `Activities` via `Template_activities`.
- Un `Activities` peut appartenir à plusieurs `Plan_templates`.

- Un `Plan_templates` peut contenir plusieurs `Diets` via `Template_diets`.
- Un `Diets` peut appartenir à plusieurs `Plan_templates`.

- Un `Users` peut avoir plusieurs `Recommendations`.
- Un `Recommendations` est générée à partir d'un seul `Plan_templates`.
- Un `Recommendations` appartient à un `Users`.

- Un `Users` peut acheter plusieurs `Diets`.
- Un `Diets` peut être acheter par plusieurs `Users`.
- `User_diet_purchases` relie `Users` et `Diets` avec les détails de l'achats.
- Un `Codes` peut etre utilise par un `Users`.

## Règles à suivre

- L'email d'un `Users` doit être unique.
- Un `Users` ne peut posséder qu'un seul `Wallets`.
- Le genre d'un `Users` doit référencer une entrée valide dans `Gender`.

- Le solde d'un `Wallets` ne peut pas être négatif.
- Le montant d'une `Transactions` doit être supérieur à 0.
- Un `Codes` ne peut être utilisé qu'une seule fois.

- La taille et le poids doivent être supérieurs à 0.
- Les `Body_measurements` sont utilisées pour calculer l'IMC utilisateur.

- Chaque objectif doit référencer un `Goals` valide.

- Une `Recommendations` doit être générée à partir d'une `Body_measurements` valide.
- La date de fin d'une `Recommendations` doit être postérieure à la date de début.

- Le pourcentage total des `Food_categories` d'un `Diets` doit être égal à 1.0000.
- La durée d'une offre de `Diet_duration_pricing` doit être supérieure à 0.
- Le prix d'un `Diet_duration_pricing` doit être positif.

## Origine des données

### Données utilisateur

Les données suivantes sont fournies ou modifiées directement par les utilisateurs :
- `Users`
- `Body_measurements`
- `User_goals`
- `User_diet_purchases`

### Données administrateur / configuration

Les données suivantes sont gérées par les administrateurs afin de configurer le système :
- `Gender`
- `Transaction_types`
- `Goals`
- `Food_categories`
- `Food_items`
- `Diets`
- `Diet_compositions`
- `Diet_duration_pricing`
- `Activities`
- `Plan_templates`
- `Template_diets`
- `Template_activities`
- `Codes`

### Données générées automatiquement

Certaines données sont générées ou mises à jour automatiquement par le système :
- `Recommendations`
- `Transactions`
- le solde et la création du `Wallets`
- le statut des `Recommendations`