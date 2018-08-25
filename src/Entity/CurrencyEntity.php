<?php
declare(strict_types=1);

namespace TMCms\Modules\Currencies\Entity;

use TMCms\Orm\Entity;

/**
 * Class Currency
 *
 * @method setCode(string $code)
 * @method setIsMain(int $flag)
 * @method setRate(float $rate)
 *
 * @method string getCode()
 * @method int getIsMain()
 * @method float getRate()
 */
class CurrencyEntity extends Entity {
    protected $translation_fields = [CurrencyEntityRepository::FIELD_NAME];

    /**
     * Auto-call before any Create or Update
     */
    protected function beforeSave()
    {
        // Currency ISO code is always uppercase
        $this->setCode(\strtoupper($this->getCode()));

        return parent::beforeSave();
    }
}
