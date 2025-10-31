<?php

return [
    // Main titles
    'title' => 'Pokalbiai',
    'conversations' => 'Pokalbiai',
    'new_conversation' => 'Naujas Pokalbis',
    'search_conversations' => 'Ieškoti pokalbių...',
    'search_users' => 'Ieškoti vartotojų...',
    'no_conversations' => 'Dar nėra pokalbių',
    'start_chatting' => 'Pradėkite kalbėtis su kuo nors',
    
    // Admin features
    'show_all_users' => 'Rodyti Visus Vartotojus',
    'show_conversations' => 'Rodyti Pokalbius',
    'all_system_users' => 'Visi Sistemos Vartotojai',
    'click_to_start_chat' => 'Spustelėkite, kad pradėtumėte pokalbį',
    'no_users_found' => 'Vartotojų nerasta',

    // Message actions
    'send_message' => 'Siųsti žinutę',
    'type_message' => 'Rašykite žinutę...',
    'edit_message' => 'Redaguoti žinutę',
    'delete_message' => 'Ištrinti žinutę',
    'reply_to_message' => 'Atsakyti į žinutę',
    'forward_message' => 'Persiųsti žinutę',
    'copy_message' => 'Kopijuoti žinutę',
    'select_messages' => 'Pasirinkti žinutes',

    // User status
    'online' => 'Prisijungęs',
    'offline' => 'Atsijungęs',
    'away' => 'Pasitraukęs',
    'busy' => 'Užsiėmęs',
    'last_seen' => 'Paskutinį kartą matytas :time',

    // Typing indicators
    'typing' => ':user rašo...',
    'multiple_typing' => 'Keli žmonės rašo...',
    'you' => 'Jūs',

    // Message management
    'message_sent' => 'Žinutė išsiųsta',
    'message_delivered' => 'Pristatyta',
    'message_read' => 'Perskaityta',
    'message_edited' => 'Redaguota',
    'message_deleted' => 'Ši žinutė buvo ištrinta',
    'reply_to' => 'Atsakyti :user',
    'forwarded' => 'Persiųsta',
    'edited' => 'redaguota',
    'deleted' => 'ištrinta',
    'failed_to_send' => 'Nepavyko išsiųsti',
    'retry_send' => 'Bandyti dar kartą',

    // File uploads
    'attach_file' => 'Prisegti failą',
    'upload_file' => 'Įkelti failą',
    'file_attached' => 'Failas prisegtas',
    'file_size_exceeded' => 'Failo dydis viršija maksimalų :size limitą',
    'file_type_not_allowed' => 'Failo tipas neleidžiamas',
    'uploading' => 'Įkeliama...',
    'download_file' => 'Atsisiųsti failą',
    'view_file' => 'Peržiūrėti failą',
    'file_upload_failed' => 'Failo įkėlimas nepavyko',

    // Reactions
    'add_reaction' => 'Pridėti reakciją',
    'remove_reaction' => 'Pašalinti reakciją',
    'reacted_with' => ':user sureagavo su :emoji',

    // User blocking
    'block_user' => 'Blokuoti vartotoją',
    'unblock_user' => 'Atblokuoti vartotoją',
    'user_blocked' => ':user buvo užblokuotas',
    'user_unblocked' => ':user buvo atblokuotas',
    'blocked_users' => 'Užblokuoti vartotojai',
    'block_reason' => 'Blokavimo priežastis',
    'block_reasons' => [
        'spam' => 'Šlamštas',
        'harassment' => 'Priekabiavimas',
        'inappropriate' => 'Netinkamas turinys',
        'other' => 'Kita',
    ],
    'blocked_message' => 'Negalite siųsti žinučių šiam vartotojui',
    'you_blocked' => 'Jūs užblokavote šį vartotoją',
    'blocked_you' => 'Šis vartotojas jus užblokavo',

    // Issue reporting
    'report_issue' => 'Pranešti apie problemą',
    'issue_reported' => 'Problema sėkmingai pranešta',
    'issue_title' => 'Problemos pavadinimas',
    'issue_description' => 'Problemos aprašymas',
    'issue_priority' => 'Prioritetas',
    'assign_to' => 'Priskirti',
    'assigned_to' => 'Priskirta :user',
    'issue_status' => 'Problemos būsena',
    'resolve_issue' => 'Išspręsti problemą',
    'issue_resolved' => 'Problema išspręsta',
    'resolution_notes' => 'Sprendimo pastabos',
    'issue_created_by' => 'Sukūrė :user',
    'issue_created_at' => 'Sukurta :time',
    'view_issue' => 'Peržiūrėti problemą',

    // Priority levels
    'priority_low' => 'Žemas',
    'priority_medium' => 'Vidutinis',
    'priority_high' => 'Aukštas',

    // Status
    'status_open' => 'Atvira',
    'status_in_progress' => 'Vykdoma',
    'status_resolved' => 'Išspręsta',

    // Notifications
    'new_message' => 'Nauja žinutė nuo :user',
    'new_message_in' => 'Nauja žinutė :channel',
    'mentioned_you' => ':user jus paminėjo',
    'user_joined' => ':user prisijungė prie pokalbio',
    'user_left' => ':user paliko pokalbį',
    'user_online' => ':user dabar prisijungęs',
    'user_offline' => ':user dabar atsijungęs',
    'message_read_by' => 'Perskaitė :user',
    'you_were_blocked' => 'Jus užblokavo :user',
    'issue_assigned' => 'Jums priskirta problema',
    'issue_status_changed' => 'Problemos būsena pakeista į :status',
    'notification_settings' => 'Pranešimų nustatymai',
    'mute_notifications' => 'Nutildyti pranešimus',
    'unmute_notifications' => 'Įjungti pranešimus',
    'mark_all_read' => 'Pažymėti viską kaip perskaityta',

    // Search
    'search_messages' => 'Ieškoti žinučių',
    'search_users' => 'Ieškoti vartotojų',
    'no_results' => 'Rezultatų nerasta',

    // Channel/Group
    'create_group' => 'Sukurti grupę',
    'group_name' => 'Grupės pavadinimas',
    'add_members' => 'Pridėti narius',
    'remove_member' => 'Pašalinti narį',
    'leave_group' => 'Palikti grupę',
    'group_info' => 'Grupės informacija',
    'members' => 'Nariai',
    'admins' => 'Administratoriai',
    'member_count' => ':count nariai',
    'members_count' => ':count nariai',
    'online_count' => ':count prisijungę',
    'direct_message' => 'Tiesioginė žinutė',
    'group_chat' => 'Grupės pokalbis',
    'enter_group_name' => 'Įveskite grupės pavadinimą...',
    'select_user' => 'Pasirinkite vartotoją',
    'select_members' => 'Pasirinkite narius (mažiausiai 2)',
    'no_users_found' => 'Vartotojų nerasta',
    'unknown_user' => 'Nežinomas vartotojas',
    'unnamed_channel' => 'Kanalas be pavadinimo',

    // Admin panel
    'admin_panel' => 'Administravimo skydelis',
    'chat_permissions' => 'Pokalbių leidimai',
    'user_assignments' => 'Vartotojų priskyrimai',
    'chat_analytics' => 'Pokalbių analitika',
    'manage_blocked_users' => 'Tvarkyti užblokuotus vartotojus',
    'view_all_conversations' => 'Peržiūrėti visus pokalbius',

    // Permissions
    'can_initiate' => 'Gali pradėti pokalbį',
    'can_receive' => 'Gali gauti žinutes',
    'permission_updated' => 'Leidimas atnaujintas',
    'role_permissions' => 'Vaidmens leidimai',
    'from_role' => 'Iš vaidmens',
    'to_role' => 'Į vaidmenį',
    'update_permissions' => 'Atnaujinti leidimus',
    'permission_denied' => 'Neturite leidimo siųsti žinučių šiam vaidmeniui',
    'no_permission_to_chat' => 'Neturite leidimo naudotis pokalbiais',

    // User assignments
    'assign_users' => 'Priskirti vartotojus',
    'assignable_role' => 'Priskiriamas vaidmuo',
    'assigned_user' => 'Priskirtas vartotojas',
    'remove_assignment' => 'Pašalinti priskyrimą',
    'assignment_created' => 'Priskyrimas sėkmingai sukurtas',

    // Analytics
    'total_messages' => 'Iš viso žinučių',
    'active_users' => 'Aktyvūs vartotojai',
    'total_channels' => 'Iš viso kanalų',
    'blocked_users_count' => 'Užblokuotų vartotojų',
    'open_issues' => 'Atvirų problemų',
    'messages_today' => 'Žinučių šiandien',
    'messages_this_week' => 'Žinučių šią savaitę',
    'messages_this_month' => 'Žinučių šį mėnesį',
    'average_response_time' => 'Vidutinis atsakymo laikas',
    'most_active_channel' => 'Aktyviausias kanalas',

    // Errors
    'error_loading_messages' => 'Klaida įkeliant žinutes',
    'error_sending_message' => 'Klaida siunčiant žinutę',
    'error_creating_channel' => 'Klaida kuriant kanalą',
    'error_uploading_file' => 'Klaida įkeliant failą',
    'error_blocking_user' => 'Klaida blokuojant vartotoją',
    'error_reporting_issue' => 'Klaida pranešant apie problemą',
    'websocket_disconnected' => 'Atsijungta nuo pokalbių serverio',
    'websocket_reconnecting' => 'Jungiamasi iš naujo...',

    // Success messages
    'channel_created' => 'Kanalas sėkmingai sukurtas',
    'message_copied' => 'Žinutė nukopijuota į iškarpinę',
    'settings_saved' => 'Nustatymai išsaugoti',
    'issue_resolved_successfully' => 'Problema sėkmingai išspręsta',

    // Time formats
    'just_now' => 'Ką tik',
    'minutes_ago' => 'Prieš :count minutę|Prieš :count minutes|Prieš :count minučių',
    'hours_ago' => 'Prieš :count valandą|Prieš :count valandas|Prieš :count valandų',
    'yesterday' => 'Vakar',
    'days_ago' => 'Prieš :count dieną|Prieš :count dienas|Prieš :count dienų',
    'weeks_ago' => 'Prieš :count savaitę|Prieš :count savaites|Prieš :count savaičių',
    'months_ago' => 'Prieš :count mėnesį|Prieš :count mėnesius|Prieš :count mėnesių',

    // Actions
    'confirm' => 'Patvirtinti',
    'cancel' => 'Atšaukti',
    'save' => 'Išsaugoti',
    'close' => 'Uždaryti',
    'submit' => 'Pateikti',
    'back' => 'Atgal',
    'next' => 'Toliau',
    'delete' => 'Ištrinti',

    // Settings
    'chat_settings' => 'Pokalbių nustatymai',
    'sound_notifications' => 'Garsiniai pranešimai',
    'desktop_notifications' => 'Darbalaukio pranešimai',
    'show_typing_indicator' => 'Rodyti rašymo indikatorių',
    'enable_read_receipts' => 'Įjungti skaitymo patvirtinimus',

    // Empty states
    'no_messages' => 'Dar nėra žinučių',
    'no_notifications' => 'Nėra pranešimų',
    'no_blocked_users' => 'Nėra užblokuotų vartotojų',
    'no_chat_selected' => 'Nepasirinktas pokalbis',
    'select_conversation_mobile' => 'Pasirinkite pokalbį iš šoninio meniu, kad pradėtumėte žinutes siųsti',
    'select_conversation_desktop' => 'Pasirinkite pokalbį iš kairės, kad pradėtumėte žinutes siųsti',
    'no_conversations' => 'Nėra pokalbių',
    
    // Additional UI
    'messages' => 'Žinutės',
    'loading' => 'Kraunama',
    'load_more_messages' => 'Įkelti daugiau žinučių',
    'user_is_typing' => ':name rašo',
    'users_are_typing' => ':name1 ir :name2 rašo',
    'multiple_users_typing' => 'Keli vartotojai rašo',
    'editing_message' => 'Redaguojama žinutė',
    'replying_to' => 'Atsakoma :name',
    'reply_to' => 'Atsakyti',
    'press_enter_to_send' => 'Paspauskite Enter siųsti, Shift + Enter naujai eilutei',
    'add_emoji' => 'Pridėti jaustuką',
    'attachment' => 'Priedas',
    'create' => 'Sukurti',
    'edit' => 'Redaguoti',
    'edited' => 'Redaguota',
];
