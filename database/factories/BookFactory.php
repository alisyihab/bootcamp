<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id" => $this->faker->numberBetween($min = 1, $max = 5),
            "category_id" => $this->faker->numberBetween($min = 1, $max = 5),
            "title" => $this->faker->name,
            "description" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente, consequatur labore perferendis sed dolorum saepe ut, laudantium voluptatibus cupiditate, cumque et officia. Amet dolorum eius architecto molestiae animi dolor rem.",
            "price" => $this->faker->numberBetween($min = 100000, $max = 200000),
            "status" => $this->faker->numberBetween($min = 0, $max = 1),
        ];
    }
}
