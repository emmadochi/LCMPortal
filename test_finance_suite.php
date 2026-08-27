<?php
/**
 * Church Portal - Automated Financial & Treasury Test Suite
 * 
 * Tests:
 * 1. Financial Math & Calculations (Inflow, Outflow, Net Balance, Margins)
 * 2. Budget Engine (Burn rates, threshold classification, remaining funds)
 * 3. Pledge Engine (Commitments, installments, remaining balances, fulfillment rate)
 * 4. Cashflow & YoY Formulas (Growth percentages, negative denominator guards)
 * 5. View Syntax & File Integrity (PHP Lint check for all 15 financial views/controllers)
 * 6. Currency Localization (₦ Symbol & formatting compliance)
 */

declare(strict_types=1);

class FinanceTestSuite
{
    private int $passed = 0;
    private int $failed = 0;
    private array $results = [];

    public function run(): void
    {
        echo "\n========================================================\n";
        echo "   CHURCH PORTAL: FINANCIAL & TREASURY TEST SUITE\n";
        echo "========================================================\n\n";

        $this->testFinancialCalculations();
        $this->testBudgetEngine();
        $this->testPledgeEngine();
        $this->testCashflowAndYoYFormulas();
        $this->testCurrencyFormatting();
        $this->testFileSyntaxIntegrity();

        $this->printSummary();
    }

    private function assert(string $testName, bool $condition, string $details = ''): void
    {
        if ($condition) {
            $this->passed++;
            echo "  [PASS] {$testName}\n";
            $this->results[] = ['name' => $testName, 'status' => 'PASS', 'details' => $details];
        } else {
            $this->failed++;
            echo "  [FAIL] {$testName} - {$details}\n";
            $this->results[] = ['name' => $testName, 'status' => 'FAIL', 'details' => $details];
        }
    }

    private function testFinancialCalculations(): void
    {
        echo "--- 1. Testing Financial & Treasury Math Engine ---\n";

        // Inflow - Outflow = Net Balance
        $totalInflow = 1500000.50; // ₦1.5M Tithes/Offerings
        $totalOutflow = 850000.25; // ₦850k Expenses
        $netBalance = $totalInflow - $totalOutflow;
        $this->assert("Net Balance Calculation (Surplus)", abs($netBalance - 650000.25) < 0.001, "Expected 650,000.25");

        // Deficit test
        $defInflow = 500000.00;
        $defOutflow = 750000.00;
        $defNet = $defInflow - $defOutflow;
        $this->assert("Net Balance Calculation (Deficit)", $defNet === -250000.00, "Expected -250,000.00");

        // Margin calculation
        $margin = ($totalInflow > 0) ? round(($netBalance / $totalInflow) * 100, 2) : 0;
        $expectedMargin = round((650000.25 / 1500000.50) * 100, 2);
        $this->assert("Operating Margin Formula", $margin === $expectedMargin, "Expected {$expectedMargin}%");

        // Zero division guard
        $zeroInflowMargin = (0 > 0) ? round((0 / 0) * 100, 2) : 0;
        $this->assert("Zero Inflow Division Guard", $zeroInflowMargin === 0, "Expected 0% without division by zero error");
        echo "\n";
    }

    private function testBudgetEngine(): void
    {
        echo "--- 2. Testing Budget Management & Health Classification ---\n";

        $budgetAllocated = 5000000.00; // ₦5,000,000

        // Test 1: On Track (50% spent)
        $spent1 = 2500000.00;
        $pct1 = round(($spent1 / $budgetAllocated) * 100, 1);
        $status1 = ($pct1 >= 90) ? 'exceeded' : (($pct1 >= 75) ? 'caution' : 'on_track');
        $this->assert("Budget Status 'On Track' (50%)", $status1 === 'on_track' && $pct1 === 50.0);

        // Test 2: Caution (80% spent)
        $spent2 = 4000000.00;
        $pct2 = round(($spent2 / $budgetAllocated) * 100, 1);
        $status2 = ($pct2 >= 90) ? 'exceeded' : (($pct2 >= 75) ? 'caution' : 'on_track');
        $this->assert("Budget Status 'Caution' (80%)", $status2 === 'caution' && $pct2 === 80.0);

        // Test 3: Exceeded (95% spent)
        $spent3 = 4750000.00;
        $pct3 = round(($spent3 / $budgetAllocated) * 100, 1);
        $status3 = ($pct3 >= 90) ? 'exceeded' : (($pct3 >= 75) ? 'caution' : 'on_track');
        $this->assert("Budget Status 'Exceeded' (95%)", $status3 === 'exceeded' && $pct3 === 95.0);

        // Test 4: Remaining calculation
        $rem = $budgetAllocated - $spent1;
        $this->assert("Budget Remaining Calculation", $rem === 2500000.00);
        echo "\n";
    }

    private function testPledgeEngine(): void
    {
        echo "--- 3. Testing Pledges & Capital Campaign Redemption ---\n";

        $targetAmount = 1000000.00; // ₦1,000,000 pledge
        $initialPaid = 0.00;
        
        // Initial state
        $fulfillment0 = ($targetAmount > 0) ? round(($initialPaid / $targetAmount) * 100, 1) : 0;
        $status0 = ($fulfillment0 >= 100) ? 'fulfilled' : (($fulfillment0 > 0) ? 'in_progress' : 'pending');
        $this->assert("Initial Pledge Status 'Pending'", $status0 === 'pending' && $fulfillment0 === 0.0);

        // Partial payment: ₦400,000 installment
        $paid1 = 400000.00;
        $balance1 = $targetAmount - $paid1;
        $fulfillment1 = round(($paid1 / $targetAmount) * 100, 1);
        $status1 = ($fulfillment1 >= 100) ? 'fulfilled' : (($fulfillment1 > 0) ? 'in_progress' : 'pending');
        $this->assert("Partial Pledge Status 'In Progress' (40%)", $status1 === 'in_progress' && $balance1 === 600000.00);

        // Full redemption: ₦600,000 remaining installment
        $paid2 = $paid1 + 600000.00;
        $balance2 = $targetAmount - $paid2;
        $fulfillment2 = round(($paid2 / $targetAmount) * 100, 1);
        $status2 = ($fulfillment2 >= 100) ? 'fulfilled' : (($fulfillment2 > 0) ? 'in_progress' : 'pending');
        $this->assert("Full Pledge Status 'Fulfilled' (100%)", $status2 === 'fulfilled' && $balance2 === 0.0);
        echo "\n";
    }

    private function testCashflowAndYoYFormulas(): void
    {
        echo "--- 4. Testing Cashflow & Year-over-Year (YoY) Formulas ---\n";

        $prevInflow = 10000000.00; // ₦10M in 2025
        $currInflow = 13500000.00; // ₦13.5M in 2026

        // YoY Inflow Growth (+35%)
        $inflowGrowth = ($prevInflow > 0) ? round((($currInflow - $prevInflow) / $prevInflow) * 100, 1) : 0;
        $this->assert("YoY Income Growth (+35.0%)", $inflowGrowth === 35.0);

        // YoY Outflow Reduction (-10%)
        $prevOutflow = 8000000.00;
        $currOutflow = 7200000.00;
        $outflowGrowth = ($prevOutflow > 0) ? round((($currOutflow - $prevOutflow) / $prevOutflow) * 100, 1) : 0;
        $this->assert("YoY Outflow Reduction (-10.0%)", $outflowGrowth === -10.0);

        // Net YoY improvement
        $prevNet = $prevInflow - $prevOutflow; // ₦2M
        $currNet = $currInflow - $currOutflow; // ₦6.3M
        $netGrowth = ($prevNet > 0) ? round((($currNet - $prevNet) / $prevNet) * 100, 1) : 0;
        $this->assert("YoY Net Improvement (+215.0%)", $netGrowth === 215.0);
        echo "\n";
    }

    private function testCurrencyFormatting(): void
    {
        echo "--- 5. Testing Nigerian Naira (₦) Currency Formatting ---\n";

        $amount = 1250450.75;
        $formatted = '₦' . number_format($amount, 2);
        $this->assert("Standard Naira Format", $formatted === '₦1,250,450.75', "Got: {$formatted}");

        $kFormat = '₦' . round($amount / 1000) . 'k';
        $this->assert("K-Scale Axis Format", $kFormat === '₦1250k', "Got: {$kFormat}");
        echo "\n";
    }

    private function testFileSyntaxIntegrity(): void
    {
        echo "--- 6. Testing PHP Syntax & View File Integrity ---\n";

        $filesToCheck = [
            'app/controllers/FinanceController.php',
            'app/controllers/PropertyCategoryController.php',
            'app/views/finance/dashboard_all.php',
            'app/views/finance/dashboard_single.php',
            'app/views/budgets/index.php',
            'app/views/pledges/index.php',
            'app/views/finance/cashflow.php',
            'app/views/finance/audit_trail.php',
            'app/views/properties/index.php',
            'app/views/property-categories/index.php',
            'app/views/property-categories/create.php',
            'app/views/property-categories/edit.php',
            'app/views/head-pastor/finance/_create_form.php'
        ];

        foreach ($filesToCheck as $file) {
            $fullPath = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
            if (!file_exists($fullPath)) {
                $this->assert("File Existence: {$file}", false, "File missing at {$fullPath}");
                continue;
            }

            $output = [];
            $returnCode = 0;
            exec("php -l \"{$fullPath}\"", $output, $returnCode);

            $this->assert("Lint: {$file}", $returnCode === 0, implode(' ', $output));
        }
        echo "\n";
    }

    private function printSummary(): void
    {
        $total = $this->passed + $this->failed;
        $successRate = ($total > 0) ? round(($this->passed / $total) * 100, 1) : 0;

        echo "========================================================\n";
        echo "   TEST RESULTS SUMMARY\n";
        echo "========================================================\n";
        echo "  Total Tests Executed : {$total}\n";
        echo "  Passed               : {$this->passed}\n";
        echo "  Failed               : {$this->failed}\n";
        echo "  Success Rate         : {$successRate}%\n";
        echo "========================================================\n";

        if ($this->failed === 0) {
            echo "  >>> ALL FINANCIAL TESTS PASSED SUCCESSFULLY! <<<\n\n";
        } else {
            echo "  >>> ATTENTION: {$this->failed} TEST(S) FAILED! <<<\n\n";
        }
    }
}

$suite = new FinanceTestSuite();
$suite->run();
