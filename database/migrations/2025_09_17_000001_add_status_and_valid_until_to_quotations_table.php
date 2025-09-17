<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'status')) {
                $table->string('status')->default('pending')->after('total');
            }
            if (!Schema::hasColumn('quotations', 'valid_until')) {
                $table->date('valid_until')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'valid_until')) {
                $table->dropColumn('valid_until');
            }
            if (Schema::hasColumn('quotations', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
