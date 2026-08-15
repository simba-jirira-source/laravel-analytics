<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_ip_bans', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->text('reason')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('banned_at');
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('banned_by')->nullable();
            $table->timestamps();

            $table->index('ip_address');
            $table->index('is_active');
            $table->index('expires_at');
            $table->index(['ip_address', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_ip_bans');
    }
};
