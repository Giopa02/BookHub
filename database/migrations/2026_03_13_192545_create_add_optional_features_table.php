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
        // 2FA : ajouter les champs sur users
        Schema::table('users', function (Blueprint $table) {
            $table->string('two_factor_code', 6)->nullable()->after('password');
            $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code');
            $table->timestamp('password_changed_at')->nullable()->after('two_factor_expires_at');
        });

        // État des exemplaires (excellent, bon, moyen)
        Schema::table('copies', function (Blueprint $table) {
            $table->enum('etat', ['excellent', 'bon', 'moyen'])->default('bon')->after('status_id');
        });

        // Historique des mots de passe (5 derniers)
        Schema::create('password_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_code', 'two_factor_expires_at', 'password_changed_at']);
        });

        Schema::table('copies', function (Blueprint $table) {
            $table->dropColumn('etat');
        });

        Schema::dropIfExists('password_histories');
    }
};