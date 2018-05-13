<?php


namespace App\Services\Implementation;


use App\Model\Lan;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Services\LanService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LanServiceImpl implements LanService
{
    protected $lanRepository;

    /**
     * LanServiceImpl constructor.
     * @param LanRepositoryImpl $lanRepositoryImpl
     */
    public function __construct(LanRepositoryImpl $lanRepositoryImpl)
    {
        $this->lanRepository = $lanRepositoryImpl;
    }

    public function createLan(Request $input): Lan
    {
        $lanValidator = Validator::make($input->all(), [
            'lan_start' => 'required|after:seat_reservation_start|after:tournament_reservation_start',
            'lan_end' => 'required|after:lan_start',
            'seat_reservation_start' => 'required|after_or_equal:now',
            'tournament_reservation_start' => 'required|after_or_equal:now',
            'event_key_id' => 'required|string|max:255',
            'public_key_id' => 'required|string|max:255',
            'secret_key_id' => 'required|string|max:255',
            'price' => 'required|integer|min:0'
        ]);

        if ($lanValidator->fails()) {
            throw new BadRequestHttpException($lanValidator->errors());
        }

        return $this->lanRepository->createLan
        (
            new DateTime($input['lan_start']),
            new DateTime($input['lan_end']),
            new DateTime($input['seat_reservation_start']),
            new DateTime($input['tournament_reservation_start']),
            $input['event_key_id'],
            $input['public_key_id'],
            $input['secret_key_id'],
            $input['price']
        );
    }
}