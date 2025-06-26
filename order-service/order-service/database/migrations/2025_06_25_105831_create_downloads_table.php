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
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('buyer_id'); // Reference to User Service
            $table->unsignedBigInteger('product_id'); // Reference to Product Service
            $table->unsignedBigInteger('file_id'); // Reference to Product Service file
            $table->string('download_token')->unique(); // Secure download token
            $table->integer('download_count')->default(0);
            $table->integer('max_downloads')->default(5); // Limit downloads per purchase
            $table->timestamp('expires_at')->nullable(); // Download link expiration
            $table->timestamps();

            // Add indexes
            $table->index('download_token');
            $table->index('buyer_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('downloads');
    }
};
