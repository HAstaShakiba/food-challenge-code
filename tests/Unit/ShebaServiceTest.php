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
            'fromShebaNumber' => 'IR123456789012345678901234',
            'toShebaNumber' => 'IR987654321098765432109876',
            'note' => 'توضیح',
        ];
        $shebaRequest = new ShebaRequest($data + ['status' => ShebaRequest::STATUS_PENDING]);

        $this->userRepository->method('findById')->willReturn($user);
        $this->userRepository->method('decrementBalanceWithLock')->willReturn(true);
        $this->shebaRequestRepository->method('create')->willReturn($shebaRequest);
        $this->transactionRepository->method('create')->willReturn(new Transaction());

        $result = $this->service->createShebaRequest($data);
        $this->assertInstanceOf(ShebaRequest::class, $result);
        $this->assertEquals(ShebaRequest::STATUS_PENDING, $result->status);
    }

    public function test_create_sheba_request_insufficient_balance()
    {
        $user = new User();
        $user->id = 1;
        $user->balance = 1000;
        $data = [
            'user_id' => 1,
            'price' => 5000,
            'fromShebaNumber' => 'IR123456789012345678901234',
            'toShebaNumber' => 'IR987654321098765432109876',
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
            'price' => 2000000, // بیشتر از موجودی
            'fromShebaNumber' => 'IR123456789012345678901234',
            'toShebaNumber' => 'IR987654321098765432109876',
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
            'fromShebaNumber' => 'IR123456789012345678901234',
            'toShebaNumber' => 'IR987654321098765432109876',
            'note' => 'توضیح',
        ];
        $this->userRepository->method('findById')->willReturn(null);
        $this->expectException(\Exception::class);
        $this->service->createShebaRequest($data);
    }

    public function test_get_filtered_requests()
    {
        $filter = new ShebaRequestFilterData(ShebaRequest::STATUS_PENDING, 1);
        $collection = new Collection([
            new ShebaRequest(['id' => 1, 'status' => ShebaRequest::STATUS_PENDING]),
        ]);
        $this->shebaRequestRepository->method('getFiltered')->with($filter)->willReturn($collection);
        $result = $this->service->getFilteredRequests($filter);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
    }
} 