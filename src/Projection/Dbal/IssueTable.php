<?php
declare(strict_types=1);
namespace Dkplus\Indicator\Projection\Dbal;

use Doctrine\DBAL\Schema\Schema;

class IssueTable
{
    const TABLE_NAME = 'indicator_issues';

    public static function upToV1(Schema $schema)
    {
        $table = $schema->createTable(self::TABLE_NAME);
        $table->addColumn('id', 'string', ['length' => 36]);
        $table->addColumn('reporter_id', 'string', ['length' => 36]);
        $table->addColumn('title', 'string', ['length' => 255]);
        $table->addColumn('text', 'text', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['reporter_id']);
    }

    public static function downToV0(Schema $schema)
    {
        $schema->dropTable(self::TABLE_NAME);
    }
}
