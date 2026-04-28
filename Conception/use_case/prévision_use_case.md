# Use cases - Plateforme ecoles primaires

## Contexte
La plateforme permet aux ecoles primaires de creer des classes, publier des cours et
des activites, et donner un acces en ligne aux eleves et aux parents.

## Acteurs
- Enseignant
- Eleve
- Parent
- Administrateur
- Systeme (notifications et statistiques)

## Hypotheses
- Chaque utilisateur possede un compte unique.
- Les eleves sont rattaches a une classe.
- Les parents sont rattaches a un ou plusieurs eleves.

## Regroupements pour diagrammes
- Authentification et profil (UC-01, UC-02, UC-03) -> diagramme: [Conception/use_case/use_case_auth_profil.puml](Conception/use_case/use_case_auth_profil.puml)
- Classes et contenu (UC-04, UC-05, UC-06, UC-07, UC-16) -> diagramme: [Conception/use_case/use_case_classes_contenu.puml](Conception/use_case/use_case_classes_contenu.puml)
- Activites et evaluation (UC-08, UC-09, UC-10, UC-11) -> diagramme: [Conception/use_case/use_case_activites_evaluation.puml](Conception/use_case/use_case_activites_evaluation.puml)
- Communication et suivi (UC-12, UC-13, UC-14) -> diagramme: [Conception/use_case/use_case_communication_suivi.puml](Conception/use_case/use_case_communication_suivi.puml)
- Administration, statistiques et support (UC-15, UC-17, UC-18) -> diagramme: [Conception/use_case/use_case_admin_stats_support.puml](Conception/use_case/use_case_admin_stats_support.puml)

## Use cases

### UC-01 - Creer un compte
**Acteurs**: Enseignant, Eleve, Parent
**Preconditions**: aucune
**Scenario nominal**:
1. L utilisateur saisit ses informations de base.
2. Le systeme verifie l unicite de l email.
3. Le systeme cree le compte et envoie une confirmation.
**Exceptions**:
- E1: Email deja utilise -> message d erreur et proposition de connexion.
**Postconditions**: compte actif ou en attente de confirmation.

### UC-02 - Se connecter
**Acteurs**: Enseignant, Eleve, Parent, Administrateur
**Preconditions**: compte existant
**Scenario nominal**:
1. L utilisateur saisit ses identifiants.
2. Le systeme valide les informations.
3. Le systeme ouvre la session.
**Exceptions**:
- E1: Identifiants invalides -> message et nouvelle tentative.
**Postconditions**: session active.

### UC-03 - Gerer son profil
**Acteurs**: Enseignant, Eleve, Parent
**Preconditions**: utilisateur connecte
**Scenario nominal**:
1. L utilisateur consulte son profil.
2. L utilisateur met a jour ses informations.
3. Le systeme enregistre les modifications.
**Exceptions**:
- E1: Champ invalide -> demande de correction.
**Postconditions**: profil a jour.

### UC-04 - Creer une classe
**Acteurs**: Enseignant
**Preconditions**: enseignant connecte
**Scenario nominal**:
1. L enseignant saisit le nom, le niveau et la description.
2. Le systeme cree la classe.
3. Le systeme affiche la classe.
**Exceptions**:
- E1: Donnees manquantes -> message et correction.
**Postconditions**: classe disponible.

### UC-05 - Modifier ou supprimer une classe
**Acteurs**: Enseignant
**Preconditions**: classe existante, enseignant proprietaire
**Scenario nominal**:
1. L enseignant ouvre la classe.
2. L enseignant modifie ou demande la suppression.
3. Le systeme enregistre ou supprime.
**Exceptions**:
- E1: Classe non trouvee -> message d erreur.
**Postconditions**: classe mise a jour ou supprimee.

### UC-06 - Enregistrer un cours
**Acteurs**: Enseignant
**Preconditions**: classe existante
**Scenario nominal**:
1. L enseignant ajoute un titre, une description et des ressources.
2. Le systeme enregistre le cours.
3. Le systeme rend le cours accessible a la classe.
**Exceptions**:
- E1: Ressource invalide -> message et correction.
**Postconditions**: cours publie.

### UC-07 - Mettre a jour ou supprimer un cours
**Acteurs**: Enseignant
**Preconditions**: cours existant
**Scenario nominal**:
1. L enseignant selectionne le cours.
2. L enseignant modifie ou supprime.
3. Le systeme enregistre les changements.
**Exceptions**:
- E1: Cours non trouve -> message d erreur.
**Postconditions**: cours mis a jour ou supprime.

### UC-08 - Ajouter une activite
**Acteurs**: Enseignant
**Preconditions**: classe existante
**Scenario nominal**:
1. L enseignant cree une activite (devoir, quiz, projet, evenement).
2. Le systeme enregistre l activite et les consignes.
3. Le systeme notifie les eleves de la classe.
**Exceptions**:
- E1: Date invalide -> demande de correction.
**Postconditions**: activite visible par les eleves.

### UC-09 - Mettre a jour ou supprimer une activite
**Acteurs**: Enseignant
**Preconditions**: activite existante
**Scenario nominal**:
1. L enseignant selectionne l activite.
2. L enseignant modifie ou supprime.
3. Le systeme enregistre les changements.
**Exceptions**:
- E1: Activite non trouvee -> message d erreur.
**Postconditions**: activite mise a jour ou supprimee.

### UC-10 - Acceder aux ressources de classe
**Acteurs**: Eleve
**Preconditions**: eleve connecte, rattache a une classe
**Scenario nominal**:
1. L eleve ouvre sa classe.
2. L eleve consulte les cours et activites.
3. Le systeme affiche les ressources.
**Exceptions**:
- E1: Eleve non rattache -> message et lien d aide.
**Postconditions**: ressources affichees.

### UC-11 - Soumettre une activite
**Acteurs**: Eleve
**Preconditions**: activite ouverte, eleve connecte
**Scenario nominal**:
1. L eleve joint sa reponse ou ses fichiers.
2. Le systeme verifie les formats.
3. Le systeme enregistre la soumission.
**Exceptions**:
- E1: Delai depasse -> message d echec.
**Postconditions**: soumission enregistree.

### UC-12 - Communiquer (messages, forum, notifications)
**Acteurs**: Enseignant, Eleve, Parent
**Preconditions**: utilisateur connecte
**Scenario nominal**:
1. L utilisateur ouvre l espace de communication.
2. L utilisateur envoie un message ou publie.
3. Le systeme diffuse et notifie les destinataires.
**Exceptions**:
- E1: Destinataire inexistant -> message d erreur.
**Postconditions**: message publie.

### UC-13 - Suivre les progres des eleves
**Acteurs**: Enseignant
**Preconditions**: cours ou activites existants
**Scenario nominal**:
1. L enseignant ouvre le tableau de suivi.
2. Le systeme calcule les indicateurs.
3. L enseignant consulte les resultats.
**Exceptions**:
- E1: Donnees insuffisantes -> message d information.
**Postconditions**: progression consultee.

### UC-14 - Consulter les performances d un enfant
**Acteurs**: Parent
**Preconditions**: parent rattache a un eleve
**Scenario nominal**:
1. Le parent ouvre le profil de l enfant.
2. Le systeme affiche les notes et activites.
3. Le parent telecharge un rapport si besoin.
**Exceptions**:
- E1: Aucun rattachement -> message et support.
**Postconditions**: performance consultee.

### UC-15 - Gerer les utilisateurs
**Acteurs**: Administrateur
**Preconditions**: administrateur connecte
**Scenario nominal**:
1. L administrateur cherche un utilisateur.
2. L administrateur met a jour, suspend ou supprime.
3. Le systeme applique les modifications.
**Exceptions**:
- E1: Utilisateur non trouve -> message d erreur.
**Postconditions**: compte mis a jour.

### UC-16 - Superviser classes et cours
**Acteurs**: Administrateur
**Preconditions**: contenus existants
**Scenario nominal**:
1. L administrateur liste les classes et cours.
2. L administrateur consulte ou archive un contenu.
3. Le systeme enregistre l action.
**Exceptions**:
- E1: Contenu non trouve -> message d erreur.
**Postconditions**: contenu supervise.

### UC-17 - Consulter statistiques et rapports
**Acteurs**: Administrateur
**Preconditions**: donnees d utilisation disponibles
**Scenario nominal**:
1. L administrateur choisit une periode.
2. Le systeme calcule les indicateurs.
3. Le systeme affiche ou exporte le rapport.
**Exceptions**:
- E1: Periode invalide -> message et correction.
**Postconditions**: rapport genere.

### UC-18 - Gerer support et assistance
**Acteurs**: Administrateur
**Preconditions**: ticket utilisateur existant
**Scenario nominal**:
1. L administrateur ouvre un ticket.
2. L administrateur repond ou escalade.
3. Le systeme notifie l utilisateur.
**Exceptions**:
- E1: Ticket ferme -> message d information.
**Postconditions**: ticket traite.
