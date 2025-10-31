<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and permissions manually for tests
        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'User']);

        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);

        // Assign permissions to roles
        $superAdmin = Role::findByName('Super Admin');
        $superAdmin->givePermissionTo(['view users', 'create users', 'edit users', 'delete users']);

        $admin = Role::findByName('Admin');
        $admin->givePermissionTo(['view users', 'create users', 'edit users']);
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $this->assertTrue($user->hasRole('Super Admin'));
        $this->assertTrue($user->can('view users'));
        $this->assertTrue($user->can('create users'));
        $this->assertTrue($user->can('edit users'));
        $this->assertTrue($user->can('delete users'));
    }

    public function test_admin_has_limited_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $this->assertTrue($user->hasRole('Admin'));
        $this->assertTrue($user->can('view users'));
        $this->assertFalse($user->can('delete users'));
    }

    public function test_user_role_has_basic_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('User');

        $this->assertTrue($user->hasRole('User'));
        $this->assertFalse($user->can('view users'));
        $this->assertFalse($user->can('create users'));
    }

    public function test_user_can_be_assigned_multiple_roles(): void
    {
        $user = User::factory()->create();
        
        $user->assignRole(['User', 'Admin']);

        $this->assertTrue($user->hasRole('User'));
        $this->assertTrue($user->hasRole('Admin'));
        $this->assertTrue($user->hasAnyRole(['User', 'Admin']));
    }

    public function test_permission_middleware_blocks_unauthorized_users(): void
    {
        $user = User::factory()->create();
        $user->assignRole('User');

        $response = $this->actingAs($user)
            ->get('/users');

        $response->assertForbidden();
    }

    public function test_permission_middleware_allows_authorized_users(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $response = $this->actingAs($user)
            ->get('/users');

        $response->assertSuccessful();
    }

    public function test_roles_can_be_created_dynamically(): void
    {
        $role = Role::create(['name' => 'Moderator']);
        $permission = Permission::create(['name' => 'moderate content']);
        
        $role->givePermissionTo($permission);

        $this->assertTrue($role->hasPermissionTo('moderate content'));
    }

    public function test_permissions_can_be_revoked(): void
    {
        $user = User::factory()->create();
        $permission = Permission::firstWhere('name', 'view users');
        
        $user->givePermissionTo($permission);
        $this->assertTrue($user->can('view users'));

        $user->revokePermissionTo($permission);
        $this->assertFalse($user->can('view users'));
    }

    public function test_roles_can_be_removed_from_users(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        
        $this->assertTrue($user->hasRole('Admin'));

        $user->removeRole('Admin');
        $this->assertFalse($user->hasRole('Admin'));
    }

    public function test_direct_permissions_work_without_roles(): void
    {
        $user = User::factory()->create();
        $permission = Permission::firstWhere('name', 'view users');
        
        $user->givePermissionTo($permission);

        $this->assertTrue($user->can('view users'));
        $this->assertFalse($user->hasRole('Admin'));
    }
}
