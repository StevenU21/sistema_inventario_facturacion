<?php

namespace App\Providers;

use App\Models\Department;
use App\Models\Municipality;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Brand;
use App\Models\Backup;
use App\Models\Company;
use App\Models\UnitMeasure;
use App\Models\Category;
use App\Policies\BackupPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\MunicipalityPolicy;
use App\Policies\PaymentMethodPolicy;
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
    }
}
