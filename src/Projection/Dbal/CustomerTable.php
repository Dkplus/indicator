<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Projection\Dbal;

use Doctrine\DBAL\Schema\Schema;

class CustomerTable
{
    const TABLE_NAME = 'indicator_customers';

    public static function upToV1(Schema $schema)
    {
        $table = $schema->createTable(self::TABLE_NAME);
        $table->addColumn('id', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
    }

    public static function downFromV1(Schema $schema)
    {
        $schema->dropTable(self::TABLE_NAME);
    }
}
