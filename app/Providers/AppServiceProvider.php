<?php

namespace App\Providers;

use App\Models\Department;
use App\Models\Entity;
use App\Models\Municipality;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Tax;
use App\Models\User;
use App\Models\Brand;
use App\Models\Backup;
use App\Models\Company;
use App\Models\UnitMeasure;
use App\Models\Category;
use App\Policies\BackupPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\EntityPolicy;
use App\Policies\MunicipalityPolicy;
use App\Policies\PaymentMethodPolicy;
use App\Policies\ProductPolicy;
use App\Policies\RolePolicy;
use App\Policies\TaxPolicy;
use App\Policies\UnitMeasurePolicy;
use App\Policies\UserPolicy;
use App\Policies\AuditPolicy;
use App\Policies\BrandPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\PermissionPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        Paginator::useTailwind();
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Activity::class, AuditPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Brand::class, BrandPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Backup::class, BackupPolicy::class);
        Gate::policy(UnitMeasure::class, UnitMeasurePolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Municipality::class, MunicipalityPolicy::class);
        Gate::policy(PaymentMethod::class, PaymentMethodPolicy::class);
        Gate::policy(Tax::class, TaxPolicy::class);
        Gate::policy(Entity::class, EntityPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
    }
}
