<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Http\Requests\Item\StoreRequest;
use App\Http\Resources\Item\ItemResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{

    public function index(): array
    {
        $items = Item::all();

        return ItemResource::collection($items)->resolve();
    }

    public function show(Item $item): array
    {
        return ItemResource::make($item)->resolve();
    }

    public function store(StoreRequest $request): array
    {
        $data = $request->validated();

        $item = Item::query()->create($data);
        
        return ItemResource::make($item)->resolve();
    }

    public function update(StoreRequest $request, Item $item): array
    {
        $data = $request->validated();

        $item->update($data);

        return ItemResource::make($item)->resolve();
    }

    public function delete(Item $item): JsonResponse
    {
        if (!$item) {
            return response()->json(["message" => "Item not found"], 404);
        }
    
        $item->delete();
    
        return response()->json(["message" => "Delete successful"]);
    }

    public function addToCart(Item $item): JsonResponse
    {
        //some logic...

        return response()->json(["message" => "The item added to cart"]);
    }
}
