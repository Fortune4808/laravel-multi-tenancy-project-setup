<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //Run the migrations.
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->string('branch_id');
            $table->string('branch_name')->unique();
            $table->string('database_name');
            $table->unsignedBigInteger('status_id')->default(1);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

            $table->foreign('status_id')->references('status_id')->on('statuses')->onDelete('restrict')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    //Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
