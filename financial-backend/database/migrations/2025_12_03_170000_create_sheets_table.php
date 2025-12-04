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
    Schema::create('sheets', function (Blueprint $table) {
        $table->id();
        $table->string('name');                    // ← THIS WAS MISSING
        $table->date('upload_date');               // ← THIS WAS MISSING
        $table->boolean('is_excluded')->default(false); // ← THIS WAS MISSING
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sheets');
    }
};
