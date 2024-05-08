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
        Schema::create('encryptions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->comment('唯一ID标识');
            $table->string('digest_alg')->comment('加密方式');
            $table->integer('private_key_bits')->comment('加密长度');
            $table->string('public_key', 4096)->comment('服务器公钥');
            $table->string('private_key', 4096)->comment('服务器私钥');
            $table->string('client_public_key', 4096)->nullable()->comment('客户端公钥，加密用');
            $table->integer('client_length')->nullable()->comment('客户端加密长度');
            $table->timestamps();
        });

        Schema::create('error_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->comment('错误码')->index();
            $table->string('desc')->comment('描述');
            $table->comment('错误码表');
        });

        Schema::create('basedatas', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('类型');
            $table->string('name')->comment('名称');
            $table->string('name_en')->nullable()->comment('英文名称');
            $table->integer('type_id')->comment('类型对应的存储ID');
            $table->tinyInteger('is_default')->comment('是否默认');
            $table->tinyInteger('active')->default(1)->comment('是否有效');
            $table->comment('枚举类,存静态类型等');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encryptions');
        Schema::dropIfExists('error_codes');
        Schema::dropIfExists('basedatas');
    }
};
