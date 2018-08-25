<?php
declare(strict_types=1);

namespace TMCms\Modules\Currencies\Entity;

use TMCms\Orm\EntityRepository;
use TMCms\Orm\TableStructure;

/**
 * Class CurrencyRepository
 *
 * @method $this setWhereIsMain(int $flag)
 */
class CurrencyEntityRepository extends EntityRepository {
    public const FIELD_CODE = 'code';
    public const FIELD_NAME = 'name';
    public const FIELD_RATE = 'rate';
    public const FIELD_IS_MAIN = 'is_main';

    protected $table_structure = [
        'fields' => [
            self::FIELD_CODE => [
                'type' => TableStructure::FIELD_TYPE_VARCHAR_255,
            ],
            self::FIELD_NAME => [
                'type' => TableStructure::FIELD_TYPE_TRANSLATION,
            ],
            self::FIELD_RATE => [
                'type' => TableStructure::FIELD_TYPE_FLOAT_DECIMAL,
            ],
            self::FIELD_IS_MAIN => [
                'type' => TableStructure::FIELD_TYPE_BOOL,
            ],
        ],
        'keys' => [
            self::FIELD_IS_MAIN => [
                'type' => TableStructure::INDEX_TYPE_KEY,
            ],
        ],
    ];
}
