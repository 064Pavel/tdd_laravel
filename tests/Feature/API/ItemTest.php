<?php

namespace Tests\Feature\API;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withHeaders([
            "accept" => "application/json",
        ]);
    }

    /** @test */
    public function an_item_can_be_stored(): void
    {
        $this->withoutExceptionHandling();

        $item = Item::factory()->make()->toArray();

        $response = $this->post('/api/items', $item);

        $response->assertJson([
            "name" => $item["name"],
            "description" => $item["description"],
            "price" => $item["price"],
        ]);

        $this->assertDatabaseCount('items', 1);

        $itemData = Item::query()->first();

        $this->assertEquals($item["name"], $itemData->name);
        $this->assertEquals($item["description"], $itemData->description);
        $this->assertEquals($item["price"], $itemData->price);
    }

    /** @test */

    public function attr_name_is_required_for_store_item(): void
    {
        $item = Item::factory()->make(["name" => null])->toArray();
        
        $response = $this->post("/api/items", $item);

        // dd($response->getContent());
        $response->assertStatus(422);
        $response->assertInvalid("name");
    }

    /** @test */

    public function attr_price_is_required_for_store_item(): void
    {
        $item = Item::factory()->make(["price" => null])->toArray();

        $response = $this->post("/api/items", $item);

        // dd($response->getContent());
        $response->assertStatus(422);
        $response->assertInvalid("price");
    }

    /** @test */

    public function an_item_can_be_updated(): void
    {
        $this->withExceptionHandling();

        $item = Item::factory()->create();

        $itemUpd = [
            "name" => "name updated",
            "description" => "description updated",
            "price" => 999.99,
        ];

        $response = $this->patch("/api/items/" . $item->id, $itemUpd);

        $response->assertJson([
            "name" => $itemUpd["name"],
            "description" => $itemUpd["description"],
            "price" => $itemUpd["price"],
        ]);

        $itemData = Item::query()->first();

        $this->assertEquals($item->id, $itemData->id);
        $this->assertEquals($itemUpd["name"], $itemData->name);
        $this->assertEquals($itemUpd["description"], $itemData->description);
        $this->assertEquals($itemUpd["price"], $itemData->price);
    }

    /** @test */

    public function items_can_be_retrieved(): void
    {
        $this->withExceptionHandling();

        $items = Item::factory(10)->create();

        $response = $this->get("/api/items");

        $response->assertOk();

        $json = $items->map(function($item){
            return [
                "name" => $item->name,
                "description" => $item->description,
                "price" => $item->price,
            ];
        })->toArray();

        $response->assertJson($json);

    }

    /** @test */

    public function item_can_be_retrived(): void
    {
        $this->withExceptionHandling();

        $item = Item::factory()->create();

        $response = $this->get("/api/items/" . $item->id);

        $response->assertOk();

        $response->assertJson([
            "name" => $item->name,
            "description" => $item->description,
            "price" => $item->price,
        ]);
    }

    /** @test */

    public function an_item_can_be_deleted(): void
    {
        $this->withExceptionHandling();

        $item = Item::factory()->create();

        $response = $this->delete("/api/items/" . $item->id);

        $response->assertOk();

        $this->assertDatabaseCount("items", 0);
    }

    /** @test */

    public function an_item_can_be_added_in_bucket_only_for_auth_user(): void
    {
        $this->withoutExceptionHandling();

        $item = Item::factory()->create()->toArray();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post("/api/items/add-to-cart", $item);

        $response->assertOk();

        $response->assertJson([
            "message" => "The item added to cart"
        ]);
    }

    /** @test */

    public function an_item_cannot_be_added_in_bucket_for_guest(): void
    {
        $item = Item::factory()->create()->toArray();

        $response = $this->post("/api/items/add-to-cart", $item);

        $response->assertStatus(401);
    }
}
