<?php

namespace Camya\Laravel\Importer;

use Camya\Laravel\Importer\Exceptions\ImportException;

class ImportManager
{
    public const CSV_ERROR_INVALID_INPUT = 1;

    public const CSV_ERROR_INVALID_CVS_PER_ROW_COUNT = 2;

    public const JSON_ERROR_INVALID_INPUT = 3;

    public const JSON_ERROR_INVALID_RESULT = 4;

    /** @throws ImportException */
    public static function csvString(
        string|null $input,
        null|int $csvPerRow = null,
        bool $hasHeader = false,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\',
        bool $csvPerRowAutodetect = true,
    ): array {
        if (! $input) {
            throw new ImportException('Invalid CSV input.', self::CSV_ERROR_INVALID_INPUT);
        }

        $data = [
            'header' => [],
            'rows' => [],
        ];

        $rows = explode("\n", $input);

        foreach ($rows as $index => $row) {
            $values = str_getcsv(
                string: $row,
                separator: $separator,
                enclosure: $enclosure,
                escape: $escape,
            );

            // Autodetect csv value count from first row.
            if ($csvPerRowAutodetect && $csvPerRow === null && $index === 0) {
                $csvPerRow = count($values);
            }

            // Check, if value count matches the value set in $valuePerRow.
            if ($csvPerRow !== null && count($values) !== $csvPerRow) {
                throw new ImportException(
                    'CSV value count '.count($values).' in row '.($index + 1).' is incorrect. '.
                    'Should be '.$csvPerRow.'.',
                    self::CSV_ERROR_INVALID_CVS_PER_ROW_COUNT
                );
            }

            $data['rows'][] = $values;
        }

        if ($hasHeader && $data['rows']) {
            $data['header'] = array_shift($data['rows']);
        }

        return $data;
    }

    /** @throws ImportException */
    public static function jsonString(string|null $input): array
    {
        $data = null;

        if ($input !== null && trim($input)) {
            try {
                $data = json_decode(
                    json: $input,
                    associative: true,
                    depth: 512,
                    flags: JSON_THROW_ON_ERROR
                );
            } catch (\JsonException) {
                throw new ImportException('Invalid JSON data.', self::JSON_ERROR_INVALID_INPUT);
            }
        }

        // If the input is an integer, it's technically a valid JSON object.
        // We throw exception nevertheless, because importing integers
        // is not the use case of the importJSON method.
        if (! is_array($data)) {
            throw new ImportException('Invalid JSON data.', self::JSON_ERROR_INVALID_RESULT);
        }

        return $data;
    }
}
