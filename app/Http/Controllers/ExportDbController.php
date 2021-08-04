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

        MySql::create()
            ->setDbName(env('DB_DATABASE'))
            ->setUserName(env('DB_USERNAME'))
            ->setPassword(env('DB_PASSWORD'))
            ->includeTables(['columns', 'cards'])
            ->dumpToFile($tmp);

        return response()->download($tmp, 'export-database-' . date('YmdHis'). '.sql');
    }
}
