<?php

namespace App\Http\Controllers;

use Spatie\DbDumper\Databases\MySql;

class ExportDbController extends Controller
{
    /**
     * @throws \Spatie\DbDumper\Exceptions\CannotSetParameter
     */
    public function export()
    {
        $tmp = tempnam('/tmp', 'export-db');

        $default = config('database.default');
        $conf = config('database.connections.' . $default);

        MySql::create()
            ->setDbName($conf['database'])
            ->setUserName($conf['username'])
            ->setPassword($conf['password'])
            ->includeTables(['columns', 'cards'])
            ->dumpToFile($tmp);

        return response()->download($tmp, 'export-database-' . date('YmdHis'). '.sql');
    }
}
