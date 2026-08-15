<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->nullable()->constrained('analytics_visitors')->nullOnDelete();
            $table->string('visitor_hash', 64);
            $table->string('route_name')->nullable();
            $table->string('path');
            $table->string('method', 10);
            $table->string('referrer_host')->nullable();
            $table->text('referrer_url')->nullable();
            $table->unsignedSmallInteger('status_code');
            $table->unsignedInteger('duration_ms')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('viewed_at');
            $table->timestamp('created_at')->nullable();

            $table->index('viewed_at');
            $table->index('visitor_hash');
            $table->index('path');
            $table->index('route_name');
            $table->index('status_code');
            $table->index(['viewed_at', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_page_views');
    }
};
