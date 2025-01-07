<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\PaymentHelper;
use App\Http\Controllers\Controller;
use App\Model\Transaction;
use App\Providers\AuthServiceProvider;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class VisitorController extends Controller
{
    protected $paymentHandler;

    /**
     * PaymentsController constructor.
     * @param PaymentsServiceProvider $paymentsProvider
     */
    public function __construct(PaymentHelper $paymentHandler)
    {
        $this->paymentHandler = $paymentHandler;
    }

    public function register(Request $request)
    {
        $user = AuthServiceProvider::createVisitant($request->get('visitor_id'));
        if (!$user) {
            return response()->json([], 401);
        }
        $recipient = User::where('id', $request->get('recipient_user_id'))->first();

        $transaction = new Transaction();
        $transaction['sender_user_id'] = $user->id;
        $transaction['recipient_user_id'] = $request->get('recipient_user_id');
        $transaction['type'] = 'one-month-subscription';
        $transaction['status'] = Transaction::PENDING_STATUS;
        $transaction['amount'] = $recipient->profile_access_price;
        $transaction['currency'] = config('app.site.currency_code');
        $transaction['payment_provider'] = 'pix';
        $transaction['visitor_id'] = $request->get('visitor_id');
        $transaction['ad'] = $request->get('visitor_id');
        $transaction['visitor_provider'] = 'telegram';
        $res = $this->paymentHandler->generationPixPayment($transaction);
        $transaction['transfer_id'] = $res['txid'];
        $transaction->save();

        $tokenData = $user->username . '&%&' . $request->get('visitor_id');
        $token = Crypt::encryptString($tokenData);

        return response()->json([
            'username' => $recipient->username,
            'password' => $request->get('visitor_id'),
            'token' => $token,
            'influencer' => $user->username,
            'pix' => $res['pixCopiaECola'],
            'amount' => $transaction['amount'],
        ], 201);
    }

    public function login(Request $request)
    {
        $decryptedToken = Crypt::decryptString($request->get('token'));
        $token = explode('&%&', $decryptedToken);
        $user_with_password = DB::table('users')
            ->select('id', 'username', 'password')
            ->where('username', $token[0])
            ->first();
        if (!$user_with_password) {
            return response()->json([
                'message' => 'Usuário não encontrado',
                'token' => $token
            ], 404);
        }

        if (Hash::check($token[1], $user_with_password->password)) {
            $user = User::where('id', $user_with_password->id)->first();
            Auth::login($user);
            return response()->json([], 200);
        }
        return response()->json([], 400);
    }
}
