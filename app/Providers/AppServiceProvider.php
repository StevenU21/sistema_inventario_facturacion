<?php

namespace App\Providers;

use App\Models\Color;
use App\Models\Entity;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Kardex;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Quotation;
use App\Models\Sale;
use App\Models\Size;
use App\Models\Tax;
use App\Models\User;
use App\Models\Brand;
use App\Models\Backup;
use App\Models\Company;
use App\Models\UnitMeasure;
use App\Models\Category;
use App\Models\Warehouse;
use App\Policies\BackupPolicy;
use App\Policies\ColorPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\EntityPolicy;
use App\Policies\InventoryMovementPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\KardexPolicy;
use App\Policies\PaymentMethodPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProductVariantPolicy;
use App\Policies\QuotationPolicy;
use App\Policies\RolePolicy;
use App\Policies\SalePolicy;
use App\Policies\SizePolicy;
use App\Policies\TaxPolicy;
use App\Policies\UnitMeasurePolicy;
use App\Policies\UserPolicy;
use App\Policies\AuditPolicy;
use App\Policies\BrandPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\WarehousePolicy;
use App\Policies\PurchasePolicy;
use App\Policies\PurchaseDetailPolicy;
use App\Policies\AccountReceivablePolicy;
use App\Models\AccountReceivable;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
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
        Gate::policy(PaymentMethod::class, PaymentMethodPolicy::class);
        Gate::policy(Tax::class, TaxPolicy::class);
        Gate::policy(Entity::class, EntityPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Warehouse::class, WarehousePolicy::class);
        Gate::policy(Inventory::class, InventoryPolicy::class);
        Gate::policy(InventoryMovement::class, InventoryMovementPolicy::class);
        Gate::policy(Size::class, SizePolicy::class);
        Gate::policy(Color::class, ColorPolicy::class);
        Gate::policy(ProductVariant::class, ProductVariantPolicy::class);
        Gate::policy(Purchase::class, PurchasePolicy::class);
        Gate::policy(PurchaseDetail::class, PurchaseDetailPolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(AccountReceivable::class, AccountReceivablePolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(Quotation::class, QuotationPolicy::class);
        Gate::policy(Kardex::class, KardexPolicy::class);
    }
}
