<?php

use AnisAronno\MediaGallery\Models\Media;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mediables', function (Blueprint $table)
        {
            $table->foreignIdFor(Media::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('mediable_id');
            $table->string('mediable_type');
            $table->tinyInteger('is_featured')->default(0);
            $table->timestamps();

            $table->index(['mediable_id', 'mediable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mediables');
    }
};
