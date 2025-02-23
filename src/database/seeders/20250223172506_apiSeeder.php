<?php
// Seed logic for the table: api_keys
return function ($db, $faker) {
    for ($i = 0; $i < 2; $i++) {
        $db->query("INSERT INTO api_keys (api_key, domain) VALUES (:api_key, :domain)")
            ->bind(':api_key', $faker->uuid)  // Generating a UUID for the API key
            ->bind(':domain', $faker->domainName)  // Generating a random domain name
            ->execute();
    }
    echo "Seeded 10 rows into the api_keys table." . PHP_EOL;
};
