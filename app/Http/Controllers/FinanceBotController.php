<?php

namespace App\Http\Controllers;

use App\User;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FinanceBotController extends Controller
{
  const $telegram_api_url = 'https://api.telegram.org/bot';

  function sendSuccessAmountMessage(User $user)
  {
    $message_text = 'Так! Теперь выбери категорию:'
    $data = array(
      'chat_id' => $user->chat_id,
      'text' => $message_text,
    );

    $client = new Client();
    $res = $client->request('POST',
      $telegram_api_url . config('financebot.token') . '/sendMessage',
      [
        'proxy' => [config('financebot.proxy')],
        'body' => $data
      ]);

		if ($res->getStatusCode() == 200)
		{
			$result_data = json_decode((string) $res->getBody(), true);
      if(!$result_data['ok'])
      {
          Log::error('Error while sending SuccessAmountMessage for user: ' . $user->name);
          Log::error($result_data);
      }
    }
  }

  function sendFailedAmountMessage(User $user)
  {

  }

  function sendSuccessCategoryMessage(User $user)
  {

  }

  function sendFailedCategoryMessage(User $user)
  {

  }

  function saveExpense(User $user)
  {

  }

  function processUserMessage(User $user, String $text)
  {
    $cache_key = 'financebot_' . $user->id;
    $chat_step = Cache::get($cache_key, 0);

    switch ($chat_step) {
      case 0:
        if(is_numeric($text)) {
          Cache::forever($cache_key . '_amount', round(floatval($text), 2));
          sendSuccessAmountMessage($user);
          Cache::forever($cache_key, 1);
        } else {
          sendFailedAmountMessage($user);
        }
        break;

      case 1:
        if(intval($text) > 0) {
          Cache::forever($cache_key . '_category', intval($text));
          sendSuccessCategoryMessage($user);
          Cache::forever($cache_key, 2);
        } else {
          sendFailedCategoryMessage($user);
        }
        break;

      case 2:
        saveExpense($user);
        Cache::forever($cache_key, 0);
        break;

      default:
        Log::error('Unknown chat_step for user: ' . $user->name . ' #' . $chat_step);
        Cache::forget($cache_key);
        break;
    }
  }

  public function getUpdate(Request $request)
  {
    $update_array = $request->all();

    // Log this shit
    Log::debug('New update');
    Log::debug($update_array);
    ////

    if(array_key_exists('message', $update_array))
    {
      $message = $update_array['message'];

      $user_id = $message['from']['id'];
      $text =  $message['text'];

      $user = User::where('chat_id', $user_id)->first();
      if(!$user) {
        Log::error('Unknown telegram user_id: ' . $user_id);
      }
      else {
        processUserMessage($user, $text);
      }
    }
  }
}
