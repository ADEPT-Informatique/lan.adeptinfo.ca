<?php

namespace App\Rules\General;

use Illuminate\Contracts\Validation\Rule;

/**
 * Exactement un seul des deux champs passés est non null.
 *
 * Class OneOfTwoFields
 */
class OneOfTwoFields implements Rule
{
    protected $secondField;
    protected $secondFieldName;

    /**
     * OneOfTwoFields constructor.
     *
     * @param null $secondField     Second champ
     * @param null $secondFieldName Nom du second champ (pour le message d'erreur))
     */
    public function __construct($secondField, $secondFieldName)
    {
        $this->secondField = $secondField;
        $this->secondFieldName = $secondFieldName;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $field     Premier champ
     *
     * @return bool
     */
    public function passes($attribute, $field): bool
    {
        // Si les 2 champs sont nuls, la validation échoue
        if (!is_null($field) && !is_null($this->secondField)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.one_of_two_fields', ['value' => ':attribute', 'second_field' => $this->secondFieldName]);
    }
}
