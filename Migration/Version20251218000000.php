<?php

declare(strict_types=1);

namespace MauticPlugin\MauticReusableTemplatesBundle\Migration;

use Doctrine\DBAL\Schema\Schema;
use Mautic\Migrations\AbstractMauticMigration;

final class Version20251218000000 extends AbstractMauticMigration
{
    public function up(Schema $schema): void
    {
        $table = $schema->getTable('reusable_templates');

        if (!$table->hasColumn('type')) {
            $table->addColumn('type', 'string', [
                'length' => 20,
                'notnull' => true,
                'default' => 'section',
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('reusable_templates');

        if ($table->hasColumn('type')) {
            $table->dropColumn('type');
        }
    }
}
