<?php

namespace Helper;

use loandbeholdru\shorts\arrays;

trait miscBots
{
    // Отправляет сообщения в телеграм бота, сохраняя статистику
    protected function send(string $chatid, string $text)
    {
        $conf = [
            operconsts::CHATID => (int)$chatid,
            operconsts::DEFTEXT => $text
        ];
        $this->tg->setCommandConfig('sendsms', $conf);
        return $this->tg->runCommands(['/sendsms']);
    }

    // Сохраняет статистику сообщений
    protected function stat(array $data)
    {
        $data['token'] = md5(date('Y-m-d G:i'));
        try {
            $json = file_get_contents($this->statfile());
            $recs = arrays::valid_json($json, true, new \Exception("Bad json!"));
        }catch (\Throwable $e){
            $recs = [];
        }
        $recs[md5(json_encode($data))] = $data;
        file_put_contents($this->statfile(), json_encode($recs));
        return $this;
    }

    // Возвращает путь и имя файла, построенное по шаблону
    protected function statfile($date = null)
    {
        $date = $date ?? date("Y-m-d");
        return sprintf($this->statpath(), md5($date));
    }

    // Сделано для возврата пути. Предусмотрено под кастомизацию для мультипользования
    protected function statpath()
    {
        return STATSTOREFILE;
    }

    // Удаляет файлы, старше одного дня
    protected function clear()
    {
        $files = [
            $this->statfile(date("Y-m-d", strtotime( '-4 days' ))),
            $this->statfile(date("Y-m-d", strtotime( '-2 days' ))),
            $this->statfile(date("Y-m-d", strtotime( '-3 days' ))),
        ];
        foreach ($files as $file)
            if (realpath($file))
                unlink($file);

        return $this;
    }
}