<?php

namespace App\Traits;

trait HasIdWithUuids
{
    /**
     * [README]
     * How to use it in models, just copy and paste the code bellow in your models files that is using HasUuids
     * use HasIdWithUuids { HasIdWithUuids::uniqueIds insteadof HasUuids; }
     */

    public function uniqueIds()
    {
        return [str_replace('id', 'uuid', $this->getKeyName())];
    }
}
