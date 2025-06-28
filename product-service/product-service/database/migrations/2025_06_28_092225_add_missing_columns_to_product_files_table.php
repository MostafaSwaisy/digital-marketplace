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
        Schema::table('product_files', function (Blueprint $table) {
            // Add missing columns that the code expects
            $table->string('mime_type')->nullable()->after('file_type');
            $table->integer('download_count')->default(0)->after('is_preview');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_files', function (Blueprint $table) {
            $table->dropColumn(['mime_type', 'download_count']);
        });
    }
};
