<?php
// Seed logic for the table: orders
return function ($db, $faker) {
    for ($i = 0; $i < 10; $i++) {
        $db->query("INSERT INTO orders (txn_id, product_id, description, amount, status) 
                    VALUES (:txn_id, :product_id, :description, :amount, :status)")
            ->bind(':txn_id', $faker->uuid)
            ->bind(':product_id', $faker->numberBetween(1, 100))
            ->bind(':description', $faker->sentence)
            ->bind(':amount', $faker->randomFloat(2, 10, 1000))
            ->bind(':status', $faker->randomElement(['pending', 'completed', 'cancel']))
            ->execute();
    }
    echo "Seeded 10 rows into the orders table." . PHP_EOL;
};
