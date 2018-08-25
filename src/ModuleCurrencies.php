<?php
declare(strict_types=1);

namespace TMCms\Modules\Currencies;

use TMCms\Modules\Currencies\Entity\CurrencyEntity;
use TMCms\Modules\Currencies\Entity\CurrencyEntityRepository;
use TMCms\Modules\IModule;
use TMCms\Traits\singletonInstanceTrait;

\defined('INC') or exit;

/**
 * Class ModuleCurrencies
 *
 * @package TMCms\Modules\Currencies
 */
class ModuleCurrencies implements IModule {
    use singletonInstanceTrait;

    public const DEFAULT_CURRENCY_CODE = 'EUR';

    /**
     * @return string
     */
    public static function getMainCurrencyCode(): string
    {
        $currencies = new CurrencyEntityRepository();
        $currencies->setWhereIsMain(1);

        /** @var CurrencyEntity $currency */
        $currency = $currencies->getFirstObjectFromCollection();

        if (!$currency) {
            return self::DEFAULT_CURRENCY_CODE;
        }

        return $currency->getCode();
    }

    /**
     * @param array $filters
     *
     * @return CurrencyEntityRepository
     */
    public static function getCurrencies(?array $filters = []): CurrencyEntityRepository
    {
        $currencies = new CurrencyEntityRepository();
        $currencies->enableUsingCache();

        if (isset($filters['active'])) {
            $currencies->setWhereActive((int)$filters['active']);
        }

        return $currencies;
    }
}
