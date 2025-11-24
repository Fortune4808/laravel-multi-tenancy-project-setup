<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\Central\Branch;
use App\Models\Central\Setup\Counter;
use Illuminate\Queue\SerializesModels;
use App\Services\BranchConnectionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SetupNewBranch implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(protected $branchName, protected $dbName, protected $createdBy)
    {
        $this->branchName = $branchName;
        $this->dbName = $dbName;
        $this->createdBy = $createdBy;
    }

    public function handle(): void
    {
        BranchConnectionService::setupBranchDatabase($this->dbName);
        $branchId = Counter::generateCustomId('BRNCH');
        Branch::create([
            'branch_id'  => $branchId,
            'branch_name' => $this->branchName,
            'database_name' => $this->dbName,
            'created_by' => $this->createdBy,
        ]);
    }
}
