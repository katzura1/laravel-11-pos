<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StockIn\StoreRequest;
use App\Models\ProductStock;
use App\Models\StockInDetail;
use App\Models\Product;

class StockInController extends Controller
{
    public function store(StoreRequest $request): JsonResponse
    {
        return $this->handleTransaction(function () use ($request) {
            $validated = $request->validated();
            $stockIn = $this->createStockIn($validated);
            $this->processStockInDetails($validated, $stockIn);

            return response()->json([
                'status' => true,
                'message' => 'Stock in successfully saved',
            ], 201);
        }, "Stock in successfully saved");
    }

    public function update(StoreRequest $request, $id): JsonResponse
    {
        return $this->handleTransaction(function () use ($request, $id) {
            $validated = $request->validated();
            $stockIn = StockIn::findOrFail($id);
            $this->updateStockIn($validated, $stockIn);
            $this->processStockInDetails($validated, $stockIn);

            return response()->json([
                'status' => true,
                'message' => 'Stock in successfully updated',
            ], 200);
        }, "Stock in successfully updated");
    }

    private function createStockIn(array $validated): StockIn
    {
        $dataHeader = [
            'stock_in_no' => $validated['stock_in_no'],
            'stock_in_date' => $validated['stock_in_date'],
            'due_date' => $validated['due_date'],
            'supplier_id' => $validated['supplier_id'],
            'outlet_id' => session()->get('outlet_id'),
            'user_id' => auth()->user()->id,
        ];
        return StockIn::create($dataHeader);
    }

    private function updateStockIn(array $validated, StockIn $stockIn): void
    {
        $dataHeader = [
            'stock_in_no' => $validated['stock_in_no'],
            'stock_in_date' => $validated['stock_in_date'],
            'due_date' => $validated['due_date'],
            'supplier_id' => $validated['supplier_id'],
            'outlet_id' => session()->get('outlet_id'),
            'user_id' => auth()->user()->id,
        ];
        $stockIn->update($dataHeader);

        $existingDetails = StockInDetail::where('stock_in_id', $stockIn->id)->get();
        foreach ($existingDetails as $detail) {
            $this->adjustProductStock($detail, -$detail->qty, "Stock out from stock in update no: $stockIn->stock_in_no, user : " . auth()->user()->name);
        }
        StockInDetail::where('stock_in_id', $stockIn->id)->delete();
    }

    private function processStockInDetails(array $validated, StockIn $stockIn): void
    {
        $productIds = $validated['product_id'];
        $qtys = $validated['qty'];
        $prices = $validated['price'];

        $dataDetails = [];
        foreach ($qtys as $key => $qty) {
            $dataDetails[] = [
                'stock_in_id' => $stockIn->id,
                'product_id' => $productIds[$key],
                'qty' => $qty,
                'price' => $prices[$key],
            ];
        }

        StockInDetail::insert($dataDetails);

        foreach ($dataDetails as $detail) {
            $this->adjustProductStock($detail, $detail['qty'], "Stock in from stock in no: $stockIn->stock_in_no, user : " . auth()->user()->name);
            $this->updateProductPrice($detail['product_id'], $detail['price']);
        }
    }

    private function adjustProductStock(array $detail, int $qty, string $description): void
    {
        $productStock = ProductStock::firstOrNew([
            'product_id' => $detail['product_id'],
            'outlet_id' => session()->get('outlet_id'),
        ]);

        $productStock->stock = ($productStock->stock ?? 0) + $qty;
        $productStock->save();

        $productStock->histories()->create([
            'qty' => $qty,
            'outlet_id' => session()->get('outlet_id'),
            'description' => $description,
        ]);
    }

    private function updateProductPrice(int $productId, float $price): void
    {
        $product = Product::find($productId);
        $product->buying_price = $price;
        $product->save();
    }

    public function delete($id): JsonResponse
    {
        return $this->handleTransaction(function () use ($id) {
            $stockIn = StockIn::findOrFail($id);

            $existingDetails = StockInDetail::where('stock_in_id', $stockIn->id)->get();
            foreach ($existingDetails as $detail) {
                $this->adjustProductStock($detail->toArray(), -$detail->qty, "Stock out from stock in delete no: $stockIn->stock_in_no, user : " . auth()->user()->name);
            }
            StockInDetail::where('stock_in_id', $stockIn->id)->delete();
            $stockIn->delete();

            return response()->json([
                'status' => true,
                'message' => 'Stock in successfully deleted',
            ], 200);
        }, "Stock in successfully deleted");
    }
}
