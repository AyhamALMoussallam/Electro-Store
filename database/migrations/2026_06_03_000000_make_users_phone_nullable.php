<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'phone')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE users MODIFY phone INT NULL');
        } elseif ($driver === 'sqlite') {
            // SQLite cannot alter column easily; skip if already nullable in fresh installs
        } else {
            Schema::table('users', function ($table) {
                $table->integer('phone')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'phone')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE users MODIFY phone INT NOT NULL');
        }
    }
};
