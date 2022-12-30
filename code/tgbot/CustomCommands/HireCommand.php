<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\assets\consts;
use App\assets\msg;
use App\misc\miscInform;
use App\share\miscGetters;
use Helper\alreadyException;
use Helper\miscBotOperations;
use Helper\miscBroadcast;
use Helper\miscCommands;
use Helper\notadminException;
use Helper\operconsts;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;


class HireCommand extends UserCommand
{
    use miscCommands;
    use miscBotOperations;
    use miscBroadcast;
    use miscGetters;


    protected $name = 'hire';                      // Your command's name
    protected $description = msg::HIRE_DESC; // Your command description
    protected $usage = '/hire <telegram-id>';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute() : ServerResponse
    {

        try {
            $tohire = $this->getMessage()
                ->getText(true);

            // Если запрос поступил не от админа, перейти к найму с отправкой сообщения,
            // выбросив исключение
            $tohire = empty($tohire) ? $this->chatid() : $tohire;
            $this->userid($tohire)
                ->checkUsers($tohire)
                ->checkAdmins(...consts::ADMINS());

            $users = array_merge($this->users(consts::USERsSTORE()), [$tohire]);
            $this->storeusers($users, consts::USERsSTORE());
            $this->sendMessage(msg::hire('DONE'), $tohire);
            $msg = sprintf(msg::hire('MSG'), $this->vpnname($tohire));
        }catch (alreadyException $ae){
            return $this->broadcast(
                msg::hire('ALREADY'), $this->chatid());
        }catch (notadminException $na){
            return $this->requestAdmin(msg::hire('REQUEST'), msg::hire('WANT_TO'));
        }catch (\Throwable $e){
            $msg = sprintf(msg::hire('ERROR'), $this->vpnname($tohire), $e->getMessage());
        }

        $resp = $this->inform()
            ->broadcast($msg, ...consts::ADMINS());

        return in_array($tohire, consts::ADMINS())
            ? $resp : $this->subs($tohire);
    }

    protected function subs($id)
    {
        $this->telegram->setCommandConfig('subs', [
            operconsts::CHATID => $id
        ]);
        return  $this->telegram->runCommands(['/subs'])[0];
    }




}