<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
          Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->enum('role', ['creator', 'buyer', 'admin'])->default('buyer')->after('email');
            $table->text('bio')->nullable()->after('role');
            $table->string('profile_image')->nullable()->after('bio');
            $table->boolean('is_verified')->default(false)->after('profile_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'bio', 'profile_image', 'is_verified']);
        });
    }
};
