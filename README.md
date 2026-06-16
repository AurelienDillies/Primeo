# Priméo

Ce dépôt contient une application full stack organisée en plusieurs parties :

- **Backend** : Symfony (API)
- **Frontend** : Angular
- **Containerisation** : Docker
- **Conception** : documentation et modélisation du projet

---

## 📁 Structure du projet
- /backend        → API Symfony
- /frontend       → Application Angular
- /docker         → Configuration nginx
- /conception     → Diagrammes, spécifications, analyse, maquettes
- compose.yaml    → Configuration Docker
- Dockerfile      → Lancement Docker

---

## ⚙️ Installation et lancement

### Lancer le projet

```bash
docker compose up --build -d
```

Cette commande démarre :

- Backend Symfony
- Frontend Angular
- Serveur Nginx
- Base de données

---

## 🗄️ Base de données

### Créer les tables (migrations)

```bash
docker compose exec php php bin/console doctrine:migrations:migrate
```

### Charger les données de test (fixtures)

```bash
docker compose exec php php bin/console doctrine:fixtures:load
```

---

## 🌐 Accès à l’application

Site : http://localhost:4300/

---

## 🎯 Objectif du projet

Ce projet a pour objectif de séparer clairement les responsabilités :

- 🧠 Logique métier : Symfony (API backend)
- 🎨 Interface utilisateur : Angular (frontend)
- 🐳 Environnement d’exécution : Docker (reproductibilité)
- 📚 Documentation : conception (UML, maquettes, spécifications)

---

## ⚠️ Notes importantes
Aucune installation locale de PHP, Node ou Composer n’est nécessaire
Toutes les dépendances sont gérées via Docker
Le projet est entièrement reproductible via une seule commande