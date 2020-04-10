<?php

namespace App\Rules\Lan;

use App\Model\LanImage;
use Illuminate\Contracts\Validation\Rule;

/**
 * Un tableau est constitué uniquement d'id d'images.
 *
 * Class ManyImageIdsExist
 */
class ManyImageIdsExist implements Rule
{
    protected $badImageIds = [];

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param string $imageIds
     *
     * @return bool
     */
    public function passes($attribute, $imageIds): bool
    {
        /*
         * Conditions de garde :
         * L'élément passé est non nul.
         * L'élément passé est une chaîne de caractères.
         * L'id du LAN passé est non nul.
         */
        if (
            is_null($imageIds) ||
            !is_string($imageIds)
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Distribuer les id divisés par des virgules dans un tableau
        $imageIdArray = array_map('intval', explode(',', $imageIds));
        // Pour chaque id d'image
        for ($i = 0; $i < count($imageIdArray); $i++) {

            // Chercher une image avec l'id de l'image et l'id du LAN
            $image = LanImage::find($imageIdArray[$i]);

            // Si aucune image n'a été trouvée, ajouter l'image à un tableau qui
            // sera retourné pour spécifier les id fautifs
            if (is_null($image)) {
                array_push($this->badImageIds, $imageIdArray[$i]);
            }
        }

        // Si des id sont dans le tableau d'id d'images fautifs
        return count($this->badImageIds) == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.many_image_ids_exist', [
            'ids'       => implode(', ', $this->badImageIds),
            'attribute' => ':attribute',
        ]);
    }
}
