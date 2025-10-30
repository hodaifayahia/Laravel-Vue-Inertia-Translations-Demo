<?php

return [
    // Main settings
    'title' => 'Paramètres',
    'description' => 'Gérer votre profil et les paramètres du compte',
    
    // Navigation
    'nav' => [
        'profile' => 'Profil',
        'password' => 'Mot de passe',
        'two_factor' => 'Authentification 2FA',
        'appearance' => 'Apparence',
        'customization' => 'Personnalisation',
    ],
    
    // Profile settings
    'profile' => [
        'title' => 'Paramètres du profil',
        'heading' => 'Informations du profil',
        'description' => 'Mettre à jour votre nom et votre adresse email',
        'name' => 'Nom',
        'email' => 'Adresse email',
        'name_placeholder' => 'Nom complet',
        'email_placeholder' => 'Adresse email',
        'save' => 'Enregistrer',
        'saved' => 'Enregistré.',
        'email_unverified' => 'Votre adresse email n\'est pas vérifiée.',
        'resend_verification' => 'Cliquez ici pour renvoyer l\'email de vérification.',
        'verification_sent' => 'Un nouveau lien de vérification a été envoyé à votre adresse email.',
    ],
    
    // Password settings
    'password' => [
        'title' => 'Paramètres du mot de passe',
        'heading' => 'Mettre à jour le mot de passe',
        'description' => 'Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé',
        'current_password' => 'Mot de passe actuel',
        'new_password' => 'Nouveau mot de passe',
        'confirm_password' => 'Confirmer le mot de passe',
        'current_password_placeholder' => 'Mot de passe actuel',
        'new_password_placeholder' => 'Nouveau mot de passe',
        'confirm_password_placeholder' => 'Confirmer le mot de passe',
        'save' => 'Enregistrer le mot de passe',
        'saved' => 'Enregistré.',
    ],
    
    // Appearance settings
    'appearance' => [
        'title' => 'Paramètres d\'apparence',
        'heading' => 'Paramètres d\'apparence',
        'description' => 'Mettre à jour les paramètres d\'apparence de votre compte',
        'language' => 'Langue',
        'language_description' => 'Sélectionnez votre langue préférée',
        'save' => 'Enregistrer les préférences',
        'saved' => 'Enregistré.',
    ],
    
    // Two-Factor Authentication
    'two_factor' => [
        'title' => 'Authentification à deux facteurs',
        'heading' => 'Authentification à deux facteurs',
        'description' => 'Gérer vos paramètres d\'authentification à deux facteurs',
        'enabled' => 'Activée',
        'disabled' => 'Désactivée',
        'enable' => 'Activer 2FA',
        'disable' => 'Désactiver 2FA',
        'continue_setup' => 'Continuer la configuration',
        'enabled_description' => 'Avec l\'authentification à deux facteurs activée, vous serez invité à saisir un code de sécurité aléatoire lors de la connexion, que vous pourrez récupérer depuis l\'application TOTP sur votre téléphone.',
        'disabled_description' => 'Lorsque vous activez l\'authentification à deux facteurs, vous serez invité à saisir un code de sécurité lors de la connexion. Ce code peut être récupéré depuis une application TOTP sur votre téléphone.',
        'setup_title' => 'Activer l\'authentification à deux facteurs',
        'setup_description' => 'L\'authentification à deux facteurs ajoute une couche de sécurité supplémentaire à votre compte en exigeant plus qu\'un simple mot de passe pour vous connecter.',
        'scan_qr' => 'Scannez le code QR ci-dessous avec votre application d\'authentification',
        'manual_entry' => 'Ou entrez ce code manuellement :',
        'enter_code' => 'Entrez le code à 6 chiffres de votre application d\'authentification',
        'code_placeholder' => '000000',
        'verify' => 'Vérifier et activer',
        'recovery_codes' => 'Codes de récupération',
        'recovery_codes_description' => 'Stockez ces codes de récupération dans un gestionnaire de mots de passe sécurisé. Ils peuvent être utilisés pour récupérer l\'accès à votre compte si votre appareil d\'authentification à deux facteurs est perdu.',
        'regenerate_codes' => 'Régénérer les codes de récupération',
        'show_codes' => 'Afficher les codes de récupération',
        'hide_codes' => 'Masquer les codes de récupération',
        'download_codes' => 'Télécharger',
        'copy_codes' => 'Copier',
        'codes_copied' => 'Codes de récupération copiés dans le presse-papiers',
    ],
    
    // Delete account
    'delete' => [
        'heading' => 'Supprimer le compte',
        'description' => 'Supprimer votre compte et toutes ses ressources',
        'warning_title' => 'Attention',
        'warning_message' => 'Veuillez procéder avec prudence, cette action est irréversible.',
        'button' => 'Supprimer le compte',
        'confirm_title' => 'Êtes-vous sûr de vouloir supprimer votre compte ?',
        'confirm_description' => 'Une fois votre compte supprimé, toutes ses ressources et données seront également définitivement supprimées. Veuillez entrer votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.',
        'password' => 'Mot de passe',
        'password_placeholder' => 'Mot de passe',
        'cancel' => 'Annuler',
        'confirm' => 'Supprimer le compte',
    ],
    
    // Customization
    'customization' => [
        'title' => 'Paramètres de personnalisation',
        'heading' => 'Personnalisez votre expérience',
        'description' => 'Personnalisez l\'apparence de votre application',
        'save' => 'Enregistrer les modifications',
        'saving' => 'Enregistrement...',
        'saved' => 'Enregistré avec succès !',
        
        'tabs' => [
            'welcome' => 'Page d\'accueil',
            'theme' => 'Couleurs du thème',
            'branding' => 'Image de marque',
        ],
        
        'welcome' => [
            'title' => 'Paramètres de la page d\'accueil',
            'description' => 'Personnalisez le contenu et l\'apparence de la page d\'accueil',
            'show_page' => 'Afficher la page d\'accueil',
            'show_page_description' => 'Afficher la page d\'accueil aux visiteurs',
            'page_title' => 'Titre de la page',
            'page_title_placeholder' => 'Entrez le titre de la page',
            'page_subtitle' => 'Sous-titre de la page',
            'page_subtitle_placeholder' => 'Entrez le sous-titre de la page',
            'page_description' => 'Description de la page',
            'page_description_placeholder' => 'Entrez la description de la page',
        ],
        
        'theme' => [
            'title' => 'Thème de couleurs',
            'description' => 'Personnalisez le schéma de couleurs de votre application',
            'primary_color' => 'Couleur primaire',
            'primary_color_description' => 'Couleur principale de la marque utilisée pour les boutons et les liens',
            'secondary_color' => 'Couleur secondaire',
            'secondary_color_description' => 'Couleur de support pour les accents et les mises en évidence',
            'accent_color' => 'Couleur d\'accentuation',
            'accent_color_description' => 'Couleur pour les éléments spéciaux et les appels à l\'action',
            'color_placeholder' => '#000000',
            'preview' => 'Aperçu des couleurs',
        ],
        
        'branding' => [
            'title' => 'Identité de marque',
            'description' => 'Configurez vos éléments de marque et votre identité',
            'logo_text' => 'Texte du logo',
            'logo_text_placeholder' => 'Entrez le nom de votre marque',
            'logo_text_description' => 'Texte affiché dans le logo de l\'application',
            'favicon' => 'URL du favicon',
            'favicon_placeholder' => 'https://exemple.com/favicon.ico',
            'favicon_description' => 'URL du favicon de votre site web',
            'preview' => 'Aperçu du logo',
        ],
        
        'reset' => [
            'title' => 'Réinitialiser la personnalisation',
            'description' => 'Réinitialiser tous les paramètres de personnalisation à leurs valeurs par défaut',
            'button' => 'Réinitialiser aux valeurs par défaut',
        ],
    ],
];
