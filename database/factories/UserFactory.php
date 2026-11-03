<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'role' => 'operator',
            'institution_id' => fn () => DB::table('institutions')->insertGetId([
                'code' => (string) Str::ulid(), 'name' => fake()->company(),
            ]),
            'department_id' => fn (array $attributes) => DB::table('departments')->insertGetId([
                'institution_id' => $attributes['institution_id'], 'code' => 'TU', 'name' => 'Tata Usaha',
            ]),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function superadmin(): static
    {
        return $this->state(fn () => [
            'role' => 'superadmin', 'institution_id' => null, 'department_id' => null,
        ]);
    }
}
