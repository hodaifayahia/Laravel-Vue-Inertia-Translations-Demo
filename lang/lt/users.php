<?php

return [
    // Page titles and headers
    'title' => 'Vartotojų valdymas',
    'description' => 'Valdyti visus sistemos vartotojus',
    'user_list' => 'Vartotojų sąrašas',
    
    // Actions
    'add_user' => 'Pridėti vartotoją',
    'add_first_user' => 'Pridėti pirmąjį vartotoją',
    'create_user' => 'Sukurti naują vartotoją',
    'edit_user' => 'Redaguoti vartotoją',
    'delete_user' => 'Ištrinti vartotoją',
    'create' => 'Sukurti vartotoją',
    'update' => 'Atnaujinti vartotoją',
    'delete_confirm' => 'Taip, ištrinti',
    'cancel' => 'Atšaukti',
    'edit' => 'Redaguoti',
    'delete' => 'Ištrinti',
    'actions' => 'Veiksmai',
    'filters' => 'Paieška ir filtrai',
    
    // Form fields
    'name' => 'Vardas',
    'email' => 'El. paštas',
    'password' => 'Slaptažodis',
    'password_confirmation' => 'Patvirtinti slaptažodį',
    'language' => 'Kalba',
    'locale' => 'Kalba',
    
    // Placeholders
    'name_placeholder' => 'Įveskite pilną vardą',
    'email_placeholder' => 'Įveskite el. pašto adresą',
    'password_placeholder' => 'Įveskite slaptažodį',
    'password_confirmation_placeholder' => 'Patvirtinkite slaptažodį',
    'search_placeholder' => 'Ieškoti pagal vardą arba el. paštą...',
    
    // Status
    'status' => 'Būsena',
    'verified' => 'Patvirtintas',
    'unverified' => 'Nepatvirtintas',
    'verified_only' => 'Tik patvirtinti',
    'unverified_only' => 'Tik nepatvirtinti',
    'all_users' => 'Visi vartotojai',
    
    // Stats
    'total_users' => 'Viso vartotojų',
    'verified_users' => 'Patvirtinti vartotojai',
    'unverified_users' => 'Nepatvirtinti vartotojai',
    
    // Table columns
    'user' => 'Vartotojas',
    'joined' => 'Prisijungė',
    
    // Messages
    'creating' => 'Kuriama...',
    'updating' => 'Atnaujinama...',
    'deleting' => 'Trinama...',
    'created_successfully' => 'Vartotojas sėkmingai sukurtas',
    'updated_successfully' => 'Vartotojas sėkmingai atnaujintas',
    'deleted_successfully' => 'Vartotojas sėkmingai ištrintas',
    'cannot_delete_yourself' => 'Negalite ištrinti savęs',
    'leave_blank_to_keep' => 'Palikite tuščią, kad išsaugotumėte dabartinį slaptažodį',
    
    // Descriptions
    'create_user_description' => 'Pridėti naują vartotoją į sistemą su jų duomenimis',
    'edit_user_description' => 'Atnaujinti vartotojo informaciją ir nustatymus',
    'delete_user_warning' => 'Šis veiksmas negali būti atšauktas',
    'delete_user_confirm' => 'Ar tikrai norite ištrinti :name?',
    
    // Empty state
    'no_users' => 'Vartotojų nerasta',
    'no_users_description' => 'Pradėkite sukurdami savo pirmąjį vartotoją',
    
    // Pagination
    'showing_results' => 'Rodoma :total vartotojų',
    'pagination_info' => 'Rodoma nuo :from iki :to iš :total vartotojų',
    
    // Validation messages
    'validation' => [
        'name_required' => 'Vardas yra privalomas',
        'email_required' => 'El. paštas yra privalomas',
        'email_invalid' => 'El. paštas turi būti galiojantis el. pašto adresas',
        'email_unique' => 'Šis el. paštas jau užimtas',
        'password_required' => 'Slaptažodis yra privalomas',
        'password_confirmed' => 'Slaptažodžio patvirtinimas nesutampa',
    ],
];
