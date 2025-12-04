<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create chamas table
        Schema::create('chamas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Add chama_id to existing tables
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('chama_id')->after('member_id')->constrained()->onDelete('cascade');
        });

        Schema::table('sheets', function (Blueprint $table) {
            $table->foreignId('chama_id')->after('id')->constrained()->onDelete('cascade');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('chama_id')->after('sheet_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('payments', fn($table) => $table->dropForeign(['chama_id']));
        Schema::table('sheets',    fn($table) => $table->dropForeign(['chama_id']));
        Schema::table('members',   fn($table) => $table->dropForeign(['chama_id']));

        Schema::table('payments', fn($table) => $table->dropColumn('chama_id'));
        Schema::table('sheets',    fn($table) => $table->dropColumn('chama_id'));
        Schema::table('members',   fn($table) => $table->dropColumn('chama_id'));

        Schema::dropIfExists('chamas');
    }
};