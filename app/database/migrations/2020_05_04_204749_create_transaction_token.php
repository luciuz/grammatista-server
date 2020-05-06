<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionToken extends Migration
{
    private const TABLE = 'transaction_token';

    private const TRANSACTION_TOKEN_KEY = 'transaction_token_key';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, static function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_token');
            $table->jsonb('result')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->unique('transaction_token', self::TRANSACTION_TOKEN_KEY);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLE);
    }
}
