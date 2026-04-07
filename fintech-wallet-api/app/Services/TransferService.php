<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class TransferService
{
    public function execute(User $sender, string $receiverEmail, float $amount)
    {
        if ($sender->email === $receiverEmail) {
            throw new Exception("Você não pode transferir para si mesmo.", 400);
        }

        $receiver = User::where('email', $receiverEmail)->first();

        return DB::transaction(function () use ($sender, $receiver, $amount) {
            $senderQuery = $sender->wallet();
            $receiverQuery = $receiver->wallet();

            // Evita erro no banco de testes (SQLite), mas mantém a segurança contra 
            // race conditions no banco de produção (MySQL/PostgreSQL).
            if (DB::getDriverName() !== 'sqlite') {
                $senderQuery->lockForUpdate();
                $receiverQuery->lockForUpdate();
            }

            $senderWallet = $senderQuery->first();
            $receiverWallet = $receiverQuery->first();

            if ($senderWallet->balance < $amount) {
                throw new Exception("Saldo insuficiente.", 400);
            }

            $senderWallet->decrement('balance', $amount);
            $receiverWallet->increment('balance', $amount);

            $sender->transactions()->create([
                'related_user_id' => $receiver->id,
                'type' => 'debit',
                'amount' => $amount,
            ]);

            $receiver->transactions()->create([
                'related_user_id' => $sender->id,
                'type' => 'credit',
                'amount' => $amount,
            ]);

            return true;
        });
    }
}