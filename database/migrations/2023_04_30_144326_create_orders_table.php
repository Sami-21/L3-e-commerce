<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('client_id')->nullable(true);
            $table->foreign('client_id')->default(null)->nullable(true)->references('id')->on('clients');
            $table->string('firstname')->nullable(true);
            $table->string('lastname')->nullable(true);
            $table->string('wilaya');
            $table->string('city');
            $table->string('address');
            $table->string('phone');
            $table->string('status')->default('pending')->nullable(false);
            $table->text('notes')->nullable();
            $table->double('total')->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
