<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',

            'user.manage',

            'product-category.manage',
            'product.manage',
            'supplier.manage',
            'warehouse.manage',

            'warehouse-stock.view',
            'warehouse-stock.sync',

            'purchase-order.view',
            'purchase-order.create',
            'purchase-order.edit',
            'purchase-order.delete',
            'purchase-order.submit',
            'purchase-order.approve',
            'purchase-order.reject',
            'purchase-order.cancel',

            'stock-receipt.view',
            'stock-receipt.create',

            'stock-movement.view',

            'stock-adjustment.view',
            'stock-adjustment.create',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'ADMIN',
            'guard_name' => 'web',
        ]);

        $purchasing = Role::firstOrCreate([
            'name' => 'PURCHASING',
            'guard_name' => 'web',
        ]);

        $manager = Role::firstOrCreate([
            'name' => 'MANAGER',
            'guard_name' => 'web',
        ]);

        $warehouse = Role::firstOrCreate([
            'name' => 'WAREHOUSE',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions($permissions);

        $purchasing->syncPermissions([
            'dashboard.view',

            'supplier.manage',

            'purchase-order.view',
            'purchase-order.create',
            'purchase-order.edit',
            'purchase-order.delete',
            'purchase-order.submit',
            'purchase-order.cancel',

            'stock-receipt.view',
            'stock-movement.view',
            'warehouse-stock.view',
        ]);

        $manager->syncPermissions([
            'dashboard.view',

            'purchase-order.view',
            'purchase-order.approve',
            'purchase-order.reject',
            'purchase-order.cancel',

            'stock-receipt.view',
            'stock-movement.view',
            'warehouse-stock.view',
        ]);

        $warehouse->syncPermissions([
            'dashboard.view',

            'warehouse.manage',
            'warehouse-stock.view',
            'warehouse-stock.sync',

            'purchase-order.view',

            'stock-receipt.view',
            'stock-receipt.create',

            'stock-movement.view',

            'stock-adjustment.view',
            'stock-adjustment.create',
        ]);

        $firstUser = User::query()->first();

        if ($firstUser && ! $firstUser->hasRole('ADMIN')) {
            $firstUser->assignRole('ADMIN');
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}