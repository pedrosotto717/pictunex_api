
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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->comment('name Related to Image');
            $table->string('keywords', 80)->nullable();
            $table->string('categories', 80);
            $table->string('nickname', 30);
            $table->text('src');
            $table->timestamp('CREATION_DATE')->useCurrent();
            $table->timestamps();
            
            $table->index('nickname');
            $table->fullText(['name', 'keywords', 'categories'], 'search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
