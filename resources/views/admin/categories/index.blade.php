@extends('layouts.admin')

@section('title', 'Category Manage | Admin')
@section('page-title', 'Category Management')

@section('content')

<div x-data="categoryAdminApp()">
    <!-- Header Controls & Search Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: var(--maroon); margin: 0;">
                Category Directory
            </h2>
            <p style="font-size: 0.88rem; color: var(--muted); margin: 4px 0 0 0;">Manage store product categories, parent-child hierarchies, images, and visibility.</p>
        </div>
        <button type="button" @click="openAddModal()" class="btn btn-primary" style="border-radius: 30px; font-weight: 700; padding: 10px 22px;">
            <i class="fa-solid fa-plus me-1"></i> Add New Category
        </button>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div style="background: #E8F8F5; border: 1px solid #A3E4D7; color: #117864; padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.2rem;"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #FDEDEC; border: 1px solid #F5B7B1; color: #922B21; padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.2rem;"></i> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #FDEDEC; border: 1px solid #F5B7B1; color: #922B21; padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-weight: 500;">
            <div style="font-weight: 700; margin-bottom: 6px;"><i class="fa-solid fa-circle-xmark"></i> Please correct the following errors:</div>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 16px; padding: 16px 20px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
        <form action="{{ route('admin.categories.index') }}" method="GET" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 220px; position: relative;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted);"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search category name, slug, or description..." style="width: 100%; padding: 10px 14px 10px 40px; border: 1.5px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; outline: none;">
            </div>
            
            <div style="width: 160px;">
                <select name="status" style="width: 100%; padding: 10px 14px; border: 1.5px solid var(--cream-dark); border-radius: 10px; font-size: 0.9rem; outline: none; background: #FFF;">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Disabled Only</option>
                </select>
            </div>

            <button type="submit" class="btn btn-outline" style="border-radius: 10px; padding: 10px 18px;">
                <i class="fa-solid fa-filter me-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline" style="border-radius: 10px; padding: 10px 14px; color: var(--muted); border-color: var(--cream-dark);">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Category Data Table -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; box-shadow: var(--shadow-sm); overflow: hidden;">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Image</th>
                        <th>Category Name</th>
                        <th>Slug</th>
                        <th>Parent Category</th>
                        <th style="text-align: center;">Products</th>
                        <th style="text-align: center;">Sort Order</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                @if($category->image)
                                    <img src="{{ Str::startsWith($category->image, ['http://', 'https://']) ? $category->image : asset($category->image) }}" alt="{{ $category->name }}" style="width: 44px; height: 44px; object-fit: cover; border-radius: 10px; border: 1px solid var(--cream-dark);">
                                @else
                                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #F8F9F9; border: 1px solid var(--cream-dark); display: flex; align-items: center; justify-content: center; color: var(--muted); font-size: 1.2rem;">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--maroon); font-size: 0.95rem;">{{ $category->name }}</div>
                                @if($category->description)
                                    <div style="font-size: 0.8rem; color: var(--muted); max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $category->description }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <code style="background: #F4F6F6; padding: 4px 8px; border-radius: 6px; font-size: 0.82rem; color: #5D6D7E;">{{ $category->slug }}</code>
                            </td>
                            <td>
                                @if($category->parent)
                                    <span style="background: #EBF5FB; color: #2980B9; padding: 4px 10px; border-radius: 12px; font-size: 0.82rem; font-weight: 600;">
                                        <i class="fa-solid fa-folder-tree me-1"></i> {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span style="color: var(--muted); font-size: 0.82rem; font-style: italic;">Top Level / Root</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-status" style="background: #F2F4F4; color: var(--maroon); font-weight: 700;">
                                    {{ $category->products_count }} {{ Str::plural('item', $category->products_count) }}
                                </span>
                            </td>
                            <td style="text-align: center; font-weight: 700; color: var(--charcoal-light);">
                                {{ $category->sort_order }}
                            </td>
                            <td>
                                <span class="badge-status {{ $category->status ? 'badge-success' : 'badge-danger' }}">
                                    {{ $category->status ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 8px; align-items: center; justify-content: flex-end;">
                                    <button type="button" @click="openEditModal({{ json_encode($category) }})" class="btn btn-outline btn-sm" style="padding: 6px 14px; border-radius: 20px; font-weight: 600;">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete category \'{{ addslashes($category->name) }}\'?');" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm" style="padding: 6px 12px; border-radius: 20px; color: #C0392B; border-color: #FADBD8;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 48px 20px; color: var(--muted);">
                                <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; color: var(--cream-dark); margin-bottom: 12px; display: block;"></i>
                                No categories found. Click <strong>"+ Add New Category"</strong> to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid var(--cream-dark); display: flex; justify-content: flex-end;">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit Category Modal Box -->
    <div x-show="showModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 999999; padding: 20px;" x-cloak>
        <div @click.away="showModal = false" style="background: var(--white); border-radius: 24px; width: 100%; max-width: 680px; max-height: 90vh; overflow-y: auto; padding: 32px; box-shadow: var(--shadow-lg); border: 1px solid var(--cream-dark); margin: auto;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 16px;">
                <div>
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin: 0;" x-text="isEdit ? 'Edit Category' : 'Add New Category'"></h3>
                    <p style="font-size: 0.82rem; color: var(--muted); margin: 4px 0 0 0;" x-text="isEdit ? 'Update details for category ID #' + activeCategory.id : 'Create a new product classification category'"></p>
                </div>
                <button type="button" @click="showModal = false" style="background: none; border: none; font-size: 1.6rem; color: var(--muted); cursor: pointer; padding: 0; line-height: 1;">&times;</button>
            </div>

            <form :action="isEdit ? '/admin/categories/' + activeCategory.id : '{{ route('admin.categories.store') }}'" method="POST" enctype="multipart/form-data">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Category Name & Parent Category -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Category Name <span style="color: #C0392B;">*</span></label>
                        <input type="text" name="name" x-model="activeCategory.name" @input="onNameChange()" required placeholder="e.g. Spices & Masalas" style="width: 100%; padding: 11px 14px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none;">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Parent Category</label>
                        <select name="parent_id" x-model="activeCategory.parent_id" style="width: 100%; padding: 11px 14px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; background: #FFF;">
                            <option value="">None (Top Level / Root Category)</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}" x-show="activeCategory.id != {{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Slug & Sort Order -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Slug (URL Identifier)</label>
                        <input type="text" name="slug" x-model="activeCategory.slug" placeholder="Auto-generated if left blank" style="width: 100%; padding: 11px 14px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none;">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Sort Order</label>
                        <input type="number" name="sort_order" x-model="activeCategory.sort_order" placeholder="0" style="width: 100%; padding: 11px 14px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none;">
                    </div>
                </div>

                <!-- Image File / URL -->
                <div style="margin-bottom: 16px; background: #FAF9F6; padding: 16px; border-radius: 16px; border: 1px solid var(--cream-dark);">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 8px;">Category Image</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <span style="font-size: 0.78rem; color: var(--muted); display: block; margin-bottom: 4px;">Upload File:</span>
                            <input type="file" name="image_file" accept="image/*" style="font-size: 0.85rem; width: 100%;">
                        </div>
                        <div>
                            <span style="font-size: 0.78rem; color: var(--muted); display: block; margin-bottom: 4px;">Or Image URL:</span>
                            <input type="text" name="image" x-model="activeCategory.image" placeholder="https://..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--cream-dark); border-radius: 8px; font-size: 0.85rem; outline: none;">
                        </div>
                    </div>
                </div>

                <!-- Banner File / URL -->
                <div style="margin-bottom: 16px; background: #FAF9F6; padding: 16px; border-radius: 16px; border: 1px solid var(--cream-dark);">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 8px;">Category Banner Image (Optional Header Banner)</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <span style="font-size: 0.78rem; color: var(--muted); display: block; margin-bottom: 4px;">Upload File:</span>
                            <input type="file" name="banner_file" accept="image/*" style="font-size: 0.85rem; width: 100%;">
                        </div>
                        <div>
                            <span style="font-size: 0.78rem; color: var(--muted); display: block; margin-bottom: 4px;">Or Banner URL:</span>
                            <input type="text" name="banner" x-model="activeCategory.banner" placeholder="https://..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--cream-dark); border-radius: 8px; font-size: 0.85rem; outline: none;">
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Description</label>
                    <textarea name="description" x-model="activeCategory.description" rows="3" placeholder="Brief category description..." style="width: 100%; padding: 11px 14px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; resize: vertical;"></textarea>
                </div>

                <!-- Status Checkbox -->
                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 600; color: var(--maroon);">
                        <input type="checkbox" name="status" value="1" :checked="activeCategory.status" style="width: 18px; height: 18px; accent-color: var(--maroon);">
                        <span>Active Category (Visible on storefront & product forms)</span>
                    </label>
                </div>

                <!-- SEO Collapsible Box -->
                <details style="margin-bottom: 24px; border: 1px solid var(--cream-dark); border-radius: 14px; padding: 12px 16px; background: #FCFBF9;">
                    <summary style="font-weight: 700; color: var(--maroon); cursor: pointer; font-size: 0.88rem;">
                        <i class="fa-solid fa-bullhorn me-1"></i> SEO Metadata (Optional)
                    </summary>
                    <div style="margin-top: 14px;">
                        <div style="margin-bottom: 12px;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 4px;">SEO Meta Title</label>
                            <input type="text" name="seo_title" x-model="activeCategory.seo_title" placeholder="e.g. Authentic Indian Spices | Desi Foods" style="width: 100%; padding: 9px 12px; border: 1px solid var(--cream-dark); border-radius: 8px; font-size: 0.88rem; outline: none;">
                        </div>
                        <div>
                            <label style="font-size: 0.82rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 4px;">SEO Meta Description</label>
                            <textarea name="seo_description" x-model="activeCategory.seo_description" rows="2" placeholder="Search engine description snippet..." style="width: 100%; padding: 9px 12px; border: 1px solid var(--cream-dark); border-radius: 8px; font-size: 0.88rem; outline: none; resize: vertical;"></textarea>
                        </div>
                    </div>
                </details>

                <!-- Modal Action Buttons -->
                <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--cream-dark); padding-top: 20px;">
                    <button type="button" @click="showModal = false" class="btn btn-outline" style="border-radius: 30px; padding: 10px 22px;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 30px; font-weight: 700; padding: 10px 26px;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function categoryAdminApp() {
        return {
            showModal: false,
            isEdit: false,
            activeCategory: {
                id: '',
                name: '',
                slug: '',
                parent_id: '',
                image: '',
                banner: '',
                description: '',
                sort_order: 0,
                status: true,
                seo_title: '',
                seo_description: ''
            },
            openAddModal() {
                this.isEdit = false;
                this.activeCategory = {
                    id: '',
                    name: '',
                    slug: '',
                    parent_id: '',
                    image: '',
                    banner: '',
                    description: '',
                    sort_order: 0,
                    status: true,
                    seo_title: '',
                    seo_description: ''
                };
                this.showModal = true;
            },
            openEditModal(category) {
                this.isEdit = true;
                this.activeCategory = Object.assign({}, category);
                // Ensure parent_id is string or empty for select matching
                this.activeCategory.parent_id = category.parent_id ? String(category.parent_id) : '';
                this.activeCategory.status = Boolean(category.status);
                this.showModal = true;
            },
            onNameChange() {
                if (!this.isEdit && !this.activeCategory.slug) {
                    // Auto suggest slug format
                    this.activeCategory.slug = this.activeCategory.name
                        .toLowerCase()
                        .trim()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                }
            }
        }
    }
</script>
@endsection
