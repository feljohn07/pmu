<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\User;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

// Import Mary UI components being used
use Mary\Traits\Toast;

class ProjectAccessManager extends Component
{
    use Toast;

    public ?string $projectId = null;
    public ?Project $project = null;

    // --- Modal State ---
    public bool $showUserModal = false;
    public string $userSearch = '';
    public array $selectedUserIds = [];

    /**
     * Mount the component.
     */
    public function mount(string|int|null $projectId = null): void
    {
        if (!empty($projectId)) {
            $this->projectId = $projectId;
            $this->project = Project::with('users')->find($this->projectId);
        }
    }

    /**
     * Computed property to get users available to be added.
     * Filters based on search, excludes users already in the project,
     * and excludes users with 'admin' or 'staff' roles.
     */
    #[Computed]
    public function availableUsers(): Collection
    {
        if (!$this->project) {
            return collect();
        }

        $existingUserIds = $this->project->users->pluck('id');

        // Roles to exclude
        $excludedRoles = ['admin', 'staff']; // Define the roles you want to exclude

        return User::query()
            // Exclude users already in the project
            ->whereNotIn('id', $existingUserIds)

            // *** Add Spatie role exclusion ***
            // Exclude users who have any of the specified roles
            ->whereDoesntHave('roles', function ($query) use ($excludedRoles) {
                $query->whereIn('name', $excludedRoles); // Check role name in the 'roles' table
            })
            // *** End Spatie role exclusion ***

            // Apply search filter
            ->when($this->userSearch, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->limit(15)
            ->get();
    }

    /**
     * Open the user selection modal.
     */
    public function openUserModal(): void
    {
        $this->reset(['userSearch', 'selectedUserIds']);
        $this->showUserModal = true;
    }

    /**
     * Close the user selection modal.
     */
    public function closeModal(): void
    {
         $this->showUserModal = false;
         $this->reset(['userSearch', 'selectedUserIds']);
    }

    /**
     * Grant access to the selected users.
     */
    public function grantAccess(): void
    {
        if (!$this->project || empty($this->selectedUserIds)) {
             $this->warning('No users selected or project not found.');
            return;
        }

        $this->project->users()->syncWithoutDetaching($this->selectedUserIds);
        $this->project->load('users'); // Reload project users

        $this->reset(['userSearch', 'selectedUserIds']);
        $this->showUserModal = false;

        $this->success(count($this->selectedUserIds) . ' user(s) granted access.', position: 'toast-bottom');
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.project-access-manager', [
            'project' => $this->project,
        ]);
    }

     /**
      * Optional: Method to revoke access (example)
      */
     public function revokeAccess(int $userId): void
     {
         if ($this->project) {
             $this->project->users()->detach($userId);
             $this->project->load('users'); // Refresh the user list
             $this->success('User access revoked.', position: 'toast-bottom');
         }
     }
}
