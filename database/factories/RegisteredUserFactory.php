<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RegisteredUserFactory extends Factory
{
    public function definition(): array
    {
        $departments = [
            'Information Technology',
            'Electrical and Electronics Engineering',
            'Civil Engineering',
            'Mechanical Engineering',
            'Agricultural and Bioresources Engineering',
            'Architecture',
            'Estate Management',
            'Urban and Regional Planning',
            'Mathematics',
            'Statistics',
            'Physics',
            'Chemistry',
            'Information and Media Technology',
        ];

        $category = fake()->randomElement(['student', 'staff', 'visitor']);

        return [
            'first_name'    => fake()->firstName(),
            'last_name'     => fake()->lastName(),
            'email'         => fake()->unique()->safeEmail(),
            'phone'         => '0' . fake()->numerify('#########'),
            'user_category' => $category,
            'id_number'     => $category === 'visitor' ? null : strtoupper(fake()->bothify('##/##??/###')),
            'department'    => $category === 'visitor' ? null : fake()->randomElement($departments),
            'status'        => 'active',
        ];
    }
}
