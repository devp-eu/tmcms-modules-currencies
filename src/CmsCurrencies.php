<?php
declare(strict_types=1);

namespace TMCms\Modules\Currencies;

use TMCms\HTML\BreadCrumbs;
use TMCms\HTML\Cms\CmsFormHelper;
use TMCms\HTML\Cms\CmsTableHelper;
use TMCms\Modules\Currencies\Entity\CurrencyEntity;
use TMCms\Modules\Currencies\Entity\CurrencyEntityRepository;

\defined('INC') or exit;

/**
 * Class CmsCurrencies
 *
 * @package TMCms\Modules\Currencies
 */
class CmsCurrencies
{
    public function _default(): void
    {
        BreadCrumbs::getInstance()
            ->addAction('Add Currency', '?p=' . P . '&do=add')
        ;

        $currencies = new CurrencyEntityRepository;

        echo CmsTableHelper::outputTable([
            'data' => $currencies,
            'columns' => [
                $currencies::FIELD_CODE => [],
                $currencies::FIELD_NAME => [
                    'translation' => true,
                ],
                $currencies::FIELD_RATE => [],
                $currencies::FIELD_IS_MAIN => [
                    'type' => CmsTableHelper::TYPE_CHECKBOX,
                ],
            ],
            'active' => true,
            'edit' => true,
            'delete' => true,
        ]);
    }

    public function add(): void
    {
        BreadCrumbs::getInstance()
            ->addCrumb('Add Currency');

        echo $this->_add_edit_form();
    }

    /**
     * @param int? $currency_id
     *
     * @return \TMCms\HTML\Cms\CmsForm
     */
    public function _add_edit_form(?int $currency_id = NULL): \TMCms\HTML\Cms\CmsForm
    {
        $currency = new CurrencyEntity($currency_id);
        $currencies = new CurrencyEntityRepository;

        $currency_main = ModuleCurrencies::getMainCurrencyCode();

        return CmsFormHelper::outputForm([
            'button' => __('Save'),
            'data' => $currency,
            'fields' => [
                $currencies::FIELD_CODE => [
                    'hint' => __('ISO code consists of 3 letter'),
                    'validate' => [
                        'required' => true,
                        'alphanum' => true,
                        'minlength' => 3,
                        'maxlength' => 3,
                    ],
                ],
                $currencies::FIELD_NAME => [
                    'translation' => true,
                    'required' => true,
                ],
                $currencies::FIELD_RATE => [
                    'hint' => __('To main currency') . ' ('. $currency_main .')',
                    'type' => CmsFormHelper::FIELD_TYPE_NUMBER,
                    'required' => true,
                    'min' => '0.0001',
                    'max' => '100000',
                    'step' => '0.0001'
                ],
            ],
        ]);
    }
}
