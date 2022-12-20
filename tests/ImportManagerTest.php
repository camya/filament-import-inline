<?php

use Camya\Laravel\Importer\Exceptions\ImportException;
use Camya\Laravel\Importer\Facades\Import;
use Camya\Laravel\Importer\ImportManager;

it('retrieves the object from the app container.', function () {
    $this->assertInstanceOf(ImportManager::class, app(ImportManager::class));
});

it('handles JSON import and throws an exception if the input is empty.', function () {
    $data = Import::jsonString('');
    $this->assertEmpty($data);
})->throws(ImportException::class, 'Invalid JSON data.');

it('handles JSON import and throws an exception if the input is invalid.', function () {
    $data = Import::jsonString('abcd');
    $this->assertEmpty($data);
})->throws(ImportException::class, 'Invalid JSON data.');

it('handles JSON import and returns an empty array if an empty JSON object is passed.', function () {
    $data = Import::jsonString('{}');

    $this->assertEmpty($data);
});

it('handles JSON import and returns an array with correct data if a valid JSON object is passed.', function () {
    $data = Import::jsonString('{"title":"Lorem ipsum","slug":"lorem-ipsum"}');

    $this->assertEquals([
        'title' => 'Lorem ipsum',
        'slug' => 'lorem-ipsum',
    ], $data);
});

it('handles CSV import and throws an exception if the input is empty.', function () {
    $data = Import::csvString('');
    $this->assertEmpty($data);
})->throws(ImportException::class, 'Invalid CSV input.');

it('handles CSV import and creates the correct array.', function () {
    $data = Import::csvString("1,2,3\n4,5,6");

    $this->assertEquals([
        'header' => [],
        'rows' => [
            0 => [
                0 => '1',
                1 => '2',
                2 => '3',
            ],
            1 => [
                0 => '4',
                1 => '5',
                2 => '6',
            ],
        ],
    ], $data);
});

it('handles CSV import with header defined and creates the correct array.', function () {
    $data = Import::csvString(
        input: "1,2,3\n4,5,6",
        hasHeader: true
    );

    $this->assertEquals([
        'header' => [
            0 => '1',
            1 => '2',
            2 => '3',
        ],
        'rows' => [
            0 => [
                0 => '4',
                1 => '5',
                2 => '6',
            ],
        ],
    ], $data);
});

it('handles CSV import with correctly for characters with # enclosure.', function () {
    $data = Import::csvString(
        input: "1,' 2 ',3\n4,'55 555',6",
        hasHeader: true,
        enclosure: "'"
    );

    $this->assertEquals([
        'header' => [
            0 => '1',
            1 => ' 2 ',
            2 => '3',
        ],
        'rows' => [
            0 => [
                0 => '4',
                1 => '55 555',
                2 => '6',
            ],
        ],
    ], $data);
});

it('handles CSV import with exception, if wrong number of values are found in a row. (Manual detect)', function () {
    $data = Import::csvString(
        input: "1,2,3\n4,5,6,7\n8,9",
        csvPerRow: 3
    );
})->throws(ImportException::class, 'CSV value count 4 in row 2 is incorrect. Should be 3.');

it('handles CSV import with exception, if wrong number of values are found in a row. (Autodetect)', function () {
    $data = Import::csvString(
        input: "1,2,3,4\n5,6,7,8\n9,10",
    );
})->throws(ImportException::class, 'CSV value count 2 in row 3 is incorrect. Should be 4.');
