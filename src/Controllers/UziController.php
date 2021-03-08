<?php

namespace MinVWS\PUZI\Laravel\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use MinVWS\PUZI\Exceptions\UziException;
use MinVWS\PUZI\Laravel\Services\UziService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Class UziController.
 *
 * For reference please read
 * https://www.zorgcsp.nl/documents/RK1%20CPS%20UZI-register%20V10.2%20ENG.pdf
 *
 * @package MinVWS\PUZI\Laravel\Controllers
 */
class UziController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /** @var UziService */
    protected $uziService;

    /**
     * UziController constructor.
     *
     * @param UziService $uziService
     */
    public function __construct(UziService $uziService)
    {
        $this->uziService = $uziService;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('uzi.login');
    }

    /**
     * @return RedirectResponse
     */
    public function login(): RedirectResponse
    {
        try {
            $user = $this->uziService->getUserFromUzi();
        } catch (UziException $exception) {
            abort(403, __($exception->getMessage()));
        }
        auth()->login($user);

        return redirect()->route('home')
            ->with('success', __('Logged in successfully'));
    }
}
