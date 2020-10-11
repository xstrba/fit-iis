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
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
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
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
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
    'required_without' => 'The :attribute field is required when :values is not present.',
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
    'timezone' => ':attribute musí být validní časová zóna.',
    'unique' => ':attribute je už použit.',
    'uploaded' => ':attribute se nepodařilo nahrat.',
    'url' => ':attribute nemá validní formát.',
    'uuid' => 'The :attribute must be a valid UUID.',

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
    ],

];
