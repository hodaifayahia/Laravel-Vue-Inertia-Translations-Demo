<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create some test permissions
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'view roles']);
        Permission::create(['name' => 'edit roles']);

        // Create Super Admin role with all permissions
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(['view users', 'edit users', 'delete users', 'view roles', 'edit roles']);

        // Create a test user with Super Admin role
        $this->user = User::factory()->create();
        $this->user->assignRole('Super Admin');

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    #[Test]
    public function it_can_create_a_role_with_permissions(): void
    {
        // Create role directly since we're testing the model functionality
        $role = Role::create(['name' => 'Test Role']);
        $role->givePermissionTo(['view users', 'edit users']);
        
        $this->assertDatabaseHas('roles', [
            'name' => 'Test Role',
        ]);

        $role = Role::where('name', 'Test Role')->first();
        $this->assertNotNull($role);
        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->hasPermissionTo('view users'));
        $this->assertTrue($role->hasPermissionTo('edit users'));
    }

    #[Test]
    public function it_can_update_role_permissions(): void
    {
        // Create a role with initial permissions
        $role = Role::create(['name' => 'Manager']);
        $role->givePermissionTo(['view users', 'edit users']);

        // Verify initial state
        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->hasPermissionTo('view users'));
        $this->assertTrue($role->hasPermissionTo('edit users'));

        // Update the role with different permissions directly
        $role->syncPermissions(['view users', 'delete users', 'view roles']);

        // Refresh the role and check permissions
        $role->refresh();
        
        $this->assertCount(3, $role->permissions, 'Role should have 3 permissions after update');
        $this->assertTrue($role->hasPermissionTo('view users'), 'Should still have view users');
        $this->assertFalse($role->hasPermissionTo('edit users'), 'Should no longer have edit users');
        $this->assertTrue($role->hasPermissionTo('delete users'), 'Should have delete users');
        $this->assertTrue($role->hasPermissionTo('view roles'), 'Should have view roles');
    }

    #[Test]
    public function it_can_remove_all_permissions_from_role(): void
    {
        // Create a role with permissions
        $role = Role::create(['name' => 'Basic Role']);
        $role->givePermissionTo(['view users', 'edit users']);

        $this->assertCount(2, $role->permissions);

        // Remove all permissions directly
        $role->syncPermissions([]);

        // Refresh and verify all permissions are removed
        $role->refresh();
        $this->assertCount(0, $role->permissions, 'Role should have no permissions after clearing');
    }

    #[Test]
    public function it_validates_permission_names_exist(): void
    {
        $role = Role::create(['name' => 'Test Role']);

        // Try to assign a non-existent permission - should throw exception
        $this->expectException(\Spatie\Permission\Exceptions\PermissionDoesNotExist::class);
        
        $role->givePermissionTo('non-existent-permission');
    }

    #[Test]
    public function it_can_add_permissions_to_role_that_had_none(): void
    {
        // Create a role with no permissions
        $role = Role::create(['name' => 'Empty Role']);
        $this->assertCount(0, $role->permissions);

        // Add permissions directly
        $role->givePermissionTo(['view users', 'view roles']);

        $role->refresh();
        $this->assertCount(2, $role->permissions, 'Should have 2 permissions after adding');
        $this->assertTrue($role->hasPermissionTo('view users'));
        $this->assertTrue($role->hasPermissionTo('view roles'));
    }

    #[Test]
    public function it_displays_role_index_page_with_permissions(): void
    {
        $role = Role::create(['name' => 'Tester']);
        $role->givePermissionTo(['view users', 'edit users']);

        $response = $this->actingAs($this->user)
            ->get(route('roles.index'));

        $response->assertStatus(200);
        
        // Check that Inertia props contain roles (including Super Admin from setup + Tester)
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('RoleManagement/Index')
            ->has('roles', 2) // Super Admin + Tester
            ->where('roles.1.name', 'Tester')
            ->has('roles.0.permissions', 5) // Super Admin has 5 permissions
            ->has('roles.1.permissions', 2) // Tester has 2 permissions
        );
    }
}
