<?php

namespace Tests\Feature;

use App\Filament\Resources\BendaharaResource\Pages\CreateBendahara;
use App\Filament\Resources\BendaharaResource\Pages\EditBendahara;
use App\Filament\Resources\BendaharaResource\Pages\ListBendaharas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BendaharaFilamentResourceTest extends TestCase
{
    protected function getAdminUser(): User
    {
        return User::where('role_id', User::ROLE_ADMIN)->first()
            ?? User::factory()->create([
                'role_id' => User::ROLE_ADMIN,
                'email' => 'admin@email.com',
                'username' => 'admin',
            ]);
    }

    protected function getBendaharaUser(): User
    {
        return User::where('role_id', User::ROLE_BENDAHARA)->first()
            ?? User::factory()->create([
                'role_id' => User::ROLE_BENDAHARA,
                'email' => 'bendaharafkip@unib.ac.id',
                'username' => 'bendahara',
            ]);
    }

    public function test_guest_cannot_access_bendahara_resource()
    {
        $response = $this->get('/admin/akun-bendahara');
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_cannot_access_bendahara_resource()
    {
        $bendahara = $this->getBendaharaUser();

        $response = $this->actingAs($bendahara)->get('/admin/akun-bendahara');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_bendahara_list_page()
    {
        $admin = $this->getAdminUser();
        $bendahara = $this->getBendaharaUser();

        $response = $this->actingAs($admin)->get('/admin/akun-bendahara');
        $response->assertStatus(200);
        $response->assertSee($bendahara->name);
        $response->assertSee($bendahara->username);
    }

    public function test_admin_can_access_bendahara_create_page()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get('/admin/akun-bendahara/create');
        $response->assertStatus(200);
        $response->assertSee('Bendahara');
    }

    public function test_admin_can_access_bendahara_edit_page()
    {
        $admin = $this->getAdminUser();
        $bendahara = $this->getBendaharaUser();

        $response = $this->actingAs($admin)->get("/admin/akun-bendahara/{$bendahara->id}/edit");
        $response->assertStatus(200);
        $response->assertSee($bendahara->name);
    }

    public function test_bendahara_resource_renders_list_with_livewire()
    {
        $admin = $this->getAdminUser();
        $bendahara = $this->getBendaharaUser();

        Livewire::actingAs($admin)
            ->test(ListBendaharas::class)
            ->assertCanSeeTableRecords([$bendahara])
            ->assertSuccessful();
    }

    public function test_admin_can_create_new_bendahara_via_livewire()
    {
        $admin = $this->getAdminUser();
        $newUsername = 'bendahara_keuangan_fkip';
        $newEmail = 'bendahara_keuangan@unib.ac.id';

        // Clean up if already exists
        User::where('username', $newUsername)->orWhere('email', $newEmail)->delete();

        Livewire::actingAs($admin)
            ->test(CreateBendahara::class)
            ->fillForm([
                'name' => 'Bendahara Keuangan Baru',
                'username' => $newUsername,
                'email' => $newEmail,
                'nip' => '198501012010011005',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $created = User::where('username', $newUsername)->first();
        $this->assertNotNull($created);
        $this->assertEquals(User::ROLE_BENDAHARA, $created->role_id);
        $this->assertEquals('Bendahara Keuangan Baru', $created->name);
        $this->assertEquals($newEmail, $created->email);
        $this->assertEquals('198501012010011005', $created->nip);

        // Clean up
        $created->delete();
    }

    public function test_admin_can_update_bendahara_via_livewire()
    {
        $admin = $this->getAdminUser();
        $testUsername = 'bendahara_edit_test';
        $testEmail = 'bendahara_edit@unib.ac.id';

        // Clean up & create a temporary bendahara
        User::where('username', $testUsername)->orWhere('email', $testEmail)->delete();
        $tempBendahara = User::create([
            'name' => 'Bendahara Edit Initial',
            'username' => $testUsername,
            'email' => $testEmail,
            'password' => bcrypt('password123'),
            'role_id' => User::ROLE_BENDAHARA,
            'nip' => '1111222233334444',
        ]);

        Livewire::actingAs($admin)
            ->test(EditBendahara::class, [
                'record' => $tempBendahara->id,
            ])
            ->fillForm([
                'name' => 'Bendahara Edit Updated',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $tempBendahara->refresh();
        $this->assertEquals('Bendahara Edit Updated', $tempBendahara->name);

        // Delete test
        Livewire::actingAs($admin)
            ->test(EditBendahara::class, [
                'record' => $tempBendahara->id,
            ])
            ->callAction('delete');

        $this->assertNull(User::find($tempBendahara->id));
    }
}

