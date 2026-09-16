<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ---------- 1. Add financial_year to billid ----------
        if (!Schema::hasColumn('billid', 'financial_year')) {
            Schema::table('billid', function (Blueprint $table) {
                $table->string('financial_year', 10)->nullable()->after('bill_id');
            });
        }

        // ---------- 2. Add financial_year to billdate ----------
        if (!Schema::hasColumn('billdate', 'financial_year')) {
            Schema::table('billdate', function (Blueprint $table) {
                $table->string('financial_year', 10)->nullable()->after('bill_date');
                $table->index('financial_year', 'billdate_fy_index');
            });
        }

        // ---------- 3. Backfill billdate.financial_year from bill_date ----------
        // FY = April 1 to March 31.
        //   month >= 4  → FY = YEAR-(YEAR+1)  e.g. 2025-26
        //   month <  4  → FY = (YEAR-1)-YEAR  e.g. 2024-25
        DB::statement("
            UPDATE billdate
            SET financial_year = CONCAT(
                YEAR(bill_date) - IF(MONTH(bill_date) < 4, 1, 0),
                '-',
                LPAD(
                    ((YEAR(bill_date) - IF(MONTH(bill_date) < 4, 1, 0) + 1) % 100),
                    2, '0'
                )
            )
            WHERE financial_year IS NULL OR financial_year = ''
        ");

        // ---------- 4. Backfill billid.financial_year by joining billdate ----------
        DB::statement("
            UPDATE billid b
            INNER JOIN billdate d ON d.bill_id = b.bill_id
            SET b.financial_year = d.financial_year
            WHERE b.financial_year IS NULL OR b.financial_year = ''
        ");

        // ---------- 5. Fallback: any remaining nulls get current FY ----------
        $currentFy = $this->currentFyString();

        DB::table('billid')
            ->where(function ($q) {
                $q->whereNull('financial_year')->orWhere('financial_year', '');
            })
            ->update(['financial_year' => $currentFy]);

        DB::table('billdate')
            ->where(function ($q) {
                $q->whereNull('financial_year')->orWhere('financial_year', '');
            })
            ->update(['financial_year' => $currentFy]);

        // ---------- 6. Drop old unique on bill_id ----------
        try {
            Schema::table('billid', function (Blueprint $table) {
                $table->dropUnique(['bill_id']);
            });
        } catch (\Throwable $e) {
            // ignore — may not exist
        }

        try {
            DB::statement('ALTER TABLE billid DROP INDEX billid_bill_id_unique');
        } catch (\Throwable $e) {
            // ignore
        }

        // ---------- 7. Add composite unique (bill_id, financial_year) ----------
        try {
            Schema::table('billid', function (Blueprint $table) {
                $table->unique(['bill_id', 'financial_year'], 'billid_bill_fy_unique');
            });
        } catch (\Throwable $e) {
            // already exists
        }
    }

    public function down(): void
    {
        try {
            Schema::table('billid', function (Blueprint $table) {
                $table->dropUnique('billid_bill_fy_unique');
            });
        } catch (\Throwable $e) {
        }

        try {
            Schema::table('billid', function (Blueprint $table) {
                $table->unique('bill_id');
            });
        } catch (\Throwable $e) {
        }

        if (Schema::hasColumn('billid', 'financial_year')) {
            Schema::table('billid', function (Blueprint $table) {
                $table->dropColumn('financial_year');
            });
        }

        if (Schema::hasColumn('billdate', 'financial_year')) {
            Schema::table('billdate', function (Blueprint $table) {
                try {
                    $table->dropIndex('billdate_fy_index');
                } catch (\Throwable $e) {
                }
                $table->dropColumn('financial_year');
            });
        }
    }

    private function currentFyString(): string
    {
        $year  = (int) date('Y');
        $month = (int) date('n');
        $start = $month >= 4 ? $year : $year - 1;
        return $start . '-' . substr((string) ($start + 1), -2);
    }
};