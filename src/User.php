<?php

namespace iMemento\SDK\Auth;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, UserMagicTrait;

    protected $guarded = [];

    /**
     * @return bool
     */
    public function belongsToOrganization()
    {
        return !empty($this->org_ids);
    }

    /**
     * @return bool
     */
    public function belongsToAgency()
    {
        return $this->org_type == 'agency';
    }
}
