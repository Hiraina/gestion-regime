# Conception Base de Donnée

## Synthèse

Cette base de données est conçue pour supporter un système de gestion de régime alimentaire et d’activités physiques.

Elle gère les utilisateurs, leurs portefeuilles, leurs informations corporelles, leurs objectifs ainsi que leurs programmes nutritionnels.

Le système suit la progression des utilisateurs, gère les transactions financières et fournit des recommandations personnalisées basées sur les données utilisateur.

Certaines parties du système sont automatisées (recommandations, suivi, etc.), tandis que d’autres dépendent des informations fournies par les utilisateurs (mesures corporelles, objectifs, etc.).

## Tables

### User
| Field | Type | Description |
|------|------|------------|
|id    | INT  |  Primary Key |
|name  | VARCHAR (50) |
|email | VARCHAR (255) | unique|
|password | VARCHAR (255)|
|gender | CHAR (1) | M / F |
|is_gold| TINYINT(1) | 0 (False) / 1 (True)|

### Wallet
| Field | Type | Description |
|-------|------|-------------|
|id     |  INT | Primary Key |
|user_id|  INT | Foreign Key |
|balance| DECIMAL (19, 4) |  |


### TransactionType
| Field | Type | Description |
|-------|------|-------------|
|id     |  INT | Primary Key |
|name|  VARCHAR(50) | (credit, debit, ...) |


### Transaction
| Field | Type | Description |
|-------|------|-------------|
|id     | INT  | PRIMARY KEY |
|wallet_id| INT| FOREIGN KEY | 
|amount| DECIMAL (19, 4) ||
|transaction_type_id| INT | FOREIGN KEY|
|created_at| DATETIME ||


### Code
| Field | Type | Description |
|-------|------|-------------|
|id     | INT  | PRIMARY KEY |
|code_value| VARCHAR(50) | e.g "A25SX71D" |
|amount| DECIMAL(19,4) | somme obtenue en rentrant le code|
|is_used| TINYINT(1) | 0 / 1 |

### BodyMeasurement
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|user_id | INT | FOREIGN KEY |
|height | DECIMAL (5, 2) | utiliser pour le calcul IMC |
|weight | DECIMAL (6, 2) | utiliser pour le calcul IMC |
|created_at| DATE | utiliser pour suivre les progès |

### Goal
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|name| VARCHAR(128) | prise de poids, perte de poids, atteindre son IMC ideal |

### UserGoal
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|user_id | INT |  FOREIGN KEY |
|goal_id | INT | FOREIGN KEY |
|start_date| DATE | |

### PlanTemplate
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|goal_id | INT | FOREIGN KEY |
|imc_min| DECIMAL(5,2) ||
|imc_max| DECIMAL(5,2) ||
|duration| INT | Durée en jour |

### Diet
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|name | VARCHAR(50) | e.g Diet A|
|description | TEXTE ||

### Diet Duration Pricing
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|diet_id | INT | FK |
|duration_days| INT ||
|price | DECIMAL (19, 4)||

### FoodCategory
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|name| VARCHAR(50) | e.g viande, poisson, volaille |

### DietComposition
| Field | Type | Description |
|-------|------|-------------|
|diet_id | INT | PRIMARY KEY / FK | 
|category_id| INT | PRIMARY KEY / FK|
|percentage| DECIMAL (5, 4)| de 0.0000 à 1.0000|

### Activity
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|name | VARCHAR (50) | |
|description | TEXTE ||

### TemplateDiet
| Field | Type | Description |
|-------|------|-------------|
|template_id | INT | PRIMARY KEY / FK |
|diet_id| INT | PRIMARY KEY / FK |

### TemplateActivity
| Field | Type | Description |
|-------|------|-------------|
|template_id | INT | PRIMARY KEY / FK |
|activity_id | INT | PRIMARY KEY / FK |

### Recommendation
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|user_id | INT | FK |
|template_id | INT | FK |
|start_date | DATE | |
|end_date | DATE | |

### UserDietPurchase
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|user_id| INT | FK |
|diet_id| INT | FK |
|duration_days| INT ||
|price_paid| DECIMAL (19, 4)| prix après remise |
|discount_applied| DECIMAL (19, 4)| remise |

