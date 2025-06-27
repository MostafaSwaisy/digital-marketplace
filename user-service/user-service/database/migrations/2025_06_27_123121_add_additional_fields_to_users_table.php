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
        Schema::table('users', function (Blueprint $table) {
            // Only add columns that don't exist
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->after('name');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['creator', 'buyer', 'admin'])->default('buyer')->after('email');
            }

            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('role');
            }

            if (!Schema::hasColumn('users', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('bio');
            }

            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('profile_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'bio')) {
                $table->dropColumn('bio');
            }
            if (Schema::hasColumn('users', 'profile_image')) {
                $table->dropColumn('profile_image');
            }
            if (Schema::hasColumn('users', 'is_verified')) {
                $table->dropColumn('is_verified');
            }
        });
    }
};
