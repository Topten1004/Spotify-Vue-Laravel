<?php

namespace App\installer\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\installer\Helpers\DatabaseManager;
use Exception;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;
    /**
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }
    /**
     * Migrate and seed the database.
     *
     * @return \Illuminate\View\View
     */
    public function database($request)
    {
        return $this->databaseManager->migrateAndSeed($request);
    }
    /**
     * Display the create admin form page.
     *
     * @return \Illuminate\View\View
     */
    public function createAdminPage()
    {
        return view('vendor.installer.create-admin');
    }
    /**
     * Validate the super-admin account data submitted by the form.
     */
    public function validateSuperAdminData(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|confirmed|string|min:8',
        ]);
        return $this->database($request);
    }

    public function testDBConnection(Request $request)
    {
        $connection = $request->connection;

        $settings = config("database.connections.$connection");

        config([
            'database' => [
                'default' => $connection,
                'connections' => [
                    $connection => array_merge($settings, [
                        'driver' => $connection,
                        'host' => $request->host,
                        'port' => $request->port,
                        'database' => $request->name,
                        'username' => $request->username,
                        'password' => $request->password,
                    ]),
                ],
            ],
        ]);
        DB::purge();
        try {
            DB::connection()->getPdo();
        } catch (Exception $e) {
            return response()->json('Failed to connect with the database. Please check your credentials.', 500);
        }
        // checking if the database is empty ( has no tables )
        $tables = DB::select("show tables");
        $table_count = count($tables);
        if( $table_count === 0 ){
            return response()->json('Connected.', 200);
        };
        
        return response()->json('Database is not empty. Please clear all the exising tables.', 500);
    }
}
