<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSession extends Migration
{
    private const TABLE_USER_SESSION = 'user_session';
    private const TABLE_USER         = 'user';

    private const FK_USER_SESSION_USER = 'fk_user_session_user';
    private const USER_SESSION_TOKEN_KEY = 'user_session_token_key';


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_USER_SESSION, static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('token');
            $table->jsonb('body');
            $table->timestamps();
            $table->timestamp('expired_at')->nullable();

            $table->foreign('user_id', self::FK_USER_SESSION_USER)
                ->references('id')->on(self::TABLE_USER);
            $table->unique('token', self::USER_SESSION_TOKEN_KEY);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLE_USER_SESSION);
    }
}
