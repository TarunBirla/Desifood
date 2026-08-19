@extends('layouts.admin')

@section('title', 'Manage Customer Testimonials | Admin Panel')

@section('content')

<div style="padding: 24px;" x-data="{ addModalOpen: false, editModalOpen: false, editItem: {} }">
    <!-- Header Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--maroon); margin-bottom: 4px;">Customer Testimonials</h1>
            <p style="color: #666; font-size: 0.95rem; margin: 0;">Add, edit, update, or remove customer reviews & testimonials displayed on the storefront home page.</p>
        </div>

        <button type="button" @click="addModalOpen = true" class="btn btn-primary" style="border-radius: 30px; font-weight: 700; padding: 10px 24px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-plus"></i> Add New Testimonial
        </button>
    </div>

    @if(session('success'))
        <div style="background: rgba(46,125,50,0.1); color: #2E7D32; padding: 14px 20px; border-radius: 12px; border-left: 4px solid #2E7D32; margin-bottom: 24px; font-weight: 600;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div style="background: #fff; border: 1px solid var(--cream-dark); border-radius: 16px; padding: 16px 20px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
        <form action="{{ route('admin.testimonials.index') }}" method="GET" style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
            <div style="position: relative; flex: 1; min-width: 260px;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--saffron);"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by customer name, company, or review..." style="width: 100%; box-sizing: border-box; padding: 10px 16px 10px 42px; border-radius: 30px; border: 1.5px solid var(--cream-dark); font-size: 0.92rem; outline: none;">
            </div>
            <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 20px; padding: 10px 20px;">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline btn-sm" style="border-radius: 20px; padding: 10px 16px;">Clear</a>
            @endif
        </form>
    </div>

    <!-- Testimonials List Table -->
    <div style="background: #fff; border: 1px solid var(--cream-dark); border-radius: 20px; box-shadow: var(--shadow-sm); overflow: hidden;">
        <div class="table-responsive">
            <table class="custom-table" style="width: 100%; min-width: 700px;">
                <thead>
                    <tr>
                        <th>Client / Customer</th>
                        <th>Title / Location</th>
                        <th>Rating</th>
                        <th>Testimonial Content</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($testimonials as $t)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="{{ $t->client_avatar ?? asset('images/avatar-placeholder.png') }}" 
                                         style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--cream-dark);" 
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($t->client_name) }}&background=890F14&color=fff'">
                                    <div>
                                        <strong style="color: var(--maroon); font-size: 0.98rem; display: block;">{{ $t->client_name }}</strong>
                                        @if($t->company_name)
                                            <span style="font-size: 0.78rem; color: var(--saffron-deep); font-weight: 500;">{{ $t->company_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 0.88rem; color: #555;">{{ $t->client_title ?? 'Verified Customer' }}</span>
                            </td>
                            <td>
                                <div style="color: #F39C12; font-size: 0.85rem; display: flex; gap: 2px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star{{ $i <= $t->rating ? '' : '-o' }}" style="color: {{ $i <= $t->rating ? '#F39C12' : '#CCC' }};"></i>
                                    @endfor
                                </div>
                            </td>
                            <td>
                                <p style="margin: 0; font-size: 0.88rem; color: #444; max-width: 320px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    "{{ $t->content }}"
                                </p>
                            </td>
                            <td>
                                @if($t->status)
                                    <span class="badge-status badge-success" style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px;">Active</span>
                                @else
                                    <span class="badge-status badge-warning" style="font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; background: #eee; color: #777;">Disabled</span>
                                @endif

                                @if($t->is_featured)
                                    <span class="badge-status" style="font-size: 0.75rem; padding: 4px 8px; border-radius: 12px; background: rgba(230,126,34,0.15); color: var(--saffron-deep); margin-left: 4px;">Featured</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 8px; align-items: center;">
                                    <button type="button" 
                                            @click="editItem = {{ json_encode($t) }}; editModalOpen = true" 
                                            class="btn btn-outline btn-sm" 
                                            style="border-radius: 20px; padding: 6px 14px; font-weight: 600;">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                    </button>

                                    <form action="{{ route('admin.testimonials.destroy', $t->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Delete this testimonial permanently?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: none; border: none; color: #C0392B; cursor: pointer; padding: 6px 8px; font-size: 1.05rem;" title="Delete Testimonial">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #777;">
                                No customer testimonials found. Click "Add New Testimonial" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding: 16px 24px;">
            {{ $testimonials->links() }}
        </div>
    </div>

    <!-- ADD TESTIMONIAL MODAL -->
    <div x-show="addModalOpen" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 20px;" x-cloak>
        <div @click.away="addModalOpen = false" style="background: #fff; border-radius: 20px; width: 100%; max-width: 580px; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--cream-dark); padding-bottom: 14px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin: 0;">Add New Testimonial</h3>
                <button type="button" @click="addModalOpen = false" style="background: none; border: none; font-size: 1.3rem; color: #777; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Client Name *</label>
                        <input type="text" name="client_name" required placeholder="e.g. Rahul Sharma" style="width: 100%; box-sizing: border-box; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--cream-dark); outline: none;">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Title / Designation</label>
                        <input type="text" name="client_title" placeholder="e.g. Hounslow Resident" style="width: 100%; box-sizing: border-box; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--cream-dark); outline: none;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Company / Location</label>
                        <input type="text" name="company_name" placeholder="e.g. London TW3" style="width: 100%; box-sizing: border-box; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--cream-dark); outline: none;">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Rating (1 to 5 Stars) *</label>
                        <select name="rating" required style="width: 100%; box-sizing: border-box; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--cream-dark); outline: none; background: #fff;">
                            <option value="5">⭐⭐⭐⭐⭐ (5 Stars)</option>
                            <option value="4">⭐⭐⭐⭐ (4 Stars)</option>
                            <option value="3">⭐⭐⭐ (3 Stars)</option>
                            <option value="2">⭐⭐ (2 Stars)</option>
                            <option value="1">⭐ (1 Star)</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Client Avatar Image (Optional)</label>
                    <input type="file" name="client_avatar" accept="image/*" style="width: 100%; box-sizing: border-box; padding: 8px 12px; border-radius: 10px; border: 1px solid var(--cream-dark); outline: none; background: #fff;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Testimonial Review Content *</label>
                    <textarea name="content" rows="4" required placeholder="Write the customer's review text here..." style="width: 100%; box-sizing: border-box; padding: 12px; border-radius: 12px; border: 1px solid var(--cream-dark); outline: none; font-family: inherit; font-size: 0.92rem;"></textarea>
                </div>

                <div style="display: flex; gap: 20px; margin-bottom: 24px; background: var(--cream); padding: 12px 16px; border-radius: 12px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: var(--maroon); cursor: pointer;">
                        <input type="checkbox" name="status" value="1" checked style="accent-color: var(--maroon);"> Active Status
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: var(--saffron-deep); cursor: pointer;">
                        <input type="checkbox" name="is_featured" value="1" checked style="accent-color: var(--saffron);"> Feature on Home Page
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" @click="addModalOpen = false" class="btn btn-outline" style="border-radius: 25px; padding: 10px 20px;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 25px; padding: 10px 24px; font-weight: 700;">Save Testimonial</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT TESTIMONIAL MODAL -->
    <div x-show="editModalOpen" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 20px;" x-cloak>
        <div @click.away="editModalOpen = false" style="background: #fff; border-radius: 20px; width: 100%; max-width: 580px; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--cream-dark); padding-bottom: 14px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin: 0;">Edit Testimonial</h3>
                <button type="button" @click="editModalOpen = false" style="background: none; border: none; font-size: 1.3rem; color: #777; cursor: pointer;">&times;</button>
            </div>

            <form :action="'/admin/testimonials/' + editItem.id" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Client Name *</label>
                        <input type="text" name="client_name" x-model="editItem.client_name" required style="width: 100%; box-sizing: border-box; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--cream-dark); outline: none;">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Title / Designation</label>
                        <input type="text" name="client_title" x-model="editItem.client_title" style="width: 100%; box-sizing: border-box; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--cream-dark); outline: none;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Company / Location</label>
                        <input type="text" name="company_name" x-model="editItem.company_name" style="width: 100%; box-sizing: border-box; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--cream-dark); outline: none;">
                    </div>
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Rating (1 to 5 Stars) *</label>
                        <select name="rating" x-model="editItem.rating" required style="width: 100%; box-sizing: border-box; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--cream-dark); outline: none; background: #fff;">
                            <option value="5">⭐⭐⭐⭐⭐ (5 Stars)</option>
                            <option value="4">⭐⭐⭐⭐ (4 Stars)</option>
                            <option value="3">⭐⭐⭐ (3 Stars)</option>
                            <option value="2">⭐⭐ (2 Stars)</option>
                            <option value="1">⭐ (1 Star)</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Client Avatar Image (Optional)</label>
                    <input type="file" name="client_avatar" accept="image/*" style="width: 100%; box-sizing: border-box; padding: 8px 12px; border-radius: 10px; border: 1px solid var(--cream-dark); outline: none; background: #fff;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Testimonial Review Content *</label>
                    <textarea name="content" x-model="editItem.content" rows="4" required style="width: 100%; box-sizing: border-box; padding: 12px; border-radius: 12px; border: 1px solid var(--cream-dark); outline: none; font-family: inherit; font-size: 0.92rem;"></textarea>
                </div>

                <div style="display: flex; gap: 20px; margin-bottom: 24px; background: var(--cream); padding: 12px 16px; border-radius: 12px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: var(--maroon); cursor: pointer;">
                        <input type="checkbox" name="status" value="1" :checked="editItem.status" style="accent-color: var(--maroon);"> Active Status
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: var(--saffron-deep); cursor: pointer;">
                        <input type="checkbox" name="is_featured" value="1" :checked="editItem.is_featured" style="accent-color: var(--saffron);"> Feature on Home Page
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" @click="editModalOpen = false" class="btn btn-outline" style="border-radius: 25px; padding: 10px 20px;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 25px; padding: 10px 24px; font-weight: 700;">Update Testimonial</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
