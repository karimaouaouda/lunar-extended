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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_line_id')->constrained('lunar_order_lines')->cascadeOnDelete();
            $table->integer('logo_heights', unsigned: true);
            $table->string('logo_color');
            $table->string('logo');
            $table->string('printing_type');
            $table->tinyText('notes')->nullable();
            $table->enum('status', \App\Enums\AttachementStatus::values());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
