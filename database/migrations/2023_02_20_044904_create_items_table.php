<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->nullable();
            $table->integer('order_key');
            $table->tinyInteger('type')->default(1);
            $table->string('name')->nullable();
            $table->string('discount_type')->nullable();
            $table->double('discount')->nullable();
            $table->longText('options')->nullable();
            $table->text('description')->nullable();

            $table->string('en_name')->nullable();
            $table->text('en_description')->nullable();
            $table->string('fr_name')->nullable();
            $table->text('fr_description')->nullable();
            $table->string('el_name')->nullable();
            $table->text('el_description')->nullable();
            $table->string('it_name')->nullable();
            $table->text('it_description')->nullable();
            $table->string('es_name')->nullable();
            $table->text('es_description')->nullable();
            $table->string('de_name')->nullable();
            $table->text('de_description')->nullable();
            $table->string('bg_name')->nullable();
            $table->text('bg_description')->nullable();
            $table->string('tr_name')->nullable();
            $table->text('tr_description')->nullable();
            $table->string('ro_name')->nullable();
            $table->text('ro_description')->nullable();
            $table->string('sr_name')->nullable();
            $table->text('sr_description')->nullable();
            $table->string('zh_name')->nullable();
            $table->text('zh_description')->nullable();
            $table->string('ru_name')->nullable();
            $table->text('ru_description')->nullable();
            $table->string('pl_name')->nullable();
            $table->text('pl_description')->nullable();
            $table->string('ka_name')->nullable();
            $table->text('ka_description')->nullable();

            $table->string('image')->nullable();
            $table->tinyInteger('is_new')->default(0);
            $table->tinyInteger('as_sign')->default(0);
            $table->tinyInteger('day_special')->default(0);
            $table->tinyInteger('published')->default(0);
            $table->tinyInteger('review')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
