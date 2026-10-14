<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\events;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_an_event(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        $this->get(route('events.create'))->assertOk();

        $this->post(route('events.store'), [
            'name' => 'Konser Awal',
            'category' => 'Musik',
            'price' => 50000,
            'description' => 'Konser musik live.',
            'image' => UploadedFile::fake()->create('awal.jpg', 10, 'image/jpeg'),
        ])->assertRedirect(route('events.index'));

        $event = events::firstOrFail();
        $originalImage = $event->image;
        Storage::disk('public')->assertExists($originalImage);

        $this->get(route('events.edit', $event))->assertOk();

        $this->put(route('events.update', $event), [
            'name' => 'Festival Baru',
            'category' => 'Festival',
            'price' => 85000,
            'description' => 'Festival seni dan musik.',
            'image' => UploadedFile::fake()->create('baru.jpg', 10, 'image/jpeg'),
        ])->assertRedirect(route('events.index'));

        $event->refresh();
        $this->assertSame('Festival Baru', $event->name);
        $this->assertSame('Festival', $event->category);
        $this->assertSame(85000, $event->price);
        Storage::disk('public')->assertMissing($originalImage);
        Storage::disk('public')->assertExists($event->image);

        $this->delete(route('events.destroy', $event))
            ->assertRedirect(route('events.index'));

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
        Storage::disk('public')->assertMissing($event->image);
    }
}
