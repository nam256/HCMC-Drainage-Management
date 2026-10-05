<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cong')) {
            Schema::table('cong', function (Blueprint $table) {
                if (!Schema::hasColumn('cong', 'file_path')) {
                    $table->string('file_path')->nullable();
                }
                if (!Schema::hasColumn('cong', 'file_name')) {
                    $table->string('file_name')->nullable();
                }
                if (!Schema::hasColumn('cong', 'file_mime')) {
                    $table->string('file_mime')->nullable();
                }
                if (!Schema::hasColumn('cong', 'file_size')) {
                    $table->unsignedBigInteger('file_size')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cong')) {
            Schema::table('cong', function (Blueprint $table) {
                if (Schema::hasColumn('cong', 'file_path')) {
                    $table->dropColumn('file_path');
                }
                if (Schema::hasColumn('cong', 'file_name')) {
                    $table->dropColumn('file_name');
                }
                if (Schema::hasColumn('cong', 'file_mime')) {
                    $table->dropColumn('file_mime');
                }
                if (Schema::hasColumn('cong', 'file_size')) {
                    $table->dropColumn('file_size');
                }
            });
        }
    }
};



