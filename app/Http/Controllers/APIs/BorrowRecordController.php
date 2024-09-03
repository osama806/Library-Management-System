<?php

namespace App\Http\Controllers\APIs;

use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\BorrowRecords\BorrowRecordFormRequest;
use App\Http\Requests\BorrowRecords\UpdateBorrowRecordFormRequest;
use App\Services\BorrowRecordService;

class BorrowRecordController extends Controller
{
    use ResponseTrait;
    protected $borrowRecordService;

    /**
     * Create a new class instance
     * @param \App\Services\BorrowRecordService $borrowRecordService
     */
    public function __construct(BorrowRecordService $borrowRecordService)
    {
        $this->borrowRecordService = $borrowRecordService;
    }

    /**
     * Display a listing of the borrow records.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = $this->borrowRecordService->index();
        return $response['status']
            ? $this->getResponse("borrow records", $response['records'], 200)
            : $this->getResponse("msg", $response['msg'], 404);
    }


    /**
     * Display the specified borrow record.
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response = $this->borrowRecordService->show($id);
        return $response['status']
            ? $this->getResponse("borrow record", $response['record'], 200)
            : $this->getResponse("msg", $response['msg'], 404);
    }

    /**
     * Store a newly created borrow record in storage.
     * @param \App\Http\Requests\BorrowRecords\BorrowRecordFormRequest $BorrowRecordFormRequest
     * @return \Illuminate\Http\Response
     */
    public function store(BorrowRecordFormRequest $BorrowRecordFormRequest)
    {
        $validatedData = $BorrowRecordFormRequest->validated();
        $response = $this->borrowRecordService->store($validatedData);
        return $response['status']
            ? $this->getResponse("msg", "Created borrow record successfully", 201)
            : $this->getResponse("msg", $response['msg'], $response['code']);
    }

    /**
     * Update the specified borrow record in storage.
     * @param \App\Http\Requests\BorrowRecords\BorrowRecordFormRequest $BorrowRecordFormRequest
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBorrowRecordFormRequest $updateBorrowRecordFormRequest, $id)
    {
        $validatedData = $updateBorrowRecordFormRequest->validated();
        $response = $this->borrowRecordService->update($validatedData, $id);
        return $response['status']
            ? $this->getResponse("msg", "Updated borrow record successfully", 200)
            : $this->getResponse("msg", $response['msg'], $response['code']);
    }

    /**
     * Remove the specified borrow record from storage.
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = $this->borrowRecordService->destroy($id);
        return $response['status']
            ? $this->getResponse("msg", "Deleted borrow record successfully", 200)
            : $this->getResponse("msg", $response['msg'], $response['code']);
    }

    /**
     * Return book
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function due($id)
    {
        $response = $this->borrowRecordService->due($id);
        return $response['status']
            ? $this->getResponse("msg", "Returned Book That Borrow successfully", 200)
            : $this->getResponse("msg", $response['msg'], $response['code']);
    }
}
