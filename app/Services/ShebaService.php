<?php

namespace App\Services;

use App\Models\ShebaRequest;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Repositories\ShebaRequestRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\TransactionRepositoryInterface;
use App\Exceptions\InsufficientBalanceException;
use App\DTOs\TransactionData;
use App\DTOs\ShebaRequestData;

class ShebaService
{
    protected $shebaRequestRepository;
    protected $userRepository;
    protected $transactionRepository;
    public function __construct(
        ShebaRequestRepositoryInterface $shebaRequestRepository,
        UserRepositoryInterface $userRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->shebaRequestRepository = $shebaRequestRepository;
        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function createShebaRequest(array $data): ShebaRequest
    {
        $user = $this->userRepository->findById($data['user_id']);
        if (!$user) {
            throw new \Exception('User not found', 404);
        }
        if ($user->balance < $data['price']) {
            throw new InsufficientBalanceException();
        }

        return DB::transaction(function () use ($data, $user) {
            $shebaRequest = $this->shebaRequestRepository->create(new ShebaRequestData(
                $user->id,
                $data['price'],
                ShebaRequest::STATUS_PENDING,
                $data['fromShebaNumber'],
                $data['toShebaNumber'],
                $data['note'] ?? null
            ));

            $this->userRepository->updateBalance($user->id, $data['price']);

            $this->transactionRepository->create(new TransactionData(
                $user->id,
                $data['price'],
                Transaction::TYPE_DEBIT,
                Transaction::NOTE_DEBIT_SHEBA,
                $shebaRequest->id
            ));

            return $shebaRequest;
        });
    }
} 