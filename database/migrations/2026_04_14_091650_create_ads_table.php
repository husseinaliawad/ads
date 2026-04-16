<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->string('city');
            $table->string('area')->nullable();
            $table->enum('condition', ['new', 'used'])->default('used');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'sold'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->string('contact_phone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->dateTime('featured_until')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['category_id', 'status', 'published_at']);
            $table->index(['city', 'status']);
            $table->index(['is_featured', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
