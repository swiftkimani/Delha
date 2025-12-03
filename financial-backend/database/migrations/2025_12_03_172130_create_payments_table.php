<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('member_id');
            $table->foreignId('sheet_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('savings', 12, 2)->default(0);
            $table->decimal('project', 12, 2)->default(0);
            $table->decimal('welfare', 12, 2)->default(0);
            $table->decimal('fine', 12, 2)->default(0);
            $table->decimal('others', 12, 2)->default(0);
            
            // FIXED: Proper PostgreSQL syntax for generated column
            $table->decimal('total', 12, 2)
                  ->storedAs('savings + project + welfare + fine + others');
            
            $table->timestamps();
            
            $table->foreign('member_id')->references('member_id')->on('members')->onDelete('cascade');
            $table->index(['member_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};