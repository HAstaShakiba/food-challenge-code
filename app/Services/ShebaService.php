<?php

namespace App\Services;

use App\Models\ShebaRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Repositories\ShebaRequestRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\TransactionRepositoryInterface;
use App\Exceptions\InsufficientBalanceException;
use App\DTOs\TransactionData;
use App\DTOs\ShebaRequestData;
use Illuminate\Support\Collection;

class ShebaService
{
    protected ShebaRequestRepositoryInterface $shebaRequestRepository;
    protected UserRepositoryInterface        $userRepository;
    protected TransactionRepositoryInterface $transactionRepository;
    public function __construct(
        ShebaRequestRepositoryInterface $shebaRequestRepository,
        UserRepositoryInterface $userRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->shebaRequestRepository = $shebaRequestRepository;
        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @throws \Exception
     */
    public function createShebaRequest(array $data): ShebaRequest
    {
        $user = $this->userRepository->findById($data['user_id']);
        if (!$user) {
            throw new \Exception('User not found', 404);
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

            if (!$this->userRepository->decrementBalanceWithLock($user->id, $data['price'])) {
                throw new InsufficientBalanceException();
            }

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

    public function getFilteredRequests(array $filter): Collection
    {
        return $this->shebaRequestRepository->getFiltered($filter);
    }

    /**
     * @throws \Exception
     */
    public function confirmOrCancelRequest(int|string $id, array $data): ?ShebaRequest
    {
        $request = $this->shebaRequestRepository->findById($id);
        if (!$request) {
            throw new \Exception('Request not found', 404);
        }
        if ($request->status !== ShebaRequest::STATUS_PENDING) {
            throw new \Exception('Request is not pending', 400);
        }
        if (($data['status'] ?? null) === ShebaRequest::STATUS_CANCELED) {
            if (!$this->userRepository->increaseBalanceWithLock($request->user_id, $request->price)) {
                throw new \Exception('Failed to refund user', 500);
            }
            $this->transactionRepository->create(new TransactionData(
                $request->user_id,
                $request->price,
                Transaction::TYPE_CREDIT,
                Transaction::NOTE_CREDIT_SHEBA,
                $request->id
            ));
        }
        return $this->shebaRequestRepository->updateStatus($id, $data);
    }
}
