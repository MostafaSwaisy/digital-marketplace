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

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('seller_id'); // Reference to User Service
            $table->string('category')->nullable();
            $table->json('tags')->nullable(); // Store tags as JSON array
            $table->enum('status', ['draft', 'published', 'suspended'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->integer('downloads_count')->default(0);
            $table->decimal('rating', 3, 2)->nullable(); // Average rating
            $table->integer('reviews_count')->default(0);
            $table->timestamps();

            // Add indexes for better performance
            $table->index('seller_id');
            $table->index('status');
            $table->index('category');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
