<?php

namespace App\Http\Controllers;

use App\Services\Implementation\SeatServiceImpl;
use Dingo\Api\Routing\Helpers;

class SeatController extends Controller
{
    use Helpers;

    protected $seatService;

    /**
     * LanController constructor.
     * @param SeatServiceImpl $seatServiceImpl
     */
    public function __construct(SeatServiceImpl $seatServiceImpl)
    {
        $this->seatService = $seatServiceImpl;
    }

    public function bookSeat(string $lan_id, string $seat_id)
    {
        return response()->json($this->seatService->book($lan_id, $seat_id), 201);
    }
}
