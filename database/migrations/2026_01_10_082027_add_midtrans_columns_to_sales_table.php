<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMidtransColumnsToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            // Tambah kolom untuk cart reference
            $table->foreignId('cart_id')->nullable()->after('device_id')->constrained('carts');
            
            // Tambah status enum (pending, completed, failed)
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])
                  ->default('pending')
                  ->after('payment_method');
            
            // Tambah kolom untuk timestamp pembayaran
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->timestamp('failed_at')->nullable()->after('paid_at');
            
            // Tambah kolom untuk data midtrans
            $table->text('midtrans_data')->nullable()->after('failed_at');
            $table->string('midtrans_transaction_id')->nullable()->after('midtrans_data');
            $table->string('midtrans_payment_type')->nullable()->after('midtrans_transaction_id');
            
            // Index untuk pencarian
            $table->index(['invoice_number', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['cart_id']);
            $table->dropColumn([
                'cart_id',
                'status', 
                'paid_at',
                'failed_at',
                'midtrans_data',
                'midtrans_transaction_id',
                'midtrans_payment_type'
            ]);
        });
    }
}
