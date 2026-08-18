@extends('layouts.admin')

@section('title', 'FAQ Manager | Admin')
@section('page-title', 'Frequently Asked Questions (FAQs) Manager')

@section('content')

<div x-data="faqAdminApp()">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin: 0;">
            Manage Customer FAQs
        </h2>
        <button type="button" @click="openAddModal()" class="btn btn-primary" style="border-radius: 30px; font-weight: 700; padding: 10px 22px;">
            <i class="fa-solid fa-plus me-1"></i> Add New FAQ
        </button>
    </div>

    @if(session('success'))
        <div style="background: #E8F8F5; border: 1px solid #A3E4D7; color: #117864; padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; box-shadow: var(--shadow-sm); overflow: hidden;">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Order</th>
                        <th>Question</th>
                        <th>Answer Snippet</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                        <tr>
                            <td style="font-weight: 800; color: var(--maroon);">#{{ $faq->order }}</td>
                            <td style="font-weight: 700; color: var(--maroon); max-width: 280px;">{{ $faq->question }}</td>
                            <td style="color: var(--charcoal-light); max-width: 320px;">{{ \Illuminate\Support\Str::limit($faq->answer, 90) }}</td>
                            <td>
                                <span class="badge-status {{ $faq->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $faq->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 8px; align-items: center; justify-content: flex-end;">
                                    <button type="button" @click="openEditModal({{ json_encode($faq) }})" class="btn btn-outline btn-sm" style="padding: 6px 14px; border-radius: 20px; font-weight: 600;">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this FAQ?');" style="margin: 0;">
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
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--muted);">No FAQs created yet. Click "+ Add New FAQ" to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add / Edit FAQ Modal Box -->
    <div x-show="showModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.55); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 999999; padding: 20px;" x-cloak>
        <div @click.away="showModal = false" style="background: var(--white); border-radius: 24px; width: 100%; max-width: 600px; padding: 32px; box-shadow: var(--shadow-lg); border: 1px solid var(--cream-dark); margin: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 12px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin: 0;" x-text="isEdit ? 'Edit FAQ' : 'Add New FAQ'"></h3>
                <button type="button" @click="showModal = false" style="background: none; border: none; font-size: 1.5rem; color: var(--muted); cursor: pointer;">&times;</button>
            </div>

            <form :action="isEdit ? '/admin/faqs/' + activeFaq.id : '{{ route('admin.faqs.store') }}'" method="POST">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Question</label>
                    <input type="text" name="question" x-model="activeFaq.question" required placeholder="e.g. How does takeaway store pickup work?" style="width: 100%; padding: 12px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; outline: none;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Answer</label>
                    <textarea name="answer" x-model="activeFaq.answer" rows="5" required placeholder="Write detailed answer text..." style="width: 100%; padding: 12px; border: 1.5px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; outline: none; resize: vertical;"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--maroon); display: block; margin-bottom: 6px;">Display Order Index</label>
                        <input type="number" name="order" x-model="activeFaq.order" style="width: 100%; padding: 10px; border: 1.5px solid var(--cream-dark); border-radius: 10px;">
                    </div>
                    <div style="display: flex; align-items: center; margin-top: 24px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600; color: var(--maroon);">
                            <input type="checkbox" name="is_active" value="1" :checked="activeFaq.is_active">
                            <span>Active / Visible on Frontend</span>
                        </label>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" @click="showModal = false" class="btn btn-outline" style="border-radius: 30px;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 30px; font-weight: 700; padding: 10px 24px;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save FAQ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function faqAdminApp() {
        return {
            showModal: false,
            isEdit: false,
            activeFaq: { id: '', question: '', answer: '', order: 1, is_active: true },
            openAddModal() {
                this.isEdit = false;
                this.activeFaq = { id: '', question: '', answer: '', order: 1, is_active: true };
                this.showModal = true;
            },
            openEditModal(faq) {
                this.isEdit = true;
                this.activeFaq = Object.assign({}, faq);
                this.showModal = true;
            }
        }
    }
</script>
@endsection
