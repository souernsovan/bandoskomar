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
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type', 20)->index();          // contact, volunteer, job, donate
            $table->string('status', 20)->default('new')->index();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 50)->nullable();
            $table->string('subject')->nullable();       // contact subject, job position, volunteer interest
            $table->decimal('amount', 12, 2)->nullable(); // donations
            $table->text('message')->nullable();
            $table->json('details')->nullable();         // any other submitted fields
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->text('admin_note')->nullable();
            $table->string('locale', 5)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('email_sent')->default(false);
            $table->timestamps();

            $table->index(['type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
