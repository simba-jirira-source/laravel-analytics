<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_errors', function (Blueprint $table) {
            $table->id();
            $table->string('fingerprint', 64);
            $table->string('exception_class');
            $table->text('message');
            $table->string('route_name')->nullable();
            $table->string('path')->nullable();
            $table->string('method', 10)->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->string('file')->nullable();
            $table->unsignedInteger('line')->nullable();
            $table->timestamp('first_occurred_at');
            $table->timestamp('last_occurred_at');
            $table->unsignedInteger('occurrence_count')->default(1);
            $table->timestamps();

            $table->index('fingerprint');
            $table->index('last_occurred_at');
            $table->index('exception_class');
            $table->index(['fingerprint', 'last_occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_errors');
    }
};
