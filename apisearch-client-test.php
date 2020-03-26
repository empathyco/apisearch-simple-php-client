<?php

require __DIR__ . '/apisearch-client.php';

/**
 * Asserts both values are equal
 *
 * @param $expected
 * @param $value
 *
 * @throws Exception
 */
function assertEquals($expected, $value)
{
    if ($expected !== $value) {
        throw new Exception(sprintf("Expected to get %d. Had %d",
            $expected,
            $value
        ));
    }

    echo '.';
}

$client = new ApisearchClient(
    'http://127.0.0.1:8100',
    'v1'
);

$client->setCredentials(
    'app1',
    'index1',
    '0e4d75ba-c640-44c1-a745-06ee51db4e93'
);

$client->resetIndex();

$result = $client->query(['q' => 'nobita']);
assertEquals(0, $result['total_hits']);
assertEquals(0, $result['total_items']);

$client->putItem([
    'uuid' => ['id' => "1", 'type' => 'element'],
    'metadata' => ['m' => 'm1'],
    'indexed_metadata' => ['im' => 'im1'],
    'searchable_metadata' => ['s' => 'nobita']
]);
$client->flush();
sleep(1);

$result = $client->query([]);
assertEquals(1, $result['total_hits']);
assertEquals(1, $result['total_items']);

$result = $client->query(['q' => 'nobita']);
assertEquals(1, $result['total_hits']);
assertEquals(1, $result['total_items']);

$client->putItems([[
    'uuid' => ['id' => "2", 'type' => 'element'],
    'metadata' => ['m' => 'm1'],
    'indexed_metadata' => ['im' => 'im1'],
    'searchable_metadata' => ['s' => 'nobita']
], [
    'uuid' => ['id' => "3", 'type' => 'element'],
    'metadata' => ['m' => 'm2'],
    'indexed_metadata' => ['im' => 'im2'],
    'searchable_metadata' => ['s' => 'suneo']
]]);
$client->flush();
sleep(1);

$result = $client->query(['q' => 'nobita']);
assertEquals(2, $result['total_hits']);
assertEquals(3, $result['total_items']);

$client->deleteItem(['id' => "2", 'type' => 'element']);
$client->flush();
sleep(1);

$result = $client->query(['q' => 'nobita']);
assertEquals(1, $result['total_hits']);
assertEquals(2, $result['total_items']);


$client->deleteItems([
    ['id' => "3", 'type' => 'element'],
    ['id' => "2", 'type' => 'element'],
]);
$client->flush();
sleep(1);


$result = $client->query(['q' => 'nobita']);
assertEquals(1, $result['total_hits']);
assertEquals(1, $result['total_items']);
echo PHP_EOL;