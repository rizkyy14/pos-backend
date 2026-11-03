<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TransactionStoreRequest;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends ApiController
{
    public function index(Request $request)
    {
        $query = Transaction::with('user:id,name,role')->latest();

        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->query('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->query('end_date'));
        }

        $transactions = $query->paginate(10);

        return $this->success(
            $transactions->items(),
            'Daftar transaksi berhasil diambil.',
            200,
            [
                'current_page' => $transactions->currentPage(),
                'per_page'     => $transactions->perPage(),
                'total'        => $transactions->total(),
                'last_page'    => $transactions->lastPage(),
            ]
        );
    }

    public function store(TransactionStoreRequest $request)
    {
        $validated = $request->validated();

        try {
            $transaction = DB::transaction(function () use ($validated, $request) {
                $total = 0;
                $detailRows = [];

                foreach ($validated['items'] as $item) {
                    $product = Product::where('id', $item['product_id'])
                        ->lockForUpdate()
                        ->first();

                    if ($product->stock < $item['quantity']) {
                        throw new \RuntimeException(
                            "Stok produk '{$product->name}' tidak mencukupi (sisa {$product->stock})."
                        );
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $total += $subtotal;

                    $product->decrement('stock', $item['quantity']);

                    $detailRows[] = [
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'price'        => $product->price,
                        'quantity'     => $item['quantity'],
                        'subtotal'     => $subtotal,
                    ];
                }

                $paid = $validated['paid'];
                if ($paid < $total) {
                    throw new \RuntimeException(
                        "Jumlah bayar (Rp" . number_format($paid, 0, ',', '.') .
                        ") kurang dari total (Rp" . number_format($total, 0, ',', '.') . ")."
                    );
                }

                $transaction = Transaction::create([
                    'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'user_id'        => $request->user()->id,
                    'total'          => $total,
                    'paid'           => $paid,
                    'change'         => $paid - $total,          
                    'status'         => 'completed',
                    'metadata'       => [                        
                        'payment_method' => $validated['payment_method'] ?? 'cash',
                        'customer_name'  => $validated['customer_name'] ?? null,
                        'note'           => $validated['note'] ?? null,
                    ],
                ]);

                $transaction->details()->createMany($detailRows);

                return $transaction;
            });
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }

        $transaction->load('details', 'user:id,name,role');
        return $this->success($transaction, 'Transaksi berhasil disimpan.', 201);
        
    }

    public function show(Request $request, Transaction $transaction)
    {
        if ($request->user()->role !== 'admin' && $transaction->user_id !== $request->user()->id) {
            return $this->error('Anda tidak memiliki izin melihat transaksi ini.', 403);
        }

        $transaction->load('details', 'user:id,name,role');
        return $this->success($transaction, 'Detail transaksi berhasil diambil.');
    }

    public function summary(Request $request)
    {
        $base = Transaction::where('status', 'completed');

        if ($request->filled('start_date')) {
            $base->whereDate('created_at', '>=', $request->query('start_date'));
        }
        if ($request->filled('end_date')) {
            $base->whereDate('created_at', '<=', $request->query('end_date'));
        }

        $totalTransactions = (clone $base)->count();
        $totalRevenue      = (float) (clone $base)->sum('total');

        $totalItemsSold = (int) TransactionDetail::whereIn(
            'transaction_id', (clone $base)->pluck('id')
        )->sum('quantity');

        return $this->success([
           'total_transactions' => $totalTransactions,
            'total_revenue'      => $totalRevenue,
            'total_items_sold'   => $totalItemsSold,
        ], 'Ringkasan transaksi berhasil diambil.');
    }
}
