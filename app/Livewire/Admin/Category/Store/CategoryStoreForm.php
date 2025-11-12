<?php

namespace App\Livewire\Admin\Category\Store;

use App\Livewire\Admin\Category\Store\Concerns\ManagesColor;
use App\Livewire\Admin\Category\Store\Concerns\ManagesLoadData;
use App\Livewire\Admin\Category\Store\Concerns\ManagesSaving;
use App\Livewire\Admin\Category\Store\Concerns\ManagesSlug;
use Livewire\Component;

class CategoryStoreForm extends Component
{
    use ManagesColor;
    use ManagesLoadData;
    use ManagesSaving;
    use ManagesSlug;

    // Mode
    public string $mode = 'create'; // 'create' or 'edit'

    public ?int $categoryId = null;

    // Category model (loaded once, used in multiple places)
    public ?\App\Models\Category $category = null;

    // Category data
    public string $name = '';

    public string $original_name = '';

    public string $slug = '';

    public ?string $description = null;

    public string $color = '#3B82F6';

    public bool $is_active = true;

    public function mount(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
        $this->mode = $categoryId ? 'edit' : 'create';

        // Load category data if editing
        if ($this->mode === 'edit' && $this->categoryId) {
            $this->loadCategory($this->categoryId);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.category.store.category-store-form');
    }
}
