<?php

namespace cosmicnebula200\ReferMeBro\player;

use cosmicnebula200\ReferMeBro\ReferMeBro;

class Player
{

    public function __construct(private string $uuid, private string $name, private string $referral, private array $cmds, private bool $referred, private int $refers)
    {

    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getReferral(): string
    {
        return $this->referral;
    }

    /**
     * @param string $referral
     */
    public function setReferral(string $referral): void
    {
        $this->referral = $referral;
        $this->save();
    }

    /**
     * @return array
     */
    public function getCmds(): array
    {
        return $this->cmds;
    }

    /**
     * @param array $cmds
     */
    public function setCmds(array $cmds): void
    {
        $this->cmds = $cmds;
        $this->save();
    }

    /**
     * @return bool
     */
    public function hasReferred(): bool
    {
        return $this->referred;
    }

    /**
     * @param bool $referred
     */
    public function setRefered(bool $referred): void
    {
        $this->referred = $referred;
        $this->save();
    }

    /**
     * @return int
     */
    public function getRefers(): int
    {
        return $this->refers;
    }

    /**
     * @param int $refers
     */
    public function setRefers(int $refers): void
    {
        $this->refers = $refers;
        $this->save();
    }

    public function save(): void
    {
        ReferMeBro::getInstance()->getDataBase()->executeChange('refermebro.update', [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'cmds' => json_encode($this->cmds),
            'referred' => (int)$this->referred,
            'refers' => $this->refers
        ]);
        ReferMeBro::getInstance()->getDataBase()->waitAll();
    }

}
