<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Atribút :attribute musí být akceptován.',
    'active_url' => 'Atribút :attribute není validní URL.',
    'after' => 'Atribút :attribute musí být datum po :date.',
    'after_or_equal' => 'Atribút :attribute musí být datum po nebo včetně :date.',
    'alpha' => ':attribute může obsahovat poze znaky.',
    'alpha_dash' => ':attribute může obsahovat poze znaky, čísla, pomlčky nebo podtržítka.',
    'alpha_num' => ':attribute může obsahovat poze znaky nebo čísla.',
    'array' => ':attribute musí být pole.',
    'before' => 'Atribút :attribute musí být datum před :date.',
    'before_or_equal' => 'Atribút :attribute musí být datum před nebo včetně :date.',
    'between' => [
        'numeric' => ':attribute musí být mezi :min a :max.',
        'file' => ':attribute musí být mezi :min a :max KB.',
        'string' => ':attribute musí mít mezi :min a :max znaků.',
        'array' => ':attribute musí mít mezi :min a :max položek.',
    ],
    'boolean' => ':attribute musí být true nebo false.',
    'confirmed' => ':attribute musí být potvrzen.',
    'date' => ':attribute není validní datum.',
    'date_equals' => ':attribute musí být datum rovné :date.',
    'date_format' => ':attribute se nezhoduje s formátem :format.',
    'different' => 'Atributy :attribute a :other musí být rozdílne.',
    'digits' => ':attribute musí obsahovat :digits číslic.',
    'digits_between' => ':attribute musí obsahovat mezi :min a :max číslic.',
    'dimensions' => ':attribute má špatné rozměry.',
    'distinct' => ':attribute má duplicitní hodnoty.',
    'email' => ':attribute musí být validní email.',
    'ends_with' => ':attribute musí končit jednou s nasledujícich hodnot: :values.',
    'exists' => 'Hodnota atributu :attribute není validní.',
    'file' => ':attribute musí být soubor.',
    'filled' => ':attribute musí mít nějakou hodnotu.',
    'gt' => [
        'numeric' => ':attribute musí být větší :value.',
        'file' => ':attribute musí mít více než :value kilobytes.',
        'string' => ':attribute musí být delší než :value znaků.',
        'array' => ':attribute musí mít více než :value položek.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => ':attribute musí být menší nebo roven :max.',
        'file' => ':attribute musí být menší nebo roven :max kilobytů.',
        'string' => ':attribute nesmí být delší než :max znaků.',
        'array' => ':attribute nesmí mít více než :max položek.',
    ],
    'mimes' => ':attribute musí být soubor typu: :values.',
    'mimetypes' => ':attribute musí být soubor typu: :values.',
    'min' => [
        'numeric' => ':attribute musí být větši nebo roven :min.',
        'file' => ':attribute musí být menší nebo roven :min kilobytů.',
        'string' => ':attribute nesmí být delší než :min znaků.',
        'array' => ':attribute nesmí mít více než :min položek.',
    ],
    'not_in' => ':attribute nemá validní hodnotu.',
    'not_regex' => ':attribute nemá validní formát.',
    'numeric' => ':attribute musí být číslo.',
    'password' => 'Heslo není správne.',
    'present' => ':attribute musí být součásti dotazu.',
    'regex' => ':attribute nemá validní formát.',
    'required' => 'Atribut :attribute musí být vyplněn.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => ':attribute musí být vyplňen pokud je vyplněn :values.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => ':attribute musí začínat jednou s nasledujícich hdnot: :values.',
    'string' => ':attribute musí být řetězec.',
    'timezone' => 'Hodnota atributu :attribute musí být validní časová zóna.',
    'unique' => 'Hodnota atributu :attribute již existuje.',
    'uploaded' => ':attribute se nepodařilo nahrat.',
    'url' => ':attribute nemá validní formát.',
    'uuid' => 'Hodnota atributu :attribute musí být validní UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'first_name' => 'jméno',
        'last_name' => 'příjmení',
        'email' => 'e-mail',
        'nickname' => 'přihlašovací jméno',
        'password' => 'heslo',
        'new_password' => 'nové heslo',
        'role' => 'role',
        'gender' => 'pohlaví',
        'birth_date' => 'datum narodení',
        'street' => 'ulice',
        'house_number' => 'čislo domu',
        'city' => 'město',
        'country' => 'stát',
        'phone' => 'telefonní čislo',
        'preferred_language' => 'preferovaný jazyk',
        'time_limit' => 'časový limit',
        'questions_number' => 'počet otázek',
        'start_date' => 'datum začátku',
        'subject' => 'předmět',
        'name' => 'název',
        'description' => 'popisek',
        'professor_id' => 'profesor',
        'test_id' => 'test',
        'group_id' => 'skupina',

        'questions' => 'otázky',
        'questions.*' => 'otázka',
        'questions.*.id' => 'id otázky',
        'questions.*.name' => 'název otázky',
        'questions.*.text' => 'text otázky',
        'questions.*.type' => 'typ otázky',
        'questions.*.min_points' => 'minimálni počet bodů otázky',
        'questions.*.max_points' => 'maximálni počet bodů otázky',
        'questions.*.files_number' => 'počet souborů otázky',

        'questions.*.files' => 'soubory otázky',
        'questions.*.files.*' => 'soubor',
        'questions.*.files.*.name' => 'název souboru',
        'questions.*.files.*.id' => 'id souboru',
        'questions.*.files.*.base64' => 'soubor',

        'questions.*.options' => 'možnosti otázky',
        'questions.*.options.*' => 'možnost',
        'questions.*.options.*.id' => 'id možnosti',
        'questions.*.options.*.text' => 'text možnosti',
        'questions.*.options.*.points' => 'počet bodů za možnost',
    ],

];
