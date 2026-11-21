<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_player_cannot_open_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_admin_can_open_admin_panel(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin')->assertOk();
    }

    public function test_admin_can_promote_player(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $player = User::factory()->create();

        $this->actingAs($admin)
            ->patch("/admin/users/{$player->id}/role")
            ->assertRedirect();

        $this->assertTrue($player->fresh()->isAdmin());
    }
}
