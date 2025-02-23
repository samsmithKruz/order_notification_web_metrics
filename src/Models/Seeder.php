<?php

namespace App\Models;

use App\Libraries\Model;

class Seeder extends Model
{
    private $seedsDir = __DIR__ . '/../database/seeders/';
    public function seed()
    {
        $faker = \Faker\Factory::create();
        $seeds = scandir($this->seedsDir);
        foreach ($seeds as $seed) {
            if ($seed === '.' || $seed === '..') {
                continue;
            }
            // Include and execute the seeder file
            $seeder = require $this->seedsDir . $seed;
            
            // Ensure the seeder is callable
            if (is_callable($seeder)) {
                $seeder($this->db, $faker);
                echo "Seeded data using $seed" . PHP_EOL;
            } else {
                echo "Seeder file $seed does not return a callable function." . PHP_EOL;
            }
        }
    }
    public function make($name)
    {
        $timestamp = date('YmdHis');
        $filename = "{$timestamp}_{$name}.php";
        $filepath = rtrim($this->seedsDir, '/') . '/' . $filename;

        // Ensure the seeds directory exists
        if (!is_dir($this->seedsDir)) {
            mkdir($this->seedsDir, 0755, true);
        }

        // Check if file already exists
        if (file_exists($filepath)) {
            echo "Seed file already exists: $filename\n";
            return;
        }

        // Template for the seed file
        $template = <<<PHP
<?php
// Seed logic for the table: {$name}
return function (\$db, \$faker) {
    for (\$i = 0; \$i < 10; \$i++) {
        \$db->query("INSERT INTO {$name} (column1, column2, column3) VALUES (:value1, :value2, :value3)")
            ->bind(':value1', \$faker->word)
            ->bind(':value2', \$faker->word)
            ->bind(':value3', \$faker->word)
            ->execute();
    }
    echo "Seeded 10 rows into the {$name} table." . PHP_EOL;
};
PHP;

        // Write the seed file
        if (file_put_contents($filepath, $template)) {
            echo "Seed created: $filename\n";
        } else {
            echo "Failed to create seed file.\n";
        }
    }

}