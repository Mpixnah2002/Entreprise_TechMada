<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Enable foreign keys for SQLite
        $db->query('PRAGMA foreign_keys = ON');

        // Create tables
        $db->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS departements (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nom TEXT NOT NULL,
  description TEXT,
  active INTEGER DEFAULT 1,
  created_at TEXT,
  updated_at TEXT
);
SQL
        );

        $db->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS types_conge (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  libelle TEXT NOT NULL,
  jours_max INTEGER DEFAULT 0,
  couleur TEXT,
  actif INTEGER DEFAULT 1,
  created_at TEXT,
  updated_at TEXT
);
SQL
        );

        $db->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS employes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nom TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password TEXT NOT NULL,
  role TEXT DEFAULT 'employe',
  departement_id INTEGER,
  active INTEGER DEFAULT 1,
  created_at TEXT,
  updated_at TEXT,
  FOREIGN KEY(departement_id) REFERENCES departements(id) ON DELETE SET NULL
);
SQL
        );

        $db->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS soldes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  employe_id INTEGER NOT NULL,
  type_conge_id INTEGER NOT NULL,
  solde_total INTEGER DEFAULT 0,
  solde_restant INTEGER DEFAULT 0,
  solde_pris INTEGER DEFAULT 0,
  created_at TEXT,
  updated_at TEXT,
  FOREIGN KEY(employe_id) REFERENCES employes(id) ON DELETE CASCADE,
  FOREIGN KEY(type_conge_id) REFERENCES types_conge(id) ON DELETE CASCADE
);
SQL
        );

        $db->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS conges (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  employe_id INTEGER NOT NULL,
  type_conge_id INTEGER NOT NULL,
  date_debut TEXT NOT NULL,
  date_fin TEXT NOT NULL,
  nb_jours INTEGER DEFAULT 0,
  motif TEXT,
  statut TEXT DEFAULT 'en_attente',
  valide_par INTEGER,
  commentaire TEXT,
  created_at TEXT,
  updated_at TEXT,
  FOREIGN KEY(employe_id) REFERENCES employes(id) ON DELETE CASCADE,
  FOREIGN KEY(type_conge_id) REFERENCES types_conge(id) ON DELETE CASCADE
);
SQL
        );

        // Insert seed data
        $now = date('Y-m-d H:i:s');

        // Departments
        $db->query("INSERT OR IGNORE INTO departements (id, nom, description, active, created_at) VALUES (1,'Direction','Direction générale',1,'$now')");
        $db->query("INSERT OR IGNORE INTO departements (id, nom, description, active, created_at) VALUES (2,'Informatique','Service informatique',1,'$now')");

        // Types de congé
        $db->query("INSERT OR IGNORE INTO types_conge (id, libelle, jours_max, couleur, actif, created_at) VALUES (1,'Annuel',30,'#0d6efd',1,'$now')");
        $db->query("INSERT OR IGNORE INTO types_conge (id, libelle, jours_max, couleur, actif, created_at) VALUES (2,'Maladie',30,'#dc3545',1,'$now')");
        $db->query("INSERT OR IGNORE INTO types_conge (id, libelle, jours_max, couleur, actif, created_at) VALUES (3,'Spécial',10,'#198754',1,'$now')");
        $db->query("INSERT OR IGNORE INTO types_conge (id, libelle, jours_max, couleur, actif, created_at) VALUES (4,'Sans solde',0,'#6c757d',1,'$now')");

        // Employés (admin, rh, 2 employees)
        $passAdmin = password_hash('admin123', PASSWORD_DEFAULT);
        $passRh = password_hash('rh123', PASSWORD_DEFAULT);
        $passEmp = password_hash('emp123', PASSWORD_DEFAULT);

        $db->query("INSERT OR IGNORE INTO employes (id, nom, email, password, role, departement_id, active, created_at) VALUES (1,'Admin','admin@techmada.mg','$passAdmin','admin',1,1,'$now')");
        $db->query("INSERT OR IGNORE INTO employes (id, nom, email, password, role, departement_id, active, created_at) VALUES (2,'RH','rh@techmada.mg','$passRh','rh',1,1,'$now')");
        $db->query("INSERT OR IGNORE INTO employes (id, nom, email, password, role, departement_id, active, created_at) VALUES (3,'Soa Rakoto','soa.rakoto@techmada.mg','$passEmp','employe',2,1,'$now')");
        $db->query("INSERT OR IGNORE INTO employes (id, nom, email, password, role, departement_id, active, created_at) VALUES (4,'Tsiry Fidy','tsiry.fidy@techmada.mg','$passEmp','employe',2,1,'$now')");

        // Soldes
        $db->query("INSERT OR IGNORE INTO soldes (employe_id, type_conge_id, solde_total, solde_restant, solde_pris, created_at) VALUES (3,1,20,20,0,'$now')");
        $db->query("INSERT OR IGNORE INTO soldes (employe_id, type_conge_id, solde_total, solde_restant, solde_pris, created_at) VALUES (4,1,20,20,0,'$now')");

        // Example demandes
        $db->query("INSERT OR IGNORE INTO conges (employe_id, type_conge_id, date_debut, date_fin, nb_jours, motif, statut, created_at) VALUES (3,1,'2026-06-01','2026-06-05',3,'Vacances','en_attente','$now')");
        $db->query("INSERT OR IGNORE INTO conges (employe_id, type_conge_id, date_debut, date_fin, nb_jours, motif, statut, created_at) VALUES (4,2,'2026-05-10','2026-05-12',2,'Maladie','approuvee','$now')");

        echo "Seeding complete.\n";
    }
}
