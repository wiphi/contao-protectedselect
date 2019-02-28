<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Contao\CoreBundle\Controller;

class BackendCsvImportControllerProtectedOption extends BackendCsvImportController
{
    public function importProtectedOptionWizardAction(DataContainer $dc): Response
    {
        return $this->importFromTemplate(
            function (array $data, array $row): array {
                $data[] = [
                    'value' => $row[0],
                    'label' => $row[1],
                    'reference' => $row[2],
                    'default' => !empty($row[3]) ? 1 : '',
                    'group' => !empty($row[4]) ? 1 : '',
                ];

                return $data;
            },
            $dc->table,
            'protectedOptions',
            (int) $dc->id,
            $this->translator->trans('MSC.ow_import.0', [], 'contao_default')
        );
    }
}