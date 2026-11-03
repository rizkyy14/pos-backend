<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ProductStoreRequest;
use App\Http\Requests\Api\ProductUpdateRequest;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ProductController extends ApiController
{

    public function search(Request $request)
    {
        $keyword = $request->query('q', '');

       $products = Product::query()
            ->where('name', 'ILIKE', "%{$keyword}%")     // ILIKE = LIKE tanpa peduli huruf besar/kecil
            ->orWhere('sku', 'ILIKE', "%{$keyword}%")
            ->orWhere('category', 'ILIKE', "%{$keyword}%")
            ->limit(20)
            ->get();

        return $this->success($products, 'Hasil pencarian produk.');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $products = Product::latest()->paginate(10);
        return $this->success(
            $products->items(),
            'Daftar produk berhasil diambil',
            200,
            [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $product = Product::create($request->validated());
        return $this->success($product, 'Produk berhasil dibuat', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
        return $this->success($product, 'Produk berhasil diambil');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $product->update($request->validated());
        return $this->success($product, 'Produk berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
        } catch (QueryException $e) {
            if (in_array($e->getCode(), ['23000'])) {
                return $this->error(
                    'Produk tidak dapat dihapus karena sudah tercatat dalam transaksi.' . 'Sebaiknya nonaktifkan produk (set is_active = false).',
                    400
                );
            }

            throw $e; // Re-throw the exception if it's not a foreign key constraint violation
        }

        return $this->success(null, 'Produk berhasil dihapus');
    }
}
