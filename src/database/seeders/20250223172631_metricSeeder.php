<?php
// Seed logic for the table: metrics
return function ($db, $faker) {
    // List of sample browsers for random selection
    $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera'];

    for ($i = 0; $i < 10; $i++) {
        $api_keys = $db->query("SELECT id, domain FROM api_keys ORDER BY RAND() LIMIT 1")->single();
        // Fetch a random API key ID
        $api_key_id = $api_keys->id;

        // Fetch the domain linked to the API key
        $domain = $api_keys->domain;

        // Append a random route to the domain
        $random_route = $faker->word . '/' . $faker->word . '/' . $faker->word;
        $page_url = $domain . '/' . $random_route;

        // Randomly select a browser
        $browser = $browsers[array_rand($browsers)];

        // Insert the data into the metrics table
        $db->query("INSERT INTO metrics (api_key_id, page, referrer, session_id, browser, device, os, screen_resolution, timestamp) 
                    VALUES (:api_key_id, :page, :referrer, :session_id, :browser, :device, :os, :screen_resolution, :timestamp)")
            ->bind(':api_key_id', $api_key_id)
            ->bind(':page', $page_url)  // Using the domain + random route as the page URL
            ->bind(':referrer', $faker->url)  // Generating a random URL for the referrer
            ->bind(':session_id', $faker->uuid)  // Generating a random session ID
            ->bind(':browser', $browser)  // Random browser name from the list
            ->bind(':device', $faker->word)  // Random device type
            ->bind(':os', $faker->word)  // Random OS name
            ->bind(':screen_resolution', $faker->randomDigitNotNull . 'x' . $faker->randomDigitNotNull)  // Random screen resolution
            ->bind(':timestamp', $faker->dateTimeThisYear()->format('Y-m-d H:i:s'))  // Random timestamp for metrics
            ->execute();
    }
    echo "Seeded 10 rows into the metrics table." . PHP_EOL;
};

