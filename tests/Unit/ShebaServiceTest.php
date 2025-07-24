<?php

namespace Tests\Unit;

use App\Services\ShebaService;
use App\Repositories\ShebaRequestRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\TransactionRepositoryInterface;
use App\DTOs\ShebaRequestData;
use App\DTOs\ShebaRequestFilterData;
use App\DTOs\ShebaRequestStatusData;
use App\DTOs\TransactionData;
use App\Models\ShebaRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Exceptions\InsufficientBalanceException;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShebaServiceTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_SHEBA_1 = 'IR123456789012345678901234';
    private const VALID_SHEBA_2 = 'IR987654321098765432109876';
    private const INVALID_SHEBA = 'IR000000000000000000000000';

    private $shebaRequestRepository;
    private $userRepository;
    private $transactionRepository;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shebaRequestRepository = $this->createMock(ShebaRequestRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $this->service = new ShebaService(
            $this->shebaRequestRepository,
            $this->userRepository,
            $this->transactionRepository
        );
    }

    public function test_create_sheba_request_success()
    {
        $user = new User();
        $user->id = 1;
        $user->balance = 1000000;
        $data = [
            'user_id' => 1,
            'price' => 500000,
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
            'note' => 'توضیح',
        ];
        $shebaRequest = new ShebaRequest($data + ['status' => \App\Models\ShebaRequest::STATUS_PENDING]);

        $this->userRepository->method('findById')->willReturn($user);
        $this->userRepository->method('decrementBalanceWithLock')->willReturn(true);
        $this->shebaRequestRepository->method('create')->willReturn($shebaRequest);
        $this->transactionRepository->method('create')->willReturn(new Transaction());

        $result = $this->service->createShebaRequest($data);
        $this->assertInstanceOf(ShebaRequest::class, $result);
        $this->assertEquals(\App\Models\ShebaRequest::STATUS_PENDING, $result->status);
    }

    public function test_create_sheba_request_insufficient_balance()
    {
        $user = new User();
        $user->id = 1;
        $user->balance = 1000;
        $data = [
            'user_id' => 1,
            'price' => 5000,
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
            'note' => 'توضیح',
        ];
        $this->userRepository->method('findById')->willReturn($user);
        $this->userRepository->method('decrementBalanceWithLock')->willReturn(false);
        $this->expectException(InsufficientBalanceException::class);
        $this->service->createShebaRequest($data);
    }

    public function test_create_sheba_request_race_condition()
    {
        $user = new User();
        $user->id = 1;
        $user->balance = 1000000;
        $data = [
            'user_id' => 1,
            'price' => 2000000,
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
            'note' => 'توضیح',
        ];
        $this->userRepository->method('findById')->willReturn($user);
        $this->userRepository->method('decrementBalanceWithLock')->willReturn(false);
        $this->expectException(InsufficientBalanceException::class);
        $this->service->createShebaRequest($data);
    }

    public function test_create_sheba_request_user_not_found()
    {
        $data = [
            'user_id' => 1,
            'price' => 5000,
            'fromShebaNumber' => self::VALID_SHEBA_1,
            'toShebaNumber' => self::VALID_SHEBA_2,
            'note' => 'توضیح',
        ];
        $this->userRepository->method('findById')->willReturn(null);
        $this->expectException(\Exception::class);
        $this->service->createShebaRequest($data);
    }

    public function test_get_filtered_requests()
    {
        $filter = new ShebaRequestFilterData(\App\Models\ShebaRequest::STATUS_PENDING, 1);
        $collection = new Collection([
            new ShebaRequest(['id' => 1, 'status' => \App\Models\ShebaRequest::STATUS_PENDING]),
        ]);
        $this->shebaRequestRepository->method('getFiltered')->with($filter)->willReturn($collection);
        $result = $this->service->getFilteredRequests($filter);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
    }

    public function test_confirm_request_success()
    {
        $user = new User();
        $user->id = 1;
        $user->balance = 1000000;
        $request = new ShebaRequest([
            'id' => 10,
            'user_id' => 1,
            'price' => 500000,
            'status' => \App\Models\ShebaRequest::STATUS_PENDING,
        ]);
        $this->shebaRequestRepository->method('findById')->willReturn($request);
        $this->shebaRequestRepository->method('updateStatus')->willReturnCallback(function ($id, $data) use ($request) {
            $request->status = $data->status;
            $request->note = $data->note;
            return $request;
        });
        $this->userRepository->method('findById')->willReturn($user);
        $this->transactionRepository->method('create')->willReturn(new Transaction());
        $data = new \App\DTOs\ShebaRequestStatusData(\App\Models\ShebaRequest::STATUS_CONFIRMED, null);
        $result = $this->service->confirmOrCancelRequest(10, $data);
        $this->assertEquals(\App\Models\ShebaRequest::STATUS_CONFIRMED, $result->status);
    }

    public function test_cancel_request_success()
    {
        $user = new User();
        $user->id = 1;
        $user->balance = 1000000;
        $request = new ShebaRequest([
            'id' => 11,
            'user_id' => 1,
            'price' => 500000,
            'status' => \App\Models\ShebaRequest::STATUS_PENDING,
        ]);
        $this->shebaRequestRepository->method('findById')->willReturn($request);
        $this->shebaRequestRepository->method('updateStatus')->willReturnCallback(function ($id, $data) use ($request) {
            $request->status = $data->status;
            $request->note = $data->note;
            return $request;
        });
        $this->userRepository->method('findById')->willReturn($user);
        $this->userRepository->method('decrementBalanceWithLock')->willReturn(true);
        $this->userRepository->method('increaseBalanceWithLock')->willReturn(true);
        $this->transactionRepository->method('create')->willReturn(new Transaction());
        $data = new \App\DTOs\ShebaRequestStatusData(\App\Models\ShebaRequest::STATUS_CANCELED, 'لغو توسط اپراتور');
        $result = $this->service->confirmOrCancelRequest(11, $data);
        $this->assertEquals(\App\Models\ShebaRequest::STATUS_CANCELED, $result->status);
        $this->assertEquals('لغو توسط اپراتور', $result->note);
    }

    public function test_confirm_or_cancel_request_not_found()
    {
        $this->shebaRequestRepository->method('findById')->willReturn(null);
        $data = new \App\DTOs\ShebaRequestStatusData(\App\Models\ShebaRequest::STATUS_CONFIRMED, null);
        $this->expectException(\Exception::class);
        $this->service->confirmOrCancelRequest(999, $data);
    }

    public function test_confirm_or_cancel_request_not_pending()
    {
        $user = new User();
        $user->id = 1;
        $user->balance = 1000000;
        $request = new ShebaRequest([
            'id' => 12,
            'user_id' => 1,
            'price' => 500000,
            'status' => \App\Models\ShebaRequest::STATUS_CONFIRMED,
        ]);
        $this->shebaRequestRepository->method('findById')->willReturn($request);
        $this->userRepository->method('findById')->willReturn($user);
        $data = new \App\DTOs\ShebaRequestStatusData(\App\Models\ShebaRequest::STATUS_CANCELED, null);
        $this->expectException(\Exception::class);
        $this->service->confirmOrCancelRequest(12, $data);
    }
} 