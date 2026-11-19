<?php

namespace Database\Factories;

use App\Domain\Authorization\SiteAuthorization;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use WeakMap;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * @var WeakMap<User, RoleName>|null
     */
    private static ?WeakMap $factoryRoles = null;

    public function configure(): static
    {
        return $this
            ->afterMaking(function (User $user): void {
                self::factoryRoles()[$user] = $this->roleFor($user);

                $attributes = $user->getAttributes();
                unset($attributes['__factory_role'], $attributes['role'], $attributes['is_admin']);
                $user->setRawAttributes($attributes);
            })
            ->afterCreating(function (User $user): void {
                SiteAuthorization::assignRole($user, self::factoryRoles()[$user] ?? RoleName::Player);
                unset(self::factoryRoles()[$user]);
            });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName().' '.fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(['__factory_role' => RoleName::Admin->value]);
    }

    public function teamAdmin(): static
    {
        return $this->state(['__factory_role' => RoleName::TeamAdmin->value]);
    }

    public function player(): static
    {
        return $this->state(['__factory_role' => RoleName::Player->value]);
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

    private static function factoryRoles(): WeakMap
    {
        return self::$factoryRoles ??= new WeakMap;
    }

    private function roleFor(User $user): RoleName
    {
        $factoryRole = $user->getAttribute('__factory_role');

        if ($factoryRole !== null) {
            return SiteAuthorization::normalizeRoleName((string) $factoryRole);
        }

        $legacyRole = $user->getAttribute('role');

        return SiteAuthorization::inferRoleNameFromLegacy(
            $legacyRole !== null ? (string) $legacyRole : null,
            (bool) $user->getAttribute('is_admin'),
        );
    }
}
