<?php

namespace App\Support;

final class Odontograma
{
    public const SUPERIORES = [
        '18', '17', '16', '15', '14', '13', '12', '11',
        '21', '22', '23', '24', '25', '26', '27', '28',
    ];

    public const INFERIORES = [
        '48', '47', '46', '45', '44', '43', '42', '41',
        '31', '32', '33', '34', '35', '36', '37', '38',
    ];

    public const CARAS = [
        'vestibular' => 'Vestibular',
        'lingual_palatina' => 'Lingual / palatina',
        'mesial' => 'Mesial',
        'distal' => 'Distal',
        'oclusal_incisal' => 'Oclusal / incisal',
        'pieza_completa' => 'Pieza completa',
    ];

    public const CONDICIONES = [
        'sano' => 'Sano',
        'caries' => 'Caries',
        'obturado' => 'Obturado',
        'fracturado' => 'Fracturado',
        'ausente' => 'Ausente',
        'corona' => 'Corona',
        'endodoncia' => 'Endodoncia',
        'extraccion_indicada' => 'Extracción indicada',
    ];

    public static function piezas(): array
    {
        return array_merge(
            self::SUPERIORES,
            self::INFERIORES
        );
    }
}
