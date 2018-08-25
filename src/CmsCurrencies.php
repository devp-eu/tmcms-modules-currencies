<?php
declare(strict_types=1);

namespace TMCms\Modules\Currencies;

use TMCms\Admin\Messages;
use TMCms\HTML\BreadCrumbs;
use TMCms\HTML\Cms\CmsForm;
use TMCms\HTML\Cms\CmsFormHelper;
use TMCms\HTML\Cms\CmsTableHelper;
use TMCms\Log\App;
use TMCms\Modules\Currencies\Entity\CurrencyEntity;
use TMCms\Modules\Currencies\Entity\CurrencyEntityRepository;
use TMCms\Modules\Settings\ModuleSettings;

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
                    'type' => CmsTableHelper::TYPE_ACTIVE,
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
     * @return CmsForm
     */
    public function _add_edit_form(?int $currency_id = NULL): CmsForm
    {
        $currency = new CurrencyEntity($currency_id);
        $currencies = new CurrencyEntityRepository;

        $currency_main = ModuleCurrencies::getMainCurrencyCode();

        return CmsFormHelper::outputForm([
            'button' => __('Save'),
            'data' => $currency,
            'fields' => [
                $currencies::FIELD_CODE => [
                    'hint' => __('ISO code consists of 3 letter, uppercase'),
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

    public function _add(): void
    {
        $currency = new CurrencyEntity();
        $currency->loadDataFromArray($_POST);
        $currency->save();

        App::add('Currency "' . $currency->getCode() . '" added');
        Messages::sendMessage('Currency added');

        go('?p='. P .'&highlight='. $currency->getId());
    }

    public function edit(): void
    {
        $currency = new CurrencyEntity($_GET['id']);

        BreadCrumbs::getInstance()
            ->addCrumb('Edit Currency')
            ->addCrumb($currency->getCode())
        ;

        echo $this->_add_edit_form($currency->getId());
    }

    public function _edit(): void
    {
        $currency = new CurrencyEntity($_GET['id']);
        $currency->loadDataFromArray($_POST);
        $currency->save();

        App::add('Currency "' . $currency->getCode() . '" updated');
        Messages::sendMessage('Currency updated');

        go('?p='. P .'&highlight='. $currency->getId());
    }

    public function _is_main(): void
    {
        // Remove flag from all
        $currencies = new CurrencyEntityRepository();
        $currencies->setIsMain(0);
        $currencies->save();

        $currency = new CurrencyEntity($_GET['id']);
        $currency->flipBoolValue(CurrencyEntityRepository::FIELD_IS_MAIN);
        $currency->save();

        App::add('Currency "' . $currency->getCode() . '" updated');
        Messages::sendMessage('Currency updated');

        exit(1);
    }

    public function _active(): void
    {
        $currency = new CurrencyEntity($_GET['id']);
        $currency->flipBoolValue('active');
        $currency->save();

        App::add('Currency "' . $currency->getCode() . '" updated');
        Messages::sendMessage('Currency updated');

        exit(1);
    }

    public function _delete(): void
    {
        $currency = new CurrencyEntity($_GET['id']);

        if ($currency->getIsMain()) {
            \error('Main currency can not be deleted');
        }

        $currency->deleteObject();

        App::add('Currency "' . $currency->getCode() . '" deleted');
        Messages::sendMessage('Currency deleted');

        back();
    }

    public function settings(): void
    {
        $predefined_settings = [
            'exchange_tax' => [
                'type' => CmsFormHelper::FIELD_TYPE_NUMBER,
                'hint' => 'How much will be added for exchange in % from main currency',
                'max' => 100,
                'min' => 0.1,
                'step' => 0.1,
            ],
        ];

        echo ModuleSettings::requireTableForExternalModule(P, $predefined_settings);
    }

    public function _settings(): void
    {
        ModuleSettings::requireUpdateModuleSettings();
    }
}
