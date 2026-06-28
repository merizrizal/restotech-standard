<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restotech_user_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('display_name')->nullable();
            $table->string('employee_code', 50)->nullable()->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('restotech_roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('restotech_permissions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('group_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('restotech_role_permissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('role_id')->constrained('restotech_roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('restotech_permissions')->cascadeOnDelete();
            $table->unique(['role_id', 'permission_id']);
        });

        Schema::create('restotech_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('setting_key')->unique();
            $table->text('setting_value')->nullable();
            $table->string('value_type')->default('string');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('restotech_number_sequences', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('prefix')->nullable();
            $table->unsignedSmallInteger('padding')->default(4);
            $table->unsignedBigInteger('next_number')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restotech_number_sequences');
        Schema::dropIfExists('restotech_settings');
        Schema::dropIfExists('restotech_role_permissions');
        Schema::dropIfExists('restotech_permissions');
        Schema::dropIfExists('restotech_roles');
        Schema::dropIfExists('restotech_user_profiles');
    }
};
