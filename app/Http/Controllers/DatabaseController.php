<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

/**
 * Class DatabaseController
 *
 * Simulate a nested database transaction for MySQL
 *
 * @package App\Http\Controllers
 */
class DatabaseController extends Controller
{
    private $transactions = 0;

    public function beginTransaction()
    {
        if (DB::transactionLevel() === 0) {
            DB::beginTransaction();
        }

        $this->transactions++;
    }

    public function commit()
    {
        $this->transactions--;

        if ($this->transactions === 0) {
            DB::commit();
        }
    }

    public function rollback()
    {
        $this->transactions = 0;

        DB::rollBack();
    }
}
