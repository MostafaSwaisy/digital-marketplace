<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('product_id'); // Reference to Product Service
            $table->unsignedBigInteger('seller_id'); // Reference to User Service
            $table->string('product_name'); // Store product name at time of purchase
            $table->decimal('price', 10, 2); // Store price at time of purchase
            $table->decimal('seller_amount', 10, 2); // Amount seller receives (after platform fee)
            $table->boolean('is_downloaded')->default(false);
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();

            // Add indexes
            $table->index('order_id');
            $table->index('product_id');
            $table->index('seller_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
