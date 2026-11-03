<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Institution;
use App\Models\Item;
use App\Models\ItemTransfer;
use App\Models\ItemTransferItem;
use App\Models\ItemUnit;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Room;
use App\Models\User;
use App\Policies\DepartmentPolicy;
use App\Policies\InstitutionPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\UserPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        foreach ([Institution::class, Department::class, Category::class, Item::class, ItemUnit::class, Room::class, Loan::class, LoanItem::class, ItemTransfer::class, ItemTransferItem::class] as $model) {
            Gate::policy($model, InventoryPolicy::class);
        }
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Institution::class, InstitutionPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Category::class, DepartmentPolicy::class);
        Gate::policy(Item::class, DepartmentPolicy::class);
        Gate::policy(Room::class, DepartmentPolicy::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
