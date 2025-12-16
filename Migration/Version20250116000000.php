<?php

declare(strict_types=1);

namespace MauticPlugin\MauticReusableTemplatesBundle\Migration;

use Doctrine\DBAL\Schema\Schema;
use Mautic\Migrations\AbstractMauticMigration;

final class Version20250116000000 extends AbstractMauticMigration
{
    public function up(Schema $schema): void
    {
        // Create reusable_templates table
        $templatesTable = $schema->createTable('reusable_templates');

        $templatesTable->addColumn('id', 'integer', [
            'autoincrement' => true,
            'notnull' => true,
        ]);

        $templatesTable->addColumn('name', 'string', [
            'length' => 255,
            'notnull' => true,
        ]);

        $templatesTable->addColumn('content', 'text', [
            'notnull' => false,
        ]);

        $templatesTable->addColumn('created_at', 'datetime', [
            'notnull' => true,
        ]);

        $templatesTable->addColumn('updated_at', 'datetime', [
            'notnull' => false,
        ]);

        $templatesTable->addColumn('updated_by', 'integer', [
            'notnull' => false,
        ]);

        $templatesTable->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('reusable_templates');
    }
}
