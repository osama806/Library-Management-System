<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ratings\RatingFormRequest;
use App\Models\Rating;
use App\Services\RatingService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    use ResponseTrait;

    protected $ratingService;
    public function __construct(RatingService   $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * Display a listing of the ratings.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = $this->ratingService->index();
        return $response['status']
            ? $this->getResponse("ratings", $response['ratings'], 200)
            : $this->getResponse("msg", $response['msg'], 404);
    }

    /**
     * Display the specified rating.
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response = $this->ratingService->show($id);
        return $response['status']
            ? $this->getResponse("rating", $response['rating'], 200)
            : $this->getResponse("msg", $response['msg'], 404);
    }

    /**
     * Store a newly created rating in storage.
     * @param \App\Http\Requests\Ratings\RatingFormRequest $ratingFormRequest
     * @return \Illuminate\Http\Response
     */
    public function store(RatingFormRequest $ratingFormRequest)
    {
        $validated = $ratingFormRequest->validated();
        $response = $this->ratingService->store($validated);
        return $response['status']
            ? $this->getResponse("msg", "Created rating successfully", 201)
            : $this->getResponse("msg", $response['msg'], $response['code']);
    }

    /**
     * Update the specified rating in storage.
     * @param \App\Http\Requests\Ratings\RatingFormRequest $ratingFormRequest
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function update(RatingFormRequest $ratingFormRequest, $id)
    {
        $validatedData = $ratingFormRequest->validated();
        $response = $this->ratingService->update($validatedData, $id);
        return $response['status']
            ? $this->getResponse("msg", "Updated rating successfully", 200)
            : $this->getResponse("msg", $response['msg'], 404);
    }

    /**
     * Remove the specified rating from storage.
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = $this->ratingService->destroy($id);
        return $response['status']
            ? $this->getResponse("msg", "Deleted rating successfully", 200)
            : $this->getResponse("msg", $response['msg'], $response['code']);
    }
}
