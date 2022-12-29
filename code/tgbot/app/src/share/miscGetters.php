<?php

namespace App\share;

use App\assets\consts;
use App\misc\local;

trait miscGetters
{
    protected $vpnusers;
    protected $virts = null;

    protected function vpnname($id)
    {
        $this->vpnusers = $this->vpnusers
            ?? local::loadusers(consts::PARTICIPANTS()) ?? [];

        return $this->vpnusers[$id] ??
            (empty($this->vpnusers) ? null : array_flip($this->vpnusers)[strtolower($id)]);
    }

    protected function userid(&$candidate, \Exception $e = null)
    {
        $id = empty($candidate) ? $this->chatid() : $candidate;
        $e = $e ?? new \Exception();
        $db = local::loadusers(consts::PARTICIPANTS());
        $id = !empty($db[$id] ?? $db[(int)$id]) ? $id : array_flip($db)[strtolower($id)];

        if (empty($this->vpnname($id))) throw $e;
        $candidate = (int)$id;
        return $this;
    }
    
    protected function virts(int $id)
    {
        return $this->virts = $this->virts
            ?? local::loadusers(consts::VIRTS())[$id] ?? [];
    }

    protected function lookforname($name, \Exception $e = null)
    {
        $id = $name = strtolower(trim($name));
        try {
            $this->userid($id);
            $name = $this->vpnname($id);
            return [$id, $name];
        }catch (\Throwable $ne){
            $db = local::loadusers(consts::VIRTS());
            foreach ($db as $id => $names)
                if (!empty($names[$name]))
                    if (empty($names[$name]['removed']))
                        return [$id, $name];
        }
        if (!empty($e)) throw $e;
        return [null, null];
    }

}