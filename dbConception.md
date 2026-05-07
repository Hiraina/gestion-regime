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
|------|------|------------|
|id    | INT  |  Primary Key |
|name  | VARCHAR (50) |
|email | VARCHAR (255) | unique|
|password | VARCHAR (255)|
|gender_id | INT | Foreign Key|
|is_gold| TINYINT(1) | 0 (False) / 1 (True)|

### Wallet
| Field | Type | Description |
|-------|------|-------------|
|id     |  INT | Primary Key |
|users_id|  INT | Foreign Key |
|balance| DECIMAL (19, 4) |  |


### TransactionTypes
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
|amount| DECIMAL(19,4) | somme obtenue en rentrant le code|
|used_by_users_id| INT | FK |
|date_of_use| DATETIME ||

### BodyMeasurement
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|users_id | INT | FOREIGN KEY |
|height | DECIMAL (5, 2) | utiliser pour le calcul IMC |
|weight | DECIMAL (6, 2) | utiliser pour le calcul IMC |
|created_at| DATE | utiliser pour suivre les progès |

### Goal
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|name| VARCHAR(128) | prise de poids, perte de poids, atteindre son IMC ideal |
|description | TEXT ||

### UsersGoal
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|users_id | INT |  FOREIGN KEY |
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
|description | TEXT ||

### DietDurationPricing
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
|description | TEXT ||

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
|users_id | INT | FK |
|template_id | INT | FK |
|generated_at | DATETIME | date de génération |
|start_date | DATE | |
|end_date | DATE | |
|status | VARCHAR(20) | active, rejected, completed |
|trigger_measurement_id | INT | FK (BodyMeasurement_id) |

### UsersDietPurchase
| Field | Type | Description |
|-------|------|-------------|
|id | INT | PRIMARY KEY |
|users_id| INT | FK |
|diet_id| INT | FK |
|duration_days| INT ||
|price_paid| DECIMAL (19, 4)| prix après remise |
|discount_applied| DECIMAL (19, 4)| remise |


## Relations
- Un `Users` dispose d'un `Wallet`.
- Un `Wallet` appartient à un `Users`.
- Un `Users` peut avoir de nombreuses `Transaction` (via `Wallet`).
- Un `Users` peut avoir de nombreuses `BodyMeasurement`.
- Un `Users` peut avoir de nombreux `Goal` via `UsersGoal`.
- Un `Users` peut avoir de nombreuses `Recommendation`.
- Un `Users` peut effectuer de `UsersDietPurchase`.

- Un `Wallet` contient plusieurs `Transaction`.
- Une `Transaction` appartient à un `Wallet`.
- Une `Transaction` a un `TransactionType`.

- Un `Users` peut avoir plusieurs `Goal`.
- Un `Goal` peut être attribué à plusieurs `Users` via `UsersGoal`.
- `UsersGoal` relie un `Users` à un `Goal`.

- Un `PlanTemplate` est associé à un `Goal`.
- Un `Goal` peut avoir plusieurs `PlanTemplate`.

- Un `Diet` peut avoir plusieurs `DietDurationPricing`.
- Un `Diet` appartient à plusieurs `FoodCategory` via `DietComposition`.
- Un `FoodCategory` peut appartenir à plusieurs `Diet` via `DietComposition`.

- Un `PlanTemplate` peut avoir plusieurs `Activity` via `TemplateActivity`.
- Un `Activity` peut appartenir à plusieurs `PlanTemplate`.

- Un `PlanTemplate` peut contenir plusieurs `Diet` via `TemplateDiet`.
- Un `Diet` peut appartenir à plusieurs `PlanTemplate`.

- Un `Users` peut avoir plusieurs `Recommendation`.
- Un `Recommendation` est générée à partir d'un seul `PlanTemplate`.
- Un `Recommendation` appartient à un `Users`.

- Un `Users` peut acheter plusieurs `Diet`.
- Un `Diet` peut être acheter par plusieurs `Users`.
- `UsersDietPurchase` relie `Users` et `Diet` avec les détails de l'achats.

- Un `Code` peut être utiliser par un `Users`.

## Règles à suivre

- L'email d'un `Users` doit être unique.
- Un `Users` ne peut posséder qu'un seul `Wallet`.
- Le genre d'un `Users` doit référencer une entrée valide dans `Gender`.

- Le solde d'un `Wallet` ne peut pas être négatif.
- Le montant d'une `Transaction` doit être supérieur à 0.
- Un `Code` ne peut être utilisé qu'une seule fois.

- La taille et le poids doivent être supérieurs à 0.
- Les `BodyMeasurement` sont utilisées pour calculer l'IMC utilisateur.

- Chaque objectif doit référencer un `Goal` valide.

- Une `Recommendation` doit être générée à partir d'une `BodyMeasurement` valide.
- La date de fin d'une `Recommendation` doit être postérieure à la date de début.

- Le pourcentage total des `FoodCategory` d'un `Diet` doit être égal à 1.0000.
- La durée d'une offre de `DietDurationPricing` doit être supérieure à 0.
- Le prix d'un `DietDurationPricing` doit être positif.

## Origine des données

### Données utilisateur

Les données suivantes sont fournies ou modifiées directement par les utilisateurs :
- `Users`
- `BodyMeasurement`
- `UsersGoal`
- `UsersDietPurchase`

### Données administrateur / configuration

Les données suivantes sont gérées par les administrateurs afin de configurer le système :
- `Gender`
- `TransactionType`
- `Goal`
- `FoodCategory`
- `Diet`
- `DietComposition`
- `DietDurationPricing`
- `Activity`
- `PlanTemplate`
- `TemplateDiet`
- `TemplateActivity`
- `Code`

### Données générées automatiquement

Certaines données sont générées ou mises à jour automatiquement par le système :
- `Recommendation`
- `Transaction`
- le solde et la création du `Wallet`
- le statut des `Recommendation`