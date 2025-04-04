-- Création de la base de données
CREATE DATABASE gestion_projet;



-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS "users" (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50),
    profile_picture VARCHAR(255),
    role VARCHAR(20) CHECK (role IN ('SUPER_ADMIN', 'ADMIN', 'USER')),
    fonction VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
);

-- Table des clients
CREATE TABLE IF NOT EXISTS "clients" (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50),
    city VARCHAR(100),
    residence VARCHAR(255),
    phone_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


-- Table des projets
CREATE TABLE IF NOT EXISTS "projects" (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    --le client associé au projet--
    client_id INTEGER REFERENCES clients(id) ON DELETE SET NULL,
    --l'équipe associée au projet--
    team_id INTEGER REFERENCES teams(id) ON DELETE SET NULL,
    --le budget du projet--
    budget DECIMAL(15, 2),  
    --le type de projet--
    project_type VARCHAR(100) NOT NULL,
    --les documents relatifs au projet--
    documents TEXT[],
    --le statut du projet--
    status VARCHAR(20) DEFAULT 'In progress' CHECK (status IN ('In progress', 'Completed', 'Cancelled', 'Delayed')),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- la date de début prévue--
    scheduled_start_date DATE NOT NULL, 
    -- la date de fin prévue--
    scheduled_end_date DATE NOT NULL,
    -- la date de début réelle--
    actual_start_date DATE,
    -- la date de fin réelle--
    actual_end_date DATE,
);

-- Table des équipes
CREATE TABLE IF NOT EXISTS "teams" (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,       
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des membres d'équipe
CREATE TABLE IF NOT EXISTS "team_members" (
    team_id INTEGER REFERENCES teams(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (team_id, user_id)
);

-- Table des tâches
CREATE TABLE IF NOT EXISTS "tasks" (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
     --si le projet est supprimé les tâches relatifs au projet le sont aussi--
    project_id INTEGER REFERENCES projects(id) ON DELETE CASCADE,
    --si l'utilisateur est supprimé la tâche devient non assigné--
    --la personne à qui est assignée la tâche--
    assigned_to INTEGER REFERENCES users(id) ON DELETE SET NULL,
    --la priorité de la tâche--
    priority VARCHAR(20) CHECK (priority IN ('high', 'medium', 'low', 'immediate')) NOT NULL,
    --le statut de la tâche--
    status VARCHAR(20) DEFAULT 'To do' CHECK (status IN ('To do', 'In progress', 'completed', 'Delayed', 'Cancelled')),    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- l'admin qui a créee la tâche-- 
    created_by INTEGER REFERENCES users(id) ON DELETE CASCADE,
    --la date de début prévue--
    scheduled_start_date DATE NOT NULL, 
    --la date de fin prévue--
    scheduled_end_date DATE NOT NULL,
    --la date de début réelle--
    actual_start_date DATE,
    --la date de fin réelle--
    actual_end_date DATE,
    
    
    
);

-- Table des notifications
CREATE TABLE IF NOT EXISTS "notifications" (
    id SERIAL PRIMARY KEY,
    sender_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    recipient_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    task_id INTEGER REFERENCES tasks(id) ON DELETE CASCADE,
    
    action VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    send_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Création d'un utilisateur SUPER_ADMIN par défaut
INSERT INTO users (username, password, email, first_name, last_name, role)
VALUES (
    'admin', 
    -- Mot de passe haché (à remplacer par un vrai hachage lors de l'implémentation)
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin@example.com',
    'Super',
    'Admin',
    'SUPER_ADMIN'
);
