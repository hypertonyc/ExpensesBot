<?php

namespace App\Http\Controllers;

use App\User;
use App\ExpenseCategory;
use App\Expense;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FinanceBotController extends Controller
{
  const TELEGRAM_API_URL = 'https://api.telegram.org/bot';

  static function checkCategory($category_id)
  {
    if($category_id == 0) {
      return true;
    } else {
      $category = ExpenseCategory::find($category_id);
      return !is_null($category);
    }
  }

  static function getCategoryId($position)
  {
    $category = ExpenseCategory::where('position', $category_id - 1)->first();
    if(!$category) {
      return 1;
    } else {
      return $category->id;
    }

  }

  static function sendMessage(User $user, String $message_text)
  {
    $data = array(
      'chat_id' => $user->chat_id,
      'text' => $message_text,
    );

    $client = new Client();
    $res = $client->request('POST',
      self::TELEGRAM_API_URL . config('financebot.token') . '/sendMessage',
      [
        'proxy' => [config('financebot.proxy')],
        'form_params' => $data
      ]);

		if ($res->getStatusCode() == 200)	{
			$result_data = json_decode((string) $res->getBody(), true);
      if(!$result_data['ok']) {
          Log::error($result_data);
      }
    } else {
      Log::error($res);
    }
  }

  static function sendSuccessAmountMessage(User $user)
  {
    $message_text = "Так! Теперь выбери категорию:\r\n";
    $categories = ExpenseCategory::orderBy('position')->get();
    foreach ($categories as $category) {
      $message_text = $message_text . ($category->position + 1) . " - " . $category->name . "\r\n";
    }
    $message_text = $message_text . "0 - для отмены.\r\n";
    self::sendMessage($user, $message_text);
  }

  static function sendFailedAmountMessage(User $user)
  {
    $message_text = 'Ёпта, просто сумма расходов цифрами и всё!';
  }

  static function sendSuccessCategoryMessage(User $user)
  {
    $message_text = 'Окай! Если нужен комментарий, пиши. Если не нужен отправь 0.';
    self::sendMessage($user, $message_text);
  }

  static function sendFailedCategoryMessage(User $user)
  {
    $message_text = 'Ёпта, просто цифру отправь!';
    self::sendMessage($user, $message_text);
    self::sendSuccessAmountMessage($user);
  }

  static function sendSuccessSavingMessage(User $user)
  {
    $message_text = 'Сохранил. Все молодцы!';
    self::sendMessage($user, $message_text);
  }

  static function sendSuccessResetMessage(User $user)
  {
    $message_text = 'Отменил! Ну ты пиши если чо...';
    self::sendMessage($user, $message_text);
  }

  static function processUserMessage(User $user, String $text)
  {
    $cache_key = 'financebot_' . $user->id;
    $chat_step = Cache::get($cache_key, 0);

    switch ($chat_step) {
      case 0:
        if(is_numeric($text)) {
          Cache::forever($cache_key . '_amount', round(floatval($text), 2));
          self::sendSuccessAmountMessage($user);
          Cache::forever($cache_key, 1);
        } else {
          self::sendFailedAmountMessage($user);
        }
        break;

      case 1:
        if(is_numeric($text) && self::checkCategory(intval($text))) {
          if(intval($text) == 0) {
              Cache::forever($cache_key, 0);
              self::sendSuccessResetMessage($user);
          } else {
            Cache::forever($cache_key . '_category', self::getCategoryId(intval($text)));
            self::sendSuccessCategoryMessage($user);
            Cache::forever($cache_key, 2);
          }
        } else {
          self::sendFailedCategoryMessage($user);
        }
        break;

      case 2:
        $description = trim(substr($text, 0, 200));

        if($description == '0') {
          $description = '';
        }

        /***/
        $expense = new Expense;
        $expense->user_id = $user->id;
        $expense->category_id = Cache::get($cache_key . '_category', 1);
        $expense->amount = Cache::get($cache_key . '_amount', 0);
        if(!empty($description)) {
          $expense->description = $description;
        }
        $expense->save();
        /***/
        self::sendSuccessSavingMessage($user);
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
        self::processUserMessage($user, $text);
      }
    }
  }
}
