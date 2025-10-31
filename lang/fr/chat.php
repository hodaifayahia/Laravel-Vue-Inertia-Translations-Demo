<?php

return [
    // Main titles
    'title' => 'Discussion',
    'conversations' => 'Conversations',
    'new_conversation' => 'Nouvelle Conversation',
    'search_conversations' => 'Rechercher des conversations...',
    'search_users' => 'Rechercher des utilisateurs...',
    'no_conversations' => 'Aucune conversation pour le moment',
    'start_chatting' => 'Commencer à discuter avec quelqu\'un',
    
    // Admin features
    'show_all_users' => 'Afficher Tous les Utilisateurs',
    'show_conversations' => 'Afficher les Conversations',
    'all_system_users' => 'Tous les Utilisateurs du Système',
    'click_to_start_chat' => 'Cliquez pour démarrer une discussion',
    'no_users_found' => 'Aucun utilisateur trouvé',

    // Message actions
    'send_message' => 'Envoyer un message',
    'type_message' => 'Tapez un message...',
    'edit_message' => 'Modifier le message',
    'delete_message' => 'Supprimer le message',
    'reply_to_message' => 'Répondre au message',
    'forward_message' => 'Transférer le message',
    'copy_message' => 'Copier le message',
    'select_messages' => 'Sélectionner les messages',

    // User status
    'online' => 'En ligne',
    'offline' => 'Hors ligne',
    'away' => 'Absent',
    'busy' => 'Occupé',
    'last_seen' => 'Vu pour la dernière fois :time',

    // Typing indicators
    'typing' => ':user tape...',
    'multiple_typing' => 'Plusieurs personnes tapent...',
    'you' => 'Vous',

    // Message management
    'message_sent' => 'Message envoyé',
    'message_delivered' => 'Livré',
    'message_read' => 'Lu',
    'message_edited' => 'Modifié',
    'message_deleted' => 'Ce message a été supprimé',
    'reply_to' => 'Répondre à :user',
    'forwarded' => 'Transféré',
    'edited' => 'modifié',
    'deleted' => 'supprimé',
    'failed_to_send' => 'Échec de l\'envoi',
    'retry_send' => 'Réessayer',

    // File uploads
    'attach_file' => 'Joindre un fichier',
    'upload_file' => 'Télécharger un fichier',
    'file_attached' => 'Fichier joint',
    'file_size_exceeded' => 'La taille du fichier dépasse la limite maximale de :size',
    'file_type_not_allowed' => 'Type de fichier non autorisé',
    'uploading' => 'Téléchargement...',
    'download_file' => 'Télécharger le fichier',
    'view_file' => 'Voir le fichier',
    'file_upload_failed' => 'Échec du téléchargement du fichier',

    // Reactions
    'add_reaction' => 'Ajouter une réaction',
    'remove_reaction' => 'Retirer la réaction',
    'reacted_with' => ':user a réagi avec :emoji',

    // User blocking
    'block_user' => 'Bloquer l\'utilisateur',
    'unblock_user' => 'Débloquer l\'utilisateur',
    'user_blocked' => ':user a été bloqué',
    'user_unblocked' => ':user a été débloqué',
    'blocked_users' => 'Utilisateurs bloqués',
    'block_reason' => 'Raison du blocage',
    'block_reasons' => [
        'spam' => 'Spam',
        'harassment' => 'Harcèlement',
        'inappropriate' => 'Contenu inapproprié',
        'other' => 'Autre',
    ],
    'blocked_message' => 'Vous ne pouvez pas envoyer de messages à cet utilisateur',
    'you_blocked' => 'Vous avez bloqué cet utilisateur',
    'blocked_you' => 'Cet utilisateur vous a bloqué',

    // Issue reporting
    'report_issue' => 'Signaler un problème',
    'issue_reported' => 'Problème signalé avec succès',
    'issue_title' => 'Titre du problème',
    'issue_description' => 'Description du problème',
    'issue_priority' => 'Priorité',
    'assign_to' => 'Assigner à',
    'assigned_to' => 'Assigné à :user',
    'issue_status' => 'Statut du problème',
    'resolve_issue' => 'Résoudre le problème',
    'issue_resolved' => 'Problème résolu',
    'resolution_notes' => 'Notes de résolution',
    'issue_created_by' => 'Créé par :user',
    'issue_created_at' => 'Créé le :time',
    'view_issue' => 'Voir le problème',

    // Priority levels
    'priority_low' => 'Faible',
    'priority_medium' => 'Moyenne',
    'priority_high' => 'Élevée',

    // Status
    'status_open' => 'Ouvert',
    'status_in_progress' => 'En cours',
    'status_resolved' => 'Résolu',

    // Notifications
    'new_message' => 'Nouveau message de :user',
    'new_message_in' => 'Nouveau message dans :channel',
    'mentioned_you' => ':user vous a mentionné',
    'user_joined' => ':user a rejoint la conversation',
    'user_left' => ':user a quitté la conversation',
    'user_online' => ':user est maintenant en ligne',
    'user_offline' => ':user est maintenant hors ligne',
    'message_read_by' => 'Lu par :user',
    'you_were_blocked' => 'Vous avez été bloqué par :user',
    'issue_assigned' => 'Un problème vous a été assigné',
    'issue_status_changed' => 'Le statut du problème a changé en :status',
    'notification_settings' => 'Paramètres de notification',
    'mute_notifications' => 'Couper les notifications',
    'unmute_notifications' => 'Activer les notifications',
    'mark_all_read' => 'Tout marquer comme lu',

    // Search
    'search_messages' => 'Rechercher des messages',
    'search_users' => 'Rechercher des utilisateurs',
    'no_results' => 'Aucun résultat trouvé',

    // Channel/Group
    'create_group' => 'Créer un groupe',
    'group_name' => 'Nom du groupe',
    'add_members' => 'Ajouter des membres',
    'remove_member' => 'Retirer le membre',
    'leave_group' => 'Quitter le groupe',
    'group_info' => 'Informations du groupe',
    'members' => 'Membres',
    'admins' => 'Administrateurs',
    'member_count' => ':count membres',
    'members_count' => ':count membres',
    'online_count' => ':count en ligne',
    'direct_message' => 'Message direct',
    'group_chat' => 'Discussion de groupe',
    'enter_group_name' => 'Entrez le nom du groupe...',
    'select_user' => 'Sélectionner un utilisateur',
    'select_members' => 'Sélectionner les membres (minimum 2)',
    'no_users_found' => 'Aucun utilisateur trouvé',
    'unknown_user' => 'Utilisateur inconnu',
    'unnamed_channel' => 'Canal sans nom',

    // Admin panel
    'admin_panel' => 'Panneau d\'administration',
    'chat_permissions' => 'Permissions de discussion',
    'user_assignments' => 'Affectations des utilisateurs',
    'chat_analytics' => 'Analyses de discussion',
    'manage_blocked_users' => 'Gérer les utilisateurs bloqués',
    'view_all_conversations' => 'Voir toutes les conversations',

    // Permissions
    'can_initiate' => 'Peut initier une conversation',
    'can_receive' => 'Peut recevoir des messages',
    'permission_updated' => 'Permission mise à jour',
    'role_permissions' => 'Permissions de rôle',
    'from_role' => 'Du rôle',
    'to_role' => 'Au rôle',
    'update_permissions' => 'Mettre à jour les permissions',
    'permission_denied' => 'Vous n\'avez pas la permission d\'envoyer des messages à ce rôle',
    'no_permission_to_chat' => 'Vous n\'avez pas la permission d\'utiliser le chat',

    // User assignments
    'assign_users' => 'Assigner des utilisateurs',
    'assignable_role' => 'Rôle assignable',
    'assigned_user' => 'Utilisateur assigné',
    'remove_assignment' => 'Retirer l\'affectation',
    'assignment_created' => 'Affectation créée avec succès',

    // Analytics
    'total_messages' => 'Total des messages',
    'active_users' => 'Utilisateurs actifs',
    'total_channels' => 'Total des canaux',
    'blocked_users_count' => 'Utilisateurs bloqués',
    'open_issues' => 'Problèmes ouverts',
    'messages_today' => 'Messages aujourd\'hui',
    'messages_this_week' => 'Messages cette semaine',
    'messages_this_month' => 'Messages ce mois',
    'average_response_time' => 'Temps de réponse moyen',
    'most_active_channel' => 'Canal le plus actif',

    // Errors
    'error_loading_messages' => 'Erreur lors du chargement des messages',
    'error_sending_message' => 'Erreur lors de l\'envoi du message',
    'error_creating_channel' => 'Erreur lors de la création du canal',
    'error_uploading_file' => 'Erreur lors du téléchargement du fichier',
    'error_blocking_user' => 'Erreur lors du blocage de l\'utilisateur',
    'error_reporting_issue' => 'Erreur lors du signalement du problème',
    'websocket_disconnected' => 'Déconnecté du serveur de discussion',
    'websocket_reconnecting' => 'Reconnexion...',

    // Success messages
    'channel_created' => 'Canal créé avec succès',
    'message_copied' => 'Message copié dans le presse-papiers',
    'settings_saved' => 'Paramètres enregistrés',
    'issue_resolved_successfully' => 'Problème résolu avec succès',

    // Time formats
    'just_now' => 'À l\'instant',
    'minutes_ago' => 'Il y a :count minute|Il y a :count minutes',
    'hours_ago' => 'Il y a :count heure|Il y a :count heures',
    'yesterday' => 'Hier',
    'days_ago' => 'Il y a :count jour|Il y a :count jours',
    'weeks_ago' => 'Il y a :count semaine|Il y a :count semaines',
    'months_ago' => 'Il y a :count mois',

    // Actions
    'confirm' => 'Confirmer',
    'cancel' => 'Annuler',
    'save' => 'Enregistrer',
    'close' => 'Fermer',
    'submit' => 'Soumettre',
    'back' => 'Retour',
    'next' => 'Suivant',
    'delete' => 'Supprimer',

    // Settings
    'chat_settings' => 'Paramètres de discussion',
    'sound_notifications' => 'Notifications sonores',
    'desktop_notifications' => 'Notifications de bureau',
    'show_typing_indicator' => 'Afficher l\'indicateur de frappe',
    'enable_read_receipts' => 'Activer les accusés de lecture',

    // Empty states
    'no_messages' => 'Aucun message encore',
    'no_notifications' => 'Aucune notification',
    'no_blocked_users' => 'Aucun utilisateur bloqué',
    'no_chat_selected' => 'Aucune discussion sélectionnée',
    'select_conversation_mobile' => 'Sélectionnez une conversation dans la barre latérale pour commencer à envoyer des messages',
    'select_conversation_desktop' => 'Sélectionnez une conversation à gauche pour commencer à envoyer des messages',
    'no_conversations' => 'Aucune conversation',
    
    // Additional UI
    'messages' => 'Messages',
    'loading' => 'Chargement',
    'load_more_messages' => 'Charger plus de messages',
    'user_is_typing' => ':name tape',
    'users_are_typing' => ':name1 et :name2 tapent',
    'multiple_users_typing' => 'Plusieurs utilisateurs tapent',
    'editing_message' => 'Modification du message',
    'replying_to' => 'Répondre à :name',
    'reply_to' => 'Répondre à',
    'press_enter_to_send' => 'Appuyez sur Entrée pour envoyer, Maj + Entrée pour une nouvelle ligne',
    'add_emoji' => 'Ajouter un emoji',
    'attachment' => 'Pièce jointe',
    'create' => 'Créer',
    'edit' => 'Modifier',
    'edited' => 'Modifié',
];
