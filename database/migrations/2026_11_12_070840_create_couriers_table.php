<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 150)->index();
            $table->string('email')->unique();
            $table->string('phone', 20)->unique();
            $table->char('national_id', 16)->unique();
            $table->text('address');
            $table->string('city', 100);
            $table->string('vehicle_type', 20);
            $table->string('plate_number', 15)->unique()->nullable();
            $table->unsignedTinyInteger('level')->default(1)->index();
            $table->string('status', 20)->default('active');
            $table->date('registered_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};
