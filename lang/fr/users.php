<?php

return [
    // Page titles and headers
    'title' => 'Gestion des utilisateurs',
    'description' => 'Gérer tous les utilisateurs du système',
    'user_list' => 'Liste des utilisateurs',
    
    // Actions
    'add_user' => 'Ajouter un utilisateur',
    'add_first_user' => 'Ajouter le premier utilisateur',
    'create_user' => 'Créer un utilisateur',
    'edit_user' => 'Modifier l\'utilisateur',
    'delete_user' => 'Supprimer l\'utilisateur',
    'create' => 'Créer',
    'update' => 'Mettre à jour',
    'delete_confirm' => 'Oui, supprimer',
    'cancel' => 'Annuler',
    'edit' => 'Modifier',
    'delete' => 'Supprimer',
    'actions' => 'Actions',
    'filters' => 'Filtres',
    
    // Form fields
    'name' => 'Nom',
    'email' => 'Email',
    'password' => 'Mot de passe',
    'password_confirmation' => 'Confirmer le mot de passe',
    'language' => 'Langue',
    'locale' => 'Langue',
    'roles' => 'Rôles',
    'assign_roles' => 'Attribuer des rôles',
    'roles_description' => 'Sélectionnez un ou plusieurs rôles à attribuer à cet utilisateur',
    
    // Placeholders
    'name_placeholder' => 'Entrez le nom complet',
    'email_placeholder' => 'exemple@domaine.com',
    'password_placeholder' => 'Créer un mot de passe sécurisé',
    'password_confirmation_placeholder' => 'Confirmez votre mot de passe',
    'search_placeholder' => 'Rechercher par nom ou email...',
    
    // Status
    'status' => 'Statut',
    'verified' => 'Vérifié',
    'unverified' => 'Non vérifié',
    'verified_only' => 'Vérifiés uniquement',
    'unverified_only' => 'Non vérifiés uniquement',
    'all_users' => 'Tous les utilisateurs',
    
    // Stats
    'total_users' => 'Total des utilisateurs',
    'verified_users' => 'Utilisateurs vérifiés',
    'unverified_users' => 'Utilisateurs non vérifiés',
    
    // Table columns
    'user' => 'Utilisateur',
    'joined' => 'Inscription',
    
    // Messages
    'creating' => 'Création...',
    'updating' => 'Mise à jour...',
    'deleting' => 'Suppression...',
    'created_successfully' => 'Utilisateur créé avec succès',
    'updated_successfully' => 'Utilisateur mis à jour avec succès',
    'deleted_successfully' => 'Utilisateur supprimé avec succès',
    'cannot_delete_yourself' => 'Vous ne pouvez pas vous supprimer vous-même',
    'leave_blank_to_keep' => 'Laisser vide pour conserver le mot de passe actuel',
    'roles_updated' => 'Rôles de l\'utilisateur mis à jour avec succès',
    'permissions_updated' => 'Permissions de l\'utilisateur mises à jour avec succès',
    
    // Descriptions
    'create_user_description' => 'Ajouter un nouvel utilisateur au système avec ses détails',
    'edit_user_description' => 'Mettre à jour les informations et les paramètres de l\'utilisateur',
    'delete_user_warning' => 'Cette action est irréversible',
    'delete_user_confirm' => 'Êtes-vous sûr de vouloir supprimer :name?',
    
    // Empty state
    'no_users' => 'Aucun utilisateur trouvé',
    'no_users_description' => 'Aucun utilisateur ne correspond à vos critères de recherche.',
    
    // Pagination
    'showing_results' => 'Affichage de :total utilisateurs',
    'pagination_info' => 'Affichage de :from à :to sur :total résultats',
    
    // Validation messages
    'validation' => [
        'name_required' => 'Le nom est requis',
        'email_required' => 'L\'email est requis',
        'email_invalid' => 'L\'email doit être une adresse email valide',
        'email_unique' => 'Cet email est déjà utilisé',
        'password_required' => 'Le mot de passe est requis',
        'password_confirmed' => 'La confirmation du mot de passe ne correspond pas',
    ],
];
